<?php

namespace App\Console\Commands;

use App\Mail\SampleInFreezerAlert;
use App\Models\Sample;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckFreezerSamples extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samples:check-freezer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica amostras armazenadas no freezer há mais de 7 dias e envia alertas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limitDate = Carbon::now()->subDays(7);

        $samples = Sample::with(['patient.user', 'user', 'sampleType'])
            ->where('status', 'stored')
            ->where('stored_at', '<=', $limitDate)
            ->where('notified', false)
            ->get();

        foreach ($samples as $sample) {
            $daysInFreezer = (int) -Carbon::now()->diffInDays($sample->stored_at);

            try {
                $studentUser = $sample->user;
                $student = $studentUser?->student;

                if (isset($studentUser->email)) {
                    Mail::to($studentUser->email)->send(new SampleInFreezerAlert($sample, $daysInFreezer));
                }

                $supervisor = $student?->supervisor;
                if ($supervisor && isset($supervisor->email)) {
                    Mail::to($supervisor->email)->send(new SampleInFreezerAlert($sample, $daysInFreezer));
                }

                $sample->update(['notified' => true]);
            } catch (\Exception $e) {
                $this->error(" - Erro ao processar amostra {$sample->code}: {$e->getMessage()}");
            }
        }

        return 0;
    }
}
