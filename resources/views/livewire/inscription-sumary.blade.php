<div class="space-y-6">
    <div class="p-4 bg-gray-50 rounded-lg">
        <h3 class="text-lg font-medium mb-4 flex justify-between items-center">
            <span>Informations personnelles</span>
            <button type="button" wire:click="$dispatch('setWizardStep', { step: 0 })"
                    class="text-primary-600 hover:text-primary-800 text-sm font-normal">
                Modifier
            </button>
        </h3>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500">Nom complet</p>
                <p>{{ $formData['prenom'] }} {{ $formData['nom'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Genre</p>
                <p>{{ $formData['genre'] === 'masculin' ? 'Masculin' : 'Féminin' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Date de naissance</p>
                <p>{{ \Carbon\Carbon::parse($formData['date_naissance'])->format('d/m/Y') }}</p>
            </div>
            @if(isset($formData['photo_identite']))
                <div>
                    <p class="text-sm text-gray-500">Photo d'identité</p>
                    <img src="{{ is_array($formData['photo_identite']) ? $formData['photo_identite'][0]->temporaryUrl() : '#' }}"
                         class="h-20 w-20 object-cover rounded">
                </div>
            @endif
            <!-- Ajoutez d'autres champs selon vos besoins -->
        </div>
    </div>

    <div class="p-4 bg-gray-50 rounded-lg">
        <h3 class="text-lg font-medium mb-4 flex justify-between items-center">
            <span>Coordonnées</span>
            <button type="button" wire:click="$dispatch('setWizardStep', { step: 1 })"
                    class="text-primary-600 hover:text-primary-800 text-sm font-normal">
                Modifier
            </button>
        </h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p>{{ $formData['email'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Téléphone mobile</p>
                <p>{{ $formData['telephone_mobile'] }}</p>
            </div>
            <!-- Ajoutez d'autres champs selon vos besoins -->
        </div>
    </div>

    <!-- Répétez pour chaque section du formulaire -->
</div>
