<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoredDevice extends Model
{
    protected $table = 'stored_devices';
    protected $fillable = ['device_id', 'stock', 'condition' , 'previous_stock'];
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }
}
