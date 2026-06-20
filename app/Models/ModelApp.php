<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ModelApp extends Model {
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
}