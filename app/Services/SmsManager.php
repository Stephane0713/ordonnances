<?php

namespace App\Services;

use App\Models\User;

class SmsManager
{
    public function getCredits(User $user)
    {
        return 0;
    }

    public function sendSms(User $user, string $to, string $message)
    {
        return true;
    }
}
