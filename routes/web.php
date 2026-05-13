<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

// Rutas de Autenticación
Route::get('login', [ClienteController::class, 'mostrarLogin'])->name('login');
Route::post('login', [ClienteController::class, 'login'])->name('login.procesar');
Route::get('logout', [ClienteController::class, 'logout'])->name('logout');

// CRUD Completo
Route::resource('clientes', ClienteController::class);

// Redirección por defecto
Route::get('/', function () {
    return redirect()->route('clientes.index');
});
