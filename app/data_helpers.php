<?php

use Carbon\Carbon;
use App\Models\Yearly\Uid;
use App\Models\Masters\Year;
use App\Models\Masters\Branch;
use App\Models\Masters\Prefix;
use App\Models\Auth\Permission;
use App\Models\Masters\Company;
use App\Models\Yearly\Cashbook;
use App\Models\Accounts\Account;
use App\Models\Yearly\Parameter;
use App\Models\Yearly\SaleOrder;
use App\Models\Masters\GstReason;
use Illuminate\Support\Facades\DB;
use App\Models\Masters\NoGenerator;
use Illuminate\Support\Facades\App;
use App\Models\Masters\BranchPrefix;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Yearly\NoGenerator as YearlyNoGenerator;
use App\Models\Company\NoGenerator as CompanyNoGenerator;

function getYears($comp_name = "")
{
    $years = Year::orderBy('year')->get();
    return $years;
}

function getCompany()
{
    $com_year = Company::all();
    return $com_year;
}

function getCom()
{
    $comp = Company::find(session()->get('company_id'));
    return $comp;
}
function nextnoShared($idname)
{
    $no = NoGenerator::firstOrCreate(['idname' => $idname, 'prefix' => '']);
    $no->increment('no', 1);
    return $no->no;
}

function getNextEntryNumberFront($idname)
{
    $no = YearlyNoGenerator::where('idname', $idname)->first();
    $num = $no ? $no->no + 1 : 1;
    return $num;
}
function getYearlyNextEntryNumberFront($idname)
{
    $no = YearlyNoGenerator::firstOrCreate(['idname' => $idname, 'prefix' => '']);
    $no->increment('no', 1);
    return $no->no;
}

function companyNextno($idname)
{
    $no = CompanyNoGenerator::firstOrCreate(['idname' => $idname, 'prefix' => '']);
    $no->increment('no', 1);
    return $no->no;
}
function getCompanyNextEntryNumberFront($idname)
{
    $no = CompanyNoGenerator::where('idname', $idname)->first();

    $num = $no ? $no->no + 1 : 1;
    return $num;
}
function getRefKey($ref, $ref_date)
{
    return trim($ref) . ' Dt.' .  Carbon::parse($ref_date)->format('d-m-Y');
}
function setParaAttribute($para_value)
{
    if ($para_value != null) {
        return $para_value;
    }
    return '';
}

function getPermissions()
{

    $user = auth()->user();
    if ($user->id == 1) {
        $permission = Permission::get();
    } else {
        $permission = $user->roles->first()->permissions()->get();
    }
    return $permission->pluck('name')->toArray();

    $permissions = [];
    foreach (Permission::all() as $permission) {
        if ($permission->admin == "Y") {
            $permissions[$permission->name] = true;
        }
        return $permission->pluck('name')->toArray();
        $permissions = [];
        foreach (Permission::all() as $permission) {
            if ($permission->admin == "Y") {
                $permissions[$permission->name] = true;
            }
        }
        return $permissions;
    }
    return [];
}

function getPrefix($type){
    $prefix = Prefix::where('type',$type)->first();
    return $prefix;
}

function getParameters()
{
    $para = Parameter::orderBy('id')->where('input_required','Y')->get()->load('account','packing','item','item_unit');
    return $para;
}

function getParameterValue($value)
{
    $para = Parameter::where('para_name', $value)->first();
    return $para;
}

function getCurrentBranch()
{
    $branch_id =getCurrentBranchId();
    $branch = Branch::find($branch_id);
    if($branch){
        $branch->load('ifsc');
    }
    return $branch;
}

function getCurrentBranchId()
{
    $branch_id = session()->get('branch_id', 0);
    return $branch_id;
}


function validateBranch(){
    $current_branch_id = getCurrentBranchId();
    if( $current_branch_id  == 0){
        throw ValidationException::withMessages(['gen_msg' =>'Sorry! You must set a branch to Proceed']);
    }

}


function getBranchPrefix($value){
    $branch_prefix= null;
    $prefix_id = getPrefix($value) ? getPrefix($value)['id'] : 0;
    if(getCurrentBranchId() > 0 && $prefix_id  > 0){
        $branch_prefix = BranchPrefix::where('branch_id',getCurrentBranchId())->where('prefix_id',$prefix_id)->first();
    }
    return $branch_prefix ? $branch_prefix['prefix_value']:null;
}

