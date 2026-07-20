<?php

namespace App\Service;

use App\Models\Exam;
use App\Models\ExamFeedback;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ExamFeedbackService
{
    public function validateFeedbackData(array $data)
    {
        $rules = [
            'clarity' => 'required|integer|min:1|max:5',
            'cordiality' => 'required|integer|min:1|max:5',
            'waiting_time' => 'required|integer|min:1|max:5',
            'result_speed' => 'required|integer|min:1|max:5',
            'confidence' => 'required|integer|min:1|max:5',
            'observation' => 'nullable|string|max:2000',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function createFeedback(Exam $exam, array $data): ExamFeedback
    {
        if ($exam->status !== 'approved') {
            throw new Exception('O feedback só pode ser enviado após a aprovação do exame.');
        }

        if ($exam->feedback()->exists()) {
            throw new Exception('Você já enviou o feedback deste exame.');
        }

        $data['exam_id'] = $exam->id;

        return ExamFeedback::create($data);
    }
}
