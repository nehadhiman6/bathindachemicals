<?php

namespace App\Http\Controllers\Yearly;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Printings\IssuePrint;
use App\Models\Yearly\Transfer;
use App\Printings\ReceiptPrint;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\TransferRequest;

class TransferController extends Controller
{
    public function index(Request $request){
        $type = $request->getRequestUri();
        $type = str_replace('-','_',ltrim($type, $type[0]));
        return Inertia::render('ProjectComponents/Transfers/TransferList',[
            'master_type' => $type,
        ]);
    }

    public function transferList (Request $request,$type){
        if (Gate::denies('issue') || Gate::denies('receipt')) {
            return deny();
        }
        // dd($type);
        $br_field = 'transfers.branch_id_from';
        if($type == 'I'){
            $br_field = 'transfers.branch_id_to';
        }
        $count = Transfer::count();
        $filteredCount = $count;
        $transfers = Transfer::join(getSharedDb().'.branches',$br_field,'=','branches.id');
        if($type == 'I'){
            $transfers = $transfers->where('transfers.branch_id_from',getCurrentBranchId());
        }else{
            $transfers = $transfers->where('transfers.branch_id_to',getCurrentBranchId());
        }
        if ($searchStr = $request->input('search.value')) {
            $transfers = $transfers->where('accounts.name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = 'transfers.'. $request->columns[$request->order[0]['column']]['data'];

        if($request->columns[$request->order[0]['column']]['data'] =='client_name'){
            $field_name = 'accounts.name';
        }

        if($field_name){
            $transfers = $transfers->orderBy($field_name, $asc_desc);
        }
        else{
            $transfers = $transfers->orderBy('transfers.id','DESC');
        }
        $transfers = $transfers->take($request->length);
        $filteredCount = $transfers->count();
        if ($request->start > 0) {
            $transfers->skip($request->start);
        }
        $transfers = $transfers->select('transfers.*','branches.name as branch')->get()->load('transport','branch_to','branch_from');
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $transfers,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(TransferRequest $request)
    {
        if (Gate::denies('issue-add') || Gate::denies('receipt-add')) {
            return deny();
        }
        return $request->save();
    }



    public function edit($id)
    {
        if (Gate::denies('issue-modify') || Gate::denies('receipt-modify')) {
            return deny();
        }
        $transfer = Transfer::findOrFail($id);
        $transfer->load(['details.item','details.item_unit','details.packing','transport','branch_to','branch_from']);
        return reply('ok', [
            'success' => true,
            'transfer' => $transfer
        ]);
    }

    public function getTransferPrint(Request $request,$id){
        if (Gate::denies('issue-print') || Gate::denies('receipt-print')) {
            return deny();
        }
        $transfer = Transfer::findOrFail($id);
        if($transfer->status == 'I'){
            $print = new IssuePrint();
            $pdf = $print->makepdf($transfer);
            $pdf->Output("Issue.pdf", 'I');
        }
        else{
            $print = new ReceiptPrint();
            $pdf = $print->makepdf($transfer);
            $pdf->Output("Receipt.pdf", 'I');
        }
        exit();
    }


    public function update(TransferRequest $request, $id)
    {
        if (Gate::denies('issue-modify') || Gate::denies('receipt-modify')) {
            return deny();
        }
        return $request->save();
    }

    public function getSlipWt($slip_no){
        $item = [];
        $item['total_wt'] = '0.00';
        $item['tare_wt'] = '0.00';
        $item['net_wt'] = '0.00';
        $msg ='';
        return reply('ok', [
            'success' => true,
            'item' => $item,
            'msg' => $msg
        ]);

    }
}
