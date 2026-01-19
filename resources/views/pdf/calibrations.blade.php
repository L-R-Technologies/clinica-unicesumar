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
    </style>
</head>
<body>
    <h2 style="text-align: center;">Histórico de Calibrações</h2>
    <table>
        <thead>
            <tr>
                <th>Equipamento</th>
                <th>Data</th>
                <th>Valor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($calibrations as $cal)
                <tr>
                    <td>{{ $cal->machine->name }}</td>
                    <td>{{ $cal->calibration_date->format('d/m/Y') }}</td>
                    <td>{{ number_format($cal->value, 2, ',', '.') }}</td>
                    <td class="{{ $cal->status === 'approved' ? 'approved' : 'rejected' }}">
                        {{ $cal->status === 'approved' ? 'Aprovada' : 'Rejeitada' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>