<x-modal name="delete">
  <form class="p-6" class="px-6 py-4 grid grid-cols-2 gap-4 content-between"
    :action="current && @js(route('prescriptions.destroy', '__ID__')).replace('__ID__', current.id)" method="POST">
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