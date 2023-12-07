<?php

namespace App\Http\Controllers\CRUD\UserParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Models\Third;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        $newThird = Third::create([
            'type_document' => $request['type_document'],
            'identification' => $request['identification'],
            'names' => $request['names'],
            'surnames' => $request['surnames'],
            'address' => $request['address'],
            'mobile' => $request['mobile'],
            'email' => $request['email'],
            'email2' => $request['email2'],
            'city_id' => $request['city_id'],
            'user_id' => Auth::id()
        ]);
        User::create([
            'name' => $request['name'],
            'password' => bcrypt($request['password']),
            'third_id'=> $newThird['id'],
            'role_id' => $request['role_id'],
            'user_id' => Auth::id(),
            'users_update_id' => Auth::id(),
            'status' => $request['status']
        ]);
        return response()->json(['message' => 'Create'], 200);
    }
}
