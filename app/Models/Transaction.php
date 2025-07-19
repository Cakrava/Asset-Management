<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions'; 

    protected $fillable = [
        'transaction_id',
        'unique_id',
        'instalation_status', // 'Deployed' atau 'Pending'
        'transaction_type',   // 'in' atau 'out'
        'client_id',          // Foreign key ke tabel users
        'other_source_id',          // Foreign key ke tabel users
        'letter_id'
        
    ];

    /**
     * Mendapatkan data user (klien) yang melakukan transaksi.
     * Relasi: Transaction "milik" satu User.
     */
    public function client()
    {
        // 'client_id' adalah foreign key, 'id' adalah primary key di tabel users.
        return $this->belongsTo(User::class, 'client_id', 'id');
    }
    public function otherSourceProfile()
    {
        // 'client_id' adalah foreign key, 'id' adalah primary key di tabel users.
        return $this->belongsTo(OtherSourceProfile::class, 'other_source_id', 'id');
    }

    /**
     * Mendapatkan semua detail item dalam transaksi ini.
     * Relasi: Satu Transaction bisa memiliki "banyak" TransactionDetail.
     */
    public function details()
    {
        // 'unique_id' adalah local key di tabel transactions.
        // 'unique_id' adalah foreign key di tabel transaction_details.
        return $this->hasMany(TransactionDetail::class, 'unique_id', 'unique_id');
    }
}