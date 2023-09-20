<?php

namespace App\Http\Controllers\Yearly;

use Inertia\Inertia;
use App\Models\Yearly\Bill;
use Illuminate\Http\Request;
use App\Models\Yearly\Voucher;
use App\Printings\VoucherPrint;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Yearly\VoucherDetail;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\VoucherRequest;

class VoucherController extends Controller
{
    public function index(Request $request)
    {

        if (
            Gate::denies('receipt-vouchers') &&
            Gate::denies('payment-vouchers') &&
            Gate::denies('journal-vouchers') &&
            Gate::denies('interest-vouchers') &&
            Gate::denies('transfer-vouchers')
        ) {
            return deny();
        }
            return Inertia::render('ProjectComponents/Transactions/Vouchers/VoucherList',[
                'voucher_types' => voucherTypes()
        ]);

    }

    public function voucherList (Request $request){
        if (
            Gate::denies('receipt-vouchers') &&
            Gate::denies('payment-vouchers') &&
            Gate::denies('journal-vouchers') &&
            Gate::denies('interest-vouchers') &&
            Gate::denies('transfer-vouchers')
        ) { return deny();
        }
        $count = Voucher::where('vouchers.branch_id',getCurrentBranchId())->count();
        $filteredCount = $count;

        $vouchers = Voucher::where('vouchers.branch_id',getCurrentBranchId());

        $v_types = [];
        if (Gate::allows('receipt-vouchers')){
            $v_types[] = ['R'];
        }
        if (Gate::allows('payment-vouchers')){
            $v_types[] = ['P'];
        }
        if (Gate::allows('journal-vouchers')){
            $v_types[] = ['J'];
        }
        if (Gate::allows('interest-vouchers')){
            $v_types[] = ['I'];
        }
        if (Gate::allows('transfer-vouchers')){
            $v_types[] = ['T'];
        }
        $vouchers = $vouchers->whereIn('voucher_type', $v_types);
        if ($searchStr = $request->input('search.value')) {
            $vouchers = $vouchers->where('accounts.name', 'like', "%{$searchStr}%");
        }

        $asc_desc = $request->order[0]['dir'];
        $field_name = 'vouchers.'. $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $vouchers = $vouchers->orderBy($field_name, $asc_desc);
        }
        else{
            $vouchers = $vouchers->orderBy('vouchers.id','DESC');
        }
        $vouchers = $vouchers->take($request->length);
        $filteredCount = $vouchers->count();
        if ($request->start > 0) {
            $vouchers->skip($request->start);
        }
        $vouchers = $vouchers->groupBy('vouchers.id')->select('vouchers.*')->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $vouchers,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store (VoucherRequest $request){
        return $request->save($request);
    }

    public function update (VoucherRequest $request){
        return $request->save($request);
    }

    public function edit(Request $request,$id){
        if($request->voucher_type == 'R'){
            if(Gate::denies('receipt-vouchers-modify')){
                return deny();
            }
        }
        if($request->voucher_type == 'P'){
            if(Gate::denies('payment-vouchers-modify')){
                return deny();
            }
        }
        if($request->voucher_type == 'J'){
            if(Gate::denies('journal-vouchers-modify')){
                return deny();
            }
        }
        if($request->voucher_type == 'I'){
            if(Gate::denies('interest-vouchers-modify')){
                return deny();
            }
        }
        if($request->voucher_type == 'T'){
            if(Gate::denies('transfer-vouchers-modify')){
                return deny();
            }
        }
        $voucher = Voucher::whereId($id)->with([
            'account_tds',
            'account_other',
            'cashbooks',
            'voucher_details.account',
            'voucher_details.section',
            'voucher_details.tds_account',
            'voucher_details.bills',
        ])->first();
        return reply(true, [
            'voucher' => $voucher,
        ]);
    }


    public function VoucherPrint(Request $request,  $id)
    {
        // if (Gate::denies('voucher-print')) {
        //     return deny();
        // }
        $vo = Voucher::find($id);
        $print = new \App\Printings\VoucherPrint();
        $pdf = $print->makepdf($vo, $request->value);
        $pdf->Output("voucher.pdf", 'I');
        exit();
    }


    public function voucherDetailIndex(Request $request)
    {
        if (Gate::denies('vouchers-bill-detail')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Transactions/Vouchers/BillRefDetail');
    }


    public function voucherDetailList (Request $request){
        $count = Voucher::where('vouchers.branch_id',getCurrentBranchId())->count();
        $filteredCount = $count;
        $bills = Bill::select('vcode','det_id',DB::raw("sum(case when dr_cr = 'D' then amount else -1*amount end) as amount"))
                    ->where('branch_id',getCurrentBranchId())
                    ->groupByRaw("1,2");
        $vouchers = Voucher::join('voucher_dets','vouchers.id','voucher_dets.voucher_id')
                            ->join(getSharedDb().'.accounts','accounts.id','voucher_dets.ac_id')
                            ->leftJoinSub($bills,'a1',function($join) {
                                $join->on('a1.vcode','=','vouchers.vcode')
                                    ->on('a1.det_id','=','voucher_dets.id');
                            })->where('vouchers.branch_id',getCurrentBranchId())
                            ->where('accounts.bill_wise','Y')
                            ->where(DB::raw("case when voucher_dets.drcr = 'D' then voucher_dets.amount else -1*voucher_dets.amount end"),'<>',DB::raw("ifnull(a1.amount,0)"));

        // $vouchers = $vouchers->whereIn('voucher_type', $v_types);
        if ($searchStr = $request->input('search.value')) {
            $vouchers = $vouchers->where('accounts.name', 'like', "%{$searchStr}%");
        }

        $asc_desc = $request->order[0]['dir'];
        $field_name = 'vouchers.'. $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $vouchers = $vouchers->orderBy($field_name, $asc_desc);
        }
        else{
            $vouchers = $vouchers->orderBy('vouchers.id','DESC');
        }
        $vouchers = $vouchers->take($request->length);
        $filteredCount = $vouchers->count();
        if ($request->start > 0) {
            $vouchers->skip($request->start);
        }
        $vouchers = $vouchers->select('voucher_dets.*','vouchers.voucher_no','vouchers.vcode','vouchers.voucher_date','accounts.name as ac_name')->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $vouchers,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function billDetailsAdd(Request $request){
        $voucher = VoucherDetail::findOrNew($request->id);
        $billids = $voucher->bills->pluck('id')->toArray();
        $bills = [];
        // dd($request->all());
        foreach ($request->bills as $key =>$billval) {
            $arr = getResultModel($billids, Bill::class, ['branch_id'=>getCurrentBranchId(),'trans_date' => $request->voucher_date, 'ac_id' => $request->ac_id, 'vcode' => $request->vcode] + $billval);
            $bills[$key] = $arr[0];
            $billids = $arr[1];
        }
        DB::connection(getYearlyDbConn())->beginTransaction();
            $voucher->bills()->saveMany($bills);
            Bill::whereIn('id', $billids)->delete();
        DB::connection(getYearlyDbConn())->commit();
        return reply(true, ['voucher' => $voucher]);
    }


}
