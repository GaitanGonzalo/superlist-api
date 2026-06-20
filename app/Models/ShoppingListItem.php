<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingListItem extends Model
{
    protected $fillable = [ 
        'id',
        'shopping_list_id',
        'name',
        'quantity',
        'price',
        'is_purchased',
        'deleted'
    ];
     protected $hidden = [
        'deleted'
    ];

    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }

}
