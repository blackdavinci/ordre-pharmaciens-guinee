<x-filament::section>
    <x-slot name="heading">
        Inscription Ordre National des Pharmaciens de Guinée
    </x-slot>

    <x-slot name="description">
        This is all the information we hold about the user.
    </x-slot>

    {{-- Content --}}
    <form wire:submit="create" class="space-y-6" enctype="multipart/form-data">
        {{ $this->form }}

        <button type="submit"
                class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition">
            Soumettre la demande
        </button>

        <!-- Ajouter un bouton pour enregistrer localement -->
        <button type="button"
                wire:click.prevent="saveLocally"
        class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition">
        Enregistrer
        </button>

    </form>

    <x-filament-actions::modals />
</x-filament::section>



