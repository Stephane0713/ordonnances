<x-app-layout>
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

  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-4 rounded-sm">
      <div class="flex flex-col" x-data="prescriptionManager({
        prescriptions: @js($prescriptions),
        defaultValues: @js(config('defaults')),
        canUseSms: {{ Auth::user()->can('use-sms') ? 'true' : 'false' }},
        routes: {
            store: @js(route('prescriptions.store')),
            update: @js(route('prescriptions.update', '__ID__'))
        }
     })" x-on:open-store-modal.window="openModal('save')"
        x-on:open-update-modal.window="openModal('save', $event.detail)"
        x-on:open-delete-modal.window="openModal('delete', $event.detail)"
        x-on:open-prepare-modal.window="openModal('prepare', $event.detail)"
        x-on:open-deliver-modal.window="openModal('deliver', $event.detail)"
        x-on:open-close-modal.window="openModal('close', $event.detail)"
        x-on:open-cancel-modal.window="openModal('cancel', $event.detail)">

        <x-primary-button class="inline-block justify-center ml-auto mb-6"
          x-on:click.prevent="$dispatch('open-store-modal')">
          Ajouter une ordonnance</x-primary-button>

        <form action="{{ route('prescriptions.index') }}" class="p-4 flex items-end gap-2 bg-white rounded shadow mb-4">
          <div class="flex-1">
            <x-input-label class="mb-2" for="patient_search">Patient</x-input-label>
            <x-text-input type="text" class="w-full text-sm" id="patient_search" name="patient_search"
              placeholder="Nom, Prénom ou N° de sécurité sociale" value="{{ request('patient_search') }}" />
          </div>
          <div class="flex-1">
            <x-input-label class="mb-2" for="doctor_search">Médecin</x-input-label>
            <x-text-input type="text" class="w-full text-sm" id="doctor_search" name="doctor_search"
              placeholder="Nom ou Prénom" value="{{ request('doctor_search') }}" />
          </div>
          <div>
            <x-input-label class="mb-2" for="prescribed_from">Prescrit entre le</x-input-label>
            <x-text-input type="date" class="w-full text-sm" id="prescribed_from" name="prescribed_from"
              value="{{ request('prescribed_from') }}" />
          </div>
          <div>
            <x-input-label class="mb-2" for="prescribed_to">et le</x-input-label>
            <x-text-input type="date" class="w-full text-sm" id="prescribed_to" name="prescribed_to"
              value="{{ request('prescribed_to') }}" />
          </div>
          <div>
            <x-input-label class="mb-2" for="status">États</x-input-label>
            <select
              class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
              name="status" id="status">
              <option class="text-sm" value="all" {{ request('status') === 'all' ? 'selected' : '' }}>
                Tout afficher</option>
              <option class="text-sm" value="to_prepare" {{ request('status') === 'to_prepare' ? 'selected' : '' }}>
                À préparer</option>
              <option class="text-sm" value="to_deliver" {{ request('status') === 'to_deliver' ? 'selected' : '' }}>
                À délivrer</option>
              <option class="text-sm" value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>
                Clôturée</option>
            </select>
          </div>
          <x-secondary-button class="py-2" x-on:click.prevent="window.location='{{ route('prescriptions.index') }}'">
            <i class="fa-solid fa-xmark text-sm"></i></x-secondary-button>
          <x-primary-button class="py-2"><i class="fa-solid fa-magnifying-glass text-sm"></i></x-primary-button>
        </form>

        @if($prescriptions->count() > 0)
          @include('prescriptions.partials.table', compact('prescriptions'))
        @else
          <p class="py-12 text-center bg-white text-gray-500 border rounded shadow mt-2">Aucun résultats...</p>
        @endif

        @include('prescriptions.partials.save-modal')
        @include('prescriptions.partials.delete-modal')
        @include('prescriptions.partials.prepare-modal')
        @include('prescriptions.partials.deliver-modal')
        @include('prescriptions.partials.cancel-modal')
        @include('prescriptions.partials.close-modal')

      </div>
    </div>
  </div>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('prescriptionManager', (initialConfig) => ({
        current: null,
        prescriptions: initialConfig.prescriptions,
        defaultValues: initialConfig.defaultValues,
        canUseSms: initialConfig.canUseSms,

        openModal(modal, id = null) {
          this.current = id && this.prescriptions.data.find(p => id === p.id);
          this.$dispatch('open-modal', modal);
        },

        getSaveRoute() {
          const storeRoute = initialConfig.routes.store;
          const updateRoute = initialConfig.routes.update;

          return !this.current
            ? storeRoute
            : updateRoute.replace('__ID__', this.current.id);
        },

        getInputValue(field) {
          return this.current && this.current[field] || this.defaultValues[field];
        },

        getContactPlaceholder(method) {
          return method === 'email' ? 'john@doe.com' : '0612345678';
        },

        getContactPattern(method) {
          return method === 'email'
            ? '[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
            : '0[1-9][0-9]{8}';
        },

        getNextDeliveryDate() {
          const date = new Date();
          const interval = this.current?.dispense_interval_days || 28;
          date.setDate(date.getDate() + interval);
          return date;
        },

        canNotify() {
          if (this.current?.patient_contact_method !== 'sms') return true;
          return this.canUseSms;
        }
      }))
    })
  </script>
</x-app-layout>