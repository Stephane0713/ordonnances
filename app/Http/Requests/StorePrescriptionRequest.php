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
            'patient_contact_method' => 'required|in:email,call,sms',
            'patient_contact_value' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $method = request('patient_contact_method');

                    if ($method === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Le champ doit être une adresse email valide.');
                    }

                    if (in_array($method, ['call', 'sms']) && !preg_match('/^0\d{9}$/', $value)) {
                        $fail('Le champ doit être un numéro de téléphone valide (format : 0XXXXXXXXX).');
                    }
                },
            ],
            'doctor_first_name' => 'required|string|max:255',
            'doctor_last_name' => 'required|string|max:255',
            'prescribed_at' => 'required|date',
            'validity_duration_in_months' => 'nullable|integer|min:1',
            'renewable_count' => 'required|integer|min:0',
            'dispensed_count' => 'nullable|integer|min:0',
            'last_dispensed_at' => 'nullable|date|before_or_equal:today',
            'dispense_interval_days' => 'required|integer|min:1',
            'status' => 'required|in:to_prepare,to_deliver,closed',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            // Prénom & Nom Patient
            'patient_first_name.required' => 'Le prénom du patient est obligatoire.',
            'patient_last_name.required' => 'Le nom du patient est obligatoire.',

            // SSN (Numéro de sécurité sociale)
            'patient_ssn.required' => 'Le numéro de sécurité sociale est requis.',
            'patient_ssn.numeric' => 'Le SSN doit contenir uniquement des chiffres.',
            'patient_ssn.digits_between' => 'Le SSN doit comporter entre :min et :max chiffres.',

            // Méthode de contact
            'patient_contact_method.required' => 'Veuillez choisir un mode de contact.',
            'patient_contact_method.in' => 'Le mode de contact sélectionné est invalide.',
            'patient_contact_value.required' => 'Le champ contact est obligatoire.',

            // Médecin
            'doctor_first_name.required' => 'Le prénom du médecin est obligatoire.',
            'doctor_last_name.required' => 'Le nom du médecin est obligatoire.',

            // Dates et durée
            'prescribed_at.required' => 'La date de prescription est obligatoire.',
            'prescribed_at.date' => 'La date de prescription n\'est pas valide.',
            'validity_duration_in_months.integer' => 'La durée de validité doit être un nombre entier.',
            'validity_duration_in_months.min' => 'La durée doit être d\'au moins :min mois.',

            // Renouvellement et délivrance
            'renewable_count.required' => 'Le nombre de renouvellements est requis.',
            'renewable_count.integer' => 'Le nombre de renouvellements doit être un nombre entier.',
            'dispense_interval_days.required' => 'L\'intervalle entre les délivrances est requis.',
            'dispense_interval_days.min' => 'L\'intervalle doit être d\'au moins :min jour.',

            'last_dispensed_at.before_or_equal' => 'La date de dernière délivrance ne peut pas être dans le futur.',

            // Statut
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné est invalide.',
        ];
    }
}
