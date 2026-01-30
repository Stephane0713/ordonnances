<x-modal name="save" maxWidth="7xl">
  <form :action="getSaveRoute()" method="POST" class="px-6 py-4 grid grid-cols-2 gap-4 content-between">
    @csrf
    <template x-if="current">
      @method('PUT')
    </template>

    <div class="flex flex-col">
      {{-- Section Ordonnance --}}
      <fieldset class="border border-gray-300 rounded-md p-4 mb-4">
        <legend class="text-lg font-semibold">Ordonnance</legend>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
          <div>
            <x-input-label class="mb-2" for="prescribed_at">Date de prescription</x-input-label>
            <x-text-input required type="date" class="w-full text-sm" id="prescribed_at" name="prescribed_at"
              placeholder="2022-01-01" ::value="getInputValue('prescribed_at')?.split('T')[0]" />
          </div>

          <div>
            <x-input-label class="mb-2" for="validity_duration_in_months">Durée de validité
              (mois)</x-input-label>
            <x-text-input required type="number" min="1" class="w-full text-sm" id="validity_duration_in_months"
              name="validity_duration_in_months" placeholder="3"
              ::value="getInputValue('validity_duration_in_months')" />
          </div>

          <div>
            <x-input-label class="mb-2" for="renewable_count">Nombre de
              renouvellements</x-input-label>
            <x-text-input required type="number" min="0" class="w-full text-sm" id="renewable_count"
              name="renewable_count" placeholder="6" ::value="getInputValue('renewable_count')" />
          </div>

          <div>
            <x-input-label class="mb-2" for="dispensed_count">Nombre déjà délivré</x-input-label>
            <x-text-input required type="number" min="0" class="w-full text-sm" id="dispensed_count"
              name="dispensed_count" placeholder="0" ::value="getInputValue('dispensed_count')" />
          </div>

          <div>
            <x-input-label class="mb-2" for="dispense_interval_days">Intervalle entre délivrances
              (jours)</x-input-label>
            <x-text-input required type="number" min="1" class="w-full text-sm" id="dispense_interval_days"
              x-model="dispense_interval_days" name="dispense_interval_days" placeholder="28"
              ::value="getInputValue('dispense_interval_days')" />
          </div>

          <div>
            <x-input-label class="mb-2" for="last_dispensed_at">Dernière délivrance</x-input-label>
            <x-text-input x-model="last_dispensed_at" type="date" class="w-full text-sm" id="last_dispensed_at"
              name="last_dispensed_at" placeholder="2022-01-01"
              ::value="getInputValue('last_dispensed_at')?.split('T')[0]" />
          </div>

          <div>
            <x-input-label class="mb-2" for="status">États</x-input-label>
            <select
              class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
              name="status" id="status">
              <option class="text-sm" value="to_prepare" :selected="getInputValue('status') === 'to_prepare'">
                À préparer</option>
              <option class="text-sm" value="to_deliver" :selected="getInputValue('status') === 'to_deliver'">
                À délivrer</option>
              <option class="text-sm" value="closed" :selected="getInputValue('status') === 'closed'">
                Clôturée</option>
            </select>
          </div>

          <div>
            <x-input-label class="mb-2" for="last_dispensed_at">Prochaine délivrance (calculée)</x-input-label>
            <div class=" min-h-[38px] text-gray-400 border p-2 border-gray-300 rounded-md shadow-sm text-sm"
              x-text="next_dispensed_at"></div>
          </div>

          <div class="md:col-span-2">
            <x-input-label class="mb-2" for="notes">Notes</x-input-label>
            <textarea rows="4" id="notes" name="notes"
              class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm"
              placeholder="Notes sur l'ordonnance" x-text="getInputValue('notes')"></textarea>
          </div>
        </div>
      </fieldset>
    </div>

    <div class="flex flex-col">
      {{-- Section Patient --}}
      <fieldset class="border border-gray-300 rounded-md p-4 mb-4">
        <legend class="text-lg font-semibold">Patient</legend>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
          <div>
            <x-input-label class="mb-2" for="patient_first_name">Prénom</x-input-label>
            <x-text-input required type="text" class="w-full text-sm" id="patient_first_name" name="patient_first_name"
              placeholder="John" ::value="getInputValue('patient_first_name')" />
          </div>

          <div>
            <x-input-label class="mb-2" for="patient_last_name">Nom</x-input-label>
            <x-text-input required type="text" class="w-full text-sm" id="patient_last_name" name="patient_last_name"
              placeholder="Doe" ::value="getInputValue('patient_last_name')" />
          </div>

          <div>
            <x-input-label class="mb-2" for="patient_ssn">N° sécurité sociale (8 premiers
              chiffres)</x-input-label>
            <x-text-input required type="text" class="w-full text-sm" id="patient_ssn" name="patient_ssn"
              pattern="\d{8,13}" placeholder="12345678" ::value="getInputValue('patient_ssn')" />
          </div>

          <div>
            <x-input-label class="mb-2" for="patient_contact_method">Méthode de
              contact</x-input-label>
            <select
              class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
              required id="patient_contact_method" name="patient_contact_method" x-on:change="
                        $refs.contact_value.value = '';
                        $refs.contact_value.placeholder = getContactPlaceholder($event.target.value);
                        $refs.contact_value.pattern = getContactPattern($event.target.value);
                      " class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
              <option class="text-sm" value="email" :selected="getInputValue('patient_contact_method') === 'email'">
                Email
              </option>
              <option class="text-sm" value="call" :selected="getInputValue('patient_contact_method') === 'call'">Appel
                téléphonique</option>
              @can('show-sms-option');
                <option class="text-sm" value="sms" :selected="getInputValue('patient_contact_method') === 'sms'">SMS
                </option>
              @endcan
            </select>
          </div>

          <div class="md:col-span-2">
            <x-input-label class="mb-2" for="patient_contact_value">Valeur de contact</x-input-label>
            <x-text-input x-ref="contact_value" required type="text" class="w-full text-sm" id="patient_contact_value"
              name="patient_contact_value" ::value="getInputValue('patient_contact_value')"
              ::placeholder="getContactPlaceholder(getInputValue('patient_contact_method'))" />
          </div>
        </div>
      </fieldset>

      {{-- Section Médecin --}}
      <fieldset class="border border-gray-300 rounded-md p-4 mb-4">
        <legend class="text-lg font-semibold">Médecin</legend>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
          <div>
            <x-input-label class="mb-2" for="doctor_first_name">Prénom</x-input-label>
            <x-text-input required type="text" class="w-full text-sm" id="doctor_first_name" name="doctor_first_name"
              placeholder="John" ::value="getInputValue('doctor_first_name')" />
          </div>

          <div>
            <x-input-label class="mb-2" for="doctor_last_name">Nom</x-input-label>
            <x-text-input required type="text" class="w-full text-sm" id="doctor_last_name" name="doctor_last_name"
              placeholder="Doe" ::value="getInputValue('doctor_last_name')" />
          </div>
        </div>
      </fieldset>

      {{-- Controls --}}
      <div class="ml-auto mt-auto col-start-2">
        <x-secondary-button class="inline-block justify-center mr-2"
          x-on:click.prevent="closeSelf()">Annuler</x-secondary-button>
        <x-primary-button class="inline-block justify-center">Enregistrer</x-primary-button>
      </div>
    </div>
  </form>
</x-modal>