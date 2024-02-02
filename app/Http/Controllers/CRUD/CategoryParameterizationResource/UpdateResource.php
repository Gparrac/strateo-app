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

            $category = Category::where('id', $request->input('category_id'))->firstOrFail();
            // Create a record in the category table
            $category->update([
                'name' => $request->input('name'),
                'code' => $request->input('code'),
                'status' => $request->input('status'),
                'users_update_id' => $userId,
            ]);
            
            //record categories 🚨
            $category->products()->get()->each(function($rCategory) use ($userId, $category){
                $category->products()->updateExistingPivot($rCategory,[
                    'status' => 'I',
                    'users_update_id' => $userId,
                ]);
            });
            foreach ($request['products_ids'] ?? [] as $value) {
                $query = DB::table('categories_products')->where('category_id', $category['id'])->where('product_id',$value);
                if ($query->count() == 0) {
                    $category->products()->attach($value, [
                        'status' => 'A',
                        'users_id' => $userId,
                    ]);
                } else {
                    $query->update([
                        'status' => 'A',
                        'users_update_id' => $userId
                    ]);
                }
            }

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
