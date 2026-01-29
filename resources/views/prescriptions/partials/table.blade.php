@props(['prescriptions'])

<div class="shadow-md sm:rounded max-h-[60vh] overflow-y-auto bg-gray-50" x-ref="table">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-blue-100 sticky top-0 z-50">
      <tr>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700"><x-order-by-button label="Patient"
            name="patient_last_name" /></th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700"><x-order-by-button label="Contact"
            name="patient_contact_method" /></th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700"><x-order-by-button
            label="N° Sécurité sociale" name="patient_ssn" /></th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700"><x-order-by-button label="Médecin"
            name="doctor_last_name" /></th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700"><x-order-by-button label="Prescrit le"
            name="prescribed_at" /></th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700"><x-order-by-button label="Dernier le"
            name="last_dispense_at" /></th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700"><x-order-by-button label="Prévue le"
            name="next_dispense_at" /></th>
        {{-- <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Nb.</th> --}}
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700"><x-order-by-button label="Statut"
            name="status" /></th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-300">
      @foreach($prescriptions as $prescription)
        @include('prescriptions.partials.row', ['prescription' => $prescription])
      @endforeach
    </tbody>
  </table>
</div>