<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use App\Service\ExamService;

class ExamPolicy
{
    public function view(User $user, Exam $exam): bool
    {
        return $user->hasRole('teacher') || $exam->user_id === $user->id;
    }

    public function update(User $user, Exam $exam): bool
    {
        return $user->hasRole('teacher') || $exam->user_id === $user->id;
    }

    public function delete(User $user, Exam $exam): bool
    {
        $isOwnerOrTeacher = $user->hasRole('teacher') || $exam->user_id === $user->id;

        return $isOwnerOrTeacher && in_array($exam->status, [
            ExamService::STATUS_PENDING,
            ExamService::STATUS_REJECTED,
        ], true);
    }

    public function approve(User $user, Exam $exam): bool
    {
        return $user->hasRole('teacher');
    }

    public function reject(User $user, Exam $exam): bool
    {
        return $user->hasRole('teacher');
    }
}
