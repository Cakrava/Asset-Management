<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
        protected $fillable = [
            'name',
            'email',
            'password',
            'confirm_password',
            'role',
            
        ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */

     protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        // Pastikan foreign key dan local key sudah benar jika tidak mengikuti konvensi Laravel
        // Default: foreign key 'user_id' di tabel tickets, local key 'id' di tabel users
        return $this->hasMany(Ticket::class, 'user_id', 'id');
    }

    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Profile::class);
    }
    public function sentMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Relasi untuk menghitung pesan BELUM DIBACA yang dikirim user ini ke Admin Pool
    public function unreadMessagesToAdmin(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'sender_id')
                    ->whereNull('receiver_id') // Ke admin pool
                    ->where('is_read', 0);      // Yang belum dibaca
    }
}
