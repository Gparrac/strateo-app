<?php

namespace App\Filament\Resources\CompanyParameterizationResource\Pages;

use App\Filament\Resources\CompanyParameterizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyParameterizations extends ListRecords
{
    protected static string $resource = CompanyParameterizationResource::class;
    protected static ?string $title = 'Parametrización de Empresa';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
