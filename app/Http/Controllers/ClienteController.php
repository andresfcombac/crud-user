<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Role; // Importamos el nuevo modelo de Roles
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

            // Validamos si existe y si la contraseña coincide con el Hash
            if ($usuario && Hash::check($request->password, $usuario->password)) {
                
                // Validamos dinámicamente si tiene permiso para ver la lista (los administradores y editores lo tienen)
                if ($usuario->tienePermiso('ver-usuarios')) {
                    
                    // 1. Generar código OTP numérico aleatorio de 6 dígitos
                    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                    // 2. Guardar el código en las columnas creadas en DBeaver y definir los 10 minutos
                    $usuario->update([
                        'otp_code' => $otp,
                        'otp_expires_at' => now()->addMinutes(10)
                    ]);

                    // 3. Enviar la notificación por correo electrónico
                    $usuario->notify(new \App\Notifications\EnviarCodigoOtp($otp));

                    // 4. Guardar el ID de forma temporal para la verificación intermedia
                    session(['2fa_purgue_user_id' => $usuario->id]);

                    return redirect()->route('login.2fa')->with('exito', '¡Credenciales correctas! Hemos enviado un código OTP de 6 dígitos a tu correo electrónico.');
                }
                
                return back()->withErrors(['login_error' => 'No tienes los permisos necesarios para acceder.']);
            }

            return back()->withErrors(['login_error' => 'Credenciales incorrectas.']);
        }

        public function logout() {
            session()->forget(['user_id']);
            return redirect()->route('login')->with('exito', 'Sesión cerrada correctamente.');
        }

        // Módulo CRUD (Protegido por validación de sesión y permisos)
        public function index(Request $request) {
            if (!session()->has('user_id')) { return redirect()->route('login'); }

            // Buscamos al usuario logueado actualmente usando el ID de la sesión
            $usuarioLogueado = Cliente::find(session('user_id'));
            if (!$usuarioLogueado || !$usuarioLogueado->tienePermiso('ver-usuarios')) {
                return redirect()->route('login')->withErrors(['login_error' => 'No tienes permiso para ver este listado.']);
            }

            $buscar = $request->get('buscar');
            
            $clientes = Cliente::where(function($query) use ($buscar) {
                            $query->where('nombres', 'LIKE', "%$buscar%")
                                ->orWhere('apellidos', 'LIKE', "%$buscar%")
                                ->orWhere('correo', 'LIKE', "%$buscar%");
                        })->paginate(5)->withQueryString();

            return view('clientes.index', compact('clientes', 'buscar', 'usuarioLogueado'));
        }

        public function create() {
            if (!session()->has('user_id')) { return redirect()->route('login'); }
            
            $usuarioLogueado = Cliente::find(session('user_id'));
            if (!$usuarioLogueado || !$usuarioLogueado->tienePermiso('crear-usuarios')) {
                return redirect()->route('clientes.index')->with('error', 'No tienes permiso para crear usuarios.');
            }

            // Enviamos los roles de la base de datos a la vista
            $roles = Role::all();
            return view('clientes.create', compact('roles'));
        }

        public function store(Request $request) {
            if (!session()->has('user_id')) { return redirect()->route('login'); }

            $usuarioLogueado = Cliente::find(session('user_id'));
            if (!$usuarioLogueado || !$usuarioLogueado->tienePermiso('crear-usuarios')) {
                return redirect()->route('clientes.index')->with('error', 'Acción no autorizada.');
            }

            $request->validate([
                'nombres' => 'required|string|max:50',
                'apellidos' => 'required|string|max:50',
                'correo' => 'required|email|unique:clientes,correo',
                'cargo' => 'required|in:Desarrollador,Diseñador,Gerente de Proyecto,Analista de QA,Soporte Técnico',
                'role_id' => 'required|exists:roles,id', // Reemplazamos validación por el ID de rol
                'password' => 'required|string|min:6',
            ]);

            $nuevoCliente = Cliente::create([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'correo' => $request->correo,
                'cargo' => $request->cargo,
                'tipo_usuario' => 'Rol Asignado', // Texto por defecto para rellenar la columna vieja
                'password' => Hash::make($request->password),
            ]);

            // Vinculamos el rol en la tabla intermedia de la base de datos
            $nuevoCliente->roles()->attach($request->role_id);

            return redirect()->route('clientes.index')->with('exito', '¡Excelente! El nuevo usuario ha sido registrado y su rol fue asignado.');
        }

        public function edit(Cliente $cliente) {
            if (!session()->has('user_id')) { return redirect()->route('login'); }
            
            $usuarioLogueado = Cliente::find(session('user_id'));
            if (!$usuarioLogueado || !$usuarioLogueado->tienePermiso('editar-usuarios')) {
                return redirect()->route('clientes.index')->with('error', 'No tienes permiso para modificar usuarios.');
            }

            $roles = Role::all();
            return view('clientes.edit', compact('cliente', 'roles'));
        }

        public function update(Request $request, Cliente $cliente) {
            if (!session()->has('user_id')) { return redirect()->route('login'); }

            $usuarioLogueado = Cliente::find(session('user_id'));
            if (!$usuarioLogueado || !$usuarioLogueado->tienePermiso('editar-usuarios')) {
                return redirect()->route('clientes.index')->with('error', 'Acción no autorizada.');
            }

            $request->validate([
                'nombres' => 'required|string|max:50',
                'apellidos' => 'required|string|max:50',
                'correo' => 'required|email|unique:clientes,correo,' . $cliente->id,
                'cargo' => 'required|in:Desarrollador,Diseñador,Gerente de Proyecto,Analista de QA,Soporte Técnico',
                'role_id' => 'required|exists:roles,id',
                'password' => 'nullable|string|min:6',
            ]);

            $data = $request->only(['nombres', 'apellidos', 'correo', 'cargo']);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $cliente->update($data);
            
            // Sincronizamos el rol para cambiar el viejo por el nuevo elegido en el formulario
            $cliente->roles()->sync([$request->role_id]);

            return redirect()->route('clientes.index')->with('exito', 'Los cambios se han guardado de forma exitosa.');
        }

        public function destroy(Cliente $cliente) {
            if (!session()->has('user_id')) { return redirect()->route('login'); }
            
            $usuarioLogueado = Cliente::find(session('user_id'));
            if (!$usuarioLogueado || !$usuarioLogueado->tienePermiso('eliminar-usuarios')) {
                return redirect()->route('clientes.index')->with('error', 'No cuentas con los permisos para eliminar registros.');
            }

            $cliente->delete();
            return redirect()->route('clientes.index')->with('exito', 'El registro del usuario ha sido eliminado permanentemente.');
        }

        // --- MÓDULO INTERMEDIO DE VERIFICACIÓN OTP ---

        public function mostrarFormulario2FA() {
            if (!session()->has('2fa_purgue_user_id')) { return redirect()->route('login'); }
            return view('clientes.verificar_2fa');
        }

        public function verificarLogin2FA(Request $request) {
            if (!session()->has('2fa_purgue_user_id')) { return redirect()->route('login'); }
            
            $request->validate(['code' => 'required|numeric|digits:6']);
            $usuario = Cliente::find(session('2fa_purgue_user_id'));

            // Validación 1: El código debe coincidir
            if (!$usuario || $usuario->otp_code !== $request->code) {
                return back()->withErrors(['2fa_error' => 'El código introducido es incorrecto.']);
            }

            // Validación 2: El tiempo actual no debe haber superado los 10 minutos establecidos
            if (now()->greaterThan($usuario->otp_expires_at)) {
                return back()->withErrors(['2fa_error' => 'El código ha expirado. Por favor, vuelve a iniciar sesión para generar uno nuevo.']);
            }

            // Éxito: Limpiamos los rastros del OTP de la base de datos por seguridad
            $usuario->update([
                'otp_code' => null,
                'otp_expires_at' => null
            ]);

            // Otorgamos el acceso definitivo a la plataforma
            session(['user_id' => $usuario->id]);
            session()->forget('2fa_purgue_user_id');

            return redirect()->route('clientes.index')->with('exito', 'Autenticación de doble factor completada con éxito.');
        }
        public function reenviarOtp() {
    // Verificamos que exista un usuario en el proceso intermedio de login
    if (!session()->has('2fa_purgue_user_id')) { 
        return redirect()->route('login'); 
    }

    $usuario = Cliente::find(session('2fa_purgue_user_id'));

    if (!$usuario) {
        return redirect()->route('login')->withErrors(['login_error' => 'Usuario no encontrado.']);
    }

    // 1. Generar un nuevo código OTP de 6 dígitos
    $nuevoOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // 2. Actualizar las columnas en DBeaver reiniciando el contador a 10 minutos
    $usuario->update([
        'otp_code' => $nuevoOtp,
        'otp_expires_at' => now()->addMinutes(10)
    ]);

    // 3. Despachar el nuevo correo (se reflejará en tu laravel.log)
    $usuario->notify(new \App\Notifications\EnviarCodigoOtp($nuevoOtp));

    return back()->with('exito', '¡Código nuevo enviado! Revisa tu bandeja de entrada o historial.');
}

}
