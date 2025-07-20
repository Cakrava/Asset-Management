<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\StoredDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class DeviceController extends Controller
{


    public function index()
    {
        // 1. Mengambil semua perangkat yang tidak dihapus, diurutkan berdasarkan yang terbaru.
        $devices = Device::where('status', '!=', 'deleted')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Daftar nama jenis perangkat untuk ditampilkan di form atau filter.
        $deviceTypeNames = [
            'router' => 'Router',
            'access_point' => 'Access Point',
            'repeater' => 'Repeater / Range Extender',
            'network_adapter' => 'Network Adapter (USB/PCIe)',
            'switch' => 'Switch',
            'hub' => 'Hub',
            'modem' => 'Modem',
            'firewall' => 'Firewall',
            'load_balancer' => 'Load Balancer',
            'vpn_gateway' => 'VPN Gateway',
            'wireless_controller' => 'Wireless Controller',
            'media_converter' => 'Media Converter',
            'print_server' => 'Print Server',
            'network_storage' => 'Network Attached Storage (NAS)',
            'ip_camera' => 'IP Camera',
            'voip_phone' => 'VoIP Phone',
            'powerline_adapter' => 'Powerline Adapter',
            'bluetooth_adapter' => 'Bluetooth Adapter',
            'zigbee_gateway' => 'Zigbee Gateway',
            'zwave_gateway' => 'Z-Wave Gateway',
            'lorawan_gateway' => 'LoRaWAN Gateway',
            'nb_iot_gateway' => 'NB-IoT Gateway',
            'ethernet_over_power' => 'Ethernet over Power (EoP) Adapter',
            'serial_device_server' => 'Serial Device Server',
            'console_server' => 'Console Server',
            'network_tap' => 'Network Tap',
            'poe_injector' => 'PoE Injector',
            'poe_splitter' => 'PoE Splitter',
            'sfp_module' => 'SFP/SFP+ Module',
            'gbic_module' => 'GBIC Module',
            'cable' => 'Network Cable (Ethernet, Fiber)',
            'connector' => 'Connector (RJ45, Fiber Connectors)',
            'patch_panel' => 'Patch Panel',
            'rack' => 'Network Rack/Cabinet',
            'ups' => 'Uninterruptible Power Supply (UPS)',
            'pdu' => 'Power Distribution Unit (PDU)',
            'cooling_fan' => 'Cooling Fan (for Rack/Devices)',
            'antena' => 'Antenna (WiFi, Cellular)',
            'surge_protector' => 'Surge Protector (Network/Power)',
            'network_analyzer' => 'Network Analyzer/Tester',
            'crimping_tool' => 'Crimping Tool (for Cables)',
            'cable_tester' => 'Cable Tester',
        ];

        return view('page.device', compact('devices', 'deviceTypeNames'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'brand' => 'required|max:255',
            'model' => 'required|max:255',
            'type' => 'required|string',
        ]);

        // Cari perangkat yang cocok tanpa mempedulikan huruf besar/kecil
        $existingDevice = Device::whereRaw('LOWER(brand) = ?', [strtolower($validatedData['brand'])])
                                ->whereRaw('LOWER(model) = ?', [strtolower($validatedData['model'])])
                                ->whereRaw('LOWER(type) = ?', [strtolower($validatedData['type'])])
                                ->first();

        if ($existingDevice) {
            // Jika perangkat ditemukan, cek statusnya
            if ($existingDevice->status === 'deleted') {
                // Jika statusnya deleted, aktifkan kembali
                $existingDevice->status = 'active'; // Asumsikan status default adalah 'active'
                $existingDevice->save();

                return response()->json(['message' => 'Perangkat yang sama pernah dihapus dan kini berhasil diaktifkan kembali.']);
            } else {
                // Jika statusnya BUKAN deleted, berarti ini duplikat aktif. Tolak.
                // Kondisi ini menangani baik yang sama persis (plek ketiplek) maupun yang beda kapitalisasi.
                return response()->json(['message' => 'Perangkat dengan brand, model, dan tipe yang sama persis sudah ada.'], 400);
            }
        }

        // Jika tidak ada perangkat yang cocok sama sekali, buat yang baru.
        Device::create($validatedData);

        return response()->json(['message' => 'Perangkat berhasil ditambahkan.']);
    }

 
    public function update(Request $request)
    {
        $deviceId = $request->input('device_id');
        $device = Device::findOrFail($deviceId);

        $validatedData = $request->validate([
            'brand' => 'required|max:255',
            'model' => 'required|max:255',
            'type' => 'required|string',
        ]);

        // Cek apakah ada perangkat LAIN yang memiliki kombinasi brand, model, dan tipe yang sama.
        $duplicateCheck = Device::where('id', '!=', $device->id) // <-- Poin Kunci: Kecualikan diri sendiri
                                ->whereRaw('LOWER(brand) = ?', [strtolower($validatedData['brand'])])
                                ->whereRaw('LOWER(model) = ?', [strtolower($validatedData['model'])])
                                ->whereRaw('LOWER(type) = ?', [strtolower($validatedData['type'])])
                                ->exists(); // `exists()` lebih efisien karena hanya butuh jawaban ya/tidak

        if ($duplicateCheck) {
            // Jika ditemukan duplikat, kembalikan error 400.
            return response()->json(['message' => 'Update gagal. Perangkat lain dengan brand, model, dan tipe ini sudah ada.'], 400);
        }

        // Jika tidak ada duplikat, lanjutkan proses update.
        $device->update($validatedData);

        return response()->json(['message' => 'Perangkat berhasil diupdate.']);
    }

  
    public function bulkDestroy(Request $request)
    {
        $deviceIds = $request->input('ids');
    
        if (!is_array($deviceIds) || empty($deviceIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada perangkat terpilih.'
            ], 400);
        }
    
        // Cek apakah ada device yang masih dipakai di stored_devices
        $usedDevice = StoredDevice::whereIn('device_id', $deviceIds)->pluck('device_id')->toArray();
    
        if (!empty($usedDevice)) {
            return response()->json([
                'success' => false,
                'message' => 'Beberapa perangkat masih digunakan dan tidak dapat dihapus.',
                'used_ids' => $usedDevice
            ], 400);
        }
    
        $jumlahDihapus = Device::whereIn('id', $deviceIds)->update(['status' => 'deleted']);
    
        return response()->json([
            'success' => true,
            'message' => "$jumlahDihapus perangkat berhasil disembunyikan."
        ]);
    }
    
    public function destroy($id)
    {
        $device = Device::findOrFail($id);
    
        // Cek apakah device ini masih digunakan di stored_devices
        $isUsed = StoredDevice::where('device_id', $device->id)->exists();
    
        if ($isUsed) {
            // Kalau masih dipakai, kirim respon error JSON
            return response()->json([
                'success' => false,
                'message' => 'Perangkat ini masih digunakan dan tidak bisa dihapus.'
            ], 400);
        }
    
        $device->status = 'deleted';
        $device->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil disembunyikan.'
        ]);
    }
    


    public function getDeviceData($id)
    {
        $device = Device::findOrFail($id);
        return response()->json($device);
    }
}

