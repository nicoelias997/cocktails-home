<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use \App\Http\Middleware\EnsureAdmin;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])
    ->name('web.')
    ->group(function () {

        Route::get('/cocktails', fn () => Inertia::render('Cocktail/Index'))
            ->name('cocktail.index');
        Route::get('/cocktails/create', fn () => Inertia::render('Cocktail/Create'))
            ->name('cocktail.create');
        Route::get('/cocktails/{id}/edit', fn ($id) => Inertia::render('Cocktail/Edit', ['id' => $id]))
            ->name('cocktail.edit');

        Route::middleware(EnsureAdmin::class)
            ->name('schema.')
            ->prefix('schemas')
            ->group(function () {
                    Route::get('', fn () => Inertia::render('Schema/Index'))
                        ->name('index');
                    Route::get('{name}/edit', fn ($name) => Inertia::render('Schema/Edit', ['name' => $name]))
                        ->name('edit');
            });

        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
    });

require __DIR__.'/auth.php';