function validatePrefix($value){
    $branch_pre = getBranchPrefix($value);
    if(!$branch_pre){
        throw ValidationException::withMessages(['gen_msg' =>'Sorry! To proceed, please set the prefix for the corresponding Form to the selected branch.']);

    }
    return $branch_pre ? $branch_pre:null;
}


function getNextSeriesNumberCompany($idname)
{
    $no = CompanyNoGenerator::firstOrCreate(['idname' => $idname, 'prefix' => '']);
    $no->increment('no', 1);
    return $no->no;
}

function addUid($value)
{
    $uid = new Uid();
    $uid->uid = $value;
    $uid->save();
}


function getTransportTypes(){
    return
    [
        ['id'=>'by_road','text'=>'By Road'],
        ['id'=>'by_rail','text'=>'By Rail'],
    ];
}
function sharedAttachmentPath(){
    $path =  'shared-files';
    return $path;
}

function companyAttachmentPath(){
    $path =  'company-files';
    $company = null;
    if(session()->get('company_id')){
        $company = Company::find(session()->get('company_id'));
        $shared_company_id = $company['shared_comp_id'];
        $path =  'company-files/' . $shared_company_id;
    }
    return $path;
}

function yearlyAttachmentPath(){
    $path =  'yearly-files';
    $company = null;
    if(session()->get('company_id')){
        $company = Company::find(session()->get('company_id'));
        $path =  'yearly-files/' . $company['id'];
    }
    return $path;
}

function getS3TempUrl($path, $minutes = 5)
{
    $url = Str::startsWith($path, '/') ? Str::after($path, '/') : $path;
    // logger($url);
    if (Storage::disk('s3')->exists($url)) {
        return Storage::disk('s3')->temporaryUrl(
            $url,
            now()->addMinutes($minutes),
            [
                // 'ResponseContentType' => 'application/octet-stream',
                'Access-Control-Allow-Origin' => '*'
            ]
        );
    }
    return $path;
}

function showFile($path, $file = null, $thumbnail = false)
{
    if ($thumbnail) {
        $img_types = ['image/bmp', 'image/jpeg', '	image/gif', 'image/png'];

        if (!in_array($file->mime_type, $img_types)) {
            return showThumbnail($file);
        }
    }
    if (config('hr.upload_disk') == 's3') {
        $headers = [
            'Content-Disposition' => 'attachment; filename=' . $file->id . '.' . ($file->file_extension ? $file->file_extension : $file->file_ext),
            'Content-Type' => $file->mime_type,
            'Access-Control-Allow-Origin' => '*'
        ];
        if ($file->mime_type == 'application/pdf') {
            return getS3TempUrl($path, 5);
        }
        return response()->make(Storage::disk('s3')->get($path), 200, $headers);
    } else {
        $path = storage_path('/app' . $path);
    }
    return response()->file($path);
}
function showThumbnail($file)
{
    $pdf_types = ['application/pdf'];
    if (in_array($file->mime_type, $pdf_types)) {
        $path = public_path() . "/images/pdf.png";
    } else {
        $path = public_path() . "/images/other.png";
    }
    return response()->file($path);
}


function voucherTypes(){
    $arr =[];
    $permissions = getPermissions();
    if ( array_intersect($permissions, [
        'receipt-vouchers-add',
        'receipt-vouchers-modify'
    ])) {
        $arr[] = [
            'text'=>'Receipt',
            'id'=>'R'
        ];
    }
    if ( array_intersect($permissions, [
        'payment-vouchers-add',
        'payment-vouchers-modify'
    ])) {
        $arr[] = [
            'text'=>'Payment',
            'id'=>'P'
        ];
    }
    if ( array_intersect($permissions, [
        'journal-vouchers-add',
        'journal-vouchers-modify'
    ])) {
        $arr[] = [
            'text'=>'Journal',
            'id'=>'J'
        ];
    }
    // if ( array_intersect($permissions, [
    //     'interest-vouchers-add',
    //     'interest-vouchers-modify'
    // ])) {
    //     $arr[] = [
    //         'text'=>'Interest',
    //         'id'=>'I'
    //     ];
    // }
    if ( array_intersect($permissions, [
        'transfer-vouchers-add',
        'transfer-vouchers-modify'
    ])) {
        $arr[] = [
            'text'=>'Transfer',
            'id'=>'T'
        ];
    }
    return $arr;

}
function getBillWiseConcept()
{
    return config('bcl.bill_wise_concept');
}
function getDateComp($dt1, $dt2)
{
    if (strpos($dt1, '-') < 3) {
        $dt1 =  Carbon::createFromFormat('d-m-Y', $dt1);
    } else {
        $dt1 =  Carbon::createFromFormat('Y-m-d', $dt1);
    }
    if (strpos($dt2, '-') < 3) {
        $dt2 =  Carbon::createFromFormat('d-m-Y', $dt2);
    } else {
        $dt2 =  Carbon::createFromFormat('Y-m-d', $dt2);
    }
    $days = $dt1->diffInDays($dt2, false);
    if ($days == 0) {
        return "E";
    } elseif ($days > 0) {
        return "S";
    } else {
        return "G";
    }
}
function getCash($date, $opening_closing = 'C', $vcode = '')
{
    if ($date) {
        $cash = Cashbook::where('cash_cr', 'C')->where('vcode', '!=', $vcode);
        if ($opening_closing == 'O') {
            $cash = $cash->where('trans_date', '<', getDateFormat($date, 'ymd'));
        } else {
            $cash = $cash->where('trans_date', '<=', getDateFormat($date, 'ymd'));
        }
        $cash = $cash->sum(DB::raw("case when dr_cr = 'C' then amount else -1*amount end"));
    } else {
        $cash = 0;
    }
    return $cash + getOpeningCash();
}


