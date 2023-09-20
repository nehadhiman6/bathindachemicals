<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Accounts\LimitAccount;
use App\Models\Masters\TypeMaster;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class    TypeMasterController extends Controller
{
    public function index(Request $request){

        $type = $request->getRequestUri();
        $type = str_replace('-','_',ltrim($type, $type[0]));
        return Inertia::render('ProjectComponents/Accounts/TypeMaster/TypeMasterList', [
            'master_type' => $type
        ]);
    }

    public function typeMastersList(Request $request){
        $count = TypeMaster::count();
        $filteredCount = $count;

        $type_masters = TypeMaster::where('type','=', $request['type']);

        if ($searchStr = $request->input('search.value')) {
            $type_masters = $type_masters->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $type_masters = $type_masters->orderBy($field_name, $asc_desc);
        }
        else{
            $type_masters = $type_masters->orderBy('id','DESC');

        }

        $type_masters = $type_masters->take($request->length);
        $filteredCount = $type_masters->count();
        if ($request->start > 0) {
            $type_masters->skip($request->start);
        }
        $type_masters = $type_masters->with('limit_accounts.account')->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $type_masters,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $type_master = TypeMaster::findOrNew($request->form_id);
        $type_master->fill($request->all());
        $old_limit_account  = $type_master->limit_accounts()->pluck('id')->toArray();

        $limit_accounts = new Collection();
        foreach($request->accounts as $account){
            $l_ac = LimitAccount::firstOrNew(['ac_id'=>$account,'limit_sub_group_id'=>$request->form_id]);
            $l_ac->ac_id = $account;
            $l_ac->limit_sub_group_id = $request->form_id;
            $limit_accounts->add($l_ac);
        }
        $new_ids = $limit_accounts->pluck('id')->toArray();
        $detach = array_diff($old_limit_account,$new_ids);

        DB::beginTransaction();
            $type_master->save();
            $type_master->limit_accounts()->saveMany($limit_accounts);
            LimitAccount::whereIn('id',$detach)->delete();
        DB::commit();

        return reply(true, [
            'type_master' => $type_master
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:type_masters,name,' . $request->form_id . ',id,type,' . $request['type'],
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        $type_master = TypeMaster::whereId($id)->with('limit_accounts.account')->first();
        return reply(true, [
            'type_master' => $type_master,
        ]);
    }

    public function update(Request $request, $id)
    {
        return $this->saveForm($request, $id);
    }
}
