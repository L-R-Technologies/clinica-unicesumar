<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados do Exame Disponíveis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #0d6efd;
            border-radius: 3px;
        }
        .info-label {
            font-weight: bold;
            color: #0d6efd;
        }
        .button {
            display: inline-block;
            background-color: #0d6efd;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Resultados do Exame Disponíveis</h1>
    </div>
    <div class="content">
        <p>Olá, <strong>{{ $exam->patient->user->name }}</strong></p>

        <p>Os resultados do seu exame já estão disponíveis.</p>

        <div class="info-box">
            <p><span class="info-label">Exame:</span> {{ $exam->examType->name }}</p>
            <p><span class="info-label">Data:</span> {{ $exam->date->format('d/m/Y') }}</p>
        </div>

        <p style="text-align: center;">
            <a href="{{ route('exam.show', $exam->id) }}" class="button">Ver Resultados</a>
        </p>
    </div>
    <div class="footer">
        <p>Clínica Unicesumar</p>
    </div>
</body>
</html>
