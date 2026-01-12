<?php

namespace App\Services;

use App\Mail\CancelMail;
use App\Mail\PrepareMail;
use App\Models\Prescription;
use Illuminate\Support\Facades\Mail;

class PrescriptionNotifier
{
    public function send(Prescription $prescription)
    {
        if ($prescription->status === 'to_deliver') {
            if ($prescription->patient_contact_method === 'email') {
                Mail::to($prescription->patient_contact_value)->send(new PrepareMail($prescription));
                return;
            }

            if ($prescription->patient_contact_method === 'sms') {
                // TODO : Send SMS
                return;
            }
        }

        if ($prescription->status === 'cancelled') {
            if ($prescription->patient_contact_method === 'email') {
                Mail::to($prescription->patient_contact_value)->send(new CancelMail($prescription));
                return;
            }

            if ($prescription->patient_contact_method === 'sms') {
                // TODO : Send SMS
                return;
            }
        }
    }
}
