<?php

namespace App\Http\Controllers;

use App\Models\Stores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoresController extends Controller
{
    public function index(Request $request)
    {
        $countryId = $request->user()->country_id;
        $stateId = $request->user()->state_id;
        $locationId = $request->user()->location_id;
        try {
            $query = Stores::query();

            // Filter by search term if provided
            if ($request->has('search') && !empty($request->query('search'))) {
                $search = $request->query('search');
                $query->where('name', 'like', '%' . $search . '%');
            }

            // Filter by country, state, location
            if ($countryId) {
                $query->where('country_id', $countryId);
            }
            if ($stateId) {
                $query->where('state_id', $stateId);
            }
            if ($locationId) {
                $query->where('location_id', $locationId);
            }

            $stores = $query->limit(50)->get();

            return response()->json([
                'success' => true,
                'data' => $stores
            ]);
        } catch (\Throwable $th) {
            Log::error('Error fetching stores: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudieron obtener los comercios',
                'errors' => [
                    'error' => 'Internal Server Error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }

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
