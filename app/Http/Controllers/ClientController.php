<?php

namespace App\Http\Controllers;

use App\Models\Client; // Pastikan model Client sudah ada
use App\Models\DeploymentDevice;
use App\Models\Message;
use App\Models\Profile;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\DB;
class ClientController extends Controller
{
    public function index()
    {
        // Ambil data user yang belum punya profile
        $users = User::where('role', 'user')
            ->whereColumn('created_at', 'updated_at')
            ->whereNotIn('id', function ($query) {
                $query->select('user_id')->from('profiles')->whereNotNull('user_id');
            })
            ->get();
    
        // Ambil client yang valid (usernya bukan status deleted/duplicate)
        $clients = Profile::with(['user' => function ($query) {
                $query->where('role', 'user')
                    ->whereNotIn('status', ['deleted', 'duplicate']);
            }])
            ->whereHas('user', function ($query) {
                $query->where('role', 'user')
                    ->whereNotIn('status', ['deleted', 'duplicate']);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    
        $institutionTypeNames = [
            'government' => 'Institusi Pemerintah',
            'private' => 'Institusi Swasta',
            'education' => 'Institusi Pendidikan',
            'healthcare' => 'Institusi Kesehatan',
            'nonprofit' => 'Organisasi Nirlaba',
            'other' => 'Lainnya',
        ];
    
        // Cari duplikat berdasarkan kombinasi institusi + type + nama
        $duplicates = Profile::selectRaw('LOWER(institution) as institution, LOWER(institution_type) as institution_type, LOWER(name) as name, COUNT(*) as total, GROUP_CONCAT(user_id) as user_ids')
            ->groupByRaw('LOWER(institution), LOWER(institution_type), LOWER(name)')
            ->having('total', '>', 1)
            ->get();
    
        if ($duplicates->isNotEmpty()) {
            $warningMessage = "<div style='position:relative;'>";
    
            foreach ($duplicates as $dup) {
                $warningMessage .= "<details>";
                $warningMessage .= "<summary>" . $dup->institution . " - " . ($institutionTypeNames[$dup->institution_type] ?? $dup->institution_type) . " - " . $dup->name . " ({$dup->total} data)</summary>";
    
                $userIds = explode(',', $dup->user_ids);
                $duplicateClients = Profile::with('user')->whereIn('user_id', $userIds)->get();
    
                $warningMessage .= "<ul class='duplicate-client-list'>";
                foreach ($duplicateClients as $client) {
                    $warningMessage .= "<li>";
                    $warningMessage .= "<div class='form-check'>";
                    $warningMessage .= "<input class='form-check-input duplicate-checkbox' type='checkbox' value='" . $client->user_id . "' id='duplicateCheckbox_" . $client->user_id . "'>";
                    $warningMessage .= "<label class='form-check-label' for='duplicateCheckbox_" . $client->user_id . "'>";
                    $warningMessage .= $client->institution . " - " . ($institutionTypeNames[$client->institution_type] ?? $client->institution_type) . " - " . $client->name;
                    $warningMessage .= "</label>";
                    $warningMessage .= "</div>";
                    $warningMessage .= "</li>";
                }
                $warningMessage .= "</ul>";
                $warningMessage .= "</details>";
            }
    
            $warningMessage .= "<button type='button' id='btn-bulk-delete-all-duplicates' class='btn btn-danger btn-sm' style='position:absolute; bottom: 10px; right: 10px;' disabled><i class='ti ti-trash'></i> Delete</button>";
            $warningMessage .= "</div>";
    
            Session::flash('warning', $warningMessage);
        }
    
        return view('page.client', compact('clients', 'institutionTypeNames', 'users'));
    }public function bulkDestroyFromDuplicates(Request $request)
    {
        $userIds = $request->input('ids');
    
        // Validasi input awal
        if (empty($userIds) || !is_array($userIds)) {
            return response()->json(['message' => 'Tidak ada user duplikat yang dipilih!'], 400);
        }
    
        // --- LOGIKA VALIDASI BARU ---
        try {
            // 1. Dapatkan ID Profile (client_id) yang terkait dengan user_id yang diberikan.
            $clientIds = Profile::whereIn('user_id', $userIds)->pluck('id')->toArray();
    
            // 2. Hanya periksa jika ada client ID yang ditemukan.
            if (!empty($clientIds)) {
                // Periksa apakah salah satu dari client_id tersebut ada di tabel DeploymentDevice.
                $isRelated = DeploymentDevice::whereIn('client_id', $clientIds)->exists();
    
                // 3. Jika terkait, tolak permintaan dan berikan pesan error.
                if ($isRelated) {
                    return response()->json([
                        'message' => 'Gagal: Beberapa user tidak dapat ditandai sebagai duplikat karena datanya terhubung dengan Deployment Device.'
                    ], 400);
                }
            }
            
            // --- PROSES LANJUTAN JIKA VALIDASI LOLOS ---
            // 4. Lanjutkan proses jika tidak ada data yang terkait.
            $jumlahDitandai = User::whereIn('id', $userIds)
                ->update(['status' => 'duplicate']);
    
            // Session::flash masih bisa digunakan jika Anda memerlukannya saat halaman di-reload.
            Session::flash('success', $jumlahDitandai . ' user duplikat berhasil ditandai!');
    
            // 5. Kembalikan respons sukses dalam format JSON.
            return response()->json([
                'message' => 'Berhasil menandai ' . $jumlahDitandai . ' user sebagai duplikat!'
            ]); // Status default adalah 200 OK
    
        } catch (\Exception $e) {
            // Menangkap kemungkinan error lain pada server.
            return response()->json(['message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            
            'name' => 'required|max:255',
            'user_id' => 'nullable|numeric',
            'phone' => 'nullable|numeric',
            'institution' => 'required|max:255',
            'institution_type' => 'required',
            'address' => 'nullable|max:255',
            'reference' => 'nullable|max:255',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama tidak boleh lebih dari 15 karakter.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max' => 'Nomor telepon tidak boleh lebih dari 255 karakter.',
            'institution.required' => 'Institusi wajib diisi.',
            'institution.max' => 'Institusi tidak boleh lebih dari 255 karakter.',
            'institution_type.required' => 'Tipe institusi wajib diisi.',
            'address.max' => 'Alamat tidak boleh lebih dari 255 karakter.',
            'reference.max' => 'Referensi tidak boleh lebih dari 255 karakter.',
        ]);

        Profile::create($validatedData);

        return response()->json(['message' => 'Client berhasil ditambahkan.']);
    }
    public function destroy($id)
    {
        // 1. Periksa relasi yang memblokir (tidak berubah)
        $isRelated = DeploymentDevice::where('client_id', $id)->exists();
    
        if ($isRelated) {
            return response()->json([
                'message' => 'Gagal menghapus: Data client ini terkait dengan data Deployment Device.'
            ], 400);
        }
    
        // Mulai Database Transaction
        DB::beginTransaction();
    
        try {
            $client = Profile::findOrFail($id);
    
            // Hanya lanjutkan jika ada user_id yang terkait
            if ($client->user_id) {
                $userId = $client->user_id;
    
                // 2. BARU: Hapus semua tiket yang dimiliki oleh user ini
                Ticket::where('user_id', $userId)->delete();
    
                // 3. BARU: Hapus semua pesan yang dikirim oleh user ini
                Message::where('sender_id', $userId)->delete();
    
                // 4. Tandai user sebagai 'deleted'
                User::where('id', $userId)->update(['status' => 'deleted']);
            }
    
            // Jika semua operasi berhasil, commit transaksi
            DB::commit();
    
            return response()->json([
                'message' => 'Berhasil menandai client ' . $client->name . ' sebagai dihapus dan membersihkan data terkait!'
            ]);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack(); // Batalkan transaksi jika client tidak ditemukan
            return response()->json(['message' => 'Gagal menghapus: Client tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error lain
            return response()->json(['message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        $clientIds = $request->input('ids');
    
        if (empty($clientIds) || !is_array($clientIds)) {
            return response()->json(['message' => 'Tidak ada client yang dipilih!'], 400);
        }
    
        // 1. Periksa relasi yang memblokir (tidak berubah)
        $relatedCount = DeploymentDevice::whereIn('client_id', $clientIds)->count();
    
        if ($relatedCount > 0) {
            return response()->json([
                'message' => 'Gagal: Terdapat ' . $relatedCount . ' client yang tidak dapat dihapus karena terkait dengan data Deployment Device.'
            ], 400);
        }
        
        // Mulai Database Transaction
        DB::beginTransaction();
    
        try {
            // Dapatkan semua user_id dari client_id yang dipilih
            $userIds = Profile::whereIn('id', $clientIds)
                            ->whereNotNull('user_id')
                            ->pluck('user_id')
                            ->toArray();
    
            $deletedCount = 0;
            if (!empty($userIds)) {
                // 2. BARU: Hapus semua tiket yang dimiliki oleh user-user ini
                Ticket::whereIn('user_id', $userIds)->delete();
    
                // 3. BARU: Hapus semua pesan yang dikirim oleh user-user ini
                Message::whereIn('sender_id', $userIds)->delete();
    
                // 4. Tandai semua user terkait sebagai 'deleted'
                $deletedCount = User::whereIn('id', $userIds)->update(['status' => 'deleted']);
            }
    
            // Jika semua operasi berhasil, commit transaksi
            DB::commit();
    
            return response()->json([
                'message' => 'Berhasil menandai ' . $deletedCount . ' client sebagai dihapus dan membersihkan data terkait!'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error
            return response()->json(['message' => 'Terjadi kesalahan saat proses penghapusan massal: ' . $e->getMessage()], 500);
        }
    }
 

    public function getStoredClientData($id)
    {
        $client = Profile::findOrFail($id);
        return response()->json($client);
    }
}