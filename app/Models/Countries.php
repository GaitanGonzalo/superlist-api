<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    protected $fillable = ['ISO', 'name'];
    protected $hidden = ['created_at', 'updated_at'];
    public static function getFirstSearch(string $search)
    {
        return self::where('name', 'LIKE', "%{$search}%")->get();
    }

    public function states()
    {
        return $this->hasMany(States::class);
    }
}
