<?php

namespace App\Http\Controllers;

use App\Models\Client; // Pastikan model Client sudah ada
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class ClientController extends Controller
{
        public function index()
        {

            // Ambil data profile hanya untuk user dengan role 'user'
            $users = User::where('role', 'user')
            ->whereColumn('created_at', 'updated_at')
            ->whereNotIn('id', function($query) {
                $query->select('user_id')->from('profiles')->whereNotNull('user_id');
            })
            ->get();
            
            $clients = Profile::with(['user' => function($query) {
                            $query->where('role', 'user');
                        }])
                        ->whereHas('user', function($query) {
                            $query->where('role', 'user');
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();
        
            $institutionTypeNames = [ // Opsi jika ingin menampilkan nama tipe institusi yang lebih user-friendly
                'government' => 'Institusi Pemerintah',
                'private' => 'Institusi Swasta',
                'education' => 'Institusi Pendidikan',
                'healthcare' => 'Institusi Kesehatan',
                'nonprofit' => 'Organisasi Nirlaba',
                'other' => 'Lainnya',
            ];
        
            $duplicates = Profile::selectRaw('LOWER(institution) as institution, LOWER(institution_type) as institution_type, LOWER(name) as name, COUNT(*) as total, GROUP_CONCAT(id) as client_ids')
                ->groupByRaw('LOWER(institution), LOWER(institution_type), LOWER(name)')
                ->having('total', '>', 1)
                ->get();
        
            if ($duplicates->isNotEmpty()) {
                $warningMessage = "<div style='position:relative;'>";
                foreach ($duplicates as $dup) {
                    $warningMessage .= "<details>";
                    $warningMessage .= "<summary>" . $dup->institution . " - " . ($institutionTypeNames[$dup->institution_type] ?? $dup->institution_type) . " - " . $dup->name . " ({$dup->total} data)</summary>";
        
                    $clientIds = explode(',', $dup->client_ids);
                    $duplicateClients = Profile::whereIn('id', $clientIds)->get();
        
                    $warningMessage .= "<ul class='duplicate-client-list'>";
                    foreach ($duplicateClients as $client) {
                        $warningMessage .= "<li>";
                        $warningMessage .= "<div class='form-check'>";
                        $warningMessage .= "<input class='form-check-input duplicate-checkbox' type='checkbox' value='" . $client->id . "' id='duplicateCheckbox_" . $client->id . "'>";
                        $warningMessage .= "<label class='form-check-label' for='duplicateCheckbox_" . $client->id . "'>";
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
        }
    public function bulkDestroyFromDuplicates(Request $request)
    {
        $clientIds = $request->input('ids');

        if (is_array($clientIds) && !empty($clientIds)) {
            $jumlahDihapus = Client::whereIn('id', $clientIds)->delete();
            Session::flash('success', $jumlahDihapus . ' row data berhasil dihapus!');
            return response()->json(['message' => 'Berhasil menghapus ' . $jumlahDihapus . ' client duplikat!']);
        } else {
            return response()->json(['error' => 'Tidak ada client duplikat yang dipilih!']);
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


    public function update(Request $request)
    {
        $clientId = $request->input('client_id');
        $client = Profile::findOrFail($clientId);

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'nullable|email|max:255',
            'institution' => 'required|max:255',
            'institution_type' => 'required',
            'address' => 'nullable|max:255',
            'reference' => 'nullable|max:255',
        ]);
        $client->update($validatedData);

        return response()->json(['message' => 'Client berhasil diupdate.']);
    }

    public function destroy($id)
    {
        $client = Profile::findOrFail($id);
    
        // Simpan user_id terlebih dahulu
        $userId = $client->user_id;
    
        // Hapus user terkait jika ada
        if ($userId) {
            User::where('id', $userId)->delete();
        }
    
        // Hapus profile
        $client->delete();
    
        return Redirect::back()->with('success', 'Berhasil menghapus data client ' . $client->name . '!');
    }
    

    public function bulkDestroy(Request $request)
    {
        $clientIds = $request->input('ids');
    
        if (is_array($clientIds) && !empty($clientIds)) {
            // Ambil user_id dari profile yang akan dihapus
            $userIds = Profile::whereIn('id', $clientIds)
                        ->whereNotNull('user_id')
                        ->pluck('user_id')
                        ->toArray();
    
            // Hapus user terkait
            if (!empty($userIds)) {
                User::whereIn('id', $userIds)->delete();
            }
    
            // Hapus profile
            $jumlahDihapus = Profile::whereIn('id', $clientIds)->delete();
    
            Session::flash('success', $jumlahDihapus . ' row data berhasil dihapus!');
            return response()->json(['message' => 'Berhasil menghapus data ' . $jumlahDihapus . ' client!']);
        } else {
            return response()->json(['error' => 'Tidak ada row terpilih!']);
        }
    }
    


    public function getStoredClientData($id)
    {
        $client = Profile::findOrFail($id);
        return response()->json($client);
    }
}