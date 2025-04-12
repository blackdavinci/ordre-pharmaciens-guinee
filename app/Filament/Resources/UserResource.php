<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Grid::make(12)
                        ->schema([
                            Grid::make()
                                ->columnSpan(12)
                                ->schema([
                                    Select::make('roles')
                                        ->relationship(name: 'roles', titleAttribute: 'name')
                                        ->label('Rôle')
                                        ->options(function () {
                                            $user = Auth::user();
                                            if ($user->hasRole('super_admin')) {
                                                return Role::all()->pluck('name', 'id');
                                            } else {
                                                // For non-admin users, exclude certain roles
                                                return Role::whereNotIn('name', ['super_admin'])->pluck('name', 'id');
                                            }
                                        })
                                        ->preload()
                                        ->required()
                                        ->columnSpanFull(),
                                    TextInput::make('nom')
                                        ->required(),
                                    TextInput::make('prenom')
                                        ->label('Prénom')
                                        ->required(),
                                    PhoneInput::make('telephone')
                                        ->defaultCountry('GN')
                                        ->validateFor(
                                            lenient: true, // default: false
                                        )
                                        ->required(),
                                    TextInput::make('email')
                                        ->email()
                                        ->unique(User::class, 'email', fn ($record) => $record)
                                        ->required(),
                                ])

                        ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')
                    ->label('Nom')
                    ->formatStateUsing(function ($state, User $user) {
                        return $user->nom . ' ' . $user->prenom;
                    }),
                TextColumn::make('email')
                    ->label('E-mail'),
                TextColumn::make('telephone')
                    ->label('Phone'),
                TextColumn::make('roles.name')
                    ->badge()
                    ->label('Rôle')
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->colors(['primary'])
                    ->searchable(),
                ToggleColumn::make('statut')
                    ->label('Statut'),

            ])
            ->modifyQueryUsing(function (Builder $query) {
                if (!auth()->user()->hasRole('super_admin')) {
                    return $query->withoutRole('super_admin');
                }
            })
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hiddenLabel()
                    ->color('primary'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}/show'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
