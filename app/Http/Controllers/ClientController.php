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
            ->where('status', '!=', 'deleted') // Hanya user aktif yang bisa dipilih
            ->whereDoesntHave('profile') // Cara Laravel yang lebih bersih untuk cek relasi
            ->get();
    
        // Ambil client yang valid (user-nya aktif)
        $clients = Profile::with('user')
            ->whereHas('user', function ($query) {
                $query->where('role', 'user')
                      ->where('status', '!=', 'deleted');
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
    
        // Logika untuk mencari dan menampilkan duplikat di sini telah dihapus.
    
        return view('page.client', compact('clients', 'institutionTypeNames', 'users'));
    }

    /**
     * Menyimpan profil klien baru dengan validasi duplikat yang canggih.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'user_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|numeric',
            'institution' => 'required|max:255',
            'institution_type' => 'required|string',
            'address' => 'nullable|max:255',
            'reference' => 'nullable|max:255',
        ]);

        // Cari profil yang cocok tanpa mempedulikan huruf besar/kecil
        $existingProfile = Profile::with('user')
            ->whereRaw('LOWER(institution) = ?', [strtolower($validatedData['institution'])])
            ->whereRaw('LOWER(institution_type) = ?', [strtolower($validatedData['institution_type'])])
            ->whereRaw('LOWER(name) = ?', [strtolower($validatedData['name'])])
            ->first();

        if ($existingProfile) {
            // Jika profil ditemukan, cek status user terkait
            if ($existingProfile->user && $existingProfile->user->status === 'deleted') {
                // Jika statusnya deleted, aktifkan kembali user-nya
                $existingProfile->user->status = 'active';
                $existingProfile->user->save();
                
                // Anda juga bisa mengupdate data profilnya jika diperlukan
                $existingProfile->update($validatedData);

                return response()->json(['message' => 'Klien yang sama pernah dihapus dan kini berhasil diaktifkan kembali dengan data terbaru.']);
            } else {
                // Jika statusnya BUKAN deleted, ini adalah duplikat aktif. Tolak.
                return response()->json(['message' => 'Klien dengan nama, institusi, dan tipe institusi yang sama persis sudah ada.'], 400);
            }
        }

        // Jika tidak ada duplikat, buat profil baru.
        Profile::create($validatedData);

        return response()->json(['message' => 'Klien berhasil ditambahkan.']);
    }

    /**
     * Memperbarui profil klien dengan validasi duplikasi terhadap data lain.
     */
    public function update(Request $request)
    {
        // TAMBAHKAN VALIDASI INI
        // Ini akan memastikan profile_id ada dan benar-benar ada di tabel profiles.
        $validatedId = $request->validate([
            'client_id' => 'required|exists:profiles,id'
        ]);
    
        // Gunakan ID yang sudah divalidasi
        $profile = Profile::findOrFail($validatedId['client_id']);
    
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'phone' => 'nullable|numeric',
            'institution' => 'required|max:255',
            'institution_type' => 'required|string',
            'address' => 'nullable|max:255',
            'reference' => 'nullable|max:255',
        ]);
    
        // Cek duplikasi dengan logika yang sudah benar
        $duplicateCheck = Profile::where('id', '!=', $profile->id)
                                ->whereRaw('LOWER(institution) = ?', [strtolower($validatedData['institution'])])
                                ->whereRaw('LOWER(institution_type) = ?', [strtolower($validatedData['institution_type'])])
                                ->whereRaw('LOWER(name) = ?', [strtolower($validatedData['name'])])
                                ->whereHas('user', function ($query) {
                                    $query->where('status', '!=', 'deleted');
                                })
                                ->exists();
    
        if ($duplicateCheck) {
            return response()->json(['message' => 'Update gagal. Klien lain dengan nama, institusi, dan tipe institusi ini sudah ada.'], 400);
        }
    
        $profile->update($validatedData);
    
        return response()->json(['message' => 'Klien berhasil diupdate.']);
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