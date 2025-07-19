<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'ticket_id',
        'message',
    ];
    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function sender()
    {
        // Ganti App\Models\User jika path model User Anda berbeda
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        // Ganti App\Models\User jika path model User Anda berbeda
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the ticket associated with the message (if any).
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
