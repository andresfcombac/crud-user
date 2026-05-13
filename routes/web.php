<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

// Rutas públicas de Autenticación
Route::get('login', [ClienteController::class, 'mostrarLogin'])->name('login');
Route::post('login', [ClienteController::class, 'login'])->name('login.procesar');
Route::get('logout', [ClienteController::class, 'logout'])->name('logout');

// Rutas accesibles por cualquier usuario autenticado (Ver y buscar)
Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');

// Rutas protegidas: Solo el Administrador puede ingresar
Route::middleware(['es.admin'])->group(function () {
    Route::get('clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
});

Route::get('/', function () {
    return redirect()->route('clientes.index');
});
