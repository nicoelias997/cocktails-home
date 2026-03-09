<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

        Route::get('/cocktails', fn () => Inertia::render('Cocktail/Index'))->name('cocktail.index');
        Route::get('/cocktails/create', fn () => Inertia::render('Cocktail/Create'))->name('cocktail.create');
        Route::get('/cocktails/{id}/edit', fn ($id) => Inertia::render('Cocktail/Edit', ['id' => $id]))->name('cocktail.edit');

        Route::get('/schemas', fn () => Inertia::render('Schema/Index'))->name('schema.index');
        Route::get('/schemas/{name}/edit', fn ($name) => Inertia::render('Schema/Edit', ['name' => $name]))->name('schema.edit');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

require __DIR__.'/auth.php';
