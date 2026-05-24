<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bitácora de Auditoría</title>
    <style>
        body { background-color: #f8f9fc; font-family: system-ui, sans-serif; padding: 40px 20px; }
        .card-bitacora { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 25px; max-width: 1000px; margin: 0 auto; }
        .d-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-primary { background-color: #4e73df; color: white; padding: 10px 15px; border-radius: 8px; font-weight: bold; text-decoration: none; font-size: 14px; display: inline-block; }
        .log-line { background-color: #f1f3f9; border-left: 4px solid #4e73df; padding: 12px; margin-bottom: 10px; border-radius: 4px; font-family: monospace; font-size: 13px; color: #333; }
        .no-data { text-align: center; color: #858796; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card-bitacora">
            <div class="d-flex">
                <h2 style="margin:0; font-weight:bold;">📋 Historial de Auditoría del Sistema</h2>
                <a href="{{ route('clientes.index') }}" class="btn btn-primary">Volver al Panel</a>
            </div>

            <div style="margin-top: 20px;">
                @if(count($registros) > 0)
                    @foreach($registros as $linea)
                        <div class="log-line">
                            {{ $linea }}
                        </div>
                    @endforeach
                @else
                    <div class="no-data">No se han registrado movimientos en la bitácora todavía.</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
