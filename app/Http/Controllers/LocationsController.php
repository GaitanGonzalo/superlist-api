<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationsController extends Controller
{
    public function search(Request $request, string $state_id)
    {
        try {
            $search = $request->query('search');
            $response = !empty($search) ? Locations::getFirstSearch($search, $state_id) : Locations::where(['state_id' => $state_id])->get();
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::info('Find Locations', [
                'error' => $th->getMessage()
            ]);
            return response()->json([
                'message' => 'No se pudo obtener Ubicaciones -[U-001]',
                'errors' => [
                    'error' => 'Internal error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }
}
