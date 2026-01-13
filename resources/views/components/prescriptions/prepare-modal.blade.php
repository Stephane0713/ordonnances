<x-modal name="prepare">
  <form x-data="{
    hasConfirmedCall: false,
    canConfirm() {
      return this.current?.patient_contact_method !== 'call' || this.hasConfirmedCall;
    }
  }" class="p-6" class="px-6 py-4 grid grid-cols-2 gap-4 content-between"
    :action="current && @js(route('prescriptions.prepare', '__ID__')).replace('__ID__', current.id)" method="POST">
    @csrf
    @method('PUT')

    <h2 class="text-lg font-medium text-gray-900">
      Classer préparée ?
    </h2>

    <p class="mt-1 text-sm text-gray-600">
      Classer l'ordonnance de <span
        x-text="`${current?.patient_first_name} ${current?.patient_last_name.toUpperCase()}`"></span> comme préparée ?
    </p>

    <template x-if="current?.patient_contact_method === 'call'">
      <div class="mt-3 space-y-2">
        <div class="flex items-center gap-2">
          <input id="confirm_contacted" type="checkbox" x-model="hasConfirmedCall"
            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">

          <label for="confirm_contacted" class="text-sm text-gray-700 cursor-pointer">
            Je confirme avoir contacté cette personne
          </label>
        </div>
      </div>
    </template>

    <template x-if="current?.patient_contact_method !== 'call'">
      <div class="mt-3 space-y-2">
        <div class="flex items-center gap-2">
          <input id="notify" name="notify" type="checkbox" checked
            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">

          <label for="notify" class="text-sm text-gray-700 cursor-pointer">
            Je confirme vouloir notifier cette personne
          </label>
        </div>
      </div>
    </template>

    <div class="mt-6 flex justify-end">
      <x-secondary-button x-on:click="$dispatch('close')">
        Annuler
      </x-secondary-button>

      <x-primary-button ::disabled="!canConfirm()" class="ms-3" ::class="{
      'bg-gray-300': !canConfirm(), 
      'hover:bg-gray-300' : !canConfirm(), 
      'active:bg-gray-300' : !canConfirm(), 
      'focus:bg-gray-300' : !canConfirm()
      }">
        Confirmer
      </x-primary-button>
    </div>

  </form>
</x-modal>