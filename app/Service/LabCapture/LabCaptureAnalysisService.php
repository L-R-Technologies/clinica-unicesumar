<?php

namespace App\Service\LabCapture;

use App\Models\ExamType;
use App\Models\ExamTypeField;
use App\Models\LabCapture;
use App\Service\Ai\TextGenerator;
use App\Service\ExamResultNormalizer;
use Illuminate\Database\Eloquent\Collection;

class LabCaptureAnalysisService
{
    private const ANALYSIS_SCHEMA = [
        'type' => 'OBJECT',
        'properties' => [
            'exam_type_id' => [
                'type' => 'INTEGER',
                'nullable' => true,
                'description' => 'ID de um tipo de exame da lista fornecida, ou null se nenhum corresponder.',
            ],
            'exam_type_confidence' => [
                'type' => 'NUMBER',
                'description' => 'Confiança na identificação do tipo, de 0 a 1.',
            ],
            'exam_type_reasoning' => [
                'type' => 'STRING',
                'description' => 'Uma frase explicando por que esse tipo foi escolhido (ou por que nenhum serviu).',
            ],
            'suggested_exam_type_name' => [
                'type' => 'STRING',
                'description' => 'Nome do exame como aparece na impressão (ex.: "TTPA"), útil quando exam_type_id é null.',
            ],
            'results' => [
                'type' => 'ARRAY',
                'description' => 'Um item por campo do tipo escolhido que tenha valor na impressão.',
                'items' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'field_name' => ['type' => 'STRING', 'description' => 'Nome técnico do campo, exatamente como na lista.'],
                        'value' => ['type' => 'STRING', 'description' => 'Valor sem unidade, com ponto decimal.'],
                        'source_label' => ['type' => 'STRING', 'description' => 'Rótulo impresso de onde o valor veio.'],
                        'source_value' => ['type' => 'STRING', 'description' => 'Valor impresso original, com unidade.'],
                    ],
                    'required' => ['field_name', 'value', 'source_label', 'source_value'],
                ],
            ],
            'unmapped_values' => [
                'type' => 'ARRAY',
                'description' => 'Valores de resultado impressos que não têm campo correspondente no tipo escolhido.',
                'items' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'label' => ['type' => 'STRING'],
                        'value' => ['type' => 'STRING'],
                    ],
                    'required' => ['label', 'value'],
                ],
            ],
            'metadata' => [
                'type' => 'OBJECT',
                'properties' => [
                    'exam_datetime' => ['type' => 'STRING', 'nullable' => true, 'description' => 'Data e hora impressas, no formato YYYY-MM-DD HH:MM:SS.'],
                    'machine_serial' => ['type' => 'STRING', 'nullable' => true],
                    'sequence_number' => ['type' => 'STRING', 'nullable' => true, 'description' => 'Número sequencial do resultado, se impresso.'],
                    'channel' => ['type' => 'STRING', 'nullable' => true],
                    'operator' => ['type' => 'STRING', 'nullable' => true],
                    'status_note' => ['type' => 'STRING', 'nullable' => true, 'description' => 'Avisos impressos, como "FALHOU!" ou erros da máquina.'],
                ],
                'required' => ['exam_datetime', 'machine_serial', 'sequence_number', 'channel', 'operator', 'status_note'],
            ],
        ],
        'required' => ['exam_type_id', 'exam_type_confidence', 'exam_type_reasoning', 'suggested_exam_type_name', 'results', 'unmapped_values', 'metadata'],
    ];

    private const PERSONAL_DATA_LABELS = ['NOME', 'PACIENTE', 'ID', 'CPF', 'RG', 'NAME', 'PATIENT'];

    private const MASK = '[omitido]';

    public function __construct(
        private readonly TextGenerator $textGenerator,
        private readonly ExamResultNormalizer $resultNormalizer,
    ) {}

    /**
     * @throws \App\Service\Ai\TextGenerationException
     */
    public function analyze(LabCapture $capture): void
    {
        $capture->update(['status' => LabCapture::STATUS_ANALYZING, 'error_message' => null]);

        $examTypes = $this->activeExamTypes();
        $prompt = $this->buildPrompt($capture, $examTypes);
        $response = $this->textGenerator->generateJson($prompt, self::ANALYSIS_SCHEMA);
        $analysis = $this->normalizeAnalysis($response, $examTypes);

        $capture->update([
            'analysis' => $analysis,
            'ai_model' => $this->textGenerator->modelName(),
            'status' => $analysis['exam_type_id'] === null
                ? LabCapture::STATUS_UNRECOGNIZED
                : LabCapture::STATUS_ANALYZED,
        ]);
    }

    public function maskPersonalData(string $rawText): string
    {
        $labels = implode('|', array_map('preg_quote', self::PERSONAL_DATA_LABELS));
        $pattern = '/^(\s*(?:'.$labels.')\s*[:(])(.*)$/miu';

        return preg_replace($pattern, '$1 '.self::MASK, $rawText) ?? $rawText;
    }

    /**
     * @return Collection<int, ExamType>
     */
    private function activeExamTypes(): Collection
    {
        return ExamType::with('fields')->where('is_active', true)->orderBy('name')->get();
    }

    /**
     * @param  Collection<int, ExamType>  $examTypes
     */
    private function buildPrompt(LabCapture $capture, Collection $examTypes): string
    {
        $capture->loadMissing('machine');
        $catalog = $examTypes->map(fn (ExamType $examType) => $this->describeExamType($examType))->implode("\n");
        $machine = $capture->machine
            ? "{$capture->machine->name} (modelo {$capture->machine->model}, série {$capture->machine->serial_number})"
            : 'não informada';
        $maskedText = $this->maskPersonalData($capture->raw_text);

        return <<<PROMPT
        Você é um analista de laboratório clínico. Abaixo está o texto impresso por um equipamento laboratorial, capturado pela porta serial, seguido da lista de TIPOS DE EXAME cadastrados no sistema com seus campos.

        Tarefa:
        1. Identifique qual tipo de exame da lista corresponde ao resultado impresso. Use SOMENTE os IDs da lista. Se nenhum corresponder, use null em exam_type_id e informe em suggested_exam_type_name o nome do exame como está impresso.
        2. Para o tipo escolhido, preencha "results" com um item por campo que tenha valor na impressão. Use o nome técnico exato do campo (field_name). Converta números para ponto decimal e sem unidade. Não invente valores: se o resultado impresso for um erro (ex.: "FALHOU!"), deixe "results" vazio e registre o aviso em metadata.status_note.
        3. Valores de resultado impressos que não tenham campo no tipo escolhido vão em "unmapped_values".
        4. Extraia em "metadata" a data/hora, número de série, sequencial, canal, operador e avisos, quando existirem.

        Equipamento de origem: {$machine}

        TEXTO IMPRESSO PELO EQUIPAMENTO:
        ---
        {$maskedText}
        ---

        TIPOS DE EXAME CADASTRADOS:
        {$catalog}
        PROMPT;
    }

    private function describeExamType(ExamType $examType): string
    {
        $fields = $examType->fields
            ->map(fn (ExamTypeField $field) => $this->describeField($field))
            ->implode('; ');
        $description = $examType->description ? " — {$examType->description}" : '';

        return "- ID {$examType->id}: {$examType->name}{$description}. Campos: ".($fields !== '' ? $fields : 'nenhum');
    }

    private function describeField(ExamTypeField $field): string
    {
        $unit = $field->unit ? ", unidade {$field->unit}" : '';

        return "{$field->name} (\"{$field->label}\", tipo {$field->field_type}{$unit})";
    }

    /**
     * @param  array<string, mixed>  $response
     * @param  Collection<int, ExamType>  $examTypes
     * @return array<string, mixed>
     */
    private function normalizeAnalysis(array $response, Collection $examTypes): array
    {
        $examTypeId = is_numeric($response['exam_type_id'] ?? null) ? (int) $response['exam_type_id'] : null;
        $examType = $examTypeId !== null ? $examTypes->firstWhere('id', $examTypeId) : null;
        $rawResults = is_array($response['results'] ?? null) ? $response['results'] : [];
        $resultsByField = $this->indexResultsByField($rawResults);

        return [
            'exam_type_id' => $examType?->id,
            'exam_type_confidence' => $this->clampConfidence($response['exam_type_confidence'] ?? null),
            'exam_type_reasoning' => $this->stringOrNull($response['exam_type_reasoning'] ?? null),
            'suggested_exam_type_name' => $this->stringOrNull($response['suggested_exam_type_name'] ?? null),
            'results' => $examType ? $this->resultNormalizer->normalize($examType, $resultsByField) : [],
            'results_details' => $examType ? $this->detailsForKnownFields($examType, $rawResults) : [],
            'unmapped_values' => $this->normalizeUnmapped($response['unmapped_values'] ?? null),
            'metadata' => $this->normalizeMetadata($response['metadata'] ?? null),
        ];
    }

    /**
     * @param  array<int, mixed>  $rawResults
     * @return array<string, mixed>
     */
    private function indexResultsByField(array $rawResults): array
    {
        $indexed = [];

        foreach ($rawResults as $item) {
            if (is_array($item) && is_string($item['field_name'] ?? null)) {
                $indexed[$item['field_name']] = $item['value'] ?? null;
            }
        }

        return $indexed;
    }

    /**
     * @param  array<int, mixed>  $rawResults
     * @return array<int, array{field_name: string, source_label: string|null, source_value: string|null}>
     */
    private function detailsForKnownFields(ExamType $examType, array $rawResults): array
    {
        $knownFields = $examType->fields->pluck('name')->all();
        $details = [];

        foreach ($rawResults as $item) {
            if (! is_array($item) || ! in_array($item['field_name'] ?? null, $knownFields, true)) {
                continue;
            }

            $details[] = [
                'field_name' => $item['field_name'],
                'source_label' => $this->stringOrNull($item['source_label'] ?? null),
                'source_value' => $this->stringOrNull($item['source_value'] ?? null),
            ];
        }

        return $details;
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    private function normalizeUnmapped(mixed $unmapped): array
    {
        if (! is_array($unmapped)) {
            return [];
        }

        $normalized = [];

        foreach ($unmapped as $item) {
            $label = $this->stringOrNull(is_array($item) ? ($item['label'] ?? null) : null);
            $value = $this->stringOrNull(is_array($item) ? ($item['value'] ?? null) : null);

            if ($label !== null || $value !== null) {
                $normalized[] = ['label' => $label ?? '', 'value' => $value ?? ''];
            }
        }

        return $normalized;
    }

    /**
     * @return array<string, string|null>
     */
    private function normalizeMetadata(mixed $metadata): array
    {
        $keys = ['exam_datetime', 'machine_serial', 'sequence_number', 'channel', 'operator', 'status_note'];
        $normalized = [];

        foreach ($keys as $key) {
            $normalized[$key] = $this->stringOrNull(is_array($metadata) ? ($metadata[$key] ?? null) : null);
        }

        return $normalized;
    }

    private function clampConfidence(mixed $value): float
    {
        if (! is_numeric($value)) {
            return 0.0;
        }

        return max(0.0, min(1.0, round((float) $value, 2)));
    }

    private function stringOrNull(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }
}
