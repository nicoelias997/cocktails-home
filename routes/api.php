<?php

use App\Http\Controllers\Api\CocktailController;
use App\Http\Controllers\Api\FormSchemaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());

    Route::prefix('schemas')
        ->name('schemas.')
        ->group(function () {
            Route::get('', [FormSchemaController::class, 'index'])->name('index');
            Route::get('{name}', [FormSchemaController::class, 'show'])->name('show');
            Route::put('{name}', [FormSchemaController::class, 'update'])->name('update');
        });

    Route::prefix('cocktails')->name('cocktails.')->group(function () {
        Route::get('', [CocktailController::class, 'index'])->name('index');
        Route::post('', [CocktailController::class, 'store'])->name('store');
        Route::get('{id}', [CocktailController::class, 'show'])->name('show');
        Route::put('{id}', [CocktailController::class, 'update'])->name('update');
        Route::delete('{id}', [CocktailController::class, 'destroy'])->name('destroy');
    });
});
