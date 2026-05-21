<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <style>
        body { background-color: #f4f7f6; font-family: system-ui, -apple-system, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px 0; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 450px; box-sizing: border-box; }
        .text-center { text-align: center; margin-bottom: 20px; }
        .fw-bold { font-weight: bold; margin: 0; color: #333; }
        .text-muted { color: #666; font-size: 14px; }
        .mb-3 { margin-bottom: 15px; }
        .form-label { display: block; font-weight: 600; margin-bottom: 5px; color: #f6c23e; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #d1d3e2; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #f5c6cb; font-size: 14px; }
        .btn { display: block; width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; text-align: center; text-decoration: none; font-size: 14px; box-sizing: border-box; }
        .btn-warning { background-color: #f6c23e; color: #333; margin-bottom: 10px; }
        .btn-light { background-color: #eaecf4; color: #3a3b45; }
    </style>
</head>
<body>
    <div class="card">
        <div class="text-center">
            <h3 class="fw-bold">Modificar Cuenta</h3>
            <p class="text-muted">Actualiza la información del usuario #{{ $cliente->id }}</p>
        </div>

        @if ($errors->any())
            <div class="alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nombres</label>
                <input type="text" name="nombres" class="form-control" value="{{ old('nombres', $cliente->nombres) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Apellidos</label>
                <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos', $cliente->apellidos) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="correo" class="form-control" value="{{ old('correo', $cliente->correo) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Cargo</label>
                <select name="cargo" class="form-control" required>
                    <option value="Desarrollador" {{ old('cargo', $cliente->cargo) == 'Desarrollador' ? 'selected' : '' }}>Desarrollador</option>
                    <option value="Diseñador" {{ old('cargo', $cliente->cargo) == 'Diseñador' ? 'selected' : '' }}>Diseñador</option>
                    <option value="Gerente de Proyecto" {{ old('cargo', $cliente->cargo) == 'Gerente de Proyecto' ? 'selected' : '' }}>Gerente de Proyecto</option>
                    <option value="Analista de QA" {{ old('cargo', $cliente->cargo) == 'Analista de QA' ? 'selected' : '' }}>Analista de QA</option>
                    <option value="Soporte Técnico" {{ old('cargo', $cliente->cargo) == 'Soporte Técnico' ? 'selected' : '' }}>Soporte Técnico</option>
                </select>
            </div>

            <!-- MODIFICACIÓN DINÁMICA DEL ROL DEL USUARIO -->
            <div class="mb-3">
                <label class="form-label" style="color: #f6c23e;">Rol asignado (Permisos)</label>
                <select name="role_id" class="form-control" required>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" {{ (old('role_id', $cliente->roles->first()?->id) == $rol->id) ? 'selected' : '' }}>
                            {{ $rol->nombre }} ({{ $rol->descripcion }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña (Dejar en blanco para no cambiar)</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div style="margin-top: 25px;">
                <button type="submit" class="btn btn-warning">Guardar Cambios</button>
                <a href="{{ route('clientes.index') }}" class="btn btn-light">Volver al listado</a>
            </div>
        </form>
    </div>
</body>
</html>
