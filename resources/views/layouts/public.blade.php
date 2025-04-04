<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles
    @livewireStyles
    <!-- Utilisation du query string personnalisé -->
    <link href="{{ asset('css/filament/filament/app.css?v=3.3.5.0') }}" rel="stylesheet">
    @vite('resources/css/app.css')
</head>

<body class="antialiased">

{{ $slot }}

@filamentScripts
@livewireScripts
@vite('resources/js/app.js')

<script>
    document.addEventListener('livewire:init', () => {
        // Charger les données sauvegardées au montage du composant
        const savedData = localStorage.getItem('pharmacienFormData');
        if (savedData) {
            Livewire.dispatch('hydrateForm', JSON.parse(savedData));
        }

        // Écouter l'événement pour sauvegarder les données
        Livewire.on('save-to-local-storage', (data) => {
            localStorage.setItem('pharmacienFormData', JSON.stringify(data));
        });

        // Écouter l'événement pour effacer les données
        Livewire.on('clear-local-storage', () => {
            localStorage.removeItem('pharmacienFormData');
        });
    });
</script>

</body>
</html>
