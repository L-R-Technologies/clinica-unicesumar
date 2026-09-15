<?php

namespace App\Http\Controllers;

use App\Service\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

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
        $user = $request->user();

        return Inertia::render('home', [
            'teacherDashboard' => $user->hasRole('teacher') ? $this->dashboardService->buildTeacherDashboard() : null,
            'studentDashboard' => $user->hasRole('student') ? $this->dashboardService->buildStudentDashboard($user) : null,
        ]);
    }
}
