@php
    $sortCol = request()->query('sort');
    $sortDir = strtolower(request()->query('dir')) === 'desc' ? 'desc' : 'asc';
    $isActive = isset($key) && $sortCol === $key;
    $nextDir = ($isActive && $sortDir === 'asc') ? 'desc' : 'asc';
    $arrow = $isActive ? ($sortDir === 'asc' ? '&uarr;' : '&darr;') : '';
    $q = request()->query();
    $q['sort'] = $key;
    $q['dir'] = $nextDir;
@endphp
<a href="{!! e(url()->current() . '?' . http_build_query($q)) !!}" class="{{ $class ?? '' }} text-decoration-none text-reset" style="cursor:pointer;">{{ $label }}<span class="ms-1">{!! $arrow !!}</span></a>
