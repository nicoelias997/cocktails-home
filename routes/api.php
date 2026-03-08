<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CocktailController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')
    ->name('api.')
    ->group(function () {
            Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::name('cocktails.')
            ->prefix('cocktails')
            ->group(function () {
                Route::get('', [CocktailController::class, 'index'])->name('index');
                Route::get('create', [CocktailController::class, 'create'])->name('create');
                Route::post('create', [CocktailController::class, 'store'])->name('store');
                Route::get('edit', [CocktailController::class, 'edit'])->name('edit');
                Route::get('{id}', [CocktailController::class, 'show'])->name('show');
                Route::put('{id}', [CocktailController::class, 'update'])->name('update');
                Route::delete('{id}', [CocktailController::class, 'destroy'])->name('destroy');
        });
});