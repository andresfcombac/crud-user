<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuarios</title>
    <style>
        body { background-color: #f8f9fc; font-family: system-ui, -apple-system, sans-serif; padding: 40px 20px; color: #333; }
        .container { max-width: 1000px; margin: 0 auto; }
        .d-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .fw-bold { font-weight: bold; }
        .btn { padding: 10px 15px; border-radius: 8px; font-weight: bold; text-decoration: none; cursor: pointer; font-size: 14px; border: none; display: inline-block; }
        .btn-success { background-color: #1cc88a; color: white; }
        .btn-primary { background-color: #4e73df; color: white; }
        .btn-danger { background-color: #e74a3b; color: white; }
        .btn-outline-warning { background-color: transparent; border: 1px solid #f6c23e; color: #f6c23e; padding: 5px 10px; border-radius: 4px; }
        .btn-outline-danger { background-color: transparent; border: 1px solid #e74a3b; color: #e74a3b; padding: 5px 10px; border-radius: 4px; }
        .btn-outline-secondary { background-color: #858796; color: white; }
        .table-container { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .input-group { display: flex; gap: 10px; margin-bottom: 20px; }
        .form-control { flex-grow: 1; padding: 10px; border: 1px solid #d1d3e2; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; text-align: left; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #e3e6f0; }
        th { background-color: #f8f9fc; color: #4e73df; }
        .badge-rol { background-color: #36b9cc; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .alert-id { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-weight: 500; transition: opacity 1s ease; }
        .text-center { text-align: center; }
        .d-inline { display: inline; }
        
        /* Estilos CSS nativos para la paginación */
        .pagination-container { margin-top: 20px; display: flex; justify-content: center; gap: 5px; }
        .pagination-container a, .pagination-container span { padding: 8px 14px; border: 1px solid #d1d3e2; background: white; color: #4e73df; text-decoration: none; border-radius: 5px; }
        .pagination-container .active { background: #4e73df; color: white; border-color: #4e73df; }
    </style>
</head>
<body>

<!-- PEGAR TEMPORALMENTE PARA DIAGNÓSTICO 
 */ <div style="background: #222; color: #fff; padding: 15px; margin: 20px auto; max-width: 1000px; border-radius: 8px; font-family: monospace; font-size: 13px; line-height: 1.6;">
    <strong style="color: #1cc88a;">🩺 DIAGNÓSTICO DE PERMISOS:</strong><br>
    • Usuario conectado: <b>{{ $usuarioLogueado->nombres }} (ID: {{ $usuarioLogueado->id }})</b><br>
    • Roles asignados: <b>[{{ $usuarioLogueado->roles->pluck('nombre')->implode(', ') ?: 'NINGUNO' }}]</b><br>
    • ¿Tiene el permiso 'crear-usuarios'?: 
    @if($usuarioLogueado->tienePermiso('crear-usuarios'))
        <span style="background: #1cc88a; color: white; padding: 2px 6px; border-radius: 4px;">SÍ TIENE EL PERMISO</span>
    @else
        <span style="background: #e74a3b; color: white; padding: 2px 6px; border-radius: 4px;">NO TIENE EL PERMISO (Error de Base de Datos)</span>
    @endif
</div> -->

    <div class="container">
        <!-- Notificación temporizada (Desaparece en 30 segundos) -->
        @if(session('exito'))
            <div id="alerta-temporal" class="alert-id">{{ session('exito') }}</div>
        @endif

        <div class="d-flex">
            <h2 class="fw-bold">Módulo de Usuarios</h2>
            <div>
                <!-- FILTRO VISUAL: Solo el Administrador (o quien tenga permiso de crear) ve este botón -->
                @if($usuarioLogueado->tienePermiso('crear-usuarios'))
                    <a href="{{ route('clientes.create') }}" class="btn btn-success">Crear Usuario</a>
                @endif
                <a href="{{ route('logout') }}" class="btn btn-danger">Salir</a>
            </div>
        </div>

        <div class="table-container">
            <form action="{{ route('clientes.index') }}" method="GET" class="input-group">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, apellido o correo..." value="{{ $buscar ?? '' }}">
                <button class="btn btn-primary" type="submit">Buscar</button>
                @if(!empty($buscar))
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                @endif
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Cargo</th>
                        <th>Rol Asignado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                    <tr>
                        <td class="fw-bold">#{{ $cliente->id }}</td>
                        <td>{{ $cliente->nombres }} {{ $cliente->apellidos }}</td>
                        <td>{{ $cliente->correo }}</td>
                        <td>{{ $cliente->cargo }}</td>
                        
                        <!-- Muestra el nombre real del Rol desde la relación de la BD -->
                        <td>
                            <span class="badge-rol">
                                {{ $cliente->roles->first()?->nombre ?? 'Sin Rol Assigned' }}
                            </span>
                        </td>
                        
                        <td class="text-center">
                            <!-- FILTRO VISUAL: Opciones que se muestran según los permisos del rol activo -->
                            @if($usuarioLogueado->tienePermiso('editar-usuarios'))
                                <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-outline-warning">Editar</a>
                            @endif

                            @if($usuarioLogueado->tienePermiso('eliminar-usuarios'))
                                <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">Borrar</button>
                                </form>
                            @endif

                            @if(!$usuarioLogueado->tienePermiso('editar-usuarios') && !$usuarioLogueado->tienePermiso('eliminar-usuarios'))
                                <span style="color: #999; font-size: 13px;">Lectura</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Renderizado Manual de Paginación Rápida -->
            <div class="pagination-container">
                @if ($clientes->hasPages())
                    {{-- Enlace Anterior --}}
                    @if ($clientes->onFirstPage())
                        <span>«</span>
                    @else
                        <a href="{{ $clientes->previousPageUrl() }}" rel="prev">«</a>
                    @endif

                    {{-- Elementos de Página --}}
                    @foreach ($clientes->getUrlRange(1, $clientes->lastPage()) as $page => $url)
                        @if ($page == $clientes->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Enlace Siguiente --}}
                    @if ($clientes->hasMorePages())
                        <a href="{{ $clientes->nextPageUrl() }}" rel="next">»</a>
                    @else
                        <span>»</span>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <script>
        // Lógica nativa: Desvanecer y ocultar el mensaje a los 30 segundos
        const alerta = document.getElementById('alerta-temporal');
        if (alerta) {
            setTimeout(() => {
                alerta.style.opacity = '0';
                setTimeout(() => alerta.remove(), 1000); // Remueve del DOM tras la animación
            }, 30000); // 30000 milisegundos = 30 segundos
        }
    </script>
</body>
</html>
    