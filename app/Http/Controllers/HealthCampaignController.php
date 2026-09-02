<?php

namespace App\Http\Controllers;

use App\Models\ExamType;
use App\Models\HealthCampaign;
use App\Service\AnonymizedExamDatasetService;
use App\Service\HealthCampaignService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class HealthCampaignController extends Controller
{
    public function __construct(protected HealthCampaignService $healthCampaignService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
    }

    public function index(): Response
    {
        return Inertia::render('health-campaigns/index', [
            'campaigns' => $this->healthCampaignService->getPaginatedCampaigns(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('health-campaigns/create', [
            'examTypes' => $this->healthCampaignService->getSelectableExamTypes(),
            'maxPatients' => AnonymizedExamDatasetService::MAX_PATIENTS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $filters = $this->healthCampaignService->validateFilters($request->all());
            $campaign = $this->healthCampaignService->createPendingCampaign($filters, Auth::user());

            return redirect()
                ->route('health-campaigns.show', $campaign->id)
                ->with('success', 'Campanha enviada para geração. A análise aparecerá aqui em instantes.');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error('Erro ao gerar campanha de saúde', ['exception' => $e]);

            return back()
                ->with('error', 'Não foi possível gerar a campanha. Tente novamente.')
                ->withInput();
        }
    }

    public function show(HealthCampaign $healthCampaign): Response
    {
        return Inertia::render('health-campaigns/show', [
            'campaign' => $healthCampaign->load('user'),
            'examTypeNames' => $this->examTypeNames($healthCampaign),
        ]);
    }

    public function retry(HealthCampaign $healthCampaign): RedirectResponse
    {
        if ($healthCampaign->status !== HealthCampaign::STATUS_FAILED) {
            return back()->with('error', 'Só campanhas com falha podem ser reprocessadas.');
        }

        try {
            $this->healthCampaignService->retry($healthCampaign);

            return back()->with('success', 'Campanha reenviada para geração.');
        } catch (Exception $e) {
            Log::error('Erro ao reprocessar campanha de saúde', ['health_campaign_id' => $healthCampaign->id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível reprocessar a campanha. Tente novamente.');
        }
    }

    public function destroy(HealthCampaign $healthCampaign): RedirectResponse
    {
        try {
            $healthCampaign->delete();

            return redirect()
                ->route('health-campaigns.index')
                ->with('success', 'Campanha removida com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao remover campanha de saúde', ['health_campaign_id' => $healthCampaign->id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível remover a campanha. Tente novamente.');
        }
    }

    /**
     * @return array<int, string>
     */
    private function examTypeNames(HealthCampaign $healthCampaign): array
    {
        if (empty($healthCampaign->exam_type_ids)) {
            return [];
        }

        return ExamType::whereIn('id', $healthCampaign->exam_type_ids)
            ->orderBy('name')
            ->pluck('name')
            ->all();
    }
}
