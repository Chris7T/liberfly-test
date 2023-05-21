<?php

use App\Http\Controllers\Item\ItemDeleteController;
use App\Http\Controllers\Item\ItemIndexController;
use App\Http\Controllers\Item\ItemRegisterController;
use App\Http\Controllers\Item\ItemShowController;
use App\Http\Controllers\Item\ItemUpdateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->prefix('items')->group(function () {
    Route::post('/', ItemRegisterController::class)->name('item.create');
    Route::get('/', ItemIndexController::class)->name('item.index');
    Route::get('/{itemId}', ItemShowController::class)->name('item.show');
    Route::put('/{itemId}', ItemUpdateController::class)->name('item.update');
    Route::delete('/{itemId}', ItemDeleteController::class)->name('item.destroy');
});
