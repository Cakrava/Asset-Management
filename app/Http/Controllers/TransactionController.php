<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\DeploymentDevice;
use App\Models\DeploymentDeviceDetail;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Letters;
use App\Models\Device;
use App\Models\StoredDevice;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log;
use App\Models\OtherSourceProfile;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Import Str facade for unique_id
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
class TransactionController extends Controller
{
    public function index()
    {
        // Transaksi aktif (bukan revoked)
        $transactions = Transaction::with('client.profile', 'otherSourceProfile', 'details.storedDevice.device')
            ->where('instalation_status', '!=', 'Revoked')
            ->latest()
            ->get();
    
        // Transaksi yang berstatus revoked (kalau mau ditampilkan terpisah)
        $revokedTransactions = Transaction::with('client.profile', 'otherSourceProfile', 'details.storedDevice.device')
            ->where('instalation_status', 'Revoked')
            ->latest()
            ->get();
    
        $letters = Letters::with('client.profile', 'details.storedDevice.device')
                          ->where('status', 'Needed')
                          ->get();
    
        $devices = Device::all();
    
        $storedDevices = StoredDevice::with('device')
            ->where('stock', '>', 0)
            ->where('condition', '!=', 'Rusak')
            ->get();
    
        $users = User::with('profile')
            ->whereHas('profile')
            ->where('role', 'user')
            ->get();
    
        // Baca file token link dari storage/app/temporary_url.json
        $tokenLinks = [];
        $filePath = storage_path('app/temporary_url.json'); 
    
        if (file_exists($filePath)) {
            $json = file_get_contents($filePath);
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $tokenLinks = $decoded;
            }
        }
    
