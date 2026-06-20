<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductsPricesStoresController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Ingresa al metodo index']);
    }

    public function searchProducts()
    {
        return response()->json(['message' => 'Ingresa al metodo searchProducts']);
    }
}
