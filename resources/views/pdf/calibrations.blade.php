<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Calibração</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .approved { color: green; }
        .rejected { color: red; }
        .machine-header { margin-top: 12px; padding: 12px; background-color: #f2f2f2; border-radius: 4px; }
        .machine-header p { margin: 2px 0; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Histórico de Calibrações</h2>

    @if ($machine)
        <div class="machine-header">
            <p><strong>Equipamento:</strong> {{ $machine->name }}</p>
            <p><strong>Modelo:</strong> {{ $machine->model ?? '—' }}</p>
            <p><strong>Nº de Série:</strong> {{ $machine->serial_number ?? '—' }}</p>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                @unless ($machine)
                    <th>Equipamento</th>
                @endunless
                <th>Data</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Responsável</th>
            </tr>
        </thead>
        <tbody>
            @foreach($calibrations as $cal)
                <tr>
                    @unless ($machine)
                        <td>{{ $cal->machine->name }}</td>
                    @endunless
                    <td>{{ $cal->calibration_date->format('d/m/Y H:i') }}</td>
                    <td>{{ number_format($cal->value, 2, ',', '.') }}</td>
                    <td class="{{ $cal->status === 'approved' ? 'approved' : 'rejected' }}">
                        {{ $cal->status === 'approved' ? 'Aprovada' : 'Rejeitada' }}
                    </td>
                    <td>{{ $cal->user->name ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
