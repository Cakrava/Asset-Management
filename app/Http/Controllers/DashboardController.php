<?php

namespace App\Http\Controllers;

use App\Models\DeploymentDevice;
use App\Models\DeploymentDeviceDetail;
use App\Models\Letters;
use App\Models\Profile;
use App\Models\StoredDevice;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ======================================================================
        // BAGIAN 1: AUTENTIKASI DAN INISIALISASI DASAR
        // ======================================================================
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // ======================================================================
        // BAGIAN 2: LOGIKA PROFIL PENGGUNA (Berlaku untuk semua role)
        // ======================================================================
        $profile = Profile::where('user_id', $user->id)->first();
        $totalFields = 7;
        $filledFields = 0;
        $missingFields = [];

        session(['istemporary' => $user->created_at->eq($user->updated_at)]);

        if ($profile) {
            if (!empty($profile->name)) $filledFields++; else $missingFields[] = 'Nama';
            if (!empty($profile->phone)) $filledFields++; else $missingFields[] = 'Nomor Telepon';
            if (!empty($profile->institution)) $filledFields++; else $missingFields[] = 'Institusi';
            if (!empty($profile->institution_type)) $filledFields++; else $missingFields[] = 'Jenis Institusi';
            if (!empty($profile->address)) $filledFields++; else $missingFields[] = 'Alamat';
            if (!empty($profile->reference)) $filledFields++; else $missingFields[] = 'Referensi';
            if (!empty($profile->image)) $filledFields++; else $missingFields[] = 'Foto Profil';
        } else {
            $missingFields = ['Nama', 'Nomor Telepon', 'Institusi', 'Jenis Institusi', 'Alamat', 'Referensi', 'Foto Profil'];
        }

        $completionPercentage = ($totalFields > 0) ? ($filledFields / $totalFields) * 100 : 0;
        session()->put('name', $profile->name ?? 'Guest');

        // Kumpulkan data profil ke dalam array utama
        $viewData = [
            'profile' => $profile,
            'completionPercentage' => $completionPercentage,
            'missingFields' => $missingFields,
            'email' => $user->email,
        ];

        // ======================================================================
        // BAGIAN 3: PENGAMBILAN DATA BERDASARKAN ROLE
        // ======================================================================
        
        // Inisialisasi variabel dashboard dengan nilai default untuk mencegah error
        $dashboardData = [
            'totalDeviceStock' => 0, 'totalDeployedDevices' => 0, 'pendingTransactionsCount' => 0,
            'neededLettersCount' => 0, 'openTicketsCount' => 0,
            'transactionChartData' => ['labels' => [], 'inData' => [], 'outData' => []],
            'transactionStatusDistribution' => [], 'recentTransactions' => collect(),
            'urgentTickets' => collect(), 'neededLetters' => collect(),
            'userLetters' => collect(), // Data spesifik untuk role 'user'
        ];

        $routePages = 'page.user.dashboard-user'; // Default view

        switch ($user->role) {
            case 'admin':
            case 'master':
                // Ambil data operasional untuk Admin & Master
                $dashboardData['totalDeviceStock'] = StoredDevice::sum('stock');
                $dashboardData['totalDeployedDevices'] = DeploymentDeviceDetail::sum('quantity');
                $dashboardData['pendingTransactionsCount'] = Transaction::where('instalation_status', 'Pending')->count();
                $dashboardData['neededLettersCount'] = Letters::where('status', 'Needed')->count();
                $dashboardData['openTicketsCount'] = Ticket::whereIn('status', ['pending', 'process'])->count();

                $transactionActivity = Transaction::select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw("SUM(CASE WHEN transaction_type = 'in' THEN 1 ELSE 0 END) as in_count"),
                        DB::raw("SUM(CASE WHEN transaction_type = 'out' THEN 1 ELSE 0 END) as out_count")
                    )->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->groupBy('date')->orderBy('date', 'asc')->get();
                
                $dashboardData['transactionChartData'] = [
                    'labels' => $transactionActivity->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d M')),
                    'inData' => $transactionActivity->pluck('in_count'),
                    'outData' => $transactionActivity->pluck('out_count'),
                ];
                $dashboardData['transactionStatusDistribution'] = Transaction::select('instalation_status', DB::raw('count(*) as total'))
                    ->groupBy('instalation_status')->pluck('total', 'instalation_status');
                $dashboardData['recentTransactions'] = Transaction::with(['client.profile', 'otherSourceProfile'])->latest()->take(5)->get();
                $dashboardData['urgentTickets'] = Ticket::with('user.profile')->whereIn('status', ['pending', 'process'])->latest('updated_at')->take(5)->get();
                $dashboardData['neededLetters'] = Letters::with('client.profile')->where('status', 'Needed')->latest()->take(5)->get();
                
                // Tentukan view untuk admin/master
                $routePages = ($user->role == 'admin') ? 'page.admin.dashboard' : 'page.master.dashboard-master';
                break;

                case 'user':
                    // Ambil semua data surat terlebih dahulu
                    $allUserLetters = Letters::where('client_id', $user->id)->where('status', '!=', 'Deleted')->latest()->get();
                    
                    // Data untuk surat yang sedang berlangsung (biasanya tidak terlalu banyak, jadi tampilkan semua)
                    $viewData['ongoingLetters'] = $allUserLetters->whereIn('status', ['Needed', 'Open']);
                
                    // ==========================================================
                    // BARU: Logika untuk memisahkan data riwayat surat
                    // ==========================================================
                    $allClosedLetters = $allUserLetters->whereIn('status', ['Closed', 'Signed']);
                    $viewData['allClosedLetters'] = $allClosedLetters; // Data lengkap untuk modal
                    $viewData['recentClosedLetters'] = $allClosedLetters->take(5); // 5 data terbaru untuk dashboard
                
                    // Ambil data perangkat yang terpasang
                    $deploymentIds = DeploymentDevice::where('client_id', $user->id)->pluck('unique_id');
                    $viewData['deployedDeviceDetails'] = DeploymentDeviceDetail::whereIn('unique_id', $deploymentIds)
                        ->with('storedDevice.device')
                        ->get()
                        ->sortBy('storedDevice.device.device_name');
                
                    // ==========================================================
                    // BARU: Logika untuk memisahkan data TIKET pengguna
                    // ==========================================================
                    $allUserTickets = Ticket::where('user_id', $user->id)->latest('updated_at')->get();
                    $viewData['allUserTickets'] = $allUserTickets; // Data lengkap untuk modal
                    $viewData['recentUserTickets'] = $allUserTickets->take(5); // 5 data terbaru untuk dashboard
                    
                    // Data untuk kartu statistik tetap menggunakan data lengkap
                    $viewData['pendingTicketsCount'] = $allUserTickets->where('status', 'pending')->count();
                    
                    $routePages = 'page.user.dashboard-user';
                    break;

         

            default:
                // Fallback jika role tidak ada
                return redirect()->route('login')->with('error', 'Role tidak valid.');
        }

        // Gabungkan data profil dengan data spesifik role
        $viewData = array_merge($viewData, $dashboardData);
        
        // ======================================================================
        // BAGIAN 4: TAMPILKAN VIEW
        // ======================================================================
        return view($routePages, $viewData);
    }

}
