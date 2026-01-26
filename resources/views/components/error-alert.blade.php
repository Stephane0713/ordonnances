@props(['errors']);

@if ($errors->any())
  <div x-data="handler" x-show.init="open" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95" class="fixed flex top-16 left-0 right-0">
    <div class="max-w-7xl mx-auto mt-4 bg-red-500 shadow-md py-2 px-4 rounded">
      <div class=" flex justify-between items-baseline">
        <strong class="text-white">
          {{ __('Des erreurs sont survenues :') }}
        </strong>
        <button class="text-white" @click="() => open = false">x</button>
      </div>
      <ul class="mt-2 text-white">
        @foreach ($errors->all() as $message)
          <li>{{ $message }}</li>
        @endforeach
      </ul>
    </div>
  </div>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('handler', () => ({
        open: false,

        init() {
          this.$nextTick(() => {
            this.open = true;
          });
        },
      }));
    });
  </script>
@endif