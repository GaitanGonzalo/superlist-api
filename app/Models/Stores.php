<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stores extends Model
{
    protected $fillable = [
        'name',
        'uuid',
        'razon_social',
        'address',
        'address_number',
        'is_subsidiary',
        'cp',
        'identification_number',
        'country_id',
        'state_id',
        'location_id'
    ];

    protected $hidden = [
        'id',
        'deleted'
    ];
}
