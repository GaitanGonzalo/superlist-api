<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProductsPricesStoresController;
use App\Http\Controllers\StoresController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\StatesController;

Route::get('/status', function () {
    return response()->json(['status' => 'OK', 'version' => '1.0.0', 'proyecto' => 'Super List App - api']);
})->name('api.status');

Route::post('login', [AuthController::class, 'login'])->name('api.login');
Route::post('register', [UserController::class, 'store'])->name('api.register');
Route::get('countries', [CountriesController::class, 'search'])->name('api.countries.search');
Route::get('countries/{country_id}/states', [CountriesController::class, 'getStatesByCountry'])->name('api.states.search');
Route::get('states/{state_id}/locations', [StatesController::class, 'getLocationsByState'])->name('api.locations.search');

//AUTH
Route::middleware(['jwt_cookies', 'jwt_auth', 'auth:api'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

//USER MANAGMENT
Route::middleware(['jwt_cookies', 'jwt_auth', 'auth:api'])->prefix('user')->group(function () {
    Route::get('/profile', [UserController::class, 'view'])->name('api.user.profile');
    Route::patch('/update', [UserController::class, 'update'])->name('api.user.profile');
});

//PRODUCTS MANAGMENT
Route::middleware(['jwt_cookies', 'jwt_auth', 'auth:api'])->prefix('products')->group(function () {
    Route::get('/', [ProductsController::class, 'search'])->name('api.products.search');
    //Route::patch('/update', [UserController::class, 'update'])->name('api.user.profile');
});


//STORES AND PRODUCTS
Route::middleware(['jwt_cookies', 'jwt_auth', 'auth:api'])->prefix('stores')->group(function () {
    Route::get('/', [StoresController::class, 'index'])->name('api.stores.index');
    Route::post('/', [StoresController::class, 'store'])->name('api.stores.store');
    Route::get('/{store_id}/products', [ProductsPricesStoresController::class, 'searchProducts'])->name('api.stores.search_products');
    //Route::patch('/update', [UserController::class, 'update'])->name('api.user.profile');
});


Route::middleware(['jwt_cookies', 'jwt_auth', 'auth:api', 'admin'])->prefix('stores')->group(function () {

    //Route::patch('/{store_id}', [ProductsPricesStoresController::class, 'search'])->name('api.stores.search');
    //Route::delete('/{store_id}', [ProductsPricesStoresController::class, 'search'])->name('api.stores.search');

    //Route::get('/{store_id}/products', [ProductsPricesStoresController::class, 'searchProducts'])->name('api.stores.search_products');
    //Route::patch('/update', [UserController::class, 'update'])->name('api.user.profile');
});
