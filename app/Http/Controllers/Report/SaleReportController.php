<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SaleReportController extends Controller
{
    public function index(){
        if (Gate::denies('party-categories')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/PartyCategory/PartyCategoryList', [
        ]);
    }
}
