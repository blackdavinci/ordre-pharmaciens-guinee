<x-filament-panels::page>
    @if($record)
        {{ $this->infolist }}
    @else
        <div class="flex items-center justify-center p-4">
            <p class="text-gray-500">Vous n'avez pas encore d'inscription.</p>
        </div>
    @endif
</x-filament-panels::page>
