<?php

namespace App\Http\Controllers\Report\Sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SaleFormatReportController extends Controller
{
    public function index(){
        if (Gate::denies('sale-format-report')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Reports/Sale/SaleReportFormat', [
        ]);
    }

    public function store(Request $request){
        if (Gate::denies('sale-format-report')) {
            return deny();
        }

        //  PARAMETERS
        // start_date
        // to_date
        // branches_ids
        // item_ids
        // ac_ids

        return reply(true,[
            'sale_format' =>[]
        ]);
    }
}
