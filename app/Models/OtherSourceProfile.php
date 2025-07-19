<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherSourceProfile extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'institution',
        'institution_type',
    ];
}
