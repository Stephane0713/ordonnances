@props(['label', 'name'])

<a href="{{ request()->fullUrlWithQuery(['orderBy' => $name, 'desc' => !request('desc')]) }}">
  <span>{{ $label }}</span>
  @if(request('orderBy') === $name && request(key: 'desc'))
    <i class="fa-solid fa-caret-down"></i>
  @elseif(request('orderBy') === $name)
    <i class="fa-solid fa-caret-up"></i>
  @endif
</a>