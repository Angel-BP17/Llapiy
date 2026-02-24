@php
    $logoPath = public_path('img/logo-ugel.png');
@endphp

@if (is_readable($logoPath) && extension_loaded('gd'))
    <img src="file://{{ $logoPath }}" alt="Logo">
@endif