function getOpeningCash()
{
    $comp_id = session()->get('company_id', 1);
    $comp = Company::find($comp_id);
    return $comp ? $comp->opening_cash : 0;
}

function checkNegativeCash($dt1, $dt2)
{
    return '';
    $date = $dt1;
    if ($dt2 && getDateComp($dt2, $dt1) == 'S') {
        $date = $dt2;
    }
    $cash = getCash($date);
    $dt1 = getDateFormat($date, 'ymd');
    $comp_id = session()->get('company_id', 1);
    $company = Company::find($comp_id);
    $data = Cashbook::where('cash_cr', 'C')->select(
        'trans_date',
        DB::raw("sum(case when dr_cr = 'C' then amount else -1*amount end) as amount")
    )->where('trans_date', '>', $dt1)->groupBy('trans_date')->orderBy('trans_date')->get();
    $msg = '';
    if ($company->neg_cash == 'N') {
        $msg = addString($msg, ($cash < 0) ? "Cash is $cash in $date" : '', '<br>');
        foreach ($data as $val) {
            $cash += $val->amount;
            $msg = addString($msg, ($cash < 0) ? "Cash is $cash in " . $val->trans_date : '', '<br>');
        }
    }
    return $msg;
}


function joinArrayByCommaSepratedString($array){
    $joinedString = '';
    $count = count($array);
    if ($count > 1) {
        $lastItem = array_pop($array); // Remove the last item from the array
        $joinedString = implode(', ', $array); // Join the remaining elements with commas
        $finalString = $joinedString . ' and ' . $lastItem; // Add "and" before the last item
    } else {
        $finalString = implode('', $array); // If there's only one item, no need for commas or "and"
    }

    return $finalString;

}

function convertToZero($value) {
    if ($value === null || $value === '') {
        return 0;
    }
    return $value;
}


function checkActiveAccount($acid){
    $account = Account::findOrFail($acid);
    if($account->active =='N')
    {
        return false;
    }
    return true;

}


function getSaleOrderCredit($acid){ // ORDERS for which Sale Invoice has not been created yet
    $account = Account::findOrFail($acid);
    $sale_order_amount = SaleOrder::where('sale_orders.bill_party_id',$acid)->leftJoin('invoices','invoices.sale_order_id','=','sale_orders.id')
                ->whereNull('invoices.sale_order_id')
                ->selectRaw('SUM(sale_orders.net_amt) as net_amt')
                ->first()->net_amt;
    return $sale_order_amount ?? 0;
}


function getAccountBalanceLimit($acid){
    $account = Account::findOrFail($acid);
    $credit_limit = $account->getAccountLimit();
    $cashbook_balance = $account->getAccountCashbookBalance();
    $sale_order_credit = getSaleOrderCredit($acid);
    return $credit_limit - ($cashbook_balance + $sale_order_credit);
}

function getGstReasons()
{
    $gst_reasons = GstReason::pluck('reason_disp_name', 'id');
    return $gst_reasons;
}

function getSessionYear(){
    $years = [];
    $session_year = session()->get('fy');
    // $session_year = "20282029";
    $years[$session_year] = $session_year;
    $year = substr($session_year, 0, 4);
    $lastyear = substr($session_year,-4);
    $yr= $session_year;
    if($yr != 20232024){
        $year = $year-1;
        $lastyear = $lastyear-1;
        $yr = $year.$lastyear;
        $years[$year.$lastyear] = $year.$lastyear;
    }
    if($yr != 20232024){
        $year = $year-1;
        $lastyear = $lastyear-1;
        $years[$year.$lastyear] = $year.$lastyear;
    }
    return $years;
}

