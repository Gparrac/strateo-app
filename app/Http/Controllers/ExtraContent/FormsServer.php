<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FormsServer extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        if ($request->has('format') and $request->input('format') == 'routes-available') {
            $forms = $this->routesAvailable();
        } else {
            $forms = $this->allRecords();

        }

            // $forms = $forms->select('id','name')->get();
        // $forms = City::where('status','A')->select('id','name','image1')->get();
        return response()->json(['message' => 'Read: ', 'data' => $forms], 200);
    }
    public function routesAvailable(){
        Log::info(Auth::id());
        $roleUser = User::find(Auth::id())->role_id;
        // $sections = Role::join('permission_roles', 'roles.id', '=', 'permission_roles.role_id')
        // ->join('forms', 'forms.id', '=', 'permission_roles.form_id')
        // ->join('sections', 'forms.section_id', '=', 'sections.id')
        // ->where('permission_roles.role_id', $roleUser)
        // ->where('permission_roles.status', 'A')
        // ->select('sections.id', 'sections.name')
        // ->groupBy('sections.id', 'sections.name')
        // ->with(['forms' => function ($query) use ($roleUser) {
        //     $query->join('permission_roles', 'forms.id', '=', 'permission_roles.form_id')
        //         ->where('permission_roles.role_id', $roleUser)
        //         ->where('permission_roles.status', 'A')
        //         ->select('forms.id', 'forms.name')
        //         ->groupBy('forms.id', 'forms.name');
        // }])
        // ->get();
        $sections = Role::join('permission_roles','roles.id','permission_roles.role_id')
        ->join('forms','forms.id','permission_roles.form_id')
        ->join('sections','forms.section_id','sections.id')
        ->where('permission_roles.role_id', $roleUser)
        ->where('permission_roles.status','A')->select('sections.id','sections.name')
        ->groupBy('sections.id','sections.name') // Solo agrupar por sections.id
        ->get();
        foreach ($sections as $key => $section) {
            $sections[$key]['forms'] = Role::join('permission_roles','roles.id','permission_roles.role_id')
            ->join('forms','forms.id','permission_roles.form_id')
            ->where('permission_roles.role_id', $roleUser)
            ->where('permission_roles.status','A')
                ->where('forms.section_id',$section['id'])->select('forms.id','forms.name','forms.href', 'forms.icon')->groupBy('forms.id','forms.name')->get();
        }
         return $sections;
    }
    public function allRecords(){
        return  Form::join('sections','forms.section_id','sections.id')->select('forms.id','forms.name', 'sections.name as section_name')->get();
    }
}
