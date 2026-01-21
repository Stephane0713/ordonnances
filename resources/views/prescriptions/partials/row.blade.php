@props(['prescription'])

@php
  $isLate = $prescription->isLate();
  $isPending = $prescription->isPending();

  $styles = match (true) {
    $prescription->status === 'closed' => 'bg-gray-100 hover:bg-gray-200 text-gray-400',
    $prescription->status === 'to_prepare' => $isLate ? 'bg-yellow-100 hover:bg-yellow-200'
    : ($isPending ? 'bg-green-100 hover:bg-green-200' : 'bg-white hover:bg-blue-50'),
    $prescription->status === 'to_deliver' => $isLate ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200',
    default => 'bg-white'
  };
@endphp

<tr {{ $attributes->merge(['class' => $styles]) }}>
  <td class="px-4 py-2 text-sm">{{ strtoupper($prescription->patient_last_name) }}
    {{ $prescription->patient_first_name }}
  </td>
  <td class="px-4 py-2 text-sm text-center"><x-contact-icon method="{{ $prescription->patient_contact_method }}"
      value="{{ $prescription->patient_contact_value }}" /></td>
  <td class="px-4 py-2 text-sm">{{ $prescription->getSSN() }}</td>
  <td class="px-4 py-2 text-sm">{{ strtoupper($prescription->doctor_last_name) }}
    {{ $prescription->doctor_first_name }}
  </td>
  <td class="px-4 py-2 text-sm">{{ $prescription->prescribed_at->format('d/m/Y') }}</td>
  <td class="px-4 py-2 text-sm">{{ $prescription->last_dispensed_at?->format('d/m/Y') }}</td>
  <td class="px-4 py-2 text-sm">{{ $prescription->next_dispense_at?->format('d/m/Y') }}</td>
  {{-- <td class="px-4 py-2 text-sm">{{ $prescription->dispensed_count }}/{{ $prescription->renewable_count }}</td> --}}
  <td class="px-4 py-2 text-sm">{{ $prescription->getProgression() }}</td>
  <td class="text-right px-4 py-2 text-sm">
    <x-dropdown align="right" width="w-max">
      <x-slot name="trigger">
        <button
          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
          <div class="ms-1">
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
          </div>
        </button>
      </x-slot>

      <x-slot name="content">
        @if ($prescription->status === 'to_prepare')
          <x-dropdown-link class="cursor-pointer"
            x-on:click.prevent="$dispatch('open-prepare-modal', {{ $prescription->id }})">Classer
            préparée</x-dropdown-link>
        @elseif($prescription->status === 'to_deliver')
          <x-dropdown-link class="cursor-pointer"
            x-on:click.prevent="$dispatch('open-deliver-modal', {{ $prescription->id }})">Classer
            délivrée</x-dropdown-link>
        @endif
        <x-dropdown-link class="cursor-pointer"
          x-on:click.prevent="$dispatch('open-update-modal', {{ $prescription->id }})">
          Voir/Corriger les informations
        </x-dropdown-link>
        @if($prescription->status !== 'closed')
          <x-dropdown-link class="cursor-pointer"
            x-on:click.prevent="$dispatch('open-cancel-modal', {{ $prescription->id }})">Annuler le
            renouvellement</x-dropdown-link>
          <x-dropdown-link class="cursor-pointer"
            x-on:click.prevent="$dispatch('open-close-modal', {{ $prescription->id }})">
            Clôturer l'ordonnance
          </x-dropdown-link>
        @endif
        @if($prescription->status === 'closed' && !$prescription->isExpired() && $prescription->hasRenewableLeft())
          <x-dropdown-link class="cursor-pointer"
            x-on:click.prevent="$dispatch('open-open-modal', {{ $prescription->id }})">
            Réouvrir l'ordonnance
          </x-dropdown-link>
        @endif
        <x-dropdown-link x-on:click.prevent="$dispatch('open-delete-modal', {{ $prescription->id }})"
          class="cursor-pointer text-red-600">
          Supprimer l'ordonnance
        </x-dropdown-link>
      </x-slot>
    </x-dropdown>
  </td>
</tr>