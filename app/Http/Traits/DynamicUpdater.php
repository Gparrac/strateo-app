<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\DB;

trait DynamicUpdater
{
    public static function dynamicUpdate($request, $third, $userId)
    {
        DB::table('code_ciiu_thirds')->where('thirds_id',$third['id'])->update(['status' => 'I']);
        if($request->has('secondary_ciiu_ids')){
            foreach ($request['secondary_ciiu_ids'] as $key => $value) {
                $codes = DB::table('code_ciiu_thirds')->where('thirds_id',$third['id'])->where('code_ciiu_id', $value);
                if($codes->count() == 0){
                    $third->secondaryCiius()->attach($value,[
                        'status' => 'A',
                        'users_id' => $userId,
                    ]);
                }else{
                    $codes->update([
                        'status' => 'A',
                        'users_update_id' => $userId
                    ]);
                }
            }
        }
    }

}
