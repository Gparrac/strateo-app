<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Third;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['type_document'] = $this->record->third->type_document;
        $data['identification'] = $this->record->third->identification;
        $data['verification_id'] = $this->record->third->verification_id;
        $data['names'] = $this->record->third->names;
        $data['surnames'] = $this->record->third->surnames;
        $data['business_name'] = $this->record->third->business_name;
        $data['address'] = $this->record->third->address;
        $data['mobile'] = $this->record->third->mobile;
        $data['email2'] = $this->record->third->email2;
        return $data;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = $this->record->id;
        if(!$data['password']) $data['password'] = $this->record->pasword;
        return $data;
    }
    protected function handleRecordUpdate($record, $data): Model{
        Third::find($this->record->third->id)->update([
            'identification' => $data['identification'],
            'type_document' => $data['type_document'],
            'verification_id' => 1,
            'names' => $data['names'],
            'surnames' => $data['surnames'],
            'business_name' => $data['business_name'],
            'address' => $data['address'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'email2' => $data['email2'],
        ]);
        $record->update($data);

        return $record;
    }
}

