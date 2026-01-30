<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Prescription extends Model
{
    use HasFactory;

    protected $casts = [
        'last_dispensed_at' => 'datetime',
        'prescribed_at' => 'datetime',
        'next_dispense_at' => 'datetime',
        'validity_duration_in_months' => 'int',
        'dispense_interval_days' => 'int'
    ];

    protected $fillable = [
        'patient_first_name',
        'patient_last_name',
        'patient_ssn',
        'patient_contact_method',
        'patient_contact_value',
        'doctor_first_name',
        'doctor_last_name',
        'prescribed_at',
        'validity_duration_in_months',
        'renewable_count',
        'dispensed_count',
        'last_dispensed_at',
        'dispense_interval_days',
        'status',
        'notes',
    ];

    public static function getNextDispenseAt(self $model)
    {
        if ($model->status === 'closed') {
            return null;
        }

        $baseDate = $model->last_dispensed_at ?? $model->prescribed_at;
        return $baseDate->copy()->addDays($model->dispense_interval_days);
    }

    protected static function booted()
    {
        static::saving(function (self $model) {
            if ($model->isExpired() || !$model->hasRenewableLeft()) {
                $model->status = 'closed';
            }

            $model->next_dispense_at = self::getNextDispenseAt($model);
        });
    }

    public function setPatientSsnAttribute($value)
    {
        $digits = preg_replace('/\D/', '', $value);
        $this->attributes['patient_ssn'] = substr($digits, 0, 8);
    }

    public function getSSN()
    {
        $ssn = (string) $this->patient_ssn;
        return str_pad($ssn, 13, '*', STR_PAD_RIGHT);
    }

    public function hasRenewableLeft(): bool
    {
        return ($this->renewable_count - $this->dispensed_count) > 0;
    }

    public function validUntil()
    {
        return $this->prescribed_at->copy()->addMonths($this->validity_duration_in_months);
    }

    public function isExpired(): bool
    {
        return now()->gt($this->validUntil());
    }

    public function isLate(): bool
    {
        $margin = $this->status === "to_deliver"
            ? config('const.margin.to_deliver.late')
            : config('const.margin.to_prepare.late');

        return $this->next_dispense_at && now()->gt($this->next_dispense_at->addDays($margin));
    }

    public function isPending(): bool
    {
        $margin = config('const.margin.to_prepare.pending');

        return $this->next_dispense_at && now()->gt($this->next_dispense_at->subDays($margin));
    }

    public function getProgression(): string
    {
        if ($this->status === 'closed') {
            return 'Clôturé';
        }

        if ($this->status === 'waiting_for_consent') {
            return 'En attente de consentement';
        }

        if ($this->isExpired()) {
            return 'Expiré';
        }

        if ($this->status === 'to_prepare' && $this->isLate()) {
            return 'En retard de préparation';
        }

        if ($this->status === 'to_deliver' && $this->isLate()) {
            return 'En retard de délivrance';
        }

        if ($this->status === 'to_prepare' && $this->isPending()) {
            return 'En attente de préparation';
        }

        if ($this->status === 'to_deliver') {
            return 'En attente de délivrance';
        }

        if ($this->status === 'to_prepare') {
            $daysLeft = Carbon::today()
                ->addDays(config('const.margin.to_prepare.pending'))
                ->diffInDays(Carbon::parse($this->next_dispense_at)->startOfDay());
            return "À préparer dans $daysLeft jours";
        }

        return 'Erreur';
    }
}
