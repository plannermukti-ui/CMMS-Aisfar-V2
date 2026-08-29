@php
    $themeSettings = app(\App\Settings\ThemeSettings::class);
@endphp
<style>
    @if($themeSettings->sidebar_color)
        .fi-sidebar { background-color: {{ $themeSettings->sidebar_color }} !important; }
        .fi-sidebar-header-ctn { background-color: {{ $themeSettings->sidebar_color }} !important; }
    @endif
    
    @if($themeSettings->header_color)
        .fi-topbar { background-color: {{ $themeSettings->header_color }} !important; }
    @endif
    
    @if($themeSettings->body_background_color)
        body, .fi-body, .fi-main { background-color: {{ $themeSettings->body_background_color }} !important; }
    @endif
</style>
