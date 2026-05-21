<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Doble Factor (2FA)</title>
    <style>
        body { 
            background-color: #f8f9fc; 
            font-family: system-ui, -apple-system, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            color: #333;
        }
        .card { 
            background: white; 
            padding: 40px 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
            text-align: center; 
            max-width: 400px; 
            width: 100%; 
        }
        h2 { 
            color: #4e73df; 
            margin-top: 0; 
            font-weight: bold;
        }
        p {
            color: #858796;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 25px;
        }
        .form-control { 
            width: 85%; 
            padding: 12px; 
            border: 2px solid #d1d3e2; 
            border-radius: 8px; 
            font-size: 22px; 
            text-align: center; 
            letter-spacing: 6px; 
            margin-bottom: 20px; 
            font-weight: bold;
            color: #4e73df;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #4e73df;
        }
        .btn { 
            background-color: #4e73df; 
            color: white; 
            border: none; 
            padding: 12px; 
            width: 100%; 
            border-radius: 8px; 
            font-weight: bold; 
            cursor: pointer; 
            font-size: 15px;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #2e59d9;
        }
        .error-box { 
            background-color: #f8d7da;
            color: #721c24; 
            font-size: 14px; 
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px; 
            border: 1px solid #f5c6cb;
            text-align: left;
        }
        .exito-box {
            background-color: #d4edda;
            color: #155724;
            font-size: 14px;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        /* --- ESTILOS ADICIONADOS PARA EL REENVÍO --- */
        .resend-link {
            display: inline-block;
            margin-top: 22px;
            font-size: 13px;
            color: #4e73df;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .resend-link:hover {
            text-decoration: underline;
            color: #2e59d9;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Código de Seguridad</h2>
        
        <!-- Mensaje de éxito al enviar o reenviar el correo -->
        @if(session('exito'))
            <div class="exito-box">{{ session('exito') }}</div>
        @endif

        <p>Por seguridad, introduce el código OTP de 6 dígitos que acabamos de enviar a tu correo electrónico. Recuerda que expira en 10 minutos.</p>
        
        <!-- Mensajes de error en caso de código inválido o expirado -->
        @if($errors->has('2fa_error'))
            <div class="error-box">
             {{ $errors->first('2fa_error') }}
            </div>
        @endif

        <form action="{{ route('login.2fa.procesar') }}" method="POST">
            @csrf
            <input 
                type="text" 
                name="code" 
                class="form-control" 
                placeholder="000000" 
                maxlength="6" 
                required 
                autocomplete="off" 
                autofocus
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
            >
            <button type="submit" class="btn">Verificar e Ingresar</button>
        </form>

        <!-- --- ENLACE DE REENVÍO ADICIONADO --- -->
        <a href="{{ route('login.2fa.reenviar') }}" class="resend-link">
            ¿No recibiste el código? Solicitar uno nuevo
        </a>
    </div>
</body>
</html>
