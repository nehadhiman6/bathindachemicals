<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Models\Accounts\Account;
use App\Models\Attachment\CompanyResource;
use App\Models\Attachment\SharedResource;
use App\Models\Company\SaleContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyResourceController extends Controller
{
    public function saveResources(Request $request, $resources_type){
        $rules = [
            'target_id'=>'required',
        ];
        foreach($request->resources as $key=>$resource){
            $rules  = [
                    'resources.'.$key.'.attachment_id'=>'required|not_in:0',
                    'resources.'.$key.'.doc_description'=>'max:500'];
        }
        $this->validate($request,$rules);
        $target_class = null;
        if($resources_type == 'sale-contracts'){
            $target_class = SaleContract::class;
        }
        $target = $target_class::findOrFail($request->target_id);
        $old_detids = $target->resources->pluck('id')->toArray();

        $resources = new Collection();
        foreach($request->resources as $resource){
            if($resource['attachment_id'] > 0){
                $res = CompanyResource::firstOrNew(['resourceable_type'=>$target_class,'attachment_id'=>$resource['attachment_id'],
                'resourceable_id'=>$request->target_id]);
                $res->attachment_id = $resource['attachment_id'];
                $res->doc_description = $resource['doc_description'];
                $res->year = session()->get('fy');
                $resources->add($res);
            }
        }
        $new_resources = $resources->pluck('id')->toArray();
        $detach = array_diff($old_detids, $new_resources);

        DB::beginTransaction();
            $target->resources()->saveMany($resources);
            CompanyResource::whereIn('id', $detach)->delete();
        DB::commit();
        return reply(true,[
            'resources'=>$target->resources
        ]);
    }

    public function showResources(Request $request, $resources_type,$target_id){
        $target_class = null;
        if($resources_type == 'sale-contracts'){
            $target_class = SaleContract::class;
        }
        $target = $target_class::findOrNew($target_id);
        $target->load('resources.attachment');
        return reply(true,[
            'resources'=>$target->resources
        ]);
    }
}
