@props(['method', 'value'])

<div
    x-data="{
        copied: false,
        async copy() {
            await navigator.clipboard.writeText('{{ $value }}');
            this.copied = true;
            setTimeout(() => this.copied = false, 1000);
        }
    }"
    @click="copy"
    class="text-sm group relative cursor-pointer inline-flex items-baseline gap-2"
>
    @if($method === 'call')
        <i class="fa-solid fa-phone"></i>
    @elseif($method === 'sms')
        <i class="fa-solid fa-comment"></i>
    @elseif($method === 'email')
        <i class="fa-solid fa-at"></i>
    @endif

    <div class="hidden absolute group-hover:block left-8 z-50 text-sm bg-white px-4 py-2 rounded shadow overflow-hidden">
      <span>{{ $value }}</span>
      <span class="absolute inset-0 text-center py-2 bg-white" x-show="copied">Copié</span>
    </div>
</div>
