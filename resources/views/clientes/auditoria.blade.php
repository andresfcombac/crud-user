<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bitácora de Auditoría</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; font-family: system-ui, sans-serif; }
        .card-bitacora { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 25px; margin-top: 30px; }
        .badge-crear { background-color: #1cc88a; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-editar { background-color: #f6c23e; color: #333; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-eliminar { background-color: #e74a3b; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card-bitacora">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark m-0">📋 Historial de Auditoría de Usuarios</h2>
                <a href="{{ route('clientes.index') }}" class="btn btn-primary">Volver al Panel</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Administrador</th>
                            <th>Acción</th>
                            <th>Detalles del Evento</th>
                            <th>Dirección IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $log)
                        <tr>
                            <td class="text-secondary small fw-semibold">{{ $log->created_at }}</td>
                            <td><span class="fw-bold text-primary">👤 {{ $log->nombres }} {{ $log->apellidos }}</span></td>
                            <td>
                                @if($log->accion == 'Crear') <span class="badge-crear">CREAR</span>
                                @elseif($log->accion == 'Editar') <span class="badge-editar">EDITAR</span>
                                @else <span class="badge-eliminar">ELIMINAR</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $log->detalles }}</td>
                            <td><code class="bg-light p-1 rounded small">{{ $log->ip_address }}</code></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No se han registrado movimientos en la bitácora todavía.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $registros->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</body>
</html>
