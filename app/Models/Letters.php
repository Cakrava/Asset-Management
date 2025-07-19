<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letters extends Model
{
    protected $fillable = [
        'unique_id',
        'letter_number',
        'status',
        'client_id',
        'ticket_id',
        'subject',
        'sign_pdf_path',
        'pdf_path',
        
    ];
    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'unique_id', 'unique_id');
    }
public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
