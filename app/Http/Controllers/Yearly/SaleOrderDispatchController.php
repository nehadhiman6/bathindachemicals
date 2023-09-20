<?php

namespace App\Http\Controllers\Yearly;

use App\Http\Controllers\Controller;
use App\Models\Accounts\Account;
use App\Models\Items\Item;
use App\Models\Masters\Brand;
use App\Models\Masters\Packing;
use App\Models\Yearly\Dispatch;
use App\Models\Yearly\SaleOrder;
use App\Models\Yearly\SaleOrderDetail;
use App\Models\Yearly\SaleOrderDetailPack;
use App\Models\Yearly\SaleOrderDispatch;
use App\Printings\SaleOrderDispatchPrint;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SaleOrderDispatchController extends Controller
{
    public function index(Request $request)
    {
        if (Gate::denies('sale-order-dispatches')) {
            return deny();
        }
            return Inertia::render('ProjectComponents/Sale/SaleOrderDispatch/SaleOrderDipatches',[

        ]);

    }


    public function saleOrderDispatchesList (Request $request){
        if (Gate::denies('sale-order-dispatches')) {
            return deny();
        }
        $count = Dispatch::count();
        $filteredCount = $count;

        $sale_order_dispatches = Dispatch::orderBy('dispatch_advise_no','DESC');

        if ($searchStr = $request->input('search.value')) {
            $sale_order_dispatches = $sale_order_dispatches->where('dispatches.dispatch_advise_no', 'like', "%{$searchStr}%");
        }

        $sale_order_dispatches = $sale_order_dispatches->where('dispatches.branch_id',getCurrentBranchId());
        $asc_desc = $request->order[0]['dir'];
        $field_name = 'dispatches.'. $request->columns[$request->order[0]['column']]['data'];

        if($field_name){
            $sale_order_dispatches = $sale_order_dispatches->orderBy($field_name, $asc_desc);
        }
        $sale_order_dispatches = $sale_order_dispatches->take($request->length);
        $filteredCount = $sale_order_dispatches->count();
        if ($request->start > 0) {
            $sale_order_dispatches->skip($request->start);
        }
        $sale_order_dispatches = $sale_order_dispatches->groupBy('dispatches.id')
        ->select('dispatches.*')->get();

        foreach($sale_order_dispatches as $sale_order_dispatch){
            $sale_orders_ids = preg_split ("/\,/",$sale_order_dispatch['sale_order_ids']);
            $orders = SaleOrder::whereIn('id',$sale_orders_ids)->pluck('sale_order_no')->toArray();
            $sale_order_dispatch->sale_oders_nos = implode(', ',$orders);
        }
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $sale_order_dispatches,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }


    public function getPackings(Request $request){
        if($request->dispatch_id == 0){
            $this->validate($request,
                [
                    'dispatch_date' =>'required|date_format:d-m-Y',
                    'sale_order_ids' =>'required|Array|min:1',
                    'dispatch_advise' =>'required|in:Y,N'
                ]
            );
        }
        if (Gate::denies('sale-order-dispatches-add')) {
            return deny();
        }


        $dispatch = Dispatch::where('id',$request->dispatch_id)->first();
        if($dispatch){
            $dispatch->load('sale_order_dispatches');
            if($dispatch->sale_order_ids){
                $sale_orders_ids = preg_split ("/\,/",$dispatch->sale_order_ids);
                $orders = SaleOrder::whereIn('id',$sale_orders_ids)->get();
                $dispatch['sale_orders'] = $orders;
            }
        }

        $sale_orders_ids = $request->sale_order_ids;
        if($dispatch){
            $sale_orders_ids = preg_split ("/\,/",$dispatch['sale_order_ids']);
        }

        $packings =  SaleOrderDetailPack::join('sale_order_dets', 'sale_order_dets.id', '=', 'sale_order_det_packs.sale_order_det_id')
            ->whereIn('sale_order_dets.sale_order_id', $sale_orders_ids)
            ->select('sale_order_dets.item_id', 'sale_order_det_packs.packing_id', 'sale_order_det_packs.brand_id','sale_order_dets.sale_order_id',
             'sale_order_det_packs.qty');

        $packings2 = SaleOrderDetail::
        whereIn('sale_order_id', $sale_orders_ids)
        ->where('foc_item_id', '!=', null)
        ->where('foc_packing_id', '!=', null)
        ->where('foc_qty', '!=', null)
        ->select('foc_item_id as item_id', 'foc_packing_id as packing_id','sale_order_dets.foc_brand_id','sale_order_id',
        'foc_qty as qty');

        $packings->union($packings2);
        $results = SaleOrder::joinSub($packings,'a1','sale_orders.id','=','a1.sale_order_id')
                ->join(getSharedDb().'.items','items.id','=','a1.item_id')
                ->join(getSharedDb().'.brands','brands.id','=','a1.brand_id')
                ->join(getSharedDb().'.packings','packings.id','=','a1.packing_id')
                ->join(getSharedDb().'.accounts','accounts.id','=','sale_orders.bill_party_id')
                ->join(getSharedDb().'.account_details','account_details.ac_id','=','sale_orders.bill_party_id')
                ->join(getSharedDb().'.cities','account_details.city_id','=','cities.id')
                ->select('sale_orders.sale_order_no','items.item_name','brands.name as brand_name','packings.name as packing_name',
                    'a1.item_id','a1.brand_id','a1.packing_id','a1.sale_order_id','cities.name as default_destination','accounts.name as bill_party',
                    DB::raw("sum(a1.qty) as total_qty")
                )->groupByRaw("1,2,3,4,5,6,7,8,9,10");

            $results = $results->get();

        $saved = false;

        if($request->dispatch_advise == 'N' && $request->dispatch_id == 0) {
            $saved = true;
            $this->saveDefaultDestination($results, $request);
        }

        return reply(true,[
            'dispatch' => $dispatch,
            'packings' => $results,
            'saved' => $saved
        ]);
    }

    public function saveDefaultDestination($results, $data){

        $this->validateForm($data);
        $current_branch_id = getCurrentBranchId();
        validateBranch();
        $branch_prefix = validatePrefix('sale_order_dispatch');
        $dispatch  = new Dispatch();
        $dispatch->fill($data->all());
        $dispatch->sale_order_ids = implode(', ', $data['sale_order_ids']);

        $destinations_packing = new Collection();
        foreach($results as $result){
            $destination_dispatch = new SaleOrderDispatch();
            $destination_dispatch->item_id  = $result['item_id'];
            $destination_dispatch->packing_id  = $result['packing_id'];
            $destination_dispatch->brand_id  = $result['brand_id'];
            $destination_dispatch->qty  = $result['total_qty'];
            $destination_dispatch->sale_order_id  = $result['sale_order_id'];
            $destination_dispatch->destination  = $result['default_destination'];
            $destinations_packing->add($destination_dispatch);
        }

        DB::connection(getYearlyDbConn())->beginTransaction();
            $dispatch->save();
            $dispatch->vcode = 'DO' . $dispatch->id;
            $generated_no = getNextSeriesNumberCompany('DO_'.$branch_prefix);
            $dispatch->dispatch_advise_no_part =$generated_no;
            $dispatch->dispatch_advise_no = $branch_prefix . $generated_no;
            $dispatch->branch_id= $current_branch_id;
            $dispatch->save();
            $dispatch->sale_order_dispatches()->saveMany($destinations_packing);
        DB::connection(getYearlyDbConn())->commit();

    }

    private function validateForm($request, $rules = []){
        $rules [] =
        [
            'dispatch_date' =>'required|date_format:d-m-Y',
            'dispatch_advise'=>'required|in:Y,N',
            'uid' => 'nullable|unique:' . getYearlyDbConn() . '.dispatches,uid,' . $request->dispatch_id
        ];
        $this->validate($request,$rules);
    }

    public function store(Request $request){
        if (Gate::denies('sale-order-dispatches-modify')) {
            return deny();
        }
        $rules =[
            'destinations.*destination' => 'required|distinct'
        ];
        $this->validateForm($request,
        $rules =[
            'destinations.*destination' => 'required|distinct'
        ]
        );

        $current_branch_id = getCurrentBranchId();
        validateBranch();
        $branch_prefix = validatePrefix('sale_order_dispatch');


        $dispatch = Dispatch::findOrNew($request->dispatch_id);
        $dispatch->fill($request->all());
        $old_dispatch_ids =$dispatch->sale_order_dispatches()->pluck('id')->toArray();
        $destinations_packing = new Collection();

        $dispatch->sale_order_ids = implode(', ', $request['sale_order_ids']);
            foreach($request['destinations'] as $destination){
                foreach($destination['packings'] as $packing){
                if($destination['destination'] !="" && $packing['qty'] > 0){
                    $destination_dispatch = SaleOrderDispatch::findOrNew($packing['id']);
                    $destination_dispatch->item_id  = $packing['item_id'];
                    $destination_dispatch->packing_id  = $packing['packing_id'];
                    $destination_dispatch->brand_id  = $packing['brand_id'];
                    $destination_dispatch->qty  = $packing['qty'];
                    $destination_dispatch->sale_order_id  = $packing['sale_order_id'];
                    $destination_dispatch->destination  = $destination['destination'];
                    $destinations_packing->add($destination_dispatch);
                }
            }
        }
        $new_ids = $destinations_packing->pluck('id')->toArray();
        $detach = array_diff($old_dispatch_ids,$new_ids);


        DB::connection(getYearlyDbConn())->beginTransaction();
            if($request->dispatch_id > 0){
                $uid = addUid($dispatch->uid);
            }

            $dispatch->save();
            if($request->dispatch_id ==0){
                $dispatch->vcode = 'DO' . $dispatch->id;
                $generated_no = getNextSeriesNumberCompany('DO_'.$current_branch_id);
                $dispatch->dispatch_advise_no_part =$generated_no;
                $dispatch->dispatch_advise_no = getBranchPrefix('sale_order_dispatch') . $generated_no;
            }
            $dispatch->branch_id= $current_branch_id;
            $dispatch->save();
            $dispatch->sale_order_dispatches()->saveMany($destinations_packing);
            SaleOrderDispatch::whereIn('id',$detach)->delete();
        DB::connection(getYearlyDbConn())->commit();

        return reply(true,[
        ]);
    }

    public function getSaleOrderDispatchPrint(Request $request,$id){
        if (Gate::denies('sale-order-dispatch-print')) {
            return deny();
        }
        $sale_order_dispatch = Dispatch::findOrFail($id);
        $print = new SaleOrderDispatchPrint();
        $pdf = $print->makepdf($sale_order_dispatch);
        $pdf->Output("SaleOrderDispatch_".$sale_order_dispatch['dispatch_advise_no'].".pdf", 'I');
        exit();
    }
}
