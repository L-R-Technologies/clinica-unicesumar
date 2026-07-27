<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\PatientHistory;
use App\Models\Sample;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function welcome(): Response
    {
        return Inertia::render('welcome');
    }

    public function privacyPolicy(): Response
    {
        return Inertia::render('privacy-policy', [
            'updatedAt' => now()->format('d/m/Y'),
        ]);
    }

    public function home(Request $request): Response
    {
        return Inertia::render('home', [
            'stats' => $this->buildStats($request->user()),
        ]);
    }

    /**
     * Monta os cartões de resumo do dashboard conforme o papel do usuário.
     *
     * @return array<int, array{label: string, value: int, tone: string}>
     */
    private function buildStats(User $user): array
    {
        if ($user->hasRole('teacher')) {
            return [
                ['label' => 'Usuários Ativos', 'value' => User::where('active', true)->count(), 'tone' => 'primary'],
                ['label' => 'Exames Pendentes', 'value' => Exam::where('status', 'pending_approval')->count(), 'tone' => 'warning'],
                ['label' => 'Amostras Cadastradas', 'value' => Sample::count(), 'tone' => 'success'],
            ];
        }

        if ($user->hasRole('student')) {
            return [
                ['label' => 'Minhas Anamneses', 'value' => PatientHistory::where('user_id', $user->id)->count(), 'tone' => 'primary'],
                ['label' => 'Meus Exames', 'value' => Exam::where('user_id', $user->id)->count(), 'tone' => 'success'],
                ['label' => 'Minhas Amostras', 'value' => Sample::where('user_id', $user->id)->count(), 'tone' => 'warning'],
            ];
        }

        return [];
    }
}
