<?php

namespace App\Http\Controllers\Other;

use App\Models\Auth\User;
use App\Models\Items\Gst;
use App\Models\Items\Uqc;
use App\Models\Items\Item;
use App\Models\Items\Store;
use App\Models\Items\VatCst;
use App\Models\Masters\Bank;
use App\Models\Masters\City;
use App\Models\Masters\Ifsc;
use Illuminate\Http\Request;
use App\Models\Masters\Brand;
use App\Models\Masters\State;
use App\Models\Items\ItemUnit;
use App\Models\Masters\Branch;
use App\Models\Auth\UserBranch;
use App\Models\Items\ItemGroup;
use App\Models\Masters\Company;
use App\Models\Masters\Packing;
use App\Models\Accounts\Account;
use App\Models\Accounts\PayTerm;
use App\Models\Auth\UserCompany;
use App\Models\Masters\District;
use App\Models\Yearly\SaleOrder;
use App\Models\Masters\TdsSection;
use App\Models\Masters\TypeMaster;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Company\SaleContract;
use Illuminate\Support\Facades\Auth;
use App\Models\Accounts\AccountGroup;
use App\Models\Auth\ReportUserBranch;
use App\Models\Masters\PartyCategory;
use App\Models\Accounts\AccountSubGroup;
use App\Models\Yearly\SaleOrderDispatch;
use App\Models\Masters\BranchBankAccount;
use App\Models\Company\SaleContractDetail;

