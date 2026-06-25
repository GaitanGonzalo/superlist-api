<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsPricesStores extends Model
{
    protected $table = 'products_prices_stores';

    protected $fillable = [
        'user_id',
        'store_id',
        'product_id',
        'price'
    ];
}
