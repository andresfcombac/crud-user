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

                    // 2. Guardar en la TABLA INTERMEDIA (cliente_mfa) usando Query Builder
                    \DB::table('cliente_mfa')->insert([
                        'cliente_id' => $usuario->id,
                        'token'      => $otp,
                        'expires_at' => now()->addMinutes(10),
                        'estado'     => 'No usado',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // 3. ENVÍO REAL CON LA FUNCIÓN mail() NATIVA DE PHP (Configuración Oficial como Array)
                    $paraAddress = $usuario->correo;
                    $asuntoEmail = '=?UTF-8?B?' . base64_encode('Tu código de acceso de doble factor') . '?='; // Evita errores de tildes
                    
                    $mensajeCuerpo = "¡Hola, " . $usuario->nombres . "!\r\n\r\n"
                                   . "Has solicitado iniciar sesión. Para completar el acceso, introduce el siguiente código de un solo uso:\r\n"
                                   . "Código OTP: " . $otp . "\r\n\r\n"
                                   . "Este código tiene una validez estricta de 10 minutos.\r\n"
                                   . "Si tú no solicitaste este acceso, por favor ignora este mensaje.";

                    // Estructura de cabeceras en formato Array (Ejemplo oficial de PHP.net)
                    $cabeceras = [
                        'From'         => 'Sistema Seguridad PHP <escaner@alianzatemporales.com>',
                        'Reply-To'     => 'escaner@alianzatemporales.com',
                        'MIME-Version' => '1.0',
                        'Content-Type' => 'text/plain; charset=UTF-8',
                        'X-Mailer'     => 'PHP/' . phpversion()
                    ];

                    // Ejecución nativa de la función oficial
                    mail($paraAddress, $asuntoEmail, $mensajeCuerpo, $cabeceras);

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
                'role_id' => 'required|exists:roles,id',
                'password' => 'required|string|min:6',
            ]);

            $nuevoCliente = Cliente::create([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'correo' => $request->correo,
                'cargo' => $request->cargo,
                'tipo_usuario' => 'Rol Asignado',
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

        // --- MÓDULO INTERMEDIO DE VERIFICACIÓN OTP CON TABLA INTERMEDIA ---

        public function mostrarFormulario2FA() {
            if (!session()->has('2fa_purgue_user_id')) { return redirect()->route('login'); }
            return view('clientes.verificar_2fa');
        }

        public function verificarLogin2FA(Request $request) {
            if (!session()->has('2fa_purgue_user_id')) { return redirect()->route('login'); }
            
            $request->validate(['code' => 'required|numeric|digits:6']);
            
            // Buscamos el token en la tabla intermedia que corresponda al usuario y esté 'No usado'
            $registroMfa = \DB::table('cliente_mfa')
                                ->where('cliente_id', session('2fa_purgue_user_id'))
                                ->where('token', $request->code)
                                ->where('estado', 'No usado')
                                ->latest()
                                ->first();

            // Validación 1: Si no existe, significa que el código es inválido o ya se usó
            if (!$registroMfa) {
                return back()->withErrors(['2fa_error' => 'El código introducido es incorrecto o ya ha sido utilizado anteriormente.']);
            }

            // Validación 2: Comprobar la expiración estricta de los 10 minutos
            if (now()->greaterThan($registroMfa->expires_at)) {
                return back()->withErrors(['2fa_error' => 'El código ha expirado (Límite de 10 minutos superado). Por favor, vuelve a iniciar sesión.']);
            }

            // ÉXITO: Actualizamos el estado a 'Usado' inmediatamente para quemarlo
            \DB::table('cliente_mfa')
                ->where('id', $registroMfa->id)
                ->update(['estado' => 'Usado', 'updated_at' => now()]);

            // Otorgamos acceso oficial a la aplicación
            session(['user_id' => session('2fa_purgue_user_id')]);
            session()->forget('2fa_purgue_user_id');

            return redirect()->route('clientes.index')->with('exito', 'Autenticación de doble factor completada con éxito.');
        }

        public function reenviarOtp() {
            if (!session()->has('2fa_purgue_user_id')) { return redirect()->route('login'); }

            $usuario = Cliente::find(session('2fa_purgue_user_id'));
            if (!$usuario) { return redirect()->route('login'); }

            // Generamos un token completamente nuevo
            $nuevoOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Creamos un nuevo registro independiente con estado 'No usado'
            \DB::table('cliente_mfa')->insert([
                'cliente_id' => $usuario->id,
                'token'      => $nuevoOtp,
                'expires_at' => now()->addMinutes(10),
                'estado'     => 'No usado',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Reenvío nativo utilizando la función mail() de PHP con Array de cabeceras
            $paraAddress = $usuario->correo;
            $asuntoEmail = '=?UTF-8?B?' . base64_encode('Nuevo código de acceso solicitado') . '?=';
            
            $mensajeCuerpo = "¡Hola, " . $usuario->nombres . "!\r\n\r\n"
                           . "Se ha solicitado un nuevo token de doble factor.\r\n"
                           . "Nuevo Código OTP: " . $nuevoOtp . "\r\n\r\n"
                           . "Este código vencerá en exactamente 10 minutos.";

            $cabeceras = [
                'From'         => 'Sistema Seguridad PHP <escaner@alianzatemporales.com>',
                'Reply-To'     => 'escaner@alianzatemporales.com',
                'MIME-Version' => '1.0',
                'Content-Type' => 'text/plain; charset=UTF-8',
                'X-Mailer'     => 'PHP/' . phpversion()
            ];

            mail($paraAddress, $asuntoEmail, $mensajeCuerpo, $cabeceras);

            return back()->with('exito', '¡Código nuevo enviado con éxito!');
        }
}
