<?php

namespace Database\Factories;

use App\Models\LabAgent;
use App\Models\LabCapture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabCapture>
 */
class LabCaptureFactory extends Factory
{
    protected $model = LabCapture::class;

    public const SAMPLE_RAW_TEXT = "UNICESUMAR\n******************************\n(0195)             CANAL 1\n20/03/2025         16:14:06\nN. SERIE(21050207)\nOPERADOR(OPERADOR)\nID()\n\nNOME:  \n\nEXAME:             TTPA\n                   TTPA\n\nTEMPO:           16.9s\n\n******************************";

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rawText = self::SAMPLE_RAW_TEXT."\n".fake()->unique()->numerify('#####');

        return [
            'lab_agent_id' => LabAgent::factory(),
            'machine_id' => fn (array $attributes) => LabAgent::find($attributes['lab_agent_id'])?->machine_id,
            'raw_text' => $rawText,
            'raw_hash' => hash('sha256', $rawText),
            'captured_at' => now(),
            'received_at' => now(),
            'status' => LabCapture::STATUS_RECEIVED,
        ];
    }

    /**
     * @param  array<string, mixed>  $analysis
     */
    public function analyzed(array $analysis): static
    {
        return $this->state(fn () => [
            'status' => $analysis['exam_type_id'] === null
                ? LabCapture::STATUS_UNRECOGNIZED
                : LabCapture::STATUS_ANALYZED,
            'analysis' => $analysis,
            'ai_model' => 'gemini-test',
        ]);
    }
}
