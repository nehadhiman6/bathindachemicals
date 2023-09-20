<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemRequest;
use App\Models\Items\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function index(Request $request){
        return Inertia::render('ProjectComponents/Items/Item/ItemList');
    }

    public function itemsList (Request $request){
        if (Gate::denies('items')) {
            return deny();
        }
        $count = Item::count();
        $filteredCount = $count;

        $items = Item::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $items = $items->where('item_name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $items = $items->orderBy($field_name, $asc_desc);
        }
        else{
            $items = $items->orderBy('id','DESC');

        }

        $items = $items->take($request->length);
        $filteredCount = $items->count();
        if ($request->start > 0) {
            $items->skip($request->start);
        }
        $items = $items->select()->get(['id', 'name']);
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $items,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }


    public function store(ItemRequest $request, $id = 0)
    {
        if (Gate::denies('items-add')) {
            return deny();
        }
        return $request->save($request);
    }




    public function edit($id)
    {
        if (Gate::denies('items-modify') || Gate::denies('items-view')) {
            return deny();
        }
        $item = Item::whereId($id)->first()->load(['item_branches.branch','item_stores.store','main_group','secondary_unit.item_unit','vat_cst',
        'sub_group', 'other_sub_group', 'item_unit', 'pur_ledger_account', 'sale_ledger_account', 'tsf_pur_ledger_account', 'tsf_sale_ledger_account', 'rebate_account', 'gst','tcs_account'
    ]);
        return reply(true, [
            'item' => $item,
        ]);
    }

    public function update(ItemRequest $request, $id)
    {
        if (Gate::denies('items-modify')) {
            return deny();
        }
        return $request->save($request, $id);
    }

    public function getItemDetails($id, Request $request)
    {
        $item =  Item::findOrFail($id);
        $item->load(['gst.gst_types.details']);
        return response()->json([
            'success' => "Item Found",
            'item' => $item,
        ], 200, ['app-status' => 'success']);
    }

}
