<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña</title>
    <style>
        body { background-color: #f4f7f6; font-family: system-ui, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; box-sizing: border-box; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #d1d3e2; border-radius: 8px; box-sizing: border-box; font-size: 14px; margin-bottom: 15px; }
        .btn-warning { background-color: #f6c23e; color: #333; display: block; width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 14px; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; font-size: 14px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h3 style="text-align: center; margin-bottom: 20px;">Actualizar Contraseña</h3>

        @if($errors->any()) <div class="alert-danger">{{ $errors->first() }}</div> @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label style="font-weight:600; font-size:14px; color:#666;">Nueva Contraseña</label>
            <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required autofocus>

            <label style="font-weight:600; font-size:14px; color:#666;">Confirmar Nueva Contraseña</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Repite la contraseña" required>

            <button type="submit" class="btn btn-warning">Restablecer e Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
