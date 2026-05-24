<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil Personal</title>
    <style>
        body { background-color: #f8f9fc; font-family: system-ui, -apple-system, sans-serif; padding: 40px 20px; color: #333; }
        .container { max-width: 800px; margin: 0 auto; }
        .card-perfil { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .d-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .fw-bold { font-weight: bold; }
        .btn { padding: 12px 20px; border-radius: 8px; font-weight: bold; text-decoration: none; cursor: pointer; font-size: 14px; border: none; display: inline-block; }
        .btn-primary { background-color: #4e73df; color: white; }
        .btn-success { background-color: #1cc88a; color: white; }
        .btn-secondary { background-color: #858796; color: white; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; color: #4e73df; font-size: 14px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #d1d3e2; border-radius: 8px; box-sizing: border-box; font-size: 14px; background-color: #f8f9fc; }
        .form-control:focus { background-color: #fff; border-color: #4e73df; outline: none; }
        .form-control:disabled { background-color: #eaecf4; color: #6e707e; cursor: not-allowed; }
        .alert-id { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-weight: 500; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; font-weight: 500; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        hr { border: 0; height: 1px; background: #e3e6f0; margin: 30px 0; }
        .badge-rol { background-color: #4e73df; color: white; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="d-flex">
            <h2 class="fw-bold" style="margin:0;">⚙️ Configuración de Mi Cuenta</h2>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Volver al Panel</a>
        </div>

        <!-- Alertas de Éxito o Errores de Validación -->
        @if(session('exito'))
            <div class="alert-id">{{ session('exito') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-danger">{{ $errors->first() }}</div>
        @endif

        <!-- TARJETA 1: DATOS BÁSICOS DEL PERFIL -->
        <div class="card-perfil">
            <h3 style="margin-top:0; color:#333; margin-bottom: 25px;">👤 Información Personal</h3>
            
            <form action="{{ route('perfil.update') }}" method="POST">
                @csrf
                
                <div class="grid-2">
                    <div class="form-group">
                        <label>Nombres</label>
                        <input type="text" name="nombres" class="form-control" value="{{ $usuarioLogueado->nombres }}" required>
                    </div>
                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" value="{{ $usuarioLogueado->apellidos }}" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Correo Electrónico (Inmodificable)</label>
                        <input type="email" class="form-control" value="{{ $usuarioLogueado->correo }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Cargo de Trabajo</label>
                        <select name="cargo" class="form-control" required>
                            <option value="Desarrollador" {{ $usuarioLogueado->cargo == 'Desarrollador' ? 'selected' : '' }}>Desarrollador</option>
                            <option value="Diseñador" {{ $usuarioLogueado->cargo == 'Diseñador' ? 'selected' : '' }}>Diseñador</option>
                            <option value="Gerente de Proyecto" {{ $usuarioLogueado->cargo == 'Gerente de Proyecto' ? 'selected' : '' }}>Gerente de Proyecto</option>
                            <option value="Analista de QA" {{ $usuarioLogueado->cargo == 'Analista de QA' ? 'selected' : '' }}>Analista de QA</option>
                            <option value="Soporte Técnico" {{ $usuarioLogueado->cargo == 'Soporte Técnico' ? 'selected' : '' }}>Soporte Técnico</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label>Rol Asignado en el Sistema</label>
                    <div style="margin-top: 5px;">
                        <span class="badge-rol">{{ $usuarioLogueado->roles->first()?->nombre ?? 'Usuario Base' }}</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Guardar Cambios Básicos</button>
            </form>
        </div>

        <!-- TARJETA 2: SEGURIDAD Y CAMBIO DE CONTRASEÑA -->
        <div class="card-perfil">
            <h3 style="margin-top:0; color:#333; margin-bottom: 5px;">🔒 Seguridad de la Cuenta</h3>
            <p style="color:#666; font-size:14px; margin-bottom:25px;">Para cambiar tu contraseña actual de acceso, completa los tres campos a continuación.</p>

            <form action="{{ route('perfil.password') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Contraseña Actual</label>
                    <input type="password" name="password_actual" class="form-control" placeholder="Escribe tu clave de inicio de sesión actual" required>
                </div>

                <hr>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Nueva Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <div class="form-group">
                        <label>Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repite la nueva clave exactamente" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Actualizar Contraseña de Seguridad</button>
            </form>
        </div>

    </div>

</body>
</html>
