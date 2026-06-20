<?php

namespace App\Http\Controllers;

use App\Models\Stores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoresController extends Controller
{
    public function index() {}

    public function search(Request $request)
    {
        try {
            $search = $request->query('search');
            $response = !empty($search) ? Stores::getFirstSearch($search) : Stores::where(['deleted' => 0])->limit(50)->get();
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

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'identification_number' => 'nullable|string|max:20',
                'cp' => 'nullable|string|max:10',
                'razon_social' => 'nullable|string|max:255',
                'country_id' => 'required|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'location_id' => 'required|exists:locations,id',
                'address' => 'nullable|string|max:255',
                'address_number' => 'nullable|string|max:255',
                'is_subsidiary' => 'required|boolean'
            ]);

            $store = Stores::create($request->all());

            return response()->json([
                'message' => 'Tienda creada exitosamente',
                'store' => $store
            ], 201);
        } catch (\Throwable $th) {
            Log::info('Create Store', [
                'error' => $th->getMessage()
            ]);
            return response()->json([
                'message' => 'No se pudo crear la tienda -[C-001]',
                'errors' => [
                    'error' => 'Internal error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }
}
