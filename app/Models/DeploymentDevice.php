<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; 
class DeploymentDevice extends Model
{
    protected $table = 'deployment_devices';
    protected $fillable = [
        'client_id', // Foreign key ke User
        'unique_id', // Penghubung ke DeploymentDeviceDetail
        'status', // Penghubung ke DeploymentDeviceDetail

        // Tambahan field lain jika ada, misal: 'status', 'location', dll.
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            // Jika unique_id belum diisi, buatkan satu.
            if (empty($model->unique_id)) {
                $model->unique_id = (string) Str::uuid();
            }
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function details()
    {
        return $this->hasMany(DeploymentDeviceDetail::class, 'unique_id', 'unique_id');
    }
}