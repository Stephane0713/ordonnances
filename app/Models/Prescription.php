<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected static function booted()
    {
        static::saving(function (self $model) {
            $validUntil = $model->prescribed_at->copy()->addMonths($model->validity_duration_in_months);

            if ($model->dispensed_count >= $model->renewable_count || now()->gt($validUntil)) {
                $model->next_dispense_at = null;
                return;
            }

            $baseDate = $model->last_dispensed_at ?? $model->prescribed_at;
            $model->next_dispense_at = $baseDate->copy()->addDays($model->dispense_interval_days);
        });
    }

    public function setPatientSsnAttribute($value)
    {
        $digits = preg_replace('/\D/', '', $value);
        $this->attributes['patient_ssn'] = substr($digits, -8);
    }

    public function getSSN()
    {
        $ssn = (string) $this->patient_ssn;
        return str_pad($ssn, 13, '•', STR_PAD_LEFT);
    }

    public function getProgression(): string
    {
        $now = now();
        $validUntil = $this->prescribed_at->copy()->addMonths($this->validity_duration_in_months);
        $daysUntilNext = $this->next_dispense_at ? $now->diffInDays($this->next_dispense_at, false) : null;

        if ($this->status === 'cancelled') {
            return 'Annulé';
        }

        if ($this->dispensed_count >= $this->renewable_count) {
            return 'Clôturé';
        }

        if ($now->gt($validUntil)) {
            return 'Expiré';
        }

        if ($this->status === 'to_prepare' && $daysUntilNext < 0) {
            return 'En retard de préparation';
        }

        if ($this->status === 'to_deliver' && $daysUntilNext < 0) {
            return 'En retard de délivrance';
        }

        if ($this->status === 'to_prepare' && $daysUntilNext !== null && $daysUntilNext < 7) {
            return 'En attente de préparation';
        }

        if ($this->status === 'to_deliver') {
            return 'En attente de délivrance';
        }

        if ($this->status === 'to_prepare') {
            $remaining = max($this->renewable_count - $this->dispensed_count, 0);
            return "$remaining délivrance(s) restante(s)";
        }

        return 'Erreur';
    }
}
