<?php

namespace App\Filament\Resources;

use App\Enums\UserDocumentTypeEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
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

                    Section::make('Detalles de usuario')
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('last_name')->label('Apellido')->required(),
                        Select::make('document_type')->options([
                            'C.C' => UserDocumentTypeEnum::CC->value,
                            'T.I' => UserDocumentTypeEnum::TI->value,
                            'C.E' => UserDocumentTypeEnum::CE->value
                        ])->label('Tipo de documento')->required(),
                        TextInput::make('number_document')->numeric()->maxLength(10),
                        TextInput::make('email')->email()->required(),
                        Select::make('third_id')
                        ->relationship('third','business_name')
                        ->required(),
                        Select::make('role_id')
                        ->relationship('role','name')
                        ->required(),

                        TextInput::make('password')->confirmed(),
                        TextInput::make('password_confirmation'),

                    ])->columns(3)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->label('Nombre'),
                TextColumn::make('last_name')
                ->searchable()
                ->sortable()
                ->label('Apellido'),
                TextColumn::make('document_type')
                ->searchable()
                ->sortable()
                ->label('Tipo ID'),
                TextColumn::make('document_number')
                ->searchable()
                ->sortable()
                ->label('ID'),
                TextColumn::make('email'),
                TextColumn::make('third.business_name')->label('Empresa'),

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
