<?php

namespace App\Http\Controllers;

use App\Models\DeploymentDevice;
use App\Models\DeploymentDeviceDetail;
use App\Models\StoredDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DeploymentDeviceController extends Controller
{
    /**
     * Menampilkan daftar riwayat deployment perangkat.
     */
    public function index()
    {
        // Eager load relasi yang dibutuhkan untuk tabel utama dan modal detail
        $deployments = DeploymentDevice::with(['user.profile', 'details.storedDevice.device'])->latest()->get();

        // Data untuk dropdown Select2 di modal "New Deployment"
        $storedDevices = StoredDevice::with('device')->where('stock', '>', 0)->get(); // Hanya tampilkan perangkat yang ada stok
        $users = User::with('profile')->where('role', '!=', 'admin')->get(); // Hanya tampilkan user non-admin sebagai penerima
        $userId = Auth::id(); // atau auth()->id()
        $myadmin = User::with('profile')
        ->where('role', 'admin')
        ->where('id', $userId)
        ->first();
    
        
        return view('page.admin.deployment-device', compact('deployments', 'storedDevices', 'users','myadmin'));
    }

    /**
     * Menyimpan deployment perangkat baru.
     */
  
}