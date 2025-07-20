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
        
        // ======================================================================
        // LOGIKA BARU: Membuat pesan dinamis untuk profil tidak lengkap
        // ======================================================================
        if ($completionPercentage < 100) {
            // Profil TIDAK LENGKAP
            $percentageNeeded = 100 - round($completionPercentage);
            $missingFieldsList = implode(', ', $missingFields);
            $message = "Profil Anda belum lengkap. Kurang {$percentageNeeded}% lagi untuk selesai. Data yang belum diisi: {$missingFieldsList}.";

            // Atur sesi dengan pesan yang informatif
            session(['profile_incomplete' => $message]);
        } else {
            // Profil LENGKAP, hapus sesi jika ada
            session()->forget('profile_incomplete');
        }

        session()->put('name', $profile ? $profile->name : 'Guest');

        $viewData = [
            'profile' => $profile,
            'completionPercentage' => round($completionPercentage),
            'missingFields' => $missingFields, // Tetap dikirim jika ingin digunakan di tempat lain
            'email' => $user->email,
        ];

        // ======================================================================
        // BAGIAN 3: PENGAMBILAN DATA BERDASARKAN ROLE (Tidak ada perubahan di sini)
        // ======================================================================
        $dashboardData = [
            'totalDeviceStock' => 0, 'totalDeployedDevices' => 0, 'pendingTransactionsCount' => 0,
            'neededLettersCount' => 0, 'openTicketsCount' => 0,
            'transactionChartData' => ['labels' => [], 'inData' => [], 'outData' => []],
            'transactionStatusDistribution' => [], 'recentTransactions' => collect(),
            'urgentTickets' => collect(), 'neededLetters' => collect(),
        ];
        $routePages = 'page.user.dashboard-user';

        switch ($user->role) {
            case 'admin':
            case 'master':
                // ... (kode untuk admin & master tetap sama)
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
                $routePages = ($user->role == 'admin') ? 'page.admin.dashboard' : 'page.master.dashboard-master';
                break;

            case 'user':
                // ... (kode untuk user tetap sama)
                $allUserLetters = Letters::where('client_id', $user->id)->where('status', '!=', 'Deleted')->latest()->get();
                $viewData['ongoingLetters'] = $allUserLetters->whereIn('status', ['Needed', 'Open']);
                $allClosedLetters = $allUserLetters->whereIn('status', ['Closed', 'Signed']);
                $viewData['allClosedLetters'] = $allClosedLetters;
                $viewData['recentClosedLetters'] = $allClosedLetters->take(5);
                $deploymentIds = DeploymentDevice::where('client_id', $user->id)->pluck('unique_id');
                $viewData['deployedDeviceDetails'] = DeploymentDeviceDetail::whereIn('unique_id', $deploymentIds)
                    ->with('storedDevice.device')
                    ->get()
                    ->sortBy('storedDevice.device.device_name');
                $allUserTickets = Ticket::where('user_id', $user->id)->latest('updated_at')->get();
                $viewData['allUserTickets'] = $allUserTickets;
                $viewData['recentUserTickets'] = $allUserTickets->take(5); 
                $viewData['pendingTicketsCount'] = $allUserTickets->where('status', 'pending')->count();
                $routePages = 'page.user.dashboard-user';
                break;

            default:
                return redirect()->route('login')->with('error', 'Role tidak valid.');
        }

        $viewData = array_merge($viewData, $dashboardData);
        
        // ======================================================================
        // BAGIAN 4: TAMPILKAN VIEW
        // ======================================================================
        return view($routePages, $viewData);
    }


}
