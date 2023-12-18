<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use App\Models\Form;


class FormsServer extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $forms = Form::join('sections','forms.section_id','sections.id')->select('forms.id','forms.name', 'sections.name as section_name')->get();
            // $forms = $forms->select('id','name')->get();
        // $forms = City::where('status','A')->select('id','name','image1')->get();
        return response()->json(['message' => 'Read: ', 'data' => $forms], 200);
    }
}
