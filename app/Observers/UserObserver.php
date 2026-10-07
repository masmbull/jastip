<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserProfile;

class UserObserver
{
    public function created(User $user): void
    {
        // Create user profile when user is created
        if (!$user->profile) {
            UserProfile::create([
                'user_id' => $user->id,
            ]);
        }
    }
}
