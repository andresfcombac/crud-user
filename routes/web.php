<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

// Rutas públicas de Autenticación
Route::get('login', [ClienteController::class, 'mostrarLogin'])->name('login');
Route::post('login', [ClienteController::class, 'login'])->name('login.procesar');
Route::get('logout', [ClienteController::class, 'logout'])->name('logout');

// Rutas para el segundo factor de autenticación (OTP)
Route::get('login/2fa', [ClienteController::class, 'mostrarFormulario2FA'])->name('login.2fa');
Route::post('login/2fa', [ClienteController::class, 'verificarLogin2FA'])->name('login.2fa.procesar');

// Ruta para regenerar y reenviar el código OTP por correo
Route::get('login/2fa/reenviar', [ClienteController::class, 'reenviarOtp'])->name('login.2fa.reenviar');


// Rutas accesibles por cualquier usuario autenticado (Ver y buscar)
Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');

// Rutas protegidas por el ClienteController de forma nativa
Route::get('clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store');
Route::get('clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
Route::put('clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

// Rutas para la recuperación de contraseña
Route::get('password/reset', [ClienteController::class, 'mostrarFormularioSolicitud'])->name('password.request');
Route::post('password/email', [ClienteController::class, 'enviarEnlaceRecuperacion'])->name('password.email');
Route::get('password/reset/{token}', [ClienteController::class, 'mostrarFormularioRestablecimiento'])->name('password.reset');
Route::post('password/reset', [ClienteController::class, 'actualizarPassword'])->name('password.update');

Route::get('/', function () {
    return redirect()->route('clientes.index');
});
