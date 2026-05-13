<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        body { background-color: #f4f7f6; font-family: system-ui, -apple-system, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; box-sizing: border-box; }
        .text-center { text-align: center; margin-bottom: 20px; }
        .fw-bold { font-weight: bold; margin: 0; color: #333; }
        .mb-3 { margin-bottom: 15px; }
        .form-label { display: block; font-weight: 600; margin-bottom: 5px; color: #4e73df; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #d1d3e2; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; font-size: 14px; }
        .btn-primary { display: block; width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: bold; background-color: #4e73df; color: white; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <div class="text-center">
            <h3 class="fw-bold">Panel de Acceso</h3>
            <p style="color: #666; font-size: 14px;">Ingresa tus credenciales de Administrador</p>
        </div>

        @if($errors->has('login_error'))
            <div class="alert-danger">{{ $errors->first('login_error') }}</div>
        @endif

        <form action="{{ route('login.procesar') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn_item btn-primary">Entrar al Sistema</button>
        </form>
    </div>
</body>
</html>
