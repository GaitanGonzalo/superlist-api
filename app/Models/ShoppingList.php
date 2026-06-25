<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    protected $fillable = [ 
        'uuid',
        'user_id',
        'store_id',
        'store_name',
        'store_address',
        'is_finished',
        'finished_at',
        'total_spent',
        'actual_total',
        'created_at',
        'deleted'
    ];
    protected $hidden = [
        'deleted'
    ];

    public function items()
    {
        return $this->hasMany(ShoppingListItem::class);
    }
}
