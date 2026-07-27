<?php

namespace App\Providers;

use App\Models\Exam;
use App\Models\PatientHistory;
use App\Models\Sample;
use App\Policies\ExamPolicy;
use App\Policies\PatientHistoryPolicy;
use App\Policies\SamplePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)
            ->mixedCase()
            ->numbers()
            ->uncompromised());

        Gate::policy(Exam::class, ExamPolicy::class);
        Gate::policy(Sample::class, SamplePolicy::class);
        Gate::policy(PatientHistory::class, PatientHistoryPolicy::class);
    }
}