class SelectController extends Controller
{
    public function citiesFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = City::orderBy('cities.name')->leftJoin('states','states.id','=','cities.state_id');

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('cities.name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('cities.name', 'like', '%' . $search);
            }
            else{
                $list->where('cities.name', 'like','%'. $search . '%');
            }
        }

        $list->orderBy('cities.name');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('cities.*','states.name as state_name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function statesFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = State::orderBy('name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }

        $list->orderBy('name');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('states.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function companiesFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Company::orderBy('company_name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('company_name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('company_name', 'like', '%' . $search);
            }
            else{
                $list->where('company_name', 'like','%'. $search . '%');
            }
        }
        $list->orderBy('company_name');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('companies.*')->groupBy('companies.id')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function loginCompaniesFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }

        $this->validate($request,[
            'email' =>'required|exists:users,email'
        ]);
        $user_companies = [];

        $user = User::where('email',$request['email'])->first();
        if($user){
            $user_companies = UserCompany::where('user_id',$user['id'])->pluck('company_id')->toArray();
            //FOR INFOWAY
            if($user['id'] == 1){
                $user_companies = Company::pluck('id')->toArray();
            }
        }

        $list = Company::whereIn('id',$user_companies)->orderBy('company_name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('company_name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('company_name', 'like', '%' . $search);
            }
            else{
                $list->where('company_name', 'like','%'. $search . '%');
            }
        }
        $list->orderBy('company_name');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('companies.*')->groupBy('companies.id')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }


    public function ifscFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Ifsc::leftJoin('banks','banks.id','=','ifscs.bank_id');

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('ifscs.ifsc_code', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('ifscs.ifsc_code', 'like', '%' . $search);
            }
            else{
                $list->where('ifscs.ifsc_code', 'like','%'. $search . '%');
            }
        }

        $list = $list->orderBy('ifscs.ifsc_code');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('ifscs.*','banks.name as bank_name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function accountGroupFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = AccountGroup::orderBy('group_name');
        $list->where('group_name', 'like', $search . '%');

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('group_name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('group_name', 'like', '%' . $search);
            }
            else{
                $list->where('group_name', 'like','%'. $search . '%');
            }
        }

        $list = $list->orderBy('group_name');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('ac_groups.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function payTermsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = PayTerm::orderBy('name');
        $list= $list->orderBy('name');

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('pay_terms.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function typeMasterFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = TypeMaster::orderBy('name')->where('type',$request->type);
        $list = $list->orderBy('name');

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('type_masters.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function accountSubGroupFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = AccountSubGroup::orderBy('sub_group_name');

         if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('sub_group_name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('sub_group_name', 'like', '%' . $search);
            }
            else{
                $list->where('sub_group_name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if($request->has('party_applicable') && $request->party_applicable != ''){
            $list->where('party_applicable', '=', $request['party_applicable']);
        }

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('ac_sub_groups.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function branchesFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Branch::orderBy('name');


        if($request->has('user_id') && $request->user_id > 0){
            $user_companies = UserCompany::where('user_id', $request->user_id)->pluck('company_id')->toArray();
            $list = $list->whereIn('company_id',$user_companies);
        }




        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('branches.*','branches.id')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
    public function reportBranchesFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Branch::orderBy('name');
        $auth_user_id = Auth()->user()->id;

        if( $auth_user_id > 0){
            $user_companies = UserCompany::where('user_id', $auth_user_id)->pluck('company_id')->toArray();
            if(count($user_companies) > 0) {
                $list = $list->whereIn('company_id',$user_companies);
            }
            if($auth_user_id != 1){
                $user_branches = ReportUserBranch::where('user_id', $auth_user_id)->pluck('branch_id')->toArray();
                if(count($user_branches) > 0){
                    $list = $list->whereIn('id',$user_branches);

                }
            }
        }

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('branches.*','branches.id')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }




    public function loginBranchesFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Branch::orderBy('name');

        $auth_user_id = Auth()->user()->id;
        if($auth_user_id != 1){
            $user_branches = UserBranch::where('user_id', $auth_user_id)->pluck('branch_id')->toArray();
            $list = $list->whereIn('id',$user_branches);
        }

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('branches.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function districtsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = District::orderBy('name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('districts.*')->orderBy('name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
    public function banksFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Bank::orderBy('name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('banks.*')->orderBy('name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function accountsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Account::join(getYearlyDb().'.accounts as a1' ,'a1.ac_id','=','accounts.id')
                ->leftJoin('account_branches','account_branches.ac_id','=','accounts.id');

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('accounts.name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('accounts.name', 'like', '%' . $search);
            }
            else{
                $list->where('accounts.name', 'like','%'. $search . '%');
            }
        }

        if($request->type =='party'){
            $list->join('account_details','account_details.ac_id','=','accounts.id');
        } else if($request->type =='bank'){
            $sub_grp_id = AccountSubGroup::getAcGrpId('bank accounts');
            logger('showing ac sub grp id '.$sub_grp_id);
            $list->leftJoin('account_details','account_details.ac_id','=','accounts.id')
            ->where('accounts.ac_sub_group_id',$sub_grp_id);
        } else if($request->type =='gl'){
            $list->leftJoin('account_details','account_details.ac_id','=','accounts.id')
            ->whereNull('account_details.id');
        } else {
            $list->leftJoin('account_details','account_details.ac_id','=','accounts.id');
        }
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        if(isset($request->report) && $request->report){
            $user =Auth::user();
            $report_branches_id=  $user->report_user_branches->pluck('branch_id')->toArray();
            if(count($report_branches_id) > 0) {
                $list =  $list->where(function($q) use($report_branches_id){
                    $q->whereIn('account_branches.branch_id', $report_branches_id)
                    ->orWhereNull('account_branches.branch_id');
                });
            }
            // dd($list->toSql());
        }
        else if(isset($request->branch_applicable) && $request->branch_applicable){
            $list =  $list->where(function($q){
                $q->where('account_branches.branch_id', getCurrentBranchId())
                ->orWhereNull('account_branches.branch_id');
            });
        }
        // dd($list->get());
        $list = $list->select(['accounts.*','a1.local_outside','account_details.party_gst_status','account_details.beneficiary_name'])->groupBy(['accounts.id','a1.local_outside','account_details.party_gst_status','account_details.beneficiary_name']);
        $list = $list->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function limitAccountsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Account::join(getYearlyDb().'.accounts as a1','a1.ac_id','=','accounts.id')
                    ->leftJoin('account_branches','account_branches.ac_id','=','accounts.id');

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }  else {
                $list->where('name', 'like','%'. $search . '%');
            }
        }

        if($request->type != 'all'){
            if($request->type =='party'){
                $list->join('account_details','account_details.ac_id','=','accounts.id')
                ->where('account_details.credit_limit','=',1);
            }
        }

        if(isset($request->branch_applicable) && $request->branch_applicable){
            $list =  $list->where(function($q){
                $q->where('account_branches.branch_id', getCurrentBranchId())
                ->orWhereNull('account_branches.branch_id');
            });
        }
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('accounts.*')->groupBy('accounts.id')->orderBy('name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function getBankAccounts(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = BranchBankAccount::orderBy('bank_account_number');

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('branch_bank_acc.bank_account_number', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('branch_bank_acc.bank_account_number', 'like', '%' . $search);
            }
            else{
                $list->where('branch_bank_acc.bank_account_number', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        if(isset($request->branch_applicable) && $request->branch_applicable){
            $list =  $list->where(function($q){
                $q->where('branch_bank_acc.branch_id', getCurrentBranchId())
                ->orWhereNull('branch_bank_acc.branch_id');
            });
        }

        $list = $list->select(['branch_bank_acc.*'])->with('ifsc');
        $list = $list->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }


    public function uqcsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Uqc::orderBy('uqc_name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('uqc_name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('uqc_name', 'like', '%' . $search);
            }
            else{
                $list->where('uqc_name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('uqcs.*')->orderBy('uqc_name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
    public function itemGroupsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = ItemGroup::orderBy('name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }
        $list = $list->where('type',$request ['type']);

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('item_groups.*')->orderBy('name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
    public function storesFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Store::orderBy('name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('stores.*')->orderBy('name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
    public function itemUnitsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = ItemUnit::orderBy('unit_name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('unit_name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('unit_name', 'like', '%' . $search);
            }
            else{
                $list->where('unit_name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('item_units.*')->orderBy('unit_name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
    public function gstFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Gst::orderBy('name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('gst.*')->orderBy('name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
    public function vatCstFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = VatCst::orderBy('name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('vat_cst.*')->orderBy('name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
    public function itemsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }

        $list = Item::where('active',"Y")->leftJoin('item_branches','item_branches.item_id','=','items.id');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('items.item_name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('items.item_name', 'like', '%' . $search);
            }
            else{
                $list->where('items.item_name', 'like','%'. $search . '%');
            }
        }

        if(isset($request->type) && $request->type != '' && $request->type != 'all'){
            $list =  $list->where('gst_vat',$request->type);
        }
        if(isset($request->report) && $request->report){
            $user =Auth::user();
            $report_branches_id=  $user->report_user_branches->pluck('branch_id')->toArray();
            if(count($report_branches_id) > 0) {
                $list =  $list->where(function($q) use($report_branches_id){
                    $q->whereIn('item_branches.branch_id', $report_branches_id)
                    ->orWhereNull('item_branches.branch_id');
                });
            }
        }
        else if(isset($request->branch_applicable) && $request->branch_applicable){
            $list =  $list->where(function($q){
                $q->where('item_branches.branch_id', getCurrentBranchId())
                ->orWhereNull('item_branches.branch_id');
            });
        }

        if($request->s_p_type == 'S'){
            $comp_id = session()->get('company_id');
            $database = config('database.data_name').$comp_id.'_'.$request->session;
            $item_ids = DB::table($database. '.invoices')
                        ->join($database. '.invoice_dets','invoices.id','=','invoice_dets.invoice_id')
                        ->where('invoices.invoice_no',$request->bill_no)->pluck('invoice_dets.item_id')->toArray();
            $list =  $list->whereIn('items.id',$item_ids);
        }

        $count_filtered = $list->count();
        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }
        $list = $list->select('items.*')->orderBy('item_name')->with('gst.gst_types.details','vat_cst','secondary_unit')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
    public function packingsFiltered(Request $request){
        // dd($request->all());
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Packing::orderBy('name');
        if($request->strict_include == 'Y'){
            $include_only = isset($request->include_only) ?$request->include_only:[];
            $list = $list->whereIn('id',$include_only);
        }
        else if(isset($request->include_only)){
            $list = $list->whereIn('id',$request->include_only);
        }
        if($request->has('type')  && $request->type != 'all'){
            $list = $list->where('type',$request->type);
        }

        if($request->s_p_type == 'S'){
            $comp_id = session()->get('company_id');
            $database = config('database.data_name').$comp_id.'_'.$request->session;
            $pack_ids = DB::table($database. '.invoices')
                        ->join($database. '.invoice_dets','invoices.id','=','invoice_dets.invoice_id')
                        ->where('invoices.invoice_no',$request->bill_no)->where('invoice_dets.item_id',$request->item_id)->pluck('invoice_dets.packing_id')->toArray();
            $list = $list->whereIn('id',$pack_ids);
        }

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('packings.*')->orderBy('name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function packingsLoosePackedFiltered(Request $request,$pack_type){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Packing::orderBy('name')->where('type',$pack_type);
        if($request->strict_include == 'Y'){
            $include_only = isset($request->include_only) ?$request->include_only:[];
            $list = $list->whereIn('id',$include_only);
        }
        else if(isset($request->include_only)){
            $list = $list->whereIn('id',$request->include_only);
        }
        if($request->has('type')  && $request->type != 'all'){
            $list = $list->where('type',$request->type);
        }

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('packings.*')->orderBy('name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function partyCategoriesFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = PartyCategory::orderBy('name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('party_cats.*')->orderBy('name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
    public function brandsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Brand::orderBy('name');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('name', 'like', '%' . $search);
            }
            else{
                $list->where('name', 'like','%'. $search . '%');
            }
        }
        if($request->strict_include == 'Y'){
            $include_only = isset($request->include_only) ?$request->include_only:[];
            $list = $list->whereIn('id',$include_only);
        }
        else if(isset($request->include_only)){
            $list = $list->whereIn('id',$request->include_only);
        }

        if($request->s_p_type == 'S'){
            $comp_id = session()->get('company_id');
            $database = config('database.data_name').$comp_id.'_'.$request->session;
            $brand_ids = DB::table($database. '.invoices')
                        ->join($database. '.invoice_dets','invoices.id','=','invoice_dets.invoice_id')
                        ->where('invoices.invoice_no',$request->bill_no)->where('invoice_dets.item_id',$request->item_id)->pluck('invoice_dets.brand_id')->toArray();
            $list = $list->whereIn('id',$brand_ids);
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('brands.*')->orderBy('name')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
    public function saleContractFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = SaleContract::orderBy('contract_no');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('contract_no', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('contract_no', 'like', '%' . $search);
            }
            else{
                $list->where('contract_no', 'like','%'. $search . '%');
            }
        }

        if($request->packed_loose != ''){
            $list = $list->where('packed_loose',$request->packed_loose);
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        if(isset($request->branch_applicable) && $request->branch_applicable){
            $list =  $list->where(function($q){
                $q->where('sales_contracts.branch_id', getCurrentBranchId())
                ->orWhereNull('sales_contracts.branch_id');
            });
        }

        $list = $list->select('sales_contracts.*')->groupBy('sales_contracts.id');
        $list = $list->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function saleContractItemsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $sale_contract_ids = SaleContract::where('branch_id',getCurrentBranchId())->pluck('id')->toArray();
        $sale_contract_item_ids = SaleContractDetail::whereIn('sale_contract_id',$sale_contract_ids)->pluck('item_id')->toArray();
        $list = Item::where('active',"Y")->whereIn('id',$sale_contract_item_ids);

        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('item_name', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('item_name', 'like', '%' . $search);
            }
            else{
                $list->where('item_name', 'like','%'. $search . '%');
            }
        }

        if(isset($request->type) && $request->type != '' && $request->type != 'all'){
            $list =  $list->where('gst_vat',$request->type);
        }
        $count_filtered = $list->count();
        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }


        $list = $list->select('items.*')->orderBy('item_name')->with('gst.gst_types.details','vat_cst')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function itemSaleContractFiltered(Request $request,$item_id,$client_id=0){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }

        $item_sale_contract_ids = SaleContractDetail::where('item_id',$item_id)->pluck('sale_contract_id')->toArray();
        $list = SaleContract::join('sales_contract_dets','sales_contract_dets.sale_contract_id','=','sales_contracts.id')
                ->where('sales_contracts.status',"O")->whereIn('sales_contracts.id',$item_sale_contract_ids);

        if($client_id > 0){
            $list = $list->where('sales_contracts.ac_id',$client_id);
        }

        if($request->packed_loose){
            $list = $list->where('sales_contracts.packed_loose',$request->packed_loose);
        }
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('sales_contracts.contract_no', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('sales_contracts.contract_no', 'like', '%' . $search);
            }
            else{
                $list->where('sales_contracts.contract_no', 'like','%'. $search . '%');
            }
        }
        // $list1 = clone $list;
        // $count_filtered = count($list1->select('sales_contracts.*')->get());
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('sales_contracts.*','sales_contract_dets.unit_id')->orderBy('sales_contracts.contract_no')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function saleOrdersFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = SaleOrder::orderBy('sale_order_no');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('sale_order_no', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('sale_order_no', 'like', '%' . $search);
            }
            else{
                $list->where('sale_order_no', 'like','%'. $search . '%');
            }
        }


        if($request->packed_loose != ''){
            $list = $list->where('packed_loose',$request->packed_loose);
        }

        if($request->dispatch_advice != ''){
            $list = $list->where('dispatch_advice',$request->dispatch_advice);
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        if(isset($request->branch_applicable) && $request->branch_applicable){
            $list =  $list->where(function($q){
                $q->where('sale_orders.branch_id', getCurrentBranchId());
            });
        }

        $list = $list->select('sale_orders.*')->groupBy('sale_orders.id');
        $list = $list->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function saleOrdersDispatchFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = SaleOrder::orderBy('sale_order_no')->join(getSharedDb().'.accounts','accounts.id','=','sale_orders.bill_party_id');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('sale_order_no', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('sale_order_no', 'like', '%' . $search);
            }
            else{
                $list->where('sale_order_no', 'like','%'. $search . '%');
            }
        }


        $exclude_ids = SaleOrderDispatch::pluck('sale_order_id')->toArray();
        $list = $list->whereNotIn('sale_orders.id',$exclude_ids);


        if($request->packed_loose != ''){
            $list = $list->where('packed_loose',$request->packed_loose);
        }

        if($request->dispatch_advice != ''){
            $list = $list->where('dispatch_advice',$request->dispatch_advice);
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        if(isset($request->branch_applicable) && $request->branch_applicable){
            $list =  $list->where(function($q){
                $q->where('sale_orders.branch_id', getCurrentBranchId());
            });
        }

        $list = $list->select('sale_orders.*','accounts.name as ac_name')->groupBy('sale_orders.id');
        $list = $list->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function getTdsSection(Request $request){

        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = TdsSection::orderBy('section');
        if ($request->has('search_type')) {
            $searchType = $request->input('search_type');
            if ($searchType == 'first') {
                $list->where('section', 'like', $search . '%');
            } elseif ($searchType == 'last') {
                $list->where('section', 'like', '%' . $search);
            }
            else{
                $list->where('section', 'like','%'. $search . '%');
            }
        }

        $list->orderBy('section');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('tds_sections.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);

    }
}
