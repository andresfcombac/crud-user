<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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

        // Creamos una llave única de bloqueo combinando el correo y la IP del cliente
        $throttleKey = Str::lower($request->correo) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            ($segundosRestantes = RateLimiter::availableIn($throttleKey));
            $minutos = ceil($segundosRestantes / 60);
            
            return back()->withErrors([
                'login_error' => "Demasiados intentos fallidos. Tu acceso ha sido bloqueado por seguridad. Vuelve a intentarlo en {\$minutos} minuto(s)."
            ]);
        }

        $usuario = Cliente::where('correo', $request->correo)->first();


        // Validamos si existe y si la contraseña coincide con el Hash
        if ($usuario && Hash::check($request->password, $usuario->password)) {
            if (!$usuario->tienePermiso('ver-usuarios')) {
                return back()->withErrors(['login_error' => 'No tienes los permisos necesarios para acceder.']);
            }

            // ÉXITO: Limpiamos los intentos fallidos acumulados
            RateLimiter::clear($throttleKey);

            // Generar código OTP numérico aleatorio de 6 dígitos
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Guardar en la TABLA INTERMEDIA (cliente_mfa) usando Query Builder
            DB::table('cliente_mfa')->insert([
                'cliente_id' => $usuario->id,
                'token'      => $otp,
                'expires_at' => now()->addMinutes(10),
                'estado'     => 'No usado',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $paraAddress = $usuario->correo;
            $asuntoEmail = '=?UTF-8?B?' . base64_encode('Tu código de acceso de doble factor') . '?=';
            
            $mensajeCuerpo = "¡Hola, " . $usuario->nombres . "!\r\n\r\n"
               . "Has solicitado iniciar sesión. Para completar el acceso, introduce el siguiente código de un solo uso:\r\n"
               . "Código OTP: " . $otp . "\r\n\r\n"
               . "Este código tiene una validez estricta de 10 minutos.\r\n"
               . "Si tú no solicitaste este acceso, por favor ignora este mensaje.";


            $cabeceras = [
                'From'         => 'Sistema Seguridad PHP <escaner@alianzatemporales.com>',
                'Reply-To'     => 'escaner@alianzatemporales.com',
                'MIME-Version' => '1.0',
                'Content-Type' => 'text/plain; charset=UTF-8',
                'X-Mailer'     => 'PHP/' . phpversion()
            ];

            mail($paraAddress, $asuntoEmail, $mensajeCuerpo, $cabeceras);


            session(['2fa_purgue_user_id' => $usuario->id]);

            return redirect()->route('login.2fa')->with('exito', '¡Credenciales correctas! Hemos enviado un código OTP de 6 dígitos a tu correo electrónico.');
        }

        RateLimiter::hit($throttleKey, 300);

        return back()->withErrors(['login_error' => 'Credenciales incorrectas.']);
    }

    public function logout() {
        session()->forget(['user_id']);
        return redirect()->route('login')->with('exito', 'Sesión cerrada correctamente.');
    }

    public function index(Request $request) {
        if (!session()->has('user_id')) { return redirect()->route('login'); }

        $usuarioLogueado = Cliente::find(session('user_id'));
        if (!$usuarioLogueado || !$usuarioLogueado->tienePermiso('ver-usuarios')) {
            return redirect()->route('login')->withErrors(['login_error' => 'No tienes permiso para ver este listado.']);
        }

                $buscar = $request->get('buscar');
        
        $clientes = Cliente::where(function($query) use ($buscar) {
                        $query->where('nombres', 'LIKE', "%" . $buscar . "%")
                            ->orWhere('apellidos', 'LIKE', "%" . $buscar . "%")
                            ->orWhere('correo', 'LIKE', "%" . $buscar . "%");
                    })->paginate(5)->withQueryString();

        return view('clientes.index', compact('clientes', 'buscar', 'usuarioLogueado'));

    }

    public function create() {
        if (!session()->has('user_id')) { return redirect()->route('login'); }
        
                $usuarioLogueado = Cliente::find(session('user_id'));
        
        if (!$usuarioLogueado || !$usuarioLogueado->tienePermiso('crear-usuarios')) {
            return redirect()->route('clientes.index')->with('error', 'No tienes permiso para crear usuarios.');
        }

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
        
        // Sincronizamos el rol para cambiar el viejo por el nuevo en la base de datos
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

    public function mostrarFormulario2FA() {
        if (!session()->has('2fa_purgue_user_id')) { return redirect()->route('login'); }
        return view('clientes.verificar_2fa');
    }

    public function verificarLogin2FA(Request $request) {
        if (!session()->has('2fa_purgue_user_id')) { return redirect()->route('login'); }
        
        $request->validate(['code' => 'required|numeric|digits:6']);

        // Fabricamos la llave de bloqueo usando strings normales
        $otpThrottleKey = 'otp|' . session('2fa_purgue_user_id') . '|' . $request->ip();

        // 1. VERIFICACIÓN: ¿Está bloqueado por adivinar códigos OTP?
        if (RateLimiter::tooManyAttempts($otpThrottleKey, 3)) {
            $segundosRestantes = RateLimiter::availableIn($otpThrottleKey);
            $minutos = ceil($segundosRestantes / 60);

            return back()->withErrors([
                '2fa_error' => "Has fallado el código OTP demasiadas veces. Bloqueado por seguridad. Intenta en {$minutos} minuto(s)."
            ]);
        }
        
        // Buscamos el token en la tabla intermedia
        $registroMfa = DB::table('cliente_mfa')
                            ->where('cliente_id', session('2fa_purgue_user_id'))
                            ->where('token', $request->code)
                            ->where('estado', 'No usado')
                            ->latest()
                            ->first();

        if (!$registroMfa) {
            // FRACASO: Registramos el intento fallido al OTP (Bloqueo por 5 minutos)
            RateLimiter::hit($otpThrottleKey, 300);
            return back()->withErrors(['2fa_error' => 'El código introducido es incorrecto o ya ha sido utilizado anteriormente.']);
        }

        // 2. VERIFICACIÓN: ¿El código ya expiró? (Validación de los 10 minutos)
        if (now()->greaterThan($registroMfa->expires_at)) {
            return back()->withErrors(['2fa_error' => 'El código OTP ha expirado. Por favor, regresa al login para solicitar uno nuevo.']);
        }

        // ÉXITO: Actualizamos el estado del token a 'Usado'
        DB::table('cliente_mfa')
            ->where('id', $registroMfa->id)
            ->update([
                'estado' => 'Usado',
                'updated_at' => now()
            ]);

        // Limpiamos los intentos de bloqueo de OTP exitosamente
        RateLimiter::clear($otpThrottleKey);

        // Iniciamos la sesión real del usuario en la aplicación
        session(['user_id' => session('2fa_purgue_user_id')]);
        
        // Limpiamos la variable temporal del paso intermedio
        session()->forget('2fa_purgue_user_id');

        // Redireccionamos a la pantalla principal del CRUD
        return redirect()->route('clientes.index')->with('exito', '¡Inicio de sesión exitoso! Bienvenido al sistema.');
    }
    // reenvio OTP
    public function reenviarOtp() {
        if (!session()->has('2fa_purgue_user_id')) { 
            return redirect()->route('login'); 
        }

        $usuario = Cliente::find(session('2fa_purgue_user_id'));
        if (!$usuario) { 
            return redirect()->route('login'); 
        }

        // Generamos un token completamente nuevo
        $nuevoOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Insertamos el nuevo registro independiente en la tabla intermedia de DBeaver
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
