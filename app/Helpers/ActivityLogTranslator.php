<?php

namespace App\Helpers;

class ActivityLogTranslator
{
    /**
     * Traduções dos nomes de log (log_name)
     */
    public static function translateLogName(string $logName): string
    {
        return match ($logName) {
            'exam' => 'Exame',
            'student' => 'Estudante',
            'teacher' => 'Professor',
            'sample' => 'Amostra',
            'patient_history' => 'Anamnese',
            'sample_type' => 'Tipo de Amostra',
            'exam_type' => 'Tipo de Exame',
            'exam_type_field' => 'Campo de Exame',
            'exam_rejection' => 'Rejeição de Exame',
            'machine' => 'Máquina',
            'user' => 'Usuário',
            'patient' => 'Paciente',
            default => ucfirst(str_replace('_', ' ', $logName)),
        };
    }

    /**
     * Traduções dos eventos
     */
    public static function translateEvent(string $event): string
    {
        return match ($event) {
            'created' => 'Criado',
            'updated' => 'Atualizado',
            'deleted' => 'Excluído',
            default => ucfirst($event),
        };
    }

    /**
     * Traduções dos nomes de campos
     */
    public static function translateFieldName(string $fieldName): string
    {
        return match ($fieldName) {
            // Campos comuns
            'id' => 'ID',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'deleted_at' => 'Excluído em',

            // Usuários
            'name' => 'Nome',
            'email' => 'E-mail',
            'password' => 'Senha',
            'role' => 'Perfil',
            'active' => 'Ativo',
            'email_verified_at' => 'E-mail verificado em',

            // Pacientes
            'user_id' => 'Usuário',
            'address_id' => 'Endereço',
            'birthday' => 'Data de Nascimento',
            'ethnicity' => 'Etnia',
            'sex' => 'Sexo',
            'cpf' => 'CPF',
            'rg' => 'RG',
            'phone' => 'Telefone',
            'lgpd_consent_at' => 'Consentimento LGPD',

            // Estudantes/Professores
            'supervisor_id' => 'Supervisor',
            'ra' => 'RA',
            'course' => 'Curso',
            'semester' => 'Semestre',
            'registration_number' => 'Número de Registro',
            'professional_license' => 'Licença Profissional',

            // Exames
            'patient_history_id' => 'Anamnese',
            'patient_id' => 'Paciente',
            'exam_type_id' => 'Tipo de Exame',
            'sample_id' => 'Amostra',
            'date' => 'Data',
            'results' => 'Resultados',
            'status' => 'Status',
            'observation' => 'Observação',

            // Amostras
            'sample_type_id' => 'Tipo de Amostra',
            'code' => 'Código',
            'location' => 'Localização',
            'notified' => 'Notificado',

            // Anamnese
            'fasting' => 'Em Jejum',
            'fasting_hours' => 'Horas de Jejum',
            'alcohol_last_24h' => 'Álcool nas últimas 24h',
            'on_medication' => 'Usa Medicamentos',
            'medications' => 'Medicamentos',
            'on_supplements' => 'Usa Suplementos',
            'supplements' => 'Suplementos',
            'chronic_disease' => 'Doença Crônica',
            'chronic_disease_details' => 'Detalhes da Doença Crônica',
            'infectious_disease_history' => 'Histórico de Doença Infecciosa',
            'infectious_disease_details' => 'Detalhes da Doença Infecciosa',
            'recent_surgery' => 'Cirurgia Recente',
            'surgery_details' => 'Detalhes da Cirurgia',
            'allergies' => 'Alergias',
            'allergy_details' => 'Detalhes das Alergias',
            'pregnant_or_lactating' => 'Grávida ou Lactante',
            'smokes' => 'Fuma',
            'cigarettes_per_day' => 'Cigarros por Dia',
            'physically_active' => 'Fisicamente Ativo',
            'menstrual_period' => 'Período Menstrual',
            'recent_fever_or_flu' => 'Febre ou Gripe Recente',
            'recorded_at' => 'Registrado em',

            // Tipos de Exame
            'description' => 'Descrição',
            'is_active' => 'Ativo',

            // Campos de Tipo de Exame
            'label' => 'Rótulo',
            'field_type' => 'Tipo de Campo',
            'unit' => 'Unidade',

            // Rejeição de Exame
            'exam_id' => 'Exame',
            'justification' => 'Justificativa',

            // Máquinas
            'model' => 'Modelo',
            'serial_number' => 'Número de Série',
            'calibration_range_min' => 'Faixa de Calibração Mínima',
            'calibration_range_max' => 'Faixa de Calibração Máxima',

            default => ucfirst(str_replace('_', ' ', $fieldName)),
        };
    }

