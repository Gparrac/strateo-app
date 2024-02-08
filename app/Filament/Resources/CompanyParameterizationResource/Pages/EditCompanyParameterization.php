<?php

namespace App\Filament\Resources\CompanyParameterizationResource\Pages;

use App\Filament\Resources\CompanyParameterizationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyParameterization extends EditRecord
{
    protected static string $resource = CompanyParameterizationResource::class;
    protected static ?string $title = 'Editar Empresa';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
