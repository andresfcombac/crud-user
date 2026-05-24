<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, -apple-system, sans-serif; background: radial-gradient(circle at 10% 20%, rgb(242, 246, 245) 0%, rgb(224, 234, 233) 90.1%); height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .wrapper { background: #ffffff; width: 100%; max-width: 850px; height: 500px; display: flex; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        .branding-side { width: 45%; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); padding: 40px; display: flex; flex-direction: column; justify-content: center; color: #ffffff; position: relative; }
        .branding-side h1 { font-size: 28px; font-weight: 800; margin-bottom: 12px; line-height: 1.2; }
        .branding-side p { font-size: 14px; opacity: 0.85; line-height: 1.5; }
        .branding-side::after { content: ''; position: absolute; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; top: -30px; left: -30px; }
        .form-side { width: 55%; padding: 50px 40px; display: flex; flex-direction: column; justify-content: center; }
        .form-header { margin-bottom: 30px; }
        .form-header h2 { font-size: 24px; color: #333333; font-weight: 700; }
        .form-header p { font-size: 14px; color: #777777; margin-top: 4px; }
        .mb-4 { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #4e73df; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { width: 100%; padding: 12px 16px; border: 1px solid #d1d3e2; border-radius: 10px; font-size: 14px; transition: border-color 0.2s, box-shadow 0.2s; outline: none; }
        .form-control:focus { border-color: #4e73df; box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.15); }
        .alert-danger { background-color: #fde8e8; color: #9b1c1c; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #fbd5d5; font-size: 13px; font-weight: 500; list-style-position: inside; }
        .btn-submit { width: 100%; padding: 14px; background: #4e73df; color: #ffffff; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; transition: background 0.2s, transform 0.1s; box-shadow: 0 4px 12px rgba(78, 115, 223, 0.25); }
        .btn-submit:hover { background: #3b5ecc; }
        .btn-submit:active { transform: scale(0.98); }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="branding-side">
            <h1>Control de<br>Operaciones</h1>
            <p>Ecosistema centralizado de gestión para administración, auditoría y control de credenciales de infraestructura.</p>
        </div>
        <div class="form-side">
            <div class="form-header">
                <h2>Identificación</h2>
                <p>Por seguridad, introduce tus claves de acceso.</p>
            </div>

            @if($errors->any())
                <div class="alert-danger">
                    <ul style="margin: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.procesar') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Correo Corporativo</label>
                    <input type="email" name="correo" class="form-control" placeholder="nombre@empresa.com" value="{{ old('correo') }}" required autocomplete="username">
                </div>
                <div class="mb-4">
                    <label class="form-label">Clave de Seguridad</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-submit">Autenticar Entrada</button>
                <a href="{{ route('password.request') }}" style="display: inline-block; margin-top: 15px; font-size: 13px; color: #4e73df; text-decoration: none; font-weight: 500;">
    ¿Olvidaste tu contraseña? Recuperar aquí
</a>

            </form>
        </div>
    </div>
</body>
</html>
