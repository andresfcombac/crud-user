<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    // Módulo de Login
    public function mostrarLogin() {
        return view('clientes.login');
    }

    public function login(Request $request) {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required',
        ]);

        // Buscamos el usuario por correo
        $usuario = Cliente::where('correo', $request->correo)->first();

        // Validamos si existe, si es Administrador y si la contraseña coincide con el Hash
        if ($usuario && $usuario->tipo_usuario === 'Administrador' && Hash::check($request->password, $usuario->password)) {
            session(['user_id' => $usuario->id, 'user_rol' => $usuario->tipo_usuario]);
            return redirect()->route('clientes.index')->with('exito', '¡Bienvenido de nuevo! Has iniciado sesión correctamente.');
        }

        return back()->withErrors(['login_error' => 'Credenciales incorrectas o no tienes permisos de Administrador.']);
    }

    public function logout() {
        session()->forget(['user_id', 'user_rol']);
        return redirect()->route('login')->with('exito', 'Sesión cerrada correctamente.');
    }

    // Módulo CRUD (Protegido por validación de sesión)
    public function index(Request $request) {
        if (!session()->has('user_id')) { return redirect()->route('login'); }

        $buscar = $request->get('buscar');
        
        // Paginación de 5 en 5 manteniendo el filtro de búsqueda
        $clientes = Cliente::where(function($query) use ($buscar) {
                        $query->where('nombres', 'LIKE', "%$buscar%")
                              ->orWhere('apellidos', 'LIKE', "%$buscar%")
                              ->orWhere('correo', 'LIKE', "%$buscar%");
                    })->paginate(5)->withQueryString();

        return view('clientes.index', compact('clientes', 'buscar'));
    }

    public function create() {
        if (!session()->has('user_id')) { return redirect()->route('login'); }
        return view('clientes.create');
    }

    public function store(Request $request) {
        if (!session()->has('user_id')) { return redirect()->route('login'); }

        $request->validate([
            'nombres' => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
            'correo' => 'required|email|unique:clientes,correo',
            'cargo' => 'required|string|max:50',
            'tipo_usuario' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        Cliente::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'correo' => $request->correo,
            'cargo' => $request->cargo,
            'tipo_usuario' => $request->tipo_usuario,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('clientes.index')->with('exito', '¡Excelente! El nuevo usuario ha sido registrado y su contraseña fue cifrada correctamente.');
    }

    public function edit(Cliente $cliente) {
        if (!session()->has('user_id')) { return redirect()->route('login'); }
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente) {
        if (!session()->has('user_id')) { return redirect()->route('login'); }

        $request->validate([
            'nombres' => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
            'correo' => 'required|email|unique:clientes,correo,' . $cliente->id,
            'cargo' => 'required|string|max:50',
            'tipo_usuario' => 'required|string',
            'password' => 'nullable|string|min:6',
        ]);

        $data = $request->only(['nombres', 'apellidos', 'correo', 'cargo', 'tipo_usuario']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $cliente->update($data);
        return redirect()->route('clientes.index')->with('exito', 'Los cambios se han guardado. El perfil del usuario fue actualizado de forma exitosa.');
    }

    public function destroy(Cliente $cliente) {
        if (!session()->has('user_id')) { return redirect()->route('login'); }
        
        $cliente->delete();
        return redirect()->route('clientes.index')->with('exito', 'El registro del usuario ha sido eliminado de la base de datos permanentemente.');
    }
}
