<?php

namespace App\Jobs;

use App\Models\LabCapture;
use App\Service\LabCapture\LabCaptureAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class AnalyzeLabCapture implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const TIMEOUT_SECONDS = 180;

    public int $tries = 1;

    public int $timeout = self::TIMEOUT_SECONDS;

    public function __construct(public LabCapture $labCapture) {}

    public function handle(LabCaptureAnalysisService $analysisService): void
    {
        $analysisService->analyze($this->labCapture);
    }

    public function failed(?Throwable $exception): void
    {
        $this->labCapture->update([
            'status' => LabCapture::STATUS_FAILED,
            'error_message' => $exception?->getMessage() ?? 'Falha desconhecida ao analisar a captura.',
        ]);
    }
}
