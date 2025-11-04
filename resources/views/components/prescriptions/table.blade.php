@props(['prescriptions'])

<div class="overflow-x-auto shadow-md sm:rounded">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-blue-100">
      <tr>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Patient</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">N° Sécurité sociale</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Médecin</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Prescrit le</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Dernier le</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Prévue le</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">État</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
      @foreach($prescriptions as $prescription)
        <x-prescriptions.row class="text-sm" :prescription="$prescription">
          <x-slot name="actions">
            @if ($prescription->status === 'to_prepare')
              <x-dropdown-link class="cursor-pointer">Classer préparée</x-dropdown-link>
            @else
              <x-dropdown-link class="cursor-pointer">Classer délivrée</x-dropdown-link>
            @endif
            <x-dropdown-link class="cursor-pointer"
              x-on:click.prevent="$dispatch('open-update-modal', {{ $prescription->id }})">
              Corriger les informations
            </x-dropdown-link>
            <x-dropdown-link x-on:click.prevent="$dispatch('open-delete-modal', {{ $prescription->id }})"
              class="cursor-pointer text-red-600">
              Supprimer l'ordonnance
            </x-dropdown-link>
          </x-slot>
        </x-prescriptions.row>
      @endforeach
    </tbody>
  </table>
</div>