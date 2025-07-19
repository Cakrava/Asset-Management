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
        $devices = Device::whereNotIn('status', ['deleted', 'duplicate'])
    ->orderBy('created_at', 'desc')
    ->get();



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


        $duplicates = Device::whereNotIn('status', ['deleted', 'duplicate'])
        ->selectRaw('LOWER(brand) as brand, LOWER(model) as model, COUNT(*) as total, GROUP_CONCAT(id) as device_ids')
        ->groupByRaw('LOWER(brand), LOWER(model)')
        ->having('total', '>', 1)
        ->get();
    

        if ($duplicates->isNotEmpty()) {
            $warningMessage = "<div style='position:relative;'>"; // Container relatif untuk positioning button
            foreach ($duplicates as $dup) {
                $warningMessage .= "<details>"; // Tag details untuk collapsible section
                $warningMessage .= "<summary>" . $dup->brand . "-" . $dup->model . " ({$dup->total} data)</summary>";

                $deviceIds = explode(',', $dup->device_ids); // Pecah string device_ids menjadi array ID
                $duplicateDevices = Device::whereIn('id', $deviceIds)->get(); // Ambil data perangkat duplikat

                $warningMessage .= "<ul class='duplicate-device-list'>"; // Tambahkan class untuk seleksi JS
                foreach ($duplicateDevices as $device) {
                    $warningMessage .= "<li>";
                    $warningMessage .= "<div class='form-check'>";
                    $warningMessage .= "<input class='form-check-input duplicate-checkbox' type='checkbox' value='" . $device->id . "' id='duplicateCheckbox_" . $device->id . "'>";
                    $warningMessage .= "<label class='form-check-label' for='duplicateCheckbox_" . $device->id . "'>";
                    $warningMessage .= $device->brand . "- " . $device->model;
                    $warningMessage .= "</label>";
                    $warningMessage .= "</div>";
                    $warningMessage .= "</li>";
                }
                $warningMessage .= "</ul>";
                $warningMessage .= "</details>"; // Penutup tag details
            }
            $warningMessage .= "<button type='button' id='btn-bulk-delete-all-duplicates' class='btn btn-danger btn-sm' style='position:absolute; bottom: 10px; right: 10px;' disabled><i class='ti ti-trash'></i> Delete</button>"; // Tombol Bersihkan dipindahkan ke luar loop, di pojok kanan bawah container
            $warningMessage .= "</div>"; // Penutup container relatif
            Session::flash('warning', $warningMessage);
        }

        return view('page.device', compact('devices','deviceTypeNames'));
    }

    public function bulkDestroyFromDuplicates(Request $request)
    {
        $deviceIds = $request->input('ids');
    
        if (is_array($deviceIds) && !empty($deviceIds)) {
            $jumlahDitandai = Device::whereIn('id', $deviceIds)->update([
                'status' => 'duplicate',
                'note' => 'ditandai sebagai duplikat otomatis',
                'updated_at' => now(), // biar ketahuan kapan ditandai
            ]);
    
            Session::flash('success', $jumlahDitandai . ' perangkat ditandai sebagai duplikat!');
            return response()->json(['message' => 'Berhasil menandai ' . $jumlahDitandai . ' perangkat duplikat sebagai "deleted".']);
        } else {
            return response()->json(['error' => 'Tidak ada perangkat duplikat yang dipilih!']);
        }
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'brand' => 'required|max:255',
            'model' => 'required|max:255',
            'type' => 'required', // Removed exists:device_types,id validation, adjust as needed
        ]);

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
            'type' => 'required', // Removed exists:device_types,id validation, adjust as needed
        ]);
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

