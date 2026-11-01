{{-- Quick launcher overlay (Cmd/Ctrl+K), driven by resources/assets/js/quicklauncher.js.
     The strings are handed to the script as data attributes so they are translated. --}}
{{-- Only for visitors /search/items is open to: it is guarded by the "allowed"
     middleware, which asks User::isOpenToVisitor(), minus the public_front
     exemption that applies to the dashboard but not here. So a public dashboard
     shows nothing until the visitor has logged in. Not on the login pages. --}}
@if(!Route::is('login', 'register', 'password.*') && $current_user->isOpenToVisitor())
{{-- The bundled Font Awesome has no search icon; the script copies this one into the button and the web search row. --}}
<template id="quicklauncher-search-icon">
    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M15.5 15.5 21 21"/></svg>
</template>
<button type="button" id="quicklauncher-button" class="quicklauncher-button" aria-label="{{ __('app.quicklauncher.open') }}"></button>
<div id="quicklauncher" class="quicklauncher" role="dialog" aria-modal="true" aria-label="{{ __('app.quicklauncher.title') }}"
     data-user="{{ $current_user->getId() }}"
     data-loading="{{ __('app.quicklauncher.loading') }}"
     data-error="{{ __('app.quicklauncher.error') }}"
     data-no-results="{{ __('app.settings.no_items') }}"
     data-search-web="{{ __('app.quicklauncher.search_web') }}">
    <div class="quicklauncher-box">
        <input type="text" class="quicklauncher-input" autocomplete="off" spellcheck="false"
               role="combobox" aria-controls="quicklauncher-list" aria-expanded="false" aria-autocomplete="list"
               placeholder="{{ __('app.quicklauncher.placeholder') }}" aria-label="{{ __('app.quicklauncher.placeholder') }}">
        <div id="quicklauncher-list" class="quicklauncher-list" role="listbox"></div>
    </div>
</div>
@endif
