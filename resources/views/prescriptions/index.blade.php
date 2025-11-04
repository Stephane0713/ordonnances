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
        },
        getNextDeliveryDate() {
          const date = new Date();
          date.setDate(date.getDate() + this.current?.dispense_interval_days || 28);
          return date;
        }
      }" x-on:open-store-modal.window="openModal('save')"
        x-on:open-update-modal.window="openModal('save', $event.detail)"
        x-on:open-delete-modal.window="openModal('delete', $event.detail)"
        x-on:open-prepare-modal.window="openModal('prepare', $event.detail)"
        x-on:open-deliver-modal.window="openModal('deliver', $event.detail)">

        <x-primary-button class="inline-block justify-center ml-auto mb-6"
          x-on:click.prevent="$dispatch('open-store-modal')">
          Ajouter une ordonnance</x-primary-button>

        <x-prescriptions.table :prescriptions="$prescriptions" />
        <x-prescriptions.save-modal />
        <x-prescriptions.delete-modal />
        <x-prescriptions.prepare-modal />
        <x-prescriptions.deliver-modal />

      </div>
    </div>
  </div>
</x-app-layout>