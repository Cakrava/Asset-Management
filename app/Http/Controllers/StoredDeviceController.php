<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\StoredDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\CssSelector\Node\FunctionNode;

class StoredDeviceController extends Controller
{
 


    public Function index(){
        $storedDevices = StoredDevice::with('device')->get(); // Ambil data StoredDevice dengan eager load 'device'
        $devices = \App\Models\Device::all(); // Ambil data Device untuk dropdown di modal (asumsi dibutuhkan)
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
    
        return view('page.stored_device', compact('storedDevices', 'devices', 'deviceTypeNames')); // Kirim data ke view
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'device_id' => 'required|max:255',
            'stock' => 'required|integer|min:1', // Tambahkan validasi integer dan minimal 1
            'condition' => 'required',
        ]);

        // Ambil data Device berdasarkan device_id dari request
        $device = Device::findOrFail($request->device_id);

        // Cari StoredDevice yang sudah ada dengan brand, type, dan condition yang sama
        $existingStoredDevice = StoredDevice::where('device_id', $request->device_id)
            ->where('condition', $request->condition)
            ->first();

        if ($existingStoredDevice) {
            // Jika ditemukan, tambahkan stok yang ada dengan stok baru dari request
            $existingStoredDevice->stock += $request->stock;
            $existingStoredDevice->save();

            Session::flash('success', 'Stok perangkat berhasil diperbarui. Brand: ' . $device->brand . ', Type: ' . $device->type . ', Condition: ' . $request->condition . ', Stok Ditambahkan: ' . $request->stock);
        } else {
            
            $storedDevice = StoredDevice::create($validatedData);
            Session::flash('success', 'Perangkat baru berhasil ditambahkan. Brand: ' . $device->brand . ', Type: ' . $device->type . ', Condition: ' . $request->condition . ', Stok: ' . $request->stock);
        }

        Session::flash('warning-stored-device', 'Penyesuaian data secara manual melalui halaman ini hanya dianjurkan dalam kondisi yang benar-benar diperlukan');

    }

    public function update(Request $request)
    {
        Log::info('Mendapatkan data dengan ID ' . $request->stored_id . ' dengan stok ' . $request->stock);
    
        $storedId = $request->input('stored_id');
        $storedDevice = StoredDevice::findOrFail($storedId);
    
        // Cek dulu apakah stok baru sama dengan stok lama
        if ((int)$request->stock === (int)$storedDevice->stock) {
            Log::info('Tidak ada perubahan stok. Update dibatalkan.');
            return; // Keluar dari fungsi tanpa melakukan apa-apa
        }
    
        $validatedData = $request->validate([
            'stock' => 'required|numeric',
        ]);
    
        Log::info('Mengupdate perangkat dengan ID ' . $storedId . ' dengan data baru: ' . json_encode($validatedData));
        $storedDevice->update($validatedData);
    
        Log::info('Perangkat dengan ID ' . $storedId . ' berhasil diupdate dengan data baru: ' . json_encode($validatedData));
    
        Session::flash('success', 'Stok ' . $storedDevice->device->brand . ' berhasil diperbarui menjadi ' . $request->stock .' item');
        Session::flash('warning-stored-device', 'Penyesuaian data secara manual melalui halaman ini hanya dianjurkan dalam kondisi yang benar-benar diperlukan');
    }
    

public function destroy($id)
{
    $storedDevice = StoredDevice::findOrFail($id);
    $storedDevice->delete();

    return Redirect::back()->with('success', 'Perangkat berhasil dihapus.')
        ->with('warning-stored-device', 'Penyesuaian data secara manual melalui halaman ini hanya dianjurkan dalam kondisi yang benar-benar diperlukan');
}


public function bulkDestroy(Request $request)
{
    $storedDeviceIds = $request->input('ids');

    if (is_array($storedDeviceIds) && !empty($storedDeviceIds)) {
        $jumlahDihapus = StoredDevice::whereIn('id', $storedDeviceIds)->delete();

        Session::flash('success', $jumlahDihapus . ' row data perangkat berhasil dihapus!');
        Session::flash('warning-stored-device', 'Penyesuaian data secara manual melalui halaman ini hanya dianjurkan dalam kondisi yang benar-benar diperlukan');
     } else {
        return response()->json(['error' => 'Tidak ada data perangkat terpilih!']); // Biarkan tetap JsonResponse untuk kasus error (tidak ada item dipilih)
    }
}

public function getStoredDeviceData($id)
{
    $storedDevice = StoredDevice::with('device')->findOrFail($id); // Eager load relation device
    return response()->json($storedDevice);
}
}
