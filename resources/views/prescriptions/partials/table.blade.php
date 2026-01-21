@props(['prescriptions'])

<div class="shadow-md sm:rounded max-h-[60vh] overflow-y-auto bg-gray-50">
  <table class="min-w-full divide-y divide-gray-200 mb-64">
    <thead class="bg-blue-100 sticky top-0 z-50">
      <tr>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Patient</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Contact</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">N° Sécurité sociale</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Médecin</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Prescrit le</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Dernier le</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Prévue le</th>
        {{-- <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Nb.</th> --}}
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">État</th>
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