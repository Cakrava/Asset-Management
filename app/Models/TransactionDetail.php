<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{

    
    protected $fillable = [
        'unique_id',
        'stored_device_id', // Ini adalah ID dari stored_devices
        'quantity',
    ];

    /**
     * Mendapatkan data StoredDevice yang terkait dengan detail transaksi ini.
     * INI PERUBAHAN KUNCI: Relasi kini menunjuk ke StoredDevice.
     */
    public function storedDevice()
    {
        return $this->belongsTo(StoredDevice::class, 'stored_device_id');
    }
    
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'unique_id', 'unique_id');
    }

}