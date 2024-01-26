<?php

namespace App\Http\Controllers\CRUD\CategoryParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();

            $category = Category::findOrFail($request->input('category_id'));
            // Create a record in the category table
            $category->fill($request->only([
                'name' => $request->input('name'),
                'code' => $request->input('code'),
                'status' => $request->input('status'),
            ]) + ['users_update_id' => $userId])->save();

            // Obtén los IDs de productos que se deben mantener y los nuevos IDs del request
            $currentProductIds = $category->products()->pluck('products.id')->toArray();
            $newProductIds = $request->input('products_ids', []);

            // Atributos adicionales solo para los nuevos registros
            $newAttributes = [
                'status' => 'A',
                'users_id' => $userId,
            ];

            // Realizar el attach solo para los nuevos registros
            $category->products()->attach($newProductIds, $newAttributes);

            // Actualizar el estado ('status') de las relaciones desvinculadas en la tabla pivot
            $category->products()->whereIn('products.id', $detachIds)->each(function ($product) {
                // Solo actualizar 'status', sin afectar 'users_id'
                $product->pivot->update([
                    'status' => 'I',
                    'users_update_id' => $userId
                ]);
            });

            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error CategoryResource@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error CategoryResource@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
}
