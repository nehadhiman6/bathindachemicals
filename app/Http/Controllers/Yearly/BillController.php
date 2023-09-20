<?php

namespace App\Http\Controllers\Yearly;

use App\Http\Controllers\Controller;
use App\Models\Yearly\Bill;
use App\Models\Yearly\Parameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function filteredBillAccountKeys(Request $request, $ac_id)
    {
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $ignore_vcode = '';
        if ($request->has('ignore_vcode') && $request->ignore_vcode != '') {
            $ignore_vcode = $request->ignore_vcode;
        }
        $bill_qry = Bill::select(
            'ref',
            'ref_date',
            DB::raw("max(ref_type) as ref_type"),
            'ref_key',
            DB::raw("sum(case when dr_cr = 'D' then amount else -1*amount end) as amount"),
            DB::raw("min(id) as id")
        )->groupByRaw("1,2,4")->where('ac_id', $ac_id)->where('vcode', '<>',$ignore_vcode);
        $para_id = Parameter::first()->id;
        $list = Parameter::joinSub($bill_qry, 'a1', 'a1.id', '<>', DB::raw("0"))->where('parameters.id', $para_id)
            ->select(
                'a1.ref',
                'a1.ref_date',
                'a1.ref_type',
                'a1.ref_key',
                'a1.id',
                DB::raw("abs(a1.amount) as amount"),
                DB::raw("case when a1.amount > 0 then 'Dr' else 'Cr' end as dr_cr")
            )->where('a1.amount', '<>', 0)
            ->where('a1.ref_key', 'like', $search . '%')
            ->orderBy('a1.ref_date')->orderBy('a1.ref_key');

        if ($request->has('ignore_ref') && count($request->ignore_ref) > 0) {
            $list = $list->whereNotIn('ref_key', $request->ignore_ref);
        }

        $count_filtered = $list->count();
        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }
        $list = $list->get();

        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
}
