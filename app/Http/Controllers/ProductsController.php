<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    public function search(Request $request){
        try {
            $search = $request->query('search');
            $response = !empty($search) ? Products::getFirstSearch($search) : Products::where(['deleted'=>0])->limit(50)->get();
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::info('Find Productos', [
                'error' => $th->getMessage()
            ]);
            return response()->json([
                'message' => 'No se pudo obtener Productos -[P-001]',
                'errors' => [
                    'error' => 'Internal error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }
}
