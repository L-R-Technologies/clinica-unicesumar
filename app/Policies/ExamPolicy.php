<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use App\Service\ExamService;

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

    /**
     * ERS (UC009): exclusão só é permitida para exames com status
     * "Pendente" ou "Rejeitado" — exames validados/em aprovação não podem
     * ser excluídos por nenhum perfil.
     */
    public function delete(User $user, Exam $exam): bool
    {
        $isOwnerOrTeacher = $user->hasRole('teacher') || $exam->user_id === $user->id;

        return $isOwnerOrTeacher && in_array($exam->status, [
            ExamService::STATUS_PENDING,
            ExamService::STATUS_REJECTED,
        ], true);
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
