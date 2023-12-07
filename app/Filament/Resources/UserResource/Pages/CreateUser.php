<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Third;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation($data):Model
    {
        $third = new Third();
        $third->type_document = $data['type_document'];
        $third->identification = $data['identification'];
        $third->verification_id = 1;
        $third->names = $data['names'];
        $third->surnames = $data['surnames'];
        $third->business_name = $data['business_name'];
        $third->address = $data['address'];
        $third->mobile = $data['mobile'];
        $third->email = $data['email'];
        $third->email2 = $data['email2'];
        $third->users_id = 1;
        $record = static::getModel()::create($data);
        $third->save();
        return $record;
    }
}
