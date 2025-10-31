@props(['prescription'])

<tr {{ $attributes->merge(['class' => 'hover:bg-gray-50']) }}>
  <td class="px-4 py-2 text-sm text-gray-800">{{ strtoupper($prescription->patient_last_name) }}
    {{ $prescription->patient_first_name }}
  </td>
  <td class="px-4 py-2 text-sm text-gray-800">{{ $prescription->patient_ssn }}</td>
  <td class="px-4 py-2 text-sm text-gray-800">{{ strtoupper($prescription->doctor_last_name) }}
    {{ $prescription->doctor_first_name }}
  </td>
  <td class="px-4 py-2 text-sm text-gray-800">{{ $prescription->prescribed_at->format('d/m/Y') }}</td>
  <td class="px-4 py-2 text-sm text-gray-800">{{ $prescription->last_dispensed_at?->format('d/m/Y') }}</td>
  <td class="px-4 py-2 text-sm text-gray-800">{{ $prescription->next_dispense_at?->format('d/m/Y') }}</td>
  <td class="px-4 py-2 text-sm text-gray-800">{{ $prescription->getProgression() }}</td>
  <td class="text-right px-4 py-2 text-sm text-gray-800">
    <x-dropdown align="right" width="48">
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
        {{ $actions }}
      </x-slot>
    </x-dropdown>
  </td>
</tr>