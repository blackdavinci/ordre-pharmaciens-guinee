<div class="flex items-center gap-2">

    {{-- If you need a view button as well --}}
    <a href="{{ $getRecord()->getUrl() }}" target="_blank" class="filament-link inline-flex items-center justify-center px-3 py-1 bg-primary-500 text-white rounded-lg hover:bg-gray-600 transition">
        <span>Voir</span>
    </a>
    <a href="{{ $getRecord()->getUrl() }}" download class="filament-link inline-flex items-center justify-center px-3 py-1 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition">
        <span>Télécharger</span>
    </a>
</div>
