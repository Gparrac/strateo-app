<?php

namespace App\Filament\Resources;

use App\Enums\UserDocumentTypeEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Third;
use App\Models\User;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                ->schema([
                Section::make('Detalles de usuario (tercero)')
                ->schema([
                    TextInput::make('names')->label('Nombres')->required(),
                    TextInput::make('surnames')->label('Apellidos')->required(),
                    Select::make('type_document')->options([
                        'C.C' => UserDocumentTypeEnum::CC->value,
                        'T.I' => UserDocumentTypeEnum::TI->value,
                        'C.E' => UserDocumentTypeEnum::CE->value
                    ])->label('Tipo de documento')->required(),
                    TextInput::make('identification')->label('Número de documento')->numeric()->maxLength(10),
                    TextInput::make('email2')->email(),
                    TextInput::make('business_name')->label('Empresa')->required(),
                    TextInput::make('address')->label('Dirección')->required()->columnSpan(2),
                    TextInput::make('mobile')->label('Telefono')->required()->columnSpan(2),
                ])->columns(2),
            ]),
                Group::make()
                ->schema([
                Section::make('Cuenta de usuario')
                    ->schema([
                        TextInput::make('name')->label('Apodo')->required(),
                        TextInput::make('email')->email()->required(),
                        Select::make('role_id')
                        ->label('Rol')
                        ->relationship('role','name')
                        ->required(),
                        TextInput::make('password')->confirmed()->label('Contraseña'),
                        TextInput::make('password_confirmation')->label('Confirmación de contraseña')
                    ]),
                    ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->label('Usuario'),
                TextColumn::make('third.surnames')
                ->searchable()
                ->sortable()
                ->label('Apellido'),
                TextColumn::make('third.type_document')
                ->searchable()
                ->sortable()
                ->label('Tipo ID'),
                TextColumn::make('third.identification')
                ->searchable()
                ->sortable()
                ->label('ID'),
                TextColumn::make('email'),
                TextColumn::make('email')->label('Correo'),

            ])
            ->filters([
                //
            ])
            ->actions([
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
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
