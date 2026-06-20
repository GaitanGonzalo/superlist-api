<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currencies extends Model
{
   protected $fillable = [
    'name',
    'symbol',
    'created_at',
    'updated_at'
   ];

   protected $hidden = [
    'created_at',
    'updated_at'
   ];
}
