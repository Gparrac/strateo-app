<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyParameterizationResource\Pages;
use App\Filament\Resources\CompanyParameterizationResource\RelationManagers;
use App\Models\Third;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

use App\Enums\UserDocumentTypeEnum;

class CompanyParameterizationResource extends Resource
{
    protected static ?string $model = Third::class;

    protected static ?string $navigationLabel = 'Parametrización de Empresa';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->columns(2)
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
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            RelationManagers\CompanyRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyParameterizations::route('/'),
            'create' => Pages\CreateCompanyParameterization::route('/create'),
            'edit' => Pages\EditCompanyParameterization::route('/{record}/edit'),
        ];
    }
}
