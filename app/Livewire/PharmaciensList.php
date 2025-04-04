<?php

namespace App\Livewire;

use App\Models\Inscription;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PharmaciensList extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Inscription::query()
                    ->where('statut', 'validé')
                    ->whereNotNull('user_id')
            )
            ->columns([
                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable(),
                TextColumn::make('telephone_mobile')
                    ->label('Téléphone')
                    ->sortable(),
                TextColumn::make('profil')
                    ->label('Profil'),
                TextColumn::make('section')
                    ->label('Section'),
                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->date('d/m/Y'),
            ]);
    }

    public function render()
    {
        return view('livewire.pharmaciens-list');
    }
}
