<?php

use App\Http\Controllers\MarketController;

Route::get('/api/market/price', [MarketController::class, 'price']);
Route::get('/api/market/chart', [MarketController::class, 'chart']);

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
