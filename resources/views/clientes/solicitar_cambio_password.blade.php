<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <style>
        body { background-color: #f4f7f6; font-family: system-ui, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; box-sizing: border-box; text-align: center; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #d1d3e2; border-radius: 8px; box-sizing: border-box; font-size: 14px; margin-bottom: 15px; }
        .btn { display: block; width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; text-decoration: none; font-size: 14px; }
        .btn-primary { background-color: #4e73df; color: white; margin-bottom: 10px; }
        .btn-light { background-color: #eaecf4; color: #3a3b45; }
        .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 8px; font-size: 14px; margin-bottom: 15px; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; font-size: 14px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h3>¿Olvidaste tu contraseña?</h3>
        <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Ingresa tu correo y te enviaremos un enlace seguro para restablecerla.</p>

        @if(session('exito')) <div class="alert-success">{{ session('exito') }}</div> @endif
        @if($errors->any()) <div class="alert-danger">{{ $errors->first() }}</div> @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <input type="email" name="correo" class="form-control" placeholder="correo@ejemplo.com" required autofocus>
            <button type="submit" class="btn btn-primary">Enviar Enlace</button>
            <a href="{{ route('login') }}" class="btn btn-light">Volver al Login</a>
        </form>
    </div>
</body>
</html>
