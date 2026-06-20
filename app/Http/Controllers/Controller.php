<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function getPaginateOptions($request){
        $options = [
                'search'=>$request->query('search') ?? null,
                'limit'=>$request->query('per_page') ?? 10,
                'sort_dir'=>$request->query('sort_dir') ?? 'DESC',
                'sort_by'=>$request->query('sort_by') ?? 'created_at',
            ];
        return $options;
    }
}