function checkBillNo($bill_no,$yr,$acid){
    $comp_id = session()->get('company_id');
    $database = config('database.data_name').$comp_id.'_'.$yr;
    $inv = DB::table($database. '.invoices')
            ->where('invoices.invoice_no',$bill_no)->where('invoices.bill_party_id','=',$acid)->select('invoices.*')->get();
    $msg = '';
    if (count($inv) == 0) {
        $msg = 'Party Bill No is Mismatch !!';
    }
    return $msg;
}

function getHeaderPrint($title,$branch_id,$company,$pdf){
    $branch = Branch::find($branch_id);
    $width= 0;
    // dd($pdf);
    // $pdf->$pdf->ln(2);
    $pdf->addCell($width, 0,$title , 0, 'C', 1, '', 10, 'B', false, false, true);
    $pdf->addHLine();
    $pdf->addCell($width, 0,strtoupper($branch['name']), 0, 'C', 1, '', 12, 'B', false, false, true);
    if($branch['address_1'] && $branch['address_1'] != '') {
        $pdf->addCell($width, 0, $branch['address_1'] , 0, 'C', 1, '', 9, '', false, false, true);
    }
    if($branch['address_2'] && $branch['address_2'] != '') {
        $pdf->addCell($width, 0, $branch['address_2'] , 0, 'C', 1, '', 9, '', false, false, true);
    }
    if($branch['address_3'] && $branch['address_3'] != '') {
        $pdf->addCell($width, 0, $branch['address_3'] , 0, 'C', 1, '', 9, '', false, false, true);
    }

    $pdf->addHLine();
    $pdf->GetYPos(true);
    // $pdf1->ln(2);

    $pdf->addCols([88,20,68,40]);
    $pdf->addCell($pdf->getColW(1), 0, 'GSTIN : '.$company['gst_number'], 0, 'L', 0, 'T', 8, '');
    $pdf->addCell($pdf->getColW(2), 0, 'CIN No. : ', 0, 'L', 0, 'T', 8, 'B');
    $pdf->addCell($pdf->getColW(3), 0, $company['cin_number'], 0, 'L', 1, 'T', 8, '');
    $pdf->addCell($pdf->getColW(1), 0, 'PAN No.: '.$company['pan_number'], 0, 'L', 0, 'T', 8, '');
    $pdf->addCell($pdf->getColW(2), 0, 'Phone No. :', 0, 'L', 0, 'T', 8, 'B');
    $pdf->addCell($pdf->getColW(3), 0,  $branch['phone'] .' Fax No.: '.$branch['fax'], 0, 'L', 1, 'T', 8, '');
    $pdf->addCell($pdf->getColW(1), 0, 'TAN No: '.$branch['tan'], 0, 'L', 0, 'T', 8, '');
    $pdf->addCell($pdf->getColW(2), 0, 'Email ID. :', 0, 'L', 0, 'T', 8, 'B');
    $pdf->addCell($pdf->getColW(3), 0, $branch['email'], 0, 'L', 1, 'T', 8, '');
    $pdf->addCell($pdf->getColW(1), 0, 'FSSAI Lic No. : '.$branch['fssai'], 0, 'L', 1, 'T', 8, '');

}


function getPurOrderTypes(){
    return
    [
        ['id'=>'Normal','text'=>'Normal'],
        ['id'=>'NON MANDI FOR PURCHASES','text'=>'NON MANDI FOR PURCHASES'],
        ['id'=>'MANDI PURCHASES','text'=>'MANDI PURCHASES'],
        ['id'=>'RICE BRAN PURCHASE','text'=>'RICE BRAN PURCHASE'],
        ['id'=>'OUT OF PUNJAB PURCHASE','text'=>'OUT OF PUNJAB PURCHASE'],
        ['id'=>'CONVERSION PURCHASE','text'=>'CONVERSION PURCHASE'],
        ['id'=>'HIGH SEAS AND BOND/IMPORT','text'=>'HIGH SEAS AND BOND/IMPORT'],
        ['id'=>'DIRECT GENERAL GST PURCHASE','text'=>'DIRECT GENERAL GST PURCHASE'],
        ['id'=>'STORE PURCHASE','text'=>'STORE PURCHASE'],
    ];
}
