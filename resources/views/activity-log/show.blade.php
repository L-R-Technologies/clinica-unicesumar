@extends('layouts.base')

@section('content')
    <div class="container">
        <div class="row mb-4 align-items-center">
            <div class="col-auto d-flex align-items-center">
                <a href="{{ route('activity-logs.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
            <div class="col text-center">
                <h2 class="mb-0">Detalhes da Atividade</h2>
            </div>
            <div class="col-auto"></div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <!-- Informações Básicas -->
                        <h5 class="mb-4"><i class="fas fa-info-circle"></i> Informações Básicas</h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <strong>Data/Hora:</strong><br>
                                <span>{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                                <small class="text-muted d-block">
                                    ({{ $log->created_at->diffForHumans() }})
                                </small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <strong>Tipo de Registro:</strong><br>
                                <span class="badge bg-secondary fs-6">
                                    {{ \App\Helpers\ActivityLogTranslator::translateLogName($log->log_name) }}
                                </span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <strong>Evento:</strong><br>
                                <span
                                    class="badge fs-6
                                    {{ match ($log->event) {
                                        'created' => 'bg-success',
                                        'updated' => 'bg-info',
                                        'deleted' => 'bg-danger',
                                        default => 'bg-secondary',
                                    } }}">
                                    {{ \App\Helpers\ActivityLogTranslator::translateEvent($log->event) }}
                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Usuário Responsável:</strong><br>
                                @if ($log->causer)
                                    <span>{{ $log->causer->name }}</span><br>
                                    <small class="text-muted">{{ $log->causer->email }}</small>
                                @else
                                    <span class="text-muted">Sistema</span>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Objeto:</strong><br>
                                <span class="badge bg-info">{{ \App\Helpers\ActivityLogTranslator::translateModelName($log->subject_type) }}</span>
                                <span class="text-muted">#{{ $log->subject_id }}</span>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Detalhes das Alterações -->
                        @if ($log->event === 'updated' && $log->properties && isset($log->properties['old']) && isset($log->properties['attributes']))
                            <h5 class="mb-4"><i class="fas fa-exchange-alt"></i> Alterações Realizadas</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="25%">Campo</th>
                                            <th width="37.5%">Valor Anterior</th>
                                            <th width="37.5%">Valor Novo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($log->properties['attributes'] as $key => $newValue)
                                            @if (isset($log->properties['old'][$key]) && $log->properties['old'][$key] != $newValue)
                                                <tr>
                                                    <td><strong>{{ \App\Helpers\ActivityLogTranslator::translateFieldName($key) }}</strong></td>
                                                    <td>
                                                        <span class="text-danger">
                                                            {{ \App\Helpers\ActivityLogTranslator::translateFieldValue($key, $log->properties['old'][$key]) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="text-success">
                                                            {{ \App\Helpers\ActivityLogTranslator::translateFieldValue($key, $newValue) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif ($log->event === 'created' && $log->properties && isset($log->properties['attributes']))
                            <h5 class="mb-4"><i class="fas fa-plus-circle"></i> Dados Criados</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="30%">Campo</th>
                                            <th width="70%">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($log->properties['attributes'] as $key => $value)
                                            @if (!in_array($key, ['password', 'remember_token']))
                                                <tr>
                                                    <td><strong>{{ \App\Helpers\ActivityLogTranslator::translateFieldName($key) }}</strong></td>
                                                    <td>
                                                        {{ \App\Helpers\ActivityLogTranslator::translateFieldValue($key, $value) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif ($log->event === 'deleted' && $log->properties && isset($log->properties['old']))
                            <h5 class="mb-4"><i class="fas fa-trash-alt"></i> Dados Excluídos</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="30%">Campo</th>
                                            <th width="70%">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($log->properties['old'] as $key => $value)
                                            @if (!in_array($key, ['password', 'remember_token']))
                                                <tr>
                                                    <td><strong>{{ \App\Helpers\ActivityLogTranslator::translateFieldName($key) }}</strong></td>
                                                    <td>
                                                        {{ \App\Helpers\ActivityLogTranslator::translateFieldValue($key, $value) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Nenhum detalhe adicional disponível para este log.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
