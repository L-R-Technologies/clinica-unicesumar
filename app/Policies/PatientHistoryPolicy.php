<?php

namespace App\Policies;

use App\Models\PatientHistory;
use App\Models\User;

class PatientHistoryPolicy
{
    public function view(User $user, PatientHistory $patientHistory): bool
    {
        return $user->hasRole('teacher') || $patientHistory->user_id === $user->id;
    }

    public function update(User $user, PatientHistory $patientHistory): bool
    {
        return $user->hasRole('teacher') || $patientHistory->user_id === $user->id;
    }

    public function delete(User $user, PatientHistory $patientHistory): bool
    {
        return $user->hasRole('teacher') || $patientHistory->user_id === $user->id;
    }
}
