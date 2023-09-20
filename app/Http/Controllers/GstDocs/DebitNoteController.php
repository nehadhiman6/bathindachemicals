<?php

namespace App\Http\Controllers\GstDocs;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\GstDocs\GstDocument;
use App\Printings\GstDocumentPrint;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\GstDocumentRequest;

class DebitNoteController extends Controller
{
    public function index(Request $request){
        $type = $request->getRequestUri();
        $type = str_replace('-','_',ltrim($type, $type[0]));
        $gstReasons =  getGstReasons();
        // dd(getSessionYear());
        return Inertia::render('ProjectComponents/GstDocs/GstDocumentList', [
            'master_type' => $type,
           'gst_reasons' => $gstReasons,
           'years' =>getSessionYear()

        ]);
    }

    public function debitNotList (Request $request){
        if (Gate::denies('debit-note')) {
            return deny();
        }
        $asc_desc = $request->order[0]['dir'];
            $field_name = $request->columns[$request->order[0]['column']]['data'];
            $count = GstDocument::where('doc_type', 'DN')->get()->count();
            $filteredCount = $count;

            $gst_docs = GstDocument::join(getSharedDb() . '.accounts', 'gst_docs.ac_id', '=', 'accounts.id')
                ->where('gst_docs.branch_id',getCurrentBranchId())
                ->where('doc_type', 'DN')->with(['details', 'account'])
                ->select('gst_docs.*')->orderBy($field_name, $asc_desc);

            if ($searchStr = $request->input('search.value')) {
                $gst_docs = $gst_docs->where(function ($q) use ($searchStr) {
                    $q->where('accounts.name', 'like', "%{$searchStr}%")
                        ->orWhere('gst_docs.id', 'like', "%{$searchStr}")
                        ->orWhere('gst_docs.doc_no', 'like', "%{$searchStr}");
                });
                $filteredCount = $gst_docs->count();
            }

            $gst_docs = $gst_docs->take($request->length);
            $filteredCount = $gst_docs->count();
            if ($request->start > 0) {
                $gst_docs->skip($request->start);
            }
            $gst_docs = $gst_docs->get();
            return [
                'draw' => intval($request->draw),
                'start' => $request->start,
                'data' => $gst_docs,
                'recordsTotal' => $count,
                'recordsFiltered' => $filteredCount,
            ];
    }


    public function store(GstDocumentRequest $request)
    {
        if (Gate::denies('debit-note-add')) {
            return deny();
        }
        return $request->save();
    }



    public function edit($id)
    {
        if (Gate::denies('debit-note-modify')) {
            return deny();
        }
        $gst_doc = GstDocument::findOrFail($id);
        $gst_doc->load(['details.gst.gst_types.details', 'details.item','details.packing','details.brand', 'details.doc_account', 'account', 'account.account_yearly', 'ac_less_one', 'ac_less_two', 'transport', 'tds_ac']);
        return reply('ok', [
            'success' => true,
            'gst_doc' => $gst_doc
        ]);
    }


    public function update(GstDocumentRequest $request, $id)
    {
        if (Gate::denies('debit-note-modify')) {
            return deny();
        }
        return $request->save();
    }

    public function getBillNo(Request $request){
        $msg = checkBillNo($request->bill_no,$request->year,$request->ac_id);
        return reply('ok', [
            'msg' => $msg
        ]);
    }


    public function getDebitNotePrint(Request $request,$id){
        // if (Gate::denies('issue-print') || Gate::denies('receipt-print')) {
        //     return deny();
        // }
        $gst_docs = GstDocument::findOrFail($id);
        $print = new GstDocumentPrint($gst_docs);
        $pdf = $print->makepdf($gst_docs);
        $pdf->Output("DebitNote.pdf", 'I');
        exit();
    }
}
