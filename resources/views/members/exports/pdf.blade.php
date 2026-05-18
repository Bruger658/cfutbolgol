<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de socios</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111827; }
        h1 { margin-bottom: 6px; }
        .meta { margin-bottom: 14px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        th { background-color: #f3f4f6; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <h1>Listado de socios</h1>
    <p class="meta">Generado: {{ now()->format('d/m/Y H:i') }} | Registros: {{ $members->count() }}</p>

    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>N° Socio</th>
                <th>Nombre y apellido</th>
                <th>Categoría</th>
                <th>Documento</th>
                <th>Ciudad</th>
                <th>Estado</th>
                <th>Meses adeudados</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
                <tr>
                    <td>{{ $member->id }}</td>
                    <td>{{ $member->first_name }} {{ $member->last_name }}</td>
                    <td>{{ $member->category }}</td>
                    <td>{{ $member->document_number }}</td>
                    <td>{{ $member->city }}</td>
                    <td>{{ $member->is_up_to_date ? 'Al día' : 'Con deuda' }}</td>
                    <td>
                        {{ collect($member->missing_months)->map(fn ($month) => \Carbon\Carbon::create()->month($month)->translatedFormat('F'))->implode(', ') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Sin socias cargadas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>