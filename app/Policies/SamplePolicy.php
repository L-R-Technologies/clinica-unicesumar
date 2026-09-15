<?php

namespace App\Policies;

use App\Models\Sample;
use App\Models\User;

class SamplePolicy
{
    public function view(User $user, Sample $sample): bool
    {
        return $user->hasRole('teacher') || $sample->user_id === $user->id;
    }

    public function update(User $user, Sample $sample): bool
    {
        return $user->hasRole('teacher') || $sample->user_id === $user->id;
    }

    public function delete(User $user, Sample $sample): bool
    {
        return $user->hasRole('teacher') || $sample->user_id === $user->id;
    }
}
