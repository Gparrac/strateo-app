<?php

namespace App\Http\Controllers\CRUD\CategoryParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Category;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {

        if ($request->has('category_id')) {
            return $this->singleRecord($request->input('category_id'));
        } else {
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Category::where('id', $id)
            ->select('id', 'name', 'code', 'status', 'updated_at')
            ->with(['products' => function($query){
                $query->select('products.id', 'consecutive', 'name', 'measure_id', 'brand_id', 'product_code')
                    ->where('categories_products.status', 'A')
                    ->with([
                        'brand'=> function($query){
                            $query->where('status','A')->select('id','name');
                        },'measure'=> function($query){
                            $query->where('status','A')->select('id','symbol');
                        }
                    ]);
            }])
            ->first();

            return response()->json(['message' => 'read: ' . $id, 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error CategoryResource@read:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error CategoryResource@read:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $filters = [], $format = null)
    {
        try {
            $data = Category::select('id', 'name', 'code', 'status', 'updated_at');
            //filter query with keyword 🚨
            foreach ($filters as $filter) {
                switch ($filter['key']) {
                    case 'name':
                        $data = $data->whereRaw("UPPER(name) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
                        break;
                    case 'code':
                            $data = $data->whereRaw("UPPER(code) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
                        break;
                    case 'status':
                        $data = $data->whereIn('status', $filter['value']);
                        break;
                    default:
                    $data = $data->where('id','LIKE', '%' . $filter['value'] . '%');
                        break;
                }
            }
            if ($format == 'short') {
                $data = $data->where('status','A')->select('id','name')->get();
            } else {
            //append shorters to query
            foreach ($sorters as $shorter) {
                $data = $data->orderBy($shorter['key'], $shorter['order']);
            }
            $data = $data->paginate($pagination);
        }
            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error CategoryResource@read:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error CategoryResource@read:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
