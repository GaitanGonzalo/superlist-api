<?php

namespace App\Http\Controllers;

use App\Models\Countries;
use App\Models\States;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class CountriesController extends Controller
{
    public function search(Request $request)
    {
        try {
            $search = $request->query('search');
            $response = !empty($search) ? Countries::getFirstSearch($search) : Countries::limit(10)->get();
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::info('Find Countries', [
                'error' => $th->getMessage()
            ]);
            return response()->json([
                'message' => 'No se pudo obtener Países -[C-001]',
                'errors' => [
                    'error' => 'Internal error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }

    public function getStatesByCountry(string $country_id, Request $request)
    {
        try {
            $search = $request->query('search');
            $response = !empty($search) ? States::getFirstSearch($search, $country_id) : States::where(['country_id' => $country_id])->get();
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::info('Find States', [
                'error' => $th->getMessage()
            ]);
            return response()->json([
                'message' => 'No se pudo obtener Estados -[E-001]',
                'errors' => [
                    'error' => 'Internal error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }
}
