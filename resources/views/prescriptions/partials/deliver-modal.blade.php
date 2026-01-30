<x-modal name="deliver">
  <form class="p-6" class="px-6 py-4 grid grid-cols-2 gap-4 content-between"
    :action="current && @js(route('prescriptions.deliver', '__ID__')).replace('__ID__', current.id)" method="POST">
    @csrf
    @method('PUT')

    <h2 class="text-lg font-medium text-gray-900">
      Classer délivrée ?
    </h2>

    <p class="mt-1 text-sm text-gray-600">
      Classer l'ordonnance de <span
        x-text="`${current?.patient.first_name} ${current?.patient.last_name.toUpperCase()}`"></span> comme délivrée ?
    </p>

    <p class="mt-1 text-sm text-gray-600">
      Prochaine délivrance prévue le <span x-text="getNextDeliveryDate().toLocaleDateString('fr-FR')"></span>.
    </p>

    <div class="mt-6 flex justify-end">
      <x-secondary-button x-on:click="$dispatch('close')">
        Annuler
      </x-secondary-button>

      <x-primary-button class="ms-3">
        Confirmer
      </x-primary-button>
    </div>

  </form>
</x-modal>