        return view('page.admin.asset-flow', compact(
            'transactions',
            'devices',
            'letters',
            'storedDevices',
            'users',
            'tokenLinks',
            'revokedTransactions'
        ));
    }
    

    public function searchOtherSource(Request $request)
{
    $query = $request->get('query');

    if (strlen($query) < 2) {
        return response()->json([]);
    }

    // Pecah query menjadi kata-kata
    $keywords = explode(' ', strtolower($query));

    // Mulai query
    $profilesQuery = \App\Models\OtherSourceProfile::query();

    $profilesQuery->where(function ($q) use ($keywords) {
        foreach ($keywords as $word) {
            $q->orWhere('name', 'LIKE', "%{$word}%")
              ->orWhere('institution', 'LIKE', "%{$word}%");
        }
    });

    $profiles = $profilesQuery->limit(10)->get();

    return response()->json($profiles);
}
    public function getPreviousDeployment($user_id)
    {
        // Cari data deployment untuk user_id yang diberikan, dan eager load relasi yang diperlukan
        $deployment = DeploymentDevice::with('details.storedDevice.device')
                                      ->where('client_id', $user_id) // Menggunakan client_id sesuai model
                                      ->first();
    
        if (!$deployment) {
            // Jika tidak ada data, kembalikan array kosong
            return response()->json(['devices' => []]);
        }
    
        // Format data agar mudah digunakan di JavaScript
        $formattedDevices = $deployment->details->map(function ($detail) {
            // Hanya sertakan data jika relasi lengkap
            if ($detail->storedDevice && $detail->storedDevice->device) {
                // Return additional necessary info like original stored_device_id and actual stock
                return [
                    'stored_device_id' => $detail->storedDevice->id,
                    'name'             => $detail->storedDevice->device->brand . ' ' . $detail->storedDevice->device->model,
                    'condition'        => $detail->storedDevice->condition,
                    'quantity'         => $detail->quantity, // This is the deployed quantity
                    'stock'            => $detail->storedDevice->stock, // Actual stock in StoredDevice
                ];
            }
            return null;
        })->filter(); // Hapus item null dari koleksi
    
        return response()->json(['devices' => $formattedDevices]);
    }

    /**
     * Helper function to generate a unique transaction ID.
     */
    protected function generateTransactionUniqueId()
    {
        return Str::uuid()->toString(); // Generates a UUID
    }

    /**
     * Helper function to determine installation status.
     */
    protected function getInstallationStatus(string $transactionType): string
    {
        return $transactionType === 'in' ? 'Intake' : 'Pending';
    }


    protected function processCartItems(array $cartItems, string $uniqueId, string $transactionType, ?int $clientId = null)
    {
        foreach ($cartItems as $item) {
            $storedDeviceId = null;
            $quantity = $item['quantity'];
            $itemCondition = $item['condition']; // Use condition from cart item for consistency

            if ($item['source'] === 'manual') { // Items added manually from Device select
                $device = StoredDevice::find($item['id']); // item['id'] here is device_id for 'in' flow
                if (!$device) {
                    throw new \Exception("Device with ID {$item['id']} not found.");
                }

                $storedDevice = StoredDevice::where('device_id', $device->id)
                                            ->where('condition', $itemCondition)
                                            ->first();

                if ($transactionType === 'in') {
                    if ($storedDevice) {
                        $storedDevice->increment('stock', $quantity);
                    } else {
                        $storedDevice = StoredDevice::create([
                            'device_id' => $device->id,
                            'condition' => $itemCondition,
                            'stock'     => $quantity,
                        ]);
                    }
                }
                // For 'out' transactions with manual source, item['id'] is already stored_device_id
                else { // transactionType === 'out'
                    $storedDevice = StoredDevice::find($item['id']); // Here item['id'] is expected to be stored_device_id
                    if (!$storedDevice) {
                         throw new \Exception("Stored Device with ID {$item['id']} not found for Out flow.");
                    }
                    // As per instruction, no stock reduction for 'out' yet.
                }
                $storedDeviceId = $storedDevice->id;

            } elseif ($item['source'] === 'deployed') { // Items from previous deployment
                $storedDeviceId = $item['id']; // item['id'] here is already stored_device_id
                $storedDevice = StoredDevice::find($storedDeviceId); // Get the actual StoredDevice to operate on

                if (!$storedDevice) {
                    throw new \Exception("Stored Device for deployed item ID {$storedDeviceId} not found.");
                }

                // Find the specific DeploymentDeviceDetail that this item refers to
                // This assumes one deployment device detail per stored_device_id per client's deployment.
                $deploymentDetail = DeploymentDeviceDetail::where('stored_device_id', $storedDeviceId)
                                                          ->whereHas('deployment', function($query) use ($clientId) {
                                                              $query->where('client_id', $clientId);
                                                          })
                                                          ->first();
                                                          
                if (!$deploymentDetail) {
                     // This could happen if a deployed item is added to cart but no matching deployment detail found for this client.
                     // Handle as appropriate, e.g., throw error or treat as new entry.
                     throw new \Exception("Deployment detail for stored device ID {$storedDeviceId} and client ID {$clientId} not found.");
                }

                if ($transactionType === 'in') {
                    // For 'in' flow, if a deployed item is returned:
                    // 1. Increment stock in StoredDevice with the NEW condition if specified (from cart item).
                    //    If condition changes, it might mean creating a new StoredDevice entry or finding existing.
                    //    Assuming here the condition in the cart is the *new* condition of the returned item.
                    $existingOrNewStoredDevice = StoredDevice::firstOrNew([
                        'device_id' => $storedDevice->device_id, // Get original device_id
                        'condition' => $itemCondition, // Use the condition from cart (user's selection)
                    ]);
                    $existingOrNewStoredDevice->stock += $quantity;
                    $existingOrNewStoredDevice->save();
                    $storedDeviceId = $existingOrNewStoredDevice->id; // Use the ID of the updated/new stored device

                    // 2. Decrement quantity in DeploymentDeviceDetail
                    $newDeploymentQuantity = $deploymentDetail->quantity - $quantity;
                    $deploymentDetail->quantity = max(0, $newDeploymentQuantity); // Prevent negative
                    $deploymentDetail->save();

                } elseif ($transactionType === 'out') {
                    // For 'out' flow with deployed items (adjusting existing deployment):
                    // As per instruction, no stock reduction from StoredDevice yet.
                    // But we should decrement the deployed quantity in DeploymentDeviceDetail.
                    $newDeploymentQuantity = $deploymentDetail->quantity - $quantity;
                    $deploymentDetail->quantity = max(0, $newDeploymentQuantity); // Prevent negative
                    $deploymentDetail->save();
                    // $storedDeviceId remains the same from the original deployed item
                }
            } else { // 'letter' source
                $storedDeviceId = $item['id']; // item['id'] here is stored_device_id from letter details
                // No stock changes for 'letter' sourced items during transaction processing as per requirement,
                // and no 'in' flow for letters.
                // For 'out' flow with letters, reduction happens later.
            }

            // Create TransactionDetail
            \App\Models\TransactionDetail::create([ // Explicitly use full namespace to avoid ambiguity
                'unique_id'        => $uniqueId,
                'stored_device_id' => $storedDeviceId,
                'quantity'         => $quantity,
            ]);
        }
    }




    public function processTransactionFromLetter(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'letter_id' => 'required|exists:letters,id',
            'transaction_cart' => 'required|array|min:1',
            'transaction_cart.*.id' => 'required|integer',
            'transaction_cart.*.quantity' => 'required|integer|min:1',
            'transaction_cart.*.source' => 'required|string|in:letter',
            'transaction_cart.*.condition' => 'required|string',
        ]);
    
        DB::beginTransaction();
        try {
            $uniqueId = $this->generateTransactionUniqueId();
            $transactionType = 'out';
            $instalationStatus = $this->getInstallationStatus($transactionType);
    
            // Buat record Transaksi utama
            $transaction = Transaction::create([
                'transaction_id'     => $request->input('transaction_id'),
                'unique_id'          => $uniqueId,
                'instalation_status' => $instalationStatus,
                'transaction_type'   => $transactionType,
                'client_id'          => $request->input('client_id'),
                'other_source_id'    => null,
                'letter_id'          => $request->input('letter_id'),
            ]);
    
            // Proses item di keranjang
            foreach ($request->input('transaction_cart') as $item) {
                \App\Models\TransactionDetail::create([
                    'unique_id'        => $uniqueId,
                    'stored_device_id' => $item['id'],
                    'quantity'         => $item['quantity'],
                ]);
            }
    
            // Inisialisasi variabel untuk URL akses, defaultnya null.
            $accessUrl = null;
    
            // Buat URL akses unik HANYA JIKA status instalasi adalah 'Pending'.
            if ($transaction->instalation_status === 'Pending') {
                $accessUrl = $this->generateLink($transaction);
            }
    
            // ==========================================================
            // LOGIKA BARU: Ubah status surat menjadi 'Open'
            // ==========================================================
            // Cari surat berdasarkan letter_id yang diberikan dalam request.
            $letter = Letters::find($request->input('letter_id'));
    
            // Jika surat ditemukan (seharusnya selalu ditemukan karena ada validasi),
            // ubah statusnya dan simpan.
            if ($letter) {
                $letter->status = 'Open';
                $letter->save();
            }
            // ==========================================================
    
            DB::commit();
            
            // Kembalikan respons JSON dengan pesan sukses dan URL (jika ada).
            return response()->json([
                'message' => 'Transaksi dari surat berhasil diproses!',
                'transaction_id' => $transaction->transaction_id,
                'access_url' => $accessUrl
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses transaksi dari surat: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 2. Fungsi untuk memproses transaksi manual dengan "Sumber Lain" diaktifkan.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processTransactionManualOtherSource(Request $request)
    {
        $request->validate([
            'other_source_profile.name' => 'required|string|max:255',
            'other_source_profile.phone' => 'nullable|string|max:255',
            'other_source_profile.institution' => 'nullable|string|max:255',
            'other_source_profile.institution_type' => 'nullable|string|max:255',
            'flow_type' => 'required|in:in,out',
            'transaction_cart' => 'required|array|min:1',
            'transaction_cart.*.id' => 'required|integer',
            'transaction_cart.*.name' => 'required|string',
            'transaction_cart.*.condition' => 'required|string',
            'transaction_cart.*.quantity' => 'required|integer|min:1',
            'transaction_cart.*.source' => 'required|string|in:manual,deployed',
        ]);

        DB::beginTransaction();
        try {
            // Buat profil untuk sumber lain
            $otherSourceProfile = OtherSourceProfile::create($request->input('other_source_profile'));

            $uniqueId = $this->generateTransactionUniqueId();
            $transactionType = $request->input('flow_type');
            $instalationStatus = $this->getInstallationStatus($transactionType);

            // Buat record Transaksi utama
            $transaction = Transaction::create([
                'transaction_id'     => $request->input('transaction_id'),
                'unique_id'          => $uniqueId,
                'instalation_status' => $instalationStatus,
                'transaction_type'   => $transactionType,
                'client_id'          => null,
                'other_source_id'    => $otherSourceProfile->id,
            ]);

            // Proses item di keranjang
            $this->processCartItems($request->input('transaction_cart'), $uniqueId, $transactionType);

            // Inisialisasi variabel untuk URL akses, defaultnya null.
            $accessUrl = null;

            // Buat URL akses unik HANYA JIKA status instalasi adalah 'Pending'.
            if ($transaction->instalation_status === 'Pending') {
                $accessUrl = $this->generateLink($transaction);
            }

            DB::commit();

            // Kembalikan respons JSON dengan pesan sukses dan URL (jika ada).
            return response()->json([
                'message' => 'Transaksi manual (Sumber Lain) berhasil diproses!',
                'transaction_id' => $transaction->transaction_id,
                'access_url' => $accessUrl
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses transaksi manual (Sumber Lain): ' . $e->getMessage()], 500);
        }
    }

    /**
     * 3. Fungsi untuk memproses transaksi manual dengan klien terpilih dan tanpa item "deployed".
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processTransactionManualSelectedClient(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'flow_type' => 'required|in:in,out',
            'transaction_cart' => 'required|array|min:1',
            'transaction_cart.*.id' => 'required|integer',
            'transaction_cart.*.name' => 'required|string',
            'transaction_cart.*.condition' => 'required|string',
            'transaction_cart.*.quantity' => 'required|integer|min:1',
            'transaction_cart.*.source' => 'required|string|in:manual',
        ]);

        // Pastikan tidak ada item 'deployed' di keranjang untuk route ini
        foreach ($request->input('transaction_cart') as $item) {
            if ($item['source'] === 'deployed') {
                return response()->json(['message' => 'Keranjang berisi item deployed, gunakan endpoint yang benar.'], 400);
            }
        }

        DB::beginTransaction();
        try {
            $uniqueId = $this->generateTransactionUniqueId();
            $transactionType = $request->input('flow_type');
            $instalationStatus = $this->getInstallationStatus($transactionType);

            // Buat record Transaksi utama
            $transaction = Transaction::create([
                'transaction_id'     => $request->input('transaction_id'),
                'unique_id'          => $uniqueId,
                'instalation_status' => $instalationStatus,
                'transaction_type'   => $transactionType,
                'client_id'          => $request->input('client_id'),
                'other_source_id'    => null,
            ]);

            // Proses item di keranjang
            $this->processCartItems($request->input('transaction_cart'), $uniqueId, $transactionType, $request->input('client_id'));

            // Inisialisasi variabel untuk URL akses, defaultnya null.
            $accessUrl = null;

            // Buat URL akses unik HANYA JIKA status instalasi adalah 'Pending'.
            if ($transaction->instalation_status === 'Pending') {
                $accessUrl = $this->generateLink($transaction);
            }

            DB::commit();

            // Kembalikan respons JSON dengan pesan sukses dan URL (jika ada).
            return response()->json([
                'message' => 'Transaksi manual (Klien Terpilih) berhasil diproses!',
                'transaction_id' => $transaction->transaction_id,
                'access_url' => $accessUrl
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses transaksi manual (Klien Terpilih): ' . $e->getMessage()], 500);
        }
    }

    /**
     * 4. Fungsi untuk memproses transaksi manual dengan klien terpilih dan mengandung item "deployed".
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processTransactionManualDeployed(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'flow_type' => 'required|in:in,out',
            'transaction_cart' => 'required|array|min:1',
            'transaction_cart.*.id' => 'required|integer',
            'transaction_cart.*.name' => 'required|string',
            'transaction_cart.*.condition' => 'required|string',
            'transaction_cart.*.quantity' => 'required|integer|min:1',
            'transaction_cart.*.source' => 'required|string|in:manual,deployed',
        ]);

        // Pastikan setidaknya ada satu item 'deployed' di keranjang untuk route ini
        $hasDeployedItems = collect($request->input('transaction_cart'))->contains('source', 'deployed');
        if (!$hasDeployedItems) {
            return response()->json(['message' => 'Keranjang tidak berisi item deployed, gunakan endpoint yang benar.'], 400);
        }

        DB::beginTransaction();
        try {
            $uniqueId = $this->generateTransactionUniqueId();
            $transactionType = $request->input('flow_type');
            $instalationStatus = $this->getInstallationStatus($transactionType);

            // Buat record Transaksi utama
            $transaction = Transaction::create([
                'transaction_id'     => $request->input('transaction_id'),
                'unique_id'          => $uniqueId,
                'instalation_status' => $instalationStatus,
                'transaction_type'   => $transactionType,
                'client_id'          => $request->input('client_id'),
                'other_source_id'    => null,
            ]);

            // Proses item di keranjang
            $this->processCartItems($request->input('transaction_cart'), $uniqueId, $transactionType, $request->input('client_id'));

            // Inisialisasi variabel untuk URL akses, defaultnya null.
            $accessUrl = null;

            // Buat URL akses unik HANYA JIKA status instalasi adalah 'Pending'.
            if ($transaction->instalation_status === 'Pending') {
                $accessUrl = $this->generateLink($transaction);
            }

            DB::commit();
            
            // Kembalikan respons JSON dengan pesan sukses dan URL (jika ada).
            return response()->json([
                'message' => 'Transaksi manual (dengan item deployed) berhasil diproses!',
                'transaction_id' => $transaction->transaction_id,
                'access_url' => $accessUrl
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses transaksi manual (dengan item deployed): ' . $e->getMessage()], 500);
        }
    }





    





/**
 * Membuat link unik untuk sebuah transaksi dan menyimpannya ke file JSON.
 *
 * @param Transaction $transaction
 * @return string Mengembalikan URL akses yang baru dibuat.
 */
private function generateLink(Transaction $transaction)
{
    $jsonFilePath = storage_path('app/temporary_url.json');
    Log::debug('Mulai generateLink untuk transaction_id: ' . $transaction->transaction_id);

    // Pastikan file JSON ada, jika tidak, buat file kosong
    if (!File::exists($jsonFilePath)) {
        Log::debug('File temporary_url.json tidak ditemukan, membuat file baru.');
        File::put($jsonFilePath, json_encode([]));
    } else {
        Log::debug('File temporary_url.json ditemukan.');
    }

    try {
        $rawContent = File::get($jsonFilePath);
        Log::debug('Isi file sebelum decode: ' . $rawContent);

        $data = json_decode($rawContent, true);
        if (!is_array($data)) {
            Log::warning('Data json tidak valid, reset ke array kosong.');
            $data = [];
        }
    } catch (\Exception $e) {
        Log::error('Gagal membaca file JSON: ' . $e->getMessage());
        $data = [];
    }

    // Buat token dan URL
    $token = Str::random(40);
    $url = url('/access-transaction/' . $token);

    Log::debug("Generated token: $token");
    Log::debug("Generated URL: $url");

    // Tambahkan ke data
    $data[$transaction->transaction_id] = [
        'token' => $token,
        'url' => $url,
    ];

    try {
        $encodedData = json_encode($data, JSON_PRETTY_PRINT);
        File::put($jsonFilePath, $encodedData);
        Log::debug("Data berhasil disimpan ke file: " . $jsonFilePath);
    } catch (\Exception $e) {
        Log::error('Gagal menulis file JSON: ' . $e->getMessage());
    }

    return $url;
}

public function accessTransaction($token)
{
    $jsonFilePath = storage_path('app/temporary_url.json');

    // Jika file tidak ada, lempar 404
    if (!File::exists($jsonFilePath)) {
        abort(404, 'Halaman tidak ditemukan.');
    }

    // Decode file JSON
    $linkData = json_decode(File::get($jsonFilePath), true);

    // Jika file kosong, rusak, atau bukan array, anggap tidak valid
    if (empty($linkData) || !is_array($linkData)) {
        abort(404, 'Halaman tidak ditemukan.');
    }

    // Cari token dalam array
    $transactionId = null;
    foreach ($linkData as $id => $details) {
        if (isset($details['token']) && $details['token'] === $token) {
            $transactionId = $id;
            break;
        }
    }

    // Token tidak ditemukan
    if (!$transactionId) {
        abort(404, 'Halaman tidak ditemukan.');
    }

    // Ambil data transaksi
    $transaction = Transaction::where('transaction_id', $transactionId)->first();

    // Validasi apakah transaksi ada dan masih pending
    if (!$transaction || $transaction->instalation_status !== 'Pending') {
        abort(404, 'Halaman tidak ditemukan atau link sudah tidak berlaku.');
    }

    // Tampilkan halaman tujuan
    return view('submit_transaction', ['transaction_id' => $transaction->transaction_id]);
}

// Pastikan Anda sudah memiliki "use Illuminate\Support\Facades\Log;" di bagian atas file controller.
public function processSubmission(Request $request)
    {
        // Menggunakan ID unik untuk log sesi ini agar mudah dicari di file log
        $logSessionId = Str::uuid()->toString();
        Log::info("Memulai proses submission.", ['session_id' => $logSessionId, 'request_data' => $request->all()]);

        // Langkah 1: Validasi input dari form
        $request->validate([
            'transaction_id' => 'required|string|exists:transactions,transaction_id',
            'nomor_surat' => 'required|string|exists:letters,letter_number',
            'lampiran_surat' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // Max 5MB
        ], [
            'transaction_id.exists' => 'ID Transaksi tidak valid atau tidak ditemukan.',
            'nomor_surat.exists' => 'Nomor Surat tidak ditemukan dalam sistem.',
        ]);
        Log::info("Langkah 1: Validasi input berhasil.", ['session_id' => $logSessionId]);

        // Memulai transaksi database untuk memastikan integritas data.
        DB::beginTransaction();
        Log::info("Transaksi database dimulai.", ['session_id' => $logSessionId]);
        
        try {
            // Langkah 2: Ambil data-data utama yang diperlukan
            $transactionId = $request->input('transaction_id');
            $nomorSurat = $request->input('nomor_surat');

            $transaction = Transaction::where('transaction_id', $transactionId)->firstOrFail();
            $letter = Letters::where('letter_number', $nomorSurat)->firstOrFail();
            Log::info("Langkah 2: Data Transaksi dan Surat berhasil ditemukan.", ['session_id' => $logSessionId, 'transaction_id' => $transactionId, 'letter_number' => $nomorSurat]);

            // Langkah 3: Lakukan validasi bisnis
            if ($transaction->instalation_status !== 'Pending') {
                Log::warning("Validasi gagal: Transaksi tidak lagi berstatus 'Pending'.", ['session_id' => $logSessionId, 'current_status' => $transaction->instalation_status]);
                DB::rollBack();
                return redirect()->back()->withErrors(['submit' => 'Transaksi ini sudah pernah diselesaikan atau link tidak valid lagi.']);
            }
            if ($transaction->client_id !== $letter->client_id) {
                Log::warning("Validasi gagal: client_id pada Transaksi dan Surat tidak cocok.", ['session_id' => $logSessionId, 'transaction_client_id' => $transaction->client_id, 'letter_client_id' => $letter->client_id]);
                DB::rollBack();
                return redirect()->back()->withErrors(['submit' => 'Nomor Surat tidak cocok untuk transaksi ini.']);
            }
            Log::info("Langkah 3: Validasi bisnis berhasil.", ['session_id' => $logSessionId]);

            // Langkah 4: Proses upload file dan update status Surat
            $publicPdfPath = $this->handleFileUpload($request->file('lampiran_surat'), $letter);
            $letter->status = 'Closed';
            $letter->sign_pdf_path = $publicPdfPath;
            $letter->save();
            Log::info("Langkah 4: File lampiran berhasil diunggah dan status Surat diubah menjadi 'Closed'.", ['session_id' => $logSessionId, 'pdf_path' => $publicPdfPath]);

            // ========================================================================
            // LANGKAH 5: PROSES UTAMA MANAJEMEN INVENTARIS
            // ========================================================================
            Log::info("Langkah 5: Memulai proses manajemen inventaris.", ['session_id' => $logSessionId]);

            // Langkah 5a: Dapatkan semua item dari detail transaksi
            $transactionDetails = TransactionDetail::where('unique_id', $transaction->unique_id)->get();
            Log::info("Langkah 5a: Ditemukan " . $transactionDetails->count() . " item detail transaksi.", ['session_id' => $logSessionId]);

            // Langkah 5b: Cari atau buat header perangkat terpasang untuk klien
            $deployedDeviceHeader = DeploymentDevice::firstOrCreate(['client_id' => $transaction->client_id]);
            Log::info("Langkah 5b: Header perangkat terpasang (DeployedDevice) siap.", ['session_id' => $logSessionId, 'deployment_id' => $deployedDeviceHeader->id, 'deployment_unique_id' => $deployedDeviceHeader->unique_id, 'client_id' => $transaction->client_id]);

            // Langkah 5c: Loop setiap item untuk memindahkan kuantitas dari gudang ke klien
            foreach ($transactionDetails as $detail) {
                Log::info("Langkah 5c: Memproses item detail...", ['session_id' => $logSessionId, 'stored_device_id' => $detail->stored_device_id, 'quantity' => $detail->quantity]);
                
                // Bagian 1: Kurangi stok dari gudang (StoredDevice)
                $storedDevice = StoredDevice::lockForUpdate()->findOrFail($detail->stored_device_id);
                Log::info("--> Mengunci StoredDevice ID: {$storedDevice->id}. Stok saat ini: {$storedDevice->stock}.", ['session_id' => $logSessionId]);

                if ($storedDevice->stock < $detail->quantity) {
                    Log::error("Proses dihentikan: Stok tidak cukup untuk StoredDevice ID: {$storedDevice->id}.", ['session_id' => $logSessionId, 'needed' => $detail->quantity, 'available' => $storedDevice->stock]);
                    DB::rollBack();
                    $deviceName = $storedDevice->name ?? 'Perangkat (ID: '.$storedDevice->id.')'; // Sesuaikan jika punya relasi ke nama perangkat
                    return redirect()->back()->withInput()->withErrors(['submit' => "Proses gagal: Stok untuk {$deviceName} tidak mencukupi."]);
                }
                $storedDevice->decrement('stock', $detail->quantity);
                Log::info("--> Stok StoredDevice ID: {$storedDevice->id} berhasil dikurangi. Stok baru: {$storedDevice->stock}.", ['session_id' => $logSessionId]);

                // Bagian 2: Catat penambahan perangkat terpasang (DeployedDeviceDetail) - INI BAGIAN YANG DIPERBAIKI
                $deployedDetail = DeploymentDeviceDetail::where('unique_id', $deployedDeviceHeader->unique_id)
                                                        ->where('stored_device_id', $detail->stored_device_id)
                                                        ->first();

                if ($deployedDetail) {
                    // Jika record sudah ada, tambahkan (increment) kuantitasnya
                    $deployedDetail->increment('quantity', $detail->quantity);
                    Log::info("--> Detail perangkat terpasang (DeployedDeviceDetail) ditemukan dan di-update. Quantity ditambah {$detail->quantity}.", ['session_id' => $logSessionId]);
                } else {
                    // Jika record belum ada, buat record baru dengan kuantitas dari transaksi
                    DeploymentDeviceDetail::create([
                        'unique_id'        => $deployedDeviceHeader->unique_id,
                        'stored_device_id' => $detail->stored_device_id,
                        'quantity'         => $detail->quantity, // Pastikan nama kolom 'quantity'
                    ]);
                    Log::info("--> Detail perangkat terpasang (DeployedDeviceDetail) baru berhasil dibuat dengan quantity {$detail->quantity}.", ['session_id' => $logSessionId]);
                }
            }
            
            Log::info("Langkah 5 selesai: Semua item inventaris berhasil diproses.", ['session_id' => $logSessionId]);
            // ========================================================================
            // AKHIR DARI PROSES INVENTARIS
            // ========================================================================

            // Langkah 6: Update status Transaksi menjadi 'Deployed'
            $transaction->instalation_status = 'Deployed';
            $transaction->save();
            Log::info("Langkah 6: Status Transaksi berhasil diubah menjadi 'Deployed'.", ['session_id' => $logSessionId]);
            
            // Langkah 7: Hapus link sementara jika diperlukan
            $this->removeLink($transactionId);
            Log::info("Langkah 7: Link sementara untuk transaksi ini telah dihapus.", ['session_id' => $logSessionId]);

            // Jika semua operasi di atas berhasil, simpan perubahan secara permanen ke database.
            DB::commit();
            Log::info("Transaksi database berhasil di-commit.", ['session_id' => $logSessionId]);

            // Langkah 8: Kembalikan pengguna dengan pesan sukses
            return redirect()->route('front.index')
                ->with('success', 'Konfirmasi berhasil! Stok gudang telah diperbarui dan perangkat terpasang telah dicatat.');

        } catch (\Exception $e) {
            // Jika terjadi error apapun di dalam blok 'try', batalkan semua perubahan.
            DB::rollBack();
            Log::info("Transaksi database di-rollback karena terjadi error.", ['session_id' => $logSessionId]);

            // Catat error untuk debugging oleh developer.
            Log::error('Gagal memproses submission.', [
                'session_id' => $logSessionId,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Kembalikan pengguna dengan pesan error yang umum.
            return redirect()->route('front.index')
                ->withErrors(['submit' => 'Terjadi kesalahan internal pada sistem. Tim kami telah diberitahu. Silakan coba lagi nanti.']);
        }
    }
    

private function handleFileUpload($file, $letter)
{
    $extension = strtolower($file->getClientOriginalExtension());
    $safeFileNameBase = Str::slug($letter->letter_number, '-') . '-TERTANDATANGANI';
    $finalPdfFileName = $safeFileNameBase . '.pdf';
    $storageDirectory = 'arsip-surat-tertanda';
    $publicPdfPath = "{$storageDirectory}/{$finalPdfFileName}";

    if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
        $imageData = base64_encode(file_get_contents($file->getRealPath()));
        $imageSrc = 'data:' . $file->getMimeType() . ';base64,' . $imageData;
        // Pastikan view 'component.image-to-pdf-wrapper' ada
        $pdf = Pdf::loadView('component.image-to-pdf-wrapper', ['imageSrc' => $imageSrc]);
        Storage::disk('public')->put($publicPdfPath, $pdf->output());
    } else {
        $file->storeAs($storageDirectory, $finalPdfFileName, 'public');
    }
    return $publicPdfPath;
}


private function removeLink($transactionId)
{
    $jsonFilePath = storage_path('app/temporary_url.json');

    if (!File::exists($jsonFilePath)) {
        return; // Tidak ada yang perlu dilakukan jika file tidak ada
    }

    $data = json_decode(File::get($jsonFilePath), true);

    // Pastikan data valid dan kunci transaksi ada sebelum menghapus
    if (is_array($data) && isset($data[$transactionId])) {
        unset($data[$transactionId]);
        // Tulis kembali data yang sudah diperbarui ke file
        File::put($jsonFilePath, json_encode($data, JSON_PRETTY_PRINT));
    }
   
}
}