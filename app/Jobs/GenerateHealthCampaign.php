<?php

namespace App\Jobs;

use App\Models\HealthCampaign;
use App\Service\HealthCampaignService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Executa as chamadas à IA (análise e campanha) fora da requisição HTTP.
 * Não faz retry automático: uma falha fica registrada na campanha e o
 * professor decide se reprocessa.
 */
class GenerateHealthCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const TIMEOUT_SECONDS = 300;

    public int $tries = 1;

    public int $timeout = self::TIMEOUT_SECONDS;

    public function __construct(public HealthCampaign $healthCampaign) {}

    public function handle(HealthCampaignService $healthCampaignService): void
    {
        $healthCampaignService->processCampaign($this->healthCampaign);
    }

    public function failed(?Throwable $exception): void
    {
        $this->healthCampaign->update([
            'status' => HealthCampaign::STATUS_FAILED,
            'error_message' => $exception?->getMessage() ?? 'Falha desconhecida ao gerar a campanha.',
        ]);
    }
}
