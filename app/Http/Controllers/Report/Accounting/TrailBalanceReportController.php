<?php

namespace App\Http\Controllers\Report\Accounting;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Masters\Company;
use App\Http\Controllers\Controller;
use App\Models\Yearly\Cashbook;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\Console\Output\Output;

class TrailBalanceReportController extends Controller
{
    public function index(){
        if (Gate::denies('trial-balance-report')) {
            return deny();
        }
        $comp_id = session()->get('company_id');
        $company = Company::find($comp_id);
        // dd($company);
        return Inertia::render('ProjectComponents/Reports/Accounting/TrialBalanceReport', [
            'company' => $company
        ]);
    }

    public function store(Request $request){
        if (Gate::denies('trial-balance-report')) {
            return deny();
        }
        $rules = [
            'start_date' => 'required',
            'to_date' => 'required'
        ];
        if($request->comparison == 'Y') {
            $rules += [
                'start_date1' => 'required',
                'to_date1' => 'required'
            ];
        }
        $this->validate($request,$rules);
        $dt1 = getDateFormat($request->start_date,'ymd');
        $dt2 = getDateFormat($request->to_date,'ymd');
        $data = Cashbook::join(getSharedDb().'.accounts','accounts.id','=','cash_book.ac_id')
                        ->join(getSharedDb().'.ac_sub_groups','ac_sub_groups.id','=','accounts.ac_sub_group_id')
                        ->join(getSharedDb().'.ac_groups','ac_sub_groups.ac_group_id','=','ac_groups.id')
                        ->where('cash_book.trans_date','<=',$dt2);
        $select_raw = "sum(case when cash_book.trans_date < '$dt1' or cash_book.vcode = 'OS' then case when cash_book.dr_cr = 'D' then cash_book.amount else -1*cash_book.amount end else 0 end) as opening";
        $select_raw = addString($select_raw,"sum(case when cash_book.trans_date between '$dt1' and '$dt2' and cash_book.vcode <> 'OS' then case when dr_cr = 'D' then cash_book.amount else 0 end else 0 end) as dr",",");
        $select_raw = addString($select_raw,"sum(case when cash_book.trans_date between '$dt1' and '$dt2' and cash_book.vcode <> 'OS' then case when dr_cr = 'C' then cash_book.amount else 0 end else 0 end) as cr",",");
        $select_raw = addString($select_raw,"sum(case when dr_cr = 'D' then cash_book.amount else -1*cash_book.amount end) as bal",",");
        if($request->sundry_comb == 'Y') {
            $select_raw = addString($select_raw,"case when ac_sub_groups.sub_group_name in ('Trade Payables','Trade Receivables') then ac_sub_groups.sub_group_name else accounts.name end as name,case when ac_sub_groups.sub_group_name in ('Trade Payables','Trade Receivables') then 0 else accounts.id end as acid",",");
        } else {
            $select_raw = addString($select_raw,"accounts.name,accounts.id as acid",",");
        }
        $groupby = "5,6";
        $orderby = "5";
        if($request->group_wise == 'Y') {
            $select_raw = addString($select_raw,"ac_groups.group_name,ac_sub_groups.sub_group_name",",");
            $groupby = "5,6,7,8";
            $orderby = "7,8,5";
        }
        $data = $data->selectRaw($select_raw)->groupByRaw($groupby)->orderByRaw($orderby);
        if(count($request->input('branch_ids',[])) > 0) {
            $data = $data->whereIn('cash_book.branch_id',$request->branch_ids);
        }
        if(count($request->input('ac_group_ids',[])) > 0) {
            $data = $data->whereIn('ac_groups.id',$request->ac_group_ids);
        }
        $data = $data->get();
        $data1 = [];
        if($request->comparison == 'Y') {
            $dt1 = getDateFormat($request->start_date1,'ymd');
            $dt2 = getDateFormat($request->to_date1,'ymd');
            $data1 = Cashbook::join(getSharedDb().'.accounts','accounts.id','=','cash_book.ac_id')
                            ->join(getSharedDb().'.ac_sub_groups','ac_sub_groups.id','=','accounts.ac_sub_group_id')
                            ->join(getSharedDb().'.ac_groups','ac_sub_groups.ac_group_id','=','ac_groups.id')
                            ->where('cash_book.trans_date','<=',$dt2);
            $data1 = $data1->selectRaw($select_raw)->groupByRaw($groupby)->orderByRaw($orderby);
            if(count($request->input('branch_ids',[])) > 0) {
                $data1 = $data1->whereIn('cash_book.branch_id',$request->branch_ids);
            }
            if(count($request->input('ac_group_ids',[])) > 0) {
                $data1 = $data1->whereIn('ac_groups.id',$request->ac_group_ids);
            }
            $data1 = $data1->get();
            $data1 = $data1->groupBy('name')->toArray();
        }




        // INPUT
        // start_date
        // to_date
        // branch_ids
        // amount_lt
        // ac_group_ids
        // group_wise
        // comparison
        // sundry_comb
        // moved
        // detail

        // Output
        // acc_desc
        // op_dr
        // op_cr
        // trans_dr
        // trans_cr
        // closing_dr
        // closing_cr

        return reply(true,[
            'data' => $data,
            'data1' => $data1,
        ]);
    }
}
