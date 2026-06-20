<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    protected $fillable = ['state_id', 'name'];
    protected $hidden = ['created_at', 'updated_at'];
    public function states()
    {
        return $this->belongsTo(States::class);
    }

    public static function getFirstSearch(string $search, string $state_id)
    {
        return self::where('name', 'LIKE', "%{$search}%")->where('state_id', $state_id)->get();
    }
}
