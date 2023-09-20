<?php

namespace App\Http\Controllers\Report\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounts\Account;
use App\Models\Yearly\Cashbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class LedgerReportController extends Controller
{
    public function index(){
        if (Gate::denies('ledger-report')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Reports/Accounting/LedgerReport', [
        ]);
    }

    public function store(Request $request){
        if (Gate::denies('ledger-report')) {
            return deny();
        }
        $rules = [
            'start_date' => 'required',
            'to_date' => 'required'
        ];
        $this->validate($request,$rules);
        $dt1 = getDateFormat($request->start_date,'ymd');
        $dt2 = getDateFormat($request->to_date,'ymd');
        $cur_bals = Cashbook::select('ac_id',DB::raw("sum(case when dr_cr = 'D' then amount else -1*amount end) as balance"))
                    ->where('trans_date','<=',$dt2)
                    ->groupByRaw("1");
        $data = Cashbook::where('cash_book.vcode','<>','OS')
                ->whereBetween('cash_book.trans_date',[$dt1,$dt2]);
        if($request->has('branch_ids') && count($request->branch_ids) > 0) {
            $data = $data->whereIn('cash_book.branch_id',$request->branch_ids);
            $cur_bals = $cur_bals->whereIn('cash_book.branch_id',$request->branch_ids);
        } else {
            $branch_id = getCurrentBranchId();
            $data = $data->where('cash_book.branch_id',$branch_id);
        }
        if($request->has('ac_ids') && count($request->ac_ids) > 0) {
            $data = $data->whereIn('cash_book.ac_id',$request->ac_ids);
            $cur_bals = $cur_bals->whereIn('cash_book.ac_id',$request->ac_ids);
        }
        $openings = Cashbook::where(function($q) use($dt1) {
            $q->where('trans_date','<',$dt1)
                ->orWhere('vcode','OS');
        });
        if($request->dr_cr != 'A') {
            $data = $data->where('dr_cr',$request->dr_cr);
            $openings = $openings->where('dr_cr',$request->dr_cr);
        }
        if(floatval($request->amount_grt) > 0 && floatval($request->amount_lt) > 0) {
            $data = $data->whereBetween('amount',[$request->lt,$request->amount_grt]);
        }
        if($request->has('ac_group_ids') && count($request->ac_group_ids) > 0) {
            $acids = Account::join('ac_sub_groups','accounts.ac_sub_grp_id','=','ac_sub_groups.id')
                        ->whereIn('ac_sub_groups.ac_group_id',$request->ac_group_ids)
                        ->select('accounts.id')->pluck('id')->toArray();
            $data = $data->whereIn('cash_book.ac_id',$acids);
            $openings = $openings->whereIn('cash_book.ac_id',$acids);
        }
        if(strlen($request->particulars) > 0) {
            $data = $data->where(DB::raw("instr(part,'$request->particulars')"),'>',0);
        }
        if($request->zero_bal_ending_date != 'A') {
            $acids = $cur_bals->having('balance','=',0);
            if($request->zero_bal_ending_date == 'Y') {
                $data = $data->whereIn('cash_book.ac_id',$acids);
                $openings = $openings->whereIn('cash_book.ac_id',$acids);
            } else {
                $data = $data->whereNotIn('cash_book.ac_id',$acids);
                $openings = $openings->whereNotIn('cash_book.ac_id',$acids);
            }
        }
        if(strlen($request->search_word) > 0) {
            $acids = Account::select('id');
            $len = strlen($request->search_word);
            if($request->search_term_position == 'G') {
                $acids = $acids->where(DB::raw("instr(name,'$request->search_word')"),'>',0);
            } elseif($request->search_term_position == 'S') {
                $acids = $acids->where(DB::raw("left(name,$len)"),$request->search_word);
            }  elseif($request->search_term_position == 'E') {
                $acids = $acids->where(DB::raw("right(name,$len)"),$request->search_word);
            } else {
                $acids = $acids->where(DB::raw("instr(name,'$request->search_word')"),'>',0)
                        ->where(DB::raw("right(name,$len)"),'<>',$request->search_word)
                        ->where(DB::raw("left(name,$len)"),'<>',$request->search_word);
            }
            $acids = $acids->pluck('id')->toArray();
            $data = $data->whereIn('cash_book.ac_id',$acids);
            $openings = $openings->whereIn('cash_book.ac_id',$acids);
        }
        $openings = $openings->select('ac_id',DB::raw("sum(case when dr_cr = 'D' then amount else -1*amount end) as balance"))
                            ->groupByRaw("1");
        $data = $data->select('cash_book.*')->orderBy('cash_book.ac_id')->orderBy('cash_book.trans_date');
        $acids = $openings->pluck('ac_id')->toArray();
        $acids = array_unique(array_merge($acids,$data->pluck('ac_id')->toArray()));
        $accounts = Account::select('name','id')->orderBy('name')->get();
        // dd($accounts);
        $data = $data->get();
        $data = $data->groupBy('ac_id')->toArray();
        $openings = $openings->get()->groupBy('ac_id')->toArray();
        // DATE	DUE DATE	VOUCHER NO.	VOUHCER TYPE	PARTICULARS	WEIGHT	DEBIT AMOUNT	CREDIT AMOUNT	BALANCE AMOUNT (DR./CR.)

        //  PARAMETERS
        // start_date
        // to_date
        // branch_ids
        // ac_ids
        // dr_cr
        // amount_grt
        // amount_lt
        // ac_group_ids
        // companies_ids
        // search_word
        // particulars
        // search_term_position
        // zero_bal_ending_date

        // To be handeled in vue

        // opening_bal
        // moved
        // weight
        // due_date
        // voucher_no


        return reply(true,[
            'ledger' => $data,
            'openings' => $openings,
            'accounts' => $accounts
        ]);
    }

    public function postingDetails($vcode){
        // dd($vcode);
        // if (Gate::denies('ledger-report')) {
        //     return deny();
        // }
        $data = Cashbook::join(getSharedDb() . '.accounts', 'cash_book.ac_id', '=', 'accounts.id')
                ->where('cash_book.vcode', '=', $vcode)
                ->select(['accounts.name', 'cash_book.vcode',
                        'cash_book.*',
                ])->where('cash_book.vcode', '<>', 'OS')->get();
        return Inertia::render('ProjectComponents/Reports/Accounting/PostingDetail', [
            'items'=>$data
        ]);
    }
}
