<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_first_name' => 'required|string|max:255',
            'patient_last_name' => 'required|string|max:255',
            'patient_ssn' => 'required|numeric|digits_between:8,13',
            'patient_contact_method' => 'required|in:email,phone_call,sms',
            'patient_contact_value' => 'required|string|max:255',
            'doctor_first_name' => 'required|string|max:255',
            'doctor_last_name' => 'required|string|max:255',
            'prescribed_at' => 'required|date',
            'validity_duration_in_months' => 'nullable|integer|min:1',
            'renewable_count' => 'required|integer|min:0',
            'dispensed_count' => 'nullable|integer|min:0',
            'last_dispensed_at' => 'nullable|date|before_or_equal:today',
            'dispense_interval_days' => 'required|integer|min:1',
            'status' => 'nullable|in:active,waiting,closed',
            'notes' => 'nullable|string',
        ];
    }
}
