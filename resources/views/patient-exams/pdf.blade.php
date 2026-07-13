<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Exame #{{ $exam->id }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #222; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin: 0; color: #0d6efd; }
        .header p { margin: 2px 0; font-size: 11px; color: #555; }
        .section-title { background: #f1f3f5; padding: 6px 10px; font-weight: bold; margin-top: 16px; margin-bottom: 8px; border-left: 4px solid #0d6efd; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table th, table td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        table th { background: #f8f9fa; }
        .info-grid td { border: none; padding: 4px 8px; vertical-align: top; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; color: #fff; }
        .badge-approved { background: #198754; }
        .badge-pending { background: #ffc107; color:#000; }
        .badge-pending_approval { background: #0dcaf0; color:#000; }
        .badge-rejected { background: #dc3545; }
        .footer { margin-top: 30px; font-size: 10px; color: #777; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Clínica Unicesumar</h1>
        <p>Laudo de Exame Laboratorial</p>
        <p>Emitido em {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section-title">Informações do Paciente</div>
    <table class="info-grid">
        <tr>
            <td><strong>Nome:</strong> {{ $exam->patient->user->name }}</td>
        </tr>
    </table>

    <div class="section-title">Informações do Exame</div>
    <table class="info-grid">
        <tr>
            <td><strong>Tipo:</strong> {{ $exam->examType->name }}</td>
            <td><strong>Data:</strong> {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>
                <strong>Status:</strong>
                <span class="badge badge-{{ $exam->status }}">
                    {{ match ($exam->status) {
                        'pending' => 'Pendente',
                        'pending_approval' => 'Pendente de Aprovação',
                        'approved' => 'Aprovado',
                        'rejected' => 'Rejeitado',
                        default => ucfirst($exam->status),
                    } }}
                </span>
            </td>
            <td><strong>Amostra:</strong> {{ $exam->sample->code ?? 'N/A' }} ({{ $exam->sample->sampleType->name ?? 'N/A' }})</td>
        </tr>
    </table>

    @if ($exam->results && is_array($exam->results))
        <div class="section-title">Resultados</div>
        @php $fieldsMap = $exam->examType->fields->keyBy('name'); @endphp
        <table>
            <thead>
                <tr><th>Parâmetro</th><th>Resultado</th></tr>
            </thead>
            <tbody>
                @foreach ($exam->results as $key => $value)
                    @php
                        $field = $fieldsMap->get($key);
                        $label = $field->label ?? ucfirst($key);
                        $unit = $field->unit ?? null;
                    @endphp
                    <tr>
                        <td>{{ $label }} @if($unit) ({{ $unit }}) @endif</td>
                        <td>{{ $value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p><em>Este exame ainda não possui resultados cadastrados.</em></p>
    @endif

    @if ($exam->observation)
        <div class="section-title">Observações</div>
        <p>{{ $exam->observation }}</p>
    @endif

    <div class="footer">
        Documento gerado eletronicamente pelo sistema Clínica Unicesumar.
    </div>
</body>
</html>
