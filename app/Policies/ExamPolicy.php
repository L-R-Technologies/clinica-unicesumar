<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

class ExamPolicy
{
    /**
     * Professores têm acesso amplo; alunos só acessam os próprios exames.
     */
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
        return $user->hasRole('teacher') || $exam->user_id === $user->id;
    }

    /**
     * Apenas professores podem aprovar exames.
     */
    public function approve(User $user, Exam $exam): bool
    {
        return $user->hasRole('teacher');
    }

    /**
     * Apenas professores podem reprovar exames.
     */
    public function reject(User $user, Exam $exam): bool
    {
        return $user->hasRole('teacher');
    }
}
