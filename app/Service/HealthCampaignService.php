<?php

namespace App\Service;

use App\Jobs\GenerateHealthCampaign;
use App\Models\ExamType;
use App\Models\HealthCampaign;
use App\Models\User;
use App\Service\Ai\TextGenerator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Orquestra a geração de campanhas de saúde: monta o dataset anonimizado e
 * registra a campanha na hora; a análise clínica e a campanha em si são
 * pedidas à IA em job (GenerateHealthCampaign), fora da requisição.
 */
class HealthCampaignService
{
    private const CAMPAIGNS_PER_PAGE = 20;

    private const ANALYSIS_SCHEMA = [
        'type' => 'OBJECT',
        'properties' => [
            'summary' => ['type' => 'STRING', 'description' => 'Resumo geral do quadro de saúde do grupo, em 2 a 4 frases.'],
            'frequent_problems' => [
                'type' => 'ARRAY',
                'items' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'problem' => ['type' => 'STRING'],
                        'affected_patients' => ['type' => 'INTEGER', 'description' => 'Quantidade de pacientes com evidência do problema.'],
                        'evidence' => ['type' => 'STRING', 'description' => 'Quais exames/valores sustentam a conclusão.'],
                    ],
                    'required' => ['problem', 'affected_patients', 'evidence'],
                ],
            ],
            'main_problem' => [
                'type' => 'OBJECT',
                'properties' => [
                    'name' => ['type' => 'STRING'],
                    'description' => ['type' => 'STRING'],
                    'affected_share' => ['type' => 'STRING', 'description' => 'Proporção de pacientes afetados, ex.: "4 de 5 pacientes (80%)".'],
                ],
                'required' => ['name', 'description', 'affected_share'],
            ],
            'severity' => ['type' => 'STRING', 'description' => 'Gravidade do problema principal e por quê.'],
            'urgency' => ['type' => 'STRING', 'description' => 'Urgência de intervenção e por quê.'],
            'risk_factors' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
        ],
        'required' => ['summary', 'frequent_problems', 'main_problem', 'severity', 'urgency', 'risk_factors'],
    ];

    private const CAMPAIGN_SCHEMA = [
        'type' => 'OBJECT',
        'properties' => [
            'name' => ['type' => 'STRING', 'description' => 'Nome criativo da campanha.'],
            'objective' => ['type' => 'STRING'],
            'target_audience' => ['type' => 'STRING'],
            'actions' => [
                'type' => 'ARRAY',
                'description' => 'Pelo menos 5 ações práticas.',
                'items' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'title' => ['type' => 'STRING'],
                        'description' => ['type' => 'STRING'],
                    ],
                    'required' => ['title', 'description'],
                ],
            ],
            'materials' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
            'partners' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING'], 'description' => 'UBS, escolas, igrejas, empresas locais etc.'],
            'timeline' => [
                'type' => 'OBJECT',
                'properties' => [
                    'short_term' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                    'medium_term' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                    'long_term' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                ],
                'required' => ['short_term', 'medium_term', 'long_term'],
            ],
            'success_indicators' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
            'key_messages' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
        ],
        'required' => ['name', 'objective', 'target_audience', 'actions', 'materials', 'partners', 'timeline', 'success_indicators', 'key_messages'],
    ];

    public function __construct(
        private readonly AnonymizedExamDatasetService $datasetService,
        private readonly TextGenerator $textGenerator,
    ) {}

    public function getPaginatedCampaigns(): LengthAwarePaginator
    {
        return HealthCampaign::with('user')
            ->latest()
            ->paginate(self::CAMPAIGNS_PER_PAGE)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{date_from: string|null, date_to: string|null, exam_type_ids: array<int, int>|null}
     *
     * @throws ValidationException
     */
    public function validateFilters(array $data): array
    {
        $validated = Validator::make($data, [
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'exam_type_ids' => ['nullable', 'array'],
            'exam_type_ids.*' => ['integer', 'exists:exam_types,id'],
        ])->validate();

        return [
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'exam_type_ids' => empty($validated['exam_type_ids']) ? null : array_map('intval', $validated['exam_type_ids']),
        ];
    }

    /**
     * Monta o dataset anonimizado, salva a campanha como pendente e enfileira
     * a geração. Falha de imediato se o recorte não tiver exames.
     *
     * @param  array{date_from: string|null, date_to: string|null, exam_type_ids: array<int, int>|null}  $filters
     *
     * @throws ValidationException quando não há exames aprovados no recorte.
     */
    public function createPendingCampaign(array $filters, User $author): HealthCampaign
    {
        $dataset = $this->datasetService->build($filters);

        if ($dataset['patients_count'] === 0) {
            throw ValidationException::withMessages([
                'date_from' => 'Nenhum exame aprovado com resultados foi encontrado para os filtros informados.',
            ]);
        }

        $campaign = HealthCampaign::create([
            'user_id' => $author->id,
            'date_from' => $filters['date_from'],
            'date_to' => $filters['date_to'],
            'exam_type_ids' => $filters['exam_type_ids'],
            'patients_count' => $dataset['patients_count'],
            'exams_count' => $dataset['exams_count'],
            'dataset' => $dataset['text'],
            'status' => HealthCampaign::STATUS_PENDING,
        ]);

        GenerateHealthCampaign::dispatch($campaign);

        return $campaign;
    }

    /**
     * Reenfileira uma campanha que falhou, limpando o erro anterior.
     */
    public function retry(HealthCampaign $campaign): void
    {
        $campaign->update([
            'status' => HealthCampaign::STATUS_PENDING,
            'error_message' => null,
        ]);

        GenerateHealthCampaign::dispatch($campaign);
    }

    /**
     * Executa as duas chamadas à IA e persiste o resultado. Chamado pelo job.
     * Se a IA falhar, o job registra a falha na campanha (GenerateHealthCampaign::failed).
     *
     * @throws \App\Service\Ai\TextGenerationException
     */
    public function processCampaign(HealthCampaign $campaign): void
    {
        $campaign->update(['status' => HealthCampaign::STATUS_PROCESSING, 'error_message' => null]);

        $dataset = [
            'text' => $campaign->dataset,
            'patients_count' => $campaign->patients_count,
            'exams_count' => $campaign->exams_count,
            'truncated' => $campaign->patients_count >= AnonymizedExamDatasetService::MAX_PATIENTS,
        ];

        $analysis = $this->textGenerator->generateJson($this->analysisPrompt($dataset), self::ANALYSIS_SCHEMA);
        $campaignPlan = $this->textGenerator->generateJson($this->campaignPrompt($analysis), self::CAMPAIGN_SCHEMA);

        $campaign->update([
            'analysis' => $analysis,
            'campaign' => $campaignPlan,
            'model' => $this->textGenerator->modelName(),
            'status' => HealthCampaign::STATUS_COMPLETED,
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ExamType>
     */
    public function getSelectableExamTypes()
    {
        return ExamType::where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    /**
     * @param  array{text: string, patients_count: int, exams_count: int, truncated: bool}  $dataset
     */
    private function analysisPrompt(array $dataset): string
    {
        $truncationNote = $dataset['truncated']
            ? 'Observação: a base foi limitada aos primeiros '.AnonymizedExamDatasetService::MAX_PATIENTS." pacientes.\n"
            : '';

        return <<<PROMPT
        Você é um médico especialista em medicina interna e saúde coletiva.

        Os dados abaixo são resultados de exames laboratoriais de {$dataset['patients_count']} pacientes de uma comunidade, já ANONIMIZADOS (apenas sexo e faixa etária). Cada resultado traz o valor, a unidade, o intervalo de referência para pessoa saudável e a situação (Normal, Abaixo ou Acima) quando disponível.
        {$truncationNote}
        Analise os resultados e identifique:
        1. Os problemas de saúde mais frequentes entre os pacientes
        2. O MAIOR PROBLEMA DE SAÚDE GERAL que afeta a maioria dos pacientes
        3. A gravidade e a urgência do problema identificado
        4. Possíveis causas e fatores de risco associados

        Baseie-se somente nos dados fornecidos; não invente exames ou valores. Seja objetivo e use linguagem técnica, mas acessível. Responda em português do Brasil.

        RESULTADOS DOS EXAMES:
        {$dataset['text']}
        PROMPT;
    }

    /**
     * @param  array<string, mixed>  $analysis
     */
    private function campaignPrompt(array $analysis): string
    {
        $analysisJson = json_encode($analysis, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return <<<PROMPT
        Você é um especialista em saúde pública e comunicação em saúde com experiência em campanhas comunitárias.

        Com base na análise médica abaixo, feita sobre exames anonimizados de uma comunidade, crie uma CAMPANHA DE SAÚDE PÚBLICA completa e detalhada, com: nome criativo, objetivo principal, público-alvo, pelo menos 5 ações práticas, materiais e recursos necessários, parceiros sugeridos (UBS, escolas, igrejas, empresas locais etc.), cronograma em curto, médio e longo prazo, indicadores de sucesso e mensagens-chave para a comunidade.

        Seja criativo, prático e considere recursos limitados de uma comunidade típica brasileira. Responda em português do Brasil.

        ANÁLISE MÉDICA:
        {$analysisJson}
        PROMPT;
    }
}
