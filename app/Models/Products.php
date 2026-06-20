<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'name',
        'ean_code',
        'user_creator_id',
        'brand_id',
        'products_category_id',
        'rubro_id',
        'caracteristics',
        'weight',
        'um',
        'deleted'
    ];

    protected $hidden = [
        'deleted', 'user_creator_id', 'created_at', 'updated_at'
    ];

    public static function getAllPaginate(Array $options)
    {
        $limit = $options['limit'];
        $sort = $options['sort_dir'];
        $sortBy = $options['sort_by'];
        $search = $options['search'];
        $query = self::query()->where('deleted', 0);
        if (!empty($sortBy)) $query->orderBy($sortBy, $sort);
        if (!empty($search)) $query->where('name LIKE', "%$search%");
        return $query->paginate($limit);
    }

    public static function getFirstSearch(string $search){
        $search = $search;
        $query = self::query()->where('deleted', 0)->whereLike('name', "%$search%")->limit(10);
        return $query->get();
    }

    public static function getFirstCode(string $search){
        $search = $search;
        $query = self::query()->where('deleted', 0)->whereLike("ean_code", "%$search%")->limit(10);
        return $query->get();
    }

}
