<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
protected $table = 'profiles';

protected $fillable = [
    'user_id',
    'name',
    'phone',
    'institution',
    'institution_type',
    'address',
    'reference',
    'image',
];
public function user()
{
    return $this->hasMany(User::class, 'id', 'user_id');
}
public function belongUser() // Nama fungsi sudah benar, hanya tipe relasinya yang perlu dikoreksi
{
    return $this->belongsTo(User::class, 'user_id', 'id'); // Koreksi di sini
}


}
