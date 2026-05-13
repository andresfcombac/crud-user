<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <style>
        body { background-color: #f4f7f6; font-family: system-ui, -apple-system, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px 0; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 450px; box-sizing: border-box; }
        .text-center { text-align: center; margin-bottom: 20px; }
        .fw-bold { font-weight: bold; margin: 0; color: #333; }
        .mb-3 { margin-bottom: 15px; }
        .form-label { display: block; font-weight: 600; margin-bottom: 5px; color: #4e73df; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #d1d3e2; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
        .error-campo { color: #e74a3b; font-size: 12px; margin-top: 5px; font-weight: bold; }
        .btn { display: block; width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; text-align: center; text-decoration: none; font-size: 14px; box-sizing: border-box; }
        .btn-primary { background-color: #4e73df; color: white; margin-bottom: 10px; }
        .btn-light { background-color: #eaecf4; color: #3a3b45; }
    </style>
</head>
<body>
    <div class="card">
        <div class="text-center">
            <h3 class="fw-bold">Crear Cuenta</h3>
            <p style="color: #666; font-size: 14px;">Completa el formulario de registro</p>
        </div>

        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nombres</label>
                <input type="text" name="nombres" class="form-control" value="{{ old('nombres') }}" required>
                @error('nombres') <div class="error-campo">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Apellidos</label>
                <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos') }}" required>
                @error('apellidos') <div class="error-campo">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" required>
                @error('correo') <div class="error-campo">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Cargo</label>
                <input type="text" name="cargo" class="form-control" value="{{ old('cargo') }}" required>
                @error('cargo') <div class="error-campo">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de Usuario (Rol)</label>
                <select name="tipo_usuario" class="form-control" required>
                    <option value="">Selecciona una opción</option>
                    <option value="Administrador" {{ old('tipo_usuario') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="Operador" {{ old('tipo_usuario') == 'Operador' ? 'selected' : '' }}>Operador</option>
                    <option value="Soporte" {{ old('tipo_usuario') == 'Soporte' ? 'selected' : '' }}>Soporte</option>
                </select>
                @error('tipo_usuario') <div class="error-campo">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
                @error('password') <div class="error-campo">{{ $message }}</div> @enderror
            </div>
            <div style="margin-top: 25px;">
                <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                <a href="{{ route('clientes.index') }}" class="btn btn-light">Volver al listado</a>
            </div>
        </form>
    </div>
</body>
</html>
