<?php

namespace App\Services;

use App\Mail\CancelMail;
use App\Mail\DeleteMail;
use App\Mail\PrepareMail;
use App\Models\Prescription;
use Illuminate\Support\Facades\Mail;

enum Subject: string
{
    case Prepared = 'prepared';
    case Cancelled = 'cancelled';
    case Deleted = 'deleted';
}

class PrescriptionNotifier
{
    public function send(Prescription $prescription, Subject $subject)
    {
        $method = $prescription->patient_contact_method;

        match ($subject) {
            Subject::Prepared => $this->notifyPrepared($prescription, $method),
            Subject::Cancelled => $this->notifyCancelled($prescription, $method),
            Subject::Deleted => $this->notifyDeleted($prescription, $method),
            default => throw new \InvalidArgumentException("Unknown subject"),
        };
    }

    private function notifyPrepared($prescription, $method)
    {
        match ($method) {
            'email' => Mail::to($prescription->patient_contact_value)->send(new PrepareMail($prescription)),
            'sms' => $this->sendSms($prescription, "Your prescription is prepared."),
            default => null,
        };
    }

    private function notifyCancelled($prescription, $method)
    {
        match ($method) {
            'email' => Mail::to($prescription->patient_contact_value)->send(new CancelMail($prescription)),
            'sms' => $this->sendSms($prescription, "Your prescription was cancelled."),
            default => null,
        };
    }

    private function notifyDeleted($prescription, $method)
    {
        match ($method) {
            'email' => Mail::to($prescription->patient_contact_value)->send(new DeleteMail($prescription)),
            'sms' => $this->sendSms($prescription, "Your prescription was deleted."),
            default => null,
        };
    }

    private function sendSms($prescription, $message)
    {
        // TODO: SMS Logic
    }
}
