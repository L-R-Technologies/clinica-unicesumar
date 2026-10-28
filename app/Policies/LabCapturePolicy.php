<?php

namespace App\Policies;

use App\Models\LabCapture;
use App\Models\User;

class LabCapturePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isLabStaff($user);
    }

    public function view(User $user, LabCapture $capture): bool
    {
        return $this->isLabStaff($user);
    }

    public function review(User $user, LabCapture $capture): bool
    {
        return $this->isLabStaff($user) && $capture->isReviewable();
    }

    public function retry(User $user, LabCapture $capture): bool
    {
        return $this->isLabStaff($user) && ! in_array($capture->status, [
            LabCapture::STATUS_LINKED,
            LabCapture::STATUS_ANALYZING,
        ], true);
    }

    public function discard(User $user, LabCapture $capture): bool
    {
        return $user->hasRole('teacher') && $capture->status !== LabCapture::STATUS_LINKED;
    }

    private function isLabStaff(User $user): bool
    {
        return $user->hasRole('teacher') || $user->hasRole('student');
    }
}
