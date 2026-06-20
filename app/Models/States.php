<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    protected $fillable = ['country_id', 'name'];
    protected $hidden = ['created_at', 'updated_at'];
    public function country()
    {
        return $this->belongsTo(Countries::class);
    }

    public static function getFirstSearch(string $search, string $country_id)
    {
        return self::where('name', 'LIKE', "%{$search}%")->where('country_id', $country_id)->get();
    }
}
