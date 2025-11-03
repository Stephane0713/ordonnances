<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Dashboard') }}
    </h2>
  </x-slot>

  @if ($errors->any())
    <div class="bg-red-500 border border-red-700 shadow-md py-2 px-4 rounded">
      <div class="max-w-7xl m-auto">
        <strong class="text-white">
          {{ __('Des erreurs sont survenues :') }}
        </strong>
        <ul class="mt-2 text-white">
          @foreach ($errors->all() as $message)
            <li>{{ $message }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-4 rounded-sm">
      <div class="flex flex-col" x-data="{
        current: null,
        prescriptions: @js($prescriptions),
        openModal(modal, id = null) { 
          this.current = id && this.prescriptions.data.find(p => id === p.id);
          $dispatch('open-modal', modal); 
        },
        getSaveRoute() { 
          return !this.current 
          ? @js(route('prescriptions.store')) 
          : @js(route('prescriptions.update', '__ID__')).replace('__ID__', this.current.id);
        },
        defaultValues: @js(config('defaults')),
        getInputValue(field) {
          return this.current && this.current[field] || this.defaultValues[field];
        },
        getContactPlaceholder(method) {
          return method === 'email' ? 'john@doe.com' : '0612345678';
        },
        getContactPattern(method) {
          return method === 'email' ? '^[^@\s]+@[^@\s]+\.[^@\s]+$' : '0[1-9][0-9]{8}';
        }
      }" x-on:open-store-modal.window="openModal('save')"
        x-on:open-update-modal.window="openModal('save', $event.detail)"
        x-on:open-delete-modal.window="openModal('delete', $event.detail)">

        <x-primary-button class="inline-block justify-center ml-auto mb-6"
          x-on:click.prevent="$dispatch('open-store-modal')">
          Ajouter une ordonnance</x-primary-button>

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
                <x-prescription-row class="text-sm" :prescription="$prescription">
                  <x-slot name="actions">
                    @if ($prescription->status === 'to_prepare')
                      <x-dropdown-link class="cursor-pointer">Classer préparée</x-dropdown-link>
                    @else
                      <x-dropdown-link class="cursor-pointer">Classer délivrée</x-dropdown-link>
                    @endif
                    @if($prescription->patient_contact_method === "email")
                      <x-dropdown-link class="cursor-pointer">Envoyer un mail</x-dropdown-link>
                    @elseif($prescription->patient_contact_method === "sms")
                      <x-dropdown-link class="cursor-pointer">Envoyer un sms</x-dropdown-link>
                    @elseif($prescription->patient_contact_method === "call")
                      <x-dropdown-link class="cursor-pointer"
                        href="tel:{{ $prescription->patient_contact_value  }}'">Appeler le
                        {{ $prescription->patient_contact_value }}</x-dropdown-link>
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
                </x-prescription-row>
              @endforeach
            </tbody>
          </table>
        </div>

        <x-modal name="save" maxWidth="7xl">
          <form :action="getSaveRoute()" method="POST" class="px-6 py-4 grid grid-cols-2 gap-4 content-between">
            @csrf
            <template x-if="current">
              @method('PUT')
            </template>

            <div class="flex flex-col justify-between">
              {{-- Section Patient --}}
              <fieldset class="border border-gray-300 rounded-md p-4 mb-4">
                <legend class="text-lg font-semibold">Patient</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                  <div>
                    <x-input-label class="mb-2" for="patient_first_name">Prénom</x-input-label>
                    <x-text-input required type="text" class="w-full" id="patient_first_name" name="patient_first_name"
                      placeholder="John" ::value="getInputValue('patient_first_name')" />
                  </div>

                  <div>
                    <x-input-label class="mb-2" for="patient_last_name">Nom</x-input-label>
                    <x-text-input required type="text" class="w-full" id="patient_last_name" name="patient_last_name"
                      placeholder="Doe" ::value="getInputValue('patient_last_name')" />
                  </div>

                  <div>
                    <x-input-label class="mb-2" for="patient_ssn">N° sécurité sociale (8 derniers
                      chiffres)</x-input-label>
                    <x-text-input required type="text" class="w-full" id="patient_ssn" name="patient_ssn"
                      pattern="\d{8,13}" placeholder="12345678" ::value="getInputValue('patient_ssn')" />
                  </div>

                  <div>
                    <x-input-label class="mb-2" for="patient_contact_method">Méthode de
                      contact</x-input-label>
                    <select required id="patient_contact_method" name="patient_contact_method" x-on:change="
                        $refs.contact_value.value = '';
                        $refs.contact_value.placeholder = getContactPlaceholder($event.target.value);
                        $refs.contact_value.pattern = getContactPattern($event.target.value);
                      " class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
                      <option value="email" :selected="getInputValue('patient_contact_method') === 'email'">Email
                      </option>
                      <option value="call" :selected="getInputValue('patient_contact_method') === 'call'">Appel
                        téléphonique</option>
                      <option value="sms" :selected="getInputValue('patient_contact_method') === 'sms'">SMS</option>
                    </select>
                  </div>

                  <div class="md:col-span-2">
                    <x-input-label class="mb-2" for="patient_contact_value">Valeur de contact</x-input-label>
                    <x-text-input x-ref="contact_value" required type="text" class="w-full" id="patient_contact_value"
                      name="patient_contact_value" ::value="getInputValue('patient_contact_value')"
                      ::placeholder="getContactPlaceholder(getInputValue('patient_contact_method'))"
                      ::pattern="getContactPattern(getInputValue('patient_contact_method'))" />
                  </div>
                </div>
              </fieldset>

              {{-- Section Médecin --}}
              <fieldset class="border border-gray-300 rounded-md p-4 mb-4">
                <legend class="text-lg font-semibold">Médecin</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                  <div>
                    <x-input-label class="mb-2" for="doctor_first_name">Prénom</x-input-label>
                    <x-text-input required type="text" class="w-full" id="doctor_first_name" name="doctor_first_name"
                      placeholder="John" ::value="getInputValue('doctor_first_name')" />
                  </div>

                  <div>
                    <x-input-label class="mb-2" for="doctor_last_name">Nom</x-input-label>
                    <x-text-input required type="text" class="w-full" id="doctor_last_name" name="doctor_last_name"
                      placeholder="Doe" ::value="getInputValue('doctor_last_name')" />
                  </div>
                </div>
              </fieldset>
            </div>

            <div class="flex flex-col">
              {{-- Section Ordonnance --}}
              <fieldset class="border border-gray-300 rounded-md p-4 mb-4">
                <legend class="text-lg font-semibold">Ordonnance</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                  <div>
                    <x-input-label class="mb-2" for="prescribed_at">Date de prescription</x-input-label>
                    <x-text-input required type="date" class="w-full" id="prescribed_at" name="prescribed_at"
                      placeholder="2022-01-01" ::value="getInputValue('prescribed_at')?.split('T')[0]" />
                  </div>

                  <div>
                    <x-input-label class="mb-2" for="validity_duration_in_months">Durée de validité
                      (mois)</x-input-label>
                    <x-text-input required type="number" min="1" class="w-full" id="validity_duration_in_months"
                      name="validity_duration_in_months" placeholder="3"
                      ::value="getInputValue('validity_duration_in_months')" />
                  </div>

                  <div>
                    <x-input-label class="mb-2" for="renewable_count">Nombre de
                      renouvellements</x-input-label>
                    <x-text-input required type="number" min="0" class="w-full" id="renewable_count"
                      name="renewable_count" placeholder="6" ::value="getInputValue('renewable_count')" />
                  </div>

                  <div>
                    <x-input-label class="mb-2" for="dispensed_count">Nombre déjà délivré</x-input-label>
                    <x-text-input required type="number" min="0" class="w-full" id="dispensed_count"
                      name="dispensed_count" placeholder="0" ::value="getInputValue('dispensed_count')" />
                  </div>

                  <div>
                    <x-input-label class="mb-2" for="last_dispensed_at">Dernière délivrance</x-input-label>
                    <x-text-input type="date" class="w-full" id="last_dispensed_at" name="last_dispensed_at"
                      placeholder="2022-01-01" ::value="getInputValue('last_dispensed_at')?.split('T')[0]" />
                  </div>

                  <div>
                    <x-input-label class="mb-2" for="dispense_interval_days">Intervalle entre délivrances
                      (jours)</x-input-label>
                    <x-text-input required type="number" min="1" class="w-full" id="dispense_interval_days"
                      name="dispense_interval_days" placeholder="30"
                      ::value="getInputValue('dispense_interval_days')" />
                  </div>

                  <div class="md:col-span-2">
                    <x-input-label class="mb-2" for="notes">Notes</x-input-label>
                    <textarea rows="4" id="notes" name="notes"
                      class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm"
                      placeholder="Notes sur l'ordonnance" x-text="getInputValue('notes')"></textarea>
                  </div>
                </div>
              </fieldset>
            </div>
            <div class="ml-auto col-start-2">
              <x-secondary-button class="inline-block justify-center mr-2"
                x-on:click.prevent="$dispatch('close-modal', 'save')">Annuler</x-secondary-button>
              <x-primary-button class="inline-block justify-center">Enregistrer</x-primary-button>
            </div>
          </form>
        </x-modal>

        <x-modal name="delete">
          <form class="p-6" class="px-6 py-4 grid grid-cols-2 gap-4 content-between"
            :action="current && @js(route('prescriptions.destroy', '__ID__')).replace('__ID__', current.id)"
            method="POST">
            @csrf
            @method('DELETE')

            <h2 class="text-lg font-medium text-gray-900">
              Êtes vous sur de vouloir supprimer l'odonnance ?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
              Cette action est définitive, il est impossible de restaurer l'odonnance une fois qu'elle a été supprimée.
            </p>

            <div class="mt-6 flex justify-end">
              <x-secondary-button x-on:click="$dispatch('close')">
                Annuler
              </x-secondary-button>

              <x-danger-button class="ms-3">
                Confirmer la suppression
              </x-danger-button>
            </div>

          </form>
        </x-modal>

      </div>
    </div>
  </div>
</x-app-layout>