    /**
     * Traduções dos valores de campos (enums, booleanos, etc)
     */
    public static function translateFieldValue(string $fieldName, $value): string
    {
        if (is_null($value)) {
            return '(vazio)';
        }

        if (is_bool($value)) {
            return $value ? 'Sim' : 'Não';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        // Formatar timestamps
        if (self::isTimestampField($fieldName)) {
            return self::formatTimestamp($value);
        }

        // Traduções específicas por campo
        return match ($fieldName) {
            'role' => match ($value) {
                'teacher' => 'Professor',
                'student' => 'Estudante',
                'patient' => 'Paciente',
                'admin' => 'Administrador',
                default => ucfirst($value),
            },
            'status' => self::translateStatus($value),
            'sex' => match ($value) {
                'male' => 'Masculino',
                'M' => 'Masculino',
                'female' => 'Feminino',
                'F' => 'Feminino',
                'other' => 'Outro',
                'prefer_not_say' => 'Prefiro não dizer',
                default => ucfirst($value),
            },
            'ethnicity' => match ($value) {
                'white' => 'Branco',
                'black' => 'Negro',
                'brown' => 'Pardo',
                'yellow' => 'Amarelo',
                'indigenous' => 'Indígena',
                default => ucfirst($value),
            },
            'field_type' => match ($value) {
                'text' => 'Texto',
                'number' => 'Número',
                'int' => 'Número Inteiro',
                'float' => 'Número Decimal',
                'string' => 'Texto',
                'boolean' => 'Sim/Não',
                'date' => 'Data',
                'select' => 'Seleção',
                'textarea' => 'Área de Texto',
                'checkbox' => 'Caixa de Seleção',
                'radio' => 'Opção Única',
                default => ucfirst($value),
            },
            'active', 'is_active', 'notified' => match ((string) $value) {
                '1', 'true' => 'Sim',
                '0', 'false', '' => 'Não',
                default => $value,
            },
            'menstrual_period' => match ($value) {
                'yes' => 'Sim',
                'no' => 'Não',
                'n/a' => 'Não se aplica',
                'regular' => 'Regular',
                'irregular' => 'Irregular',
                'none' => 'Nenhum',
                default => ucfirst($value),
            },
            default => $value,
        };
    }

    /**
     * Traduz status de diferentes contextos
     */
    private static function translateStatus(string $status): string
    {
        return match ($status) {
            // Status gerais
            'pending' => 'Pendente',
            'pending_approval' => 'Pendente de Aprovação',
            'approved' => 'Aprovado',
            'rejected' => 'Rejeitado',
            'completed' => 'Concluído',
            'in_progress' => 'Em Progresso',
            'canceled' => 'Cancelado',
            'cancelled' => 'Cancelado',

            // Status de amostras
            'under review' => 'Em Análise',
            'stored' => 'Armazenado',
            'discarded' => 'Descartado',

            // Status de máquinas
            'active' => 'Ativo',
            'calibrating' => 'Calibrando',
            'inactive' => 'Inativo',

            default => ucfirst($status),
        };
    }

    /**
     * Verifica se o campo é um timestamp
     */
    private static function isTimestampField(string $fieldName): bool
    {
        $timestampFields = [
            'created_at',
            'updated_at',
            'deleted_at',
            'email_verified_at',
            'lgpd_consent_at',
            'recorded_at',
            'date',
            'birthday',
        ];

        return in_array($fieldName, $timestampFields);
    }

    /**
     * Formata timestamp para formato brasileiro
     */
    private static function formatTimestamp(string $value): string
    {
        try {
            // Tenta parsear a data
            $date = new \DateTime($value);

            // Se tiver hora (não é meia-noite), mostra data e hora
            if ($date->format('H:i:s') !== '00:00:00') {
                return $date->format('d/m/Y H:i:s');
            }

            // Se for meia-noite, mostra apenas a data
            return $date->format('d/m/Y');
        } catch (\Exception $e) {
            // Se não conseguir parsear, retorna o valor original
            return $value;
        }
    }

    /**
     * Traduz o nome da model (classe)
     */
    public static function translateModelName(string $className): string
    {
        $baseName = class_basename($className);

        return match ($baseName) {
            'Exam' => 'Exame',
            'Student' => 'Estudante',
            'Teacher' => 'Professor',
            'Sample' => 'Amostra',
            'PatientHistory' => 'Anamnese',
            'SampleType' => 'Tipo de Amostra',
            'ExamType' => 'Tipo de Exame',
            'ExamTypeField' => 'Campo de Exame',
            'ExamRejection' => 'Rejeição de Exame',
            'Machine' => 'Máquina',
            'User' => 'Usuário',
            'Patient' => 'Paciente',
            'Address' => 'Endereço',
            'Calibration' => 'Calibração',
            'ExamFeedback' => 'Feedback de Exame',
            default => $baseName,
        };
    }
}
