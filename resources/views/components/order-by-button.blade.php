@props(['label', 'name'])

@php
  $query = ['orderBy' => $name];
  $query['desc'] = request('orderBy') === $name ? !request('desc') : config('const.default.direction');
@endphp

<a href="{{ request()->fullUrlWithQuery($query)}}">
  <span>{{ $label }}</span>
  @if(request('orderBy') === $name && request(key: 'desc'))
    <i class="fa-solid fa-caret-down"></i>
  @elseif(request('orderBy') === $name)
    <i class="fa-solid fa-caret-up"></i>
  @endif
</a>