<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MonthlyReport
 *
 * @author Ani
 */

namespace App\Printings;

use App\Models\Masters\Branch;
use App\Models\Yearly\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Yearly\TransGstDetail;

class GstDocumentPrint extends PrintPdf
{
    protected $testpdf = null;
    protected $pageno = 0;
    protected $nextpage = '';
    protected $copyno = 0;
    protected $secHeight = 0;
    protected $hight = 0;
    protected $footerHeight = 0;
    protected $detailHeight = 0;
    protected $detHeight = 0;
    protected $company = null;
    protected $border_gap = 0;
    protected $sale_order = null;
    protected $packings = [];
    protected $gst_docs = null;
    protected $sale_order_dispatches = [];
    protected $det_cols =[];
    protected $total_qty = 0;
    protected $total_weight = 0;
    protected $total_discount = 0;
    protected $total_amount = 0;
    protected $amt_without_gst = 0;
    protected $term_conditions = null;
    protected $gst_rates = [];

    public function makepdf($gst_docs)
    {
        $this->gst_docs = $gst_docs;
        $this->gst_docs->load([
            'account','transport','details.item', 'trans_gst_details', 'details.doc_account','user_created','details.gst_details'
        ]);
        $this->company = getCom();
        $this->testpdf = new TestPdf();
        $this->testpdf->makePdf("P", "A4");
        $this->main();
        return $this->pdf;
    }

    private function setCopy($copyno)
    {
        $pdf = $this->pdf;
        $this->copyno = $copyno;
        $this->header();
        $this->details();
        $this->footer("E");
    }

    private function main($title = '')
    {
        $pdf = $this->pdf;
        $this->pageno = 0;
        $this->footerHeight = 70;
        do {
            $this->pageno++;
            $this->nextpage = "N";
            $this->addNewPage("P", "A4");
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->setCopy(1);
        } while ($this->nextpage == "Y");
    }


    private function header()
    {
        $pdf = $this->pdf;
        $this->lineHeight = 5;
        $leftSideYPos = 0;
        $this->pdf->Line($this->lm, $this->pdf->GetY(), $this->pageWidth + $this->lm, $this->pdf->GetY());
        $this->GetYPos(true);
        $this->pdf->ln(2);
        $width = 0;

        if ($this->gst_docs->doc_type == 'DN') {
            $title = 'DEBIT NOTE';
        } elseif ($this->gst_docs->doc_type == 'CN') {
            $title = 'CREDIT NOTE';
        }
        $branch_id = getCurrentBranchId();

        // getHeaderPrint($title,$branch_id,$this->company,$this);

        $branch = Branch::find($this->gst_docs['branch_id']);
        $width= 0;
        // dd($pdf);
        // $pdf->$pdf->ln(2);
        $this->addCell($width, 0,$title , 0, 'C', 1, '', 12, 'B', false, false, true);
        $this->pdf->ln(2);
        $this->addHLine();
        $this->addCell($width, 0,strtoupper($branch['name']), 0, 'C', 1, '', 12, 'B', false, false, true);
        if($branch['address_1'] && $branch['address_1'] != '') {
            $this->addCell($width, 0, $branch['address_1'] , 0, 'C', 1, '', 9, '', false, false, true);
        }
        if($branch['address_2'] && $branch['address_2'] != '') {
            $this->addCell($width, 0, $branch['address_2'] , 0, 'C', 1, '', 9, '', false, false, true);
        }
        if($branch['address_3'] && $branch['address_3'] != '') {
            $this->addCell($width, 0, $branch['address_3'] , 0, 'C', 1, '', 9, '', false, false, true);
        }

        $this->addHLine();
        $this->GetYPos(true);
        // $this1->ln(2);
        $font = 10;
        $this->addCols([88,20,68,40]);
        $this->addCell($this->getColW(1), 0, 'GSTIN : '.$this->company['gst_number'], 0, 'L', 0, 'T', $font, '');
        $this->addCell($this->getColW(2), 0, 'CIN No. : ', 0, 'L', 0, 'T', $font, '');
        $this->addCell($this->getColW(3), 0, $this->company['cin_number'], 0, 'L', 1, 'T', $font, '');
        $this->addCell($this->getColW(1), 0, 'PAN No.: '.$this->company['pan_number'], 0, 'L', 0, 'T', $font, '');
        $this->addCell($this->getColW(2), 0, 'Phone No. :', 0, 'L', 0, 'T', $font, '');
        $this->addCell($this->getColW(3), 0,  $branch['phone'] .' Fax No.: '.$branch['fax'], 0, 'L', 1, 'T', $font, '');
        $this->addCell($this->getColW(1), 0, 'TAN No: '.$branch['tan'], 0, 'L', 0, 'T', $font, '');
        $this->addCell($this->getColW(2), 0, 'Email ID. :', 0, 'L', 0, 'T', $font, '');
        $this->addCell($this->getColW(3), 0, $branch['email'], 0, 'L', 1, 'T', $font, '');
        $this->addCell($this->getColW(1), 0, 'FSSAI Lic No. : '.$branch['fssai'], 0, 'L', 1, 'T', $font, '');
        $this->pdf->ln(1);
        $this->GetYPos(true);
        $this->addHLine();
        $this->GetYPos();
        $this->addCols([20, 3, 56, 20, 3, 27, 24, 3, 20]);
        $this->pdf->ln(3);
        $this->addCell($this->getColW(1, 2), 0, 'M/s', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(3, 6), 0, $this->gst_docs->account->name, 0, 'L', 0, 'T', 10, 'B');
        $cr_dr = '';
        if ($this->gst_docs->doc_type == 'DN') {
            $cr_dr = 'Cr';
            $this->addCell($this->getColW(7), 0, 'Debit Note No.', 0, 'L', 0, 'T', 10, 'B');
        } elseif ($this->gst_docs->doc_type == 'CN') {
            $cr_dr = 'Dr';
            $this->addCell($this->getColW(7), 0, 'Credit Note No.', 0, 'L', 0, 'T', 10, 'B');
        }
        $this->addCell($this->getColW(8), 0, ':', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(9), 0, $this->gst_docs->doc_no_print, 0, 'L', 1, 'T', 10, '');
        $this->addCell($this->getColW(1, 2), 0, '', 0, 'L', 0, 'T', 10, 'B');
        // dd($this->gst_docs['account']['account_detail'] );
        $add = $this->gst_docs['account'] ? $this->gst_docs['account']['account_detail']['add1'] : '';
        $add .= $this->gst_docs['account']? $this->gst_docs['account']['account_detail'] != '' ? ', '.$this->gst_docs['account']['account_detail']['add2']:'' :'';
        $add .= $this->gst_docs['account'] ? $this->gst_docs['account']['account_detail'] != '' ? ', '.$this->gst_docs['account']['account_detail']['add3']:'' :'';
        $this->addMCell($this->getColW(3, 6), 0, $add, 0, 'L', 0, 'L', 9, '');

        $this->addCell($this->getColW(7), 0, 'Date', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(8), 0, ':', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(9), 0, $this->gst_docs->doc_date, 0, 'L', 1, 'T', 10, '');
        if (strlen($add) > 60) {
            $this->pdf->ln(3);
        }
        $city = $this->gst_docs['account'] ? $this->gst_docs['account']['account_detail'] != '' ? $this->gst_docs['account']['account_detail']['city']:'' :'';
        $phone= $this->gst_docs['account'] ? $this->gst_docs['account']['account_detail'] != '' ? $this->gst_docs['account']['account_detail']['phone_no']:'' :'';
        $city_name = $city ? $city['name']:'';
        $this->addCell($this->getColW(1, 2), 0, '', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(3), 0, $city_name, 0, 'L', 1, 'T', 10, '');
        $this->addCell($this->getColW(1, 2), 0, '', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(3), 0, 'Mobile No. :'.$phone, 0, 'L', 1, 'T', 10, '');
        $this->pdf->ln(2);
        $this->pdf->ln(1);
        $this->addCell($this->getColW(1), 0, 'GST No', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(2), 0, ':', 0, 'L', 0, 'T', 10, 'B');
        $gst_no= $this->gst_docs['account'] ? $this->gst_docs['account']['account_detail'] != '' ? $this->gst_docs['account']['account_detail']['gst_no']:'' :'';
        $this->addCell($this->getColW(3), 0, $gst_no, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(4, 6), 0, '', 0, 'L', 0, 'T', 10, 'B');
        // dd($this->gst_docs);
        $this->addCell($this->getColW(7), 0, $cr_dr.'.Note No.', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(8), 0, ':', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(9), 0,$this->gst_docs['party_doc_no'], 0, 'L', 1, 'T', 10, '');
        $this->pdf->ln(1);
        $state = $this->gst_docs['account']['account_detail'] && $this->gst_docs['account']['account_detail']['city']
        && $this->gst_docs['account']['account_detail']['city']['district'] && $this->gst_docs['account']['account_detail']['city']['district']['state']
        ?  $this->gst_docs['account']['account_detail']['city']['district']['state']:'';
        $state_name = $state ? $state['name']:'';
        $state_code = $state ? $state['state_code']:'';
        $this->addCell($this->getColW(1), 0, 'State', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(2), 0, ':', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(3), 0, $state_name, 0, 'L', 0, 'T', 10, '');
        // $this->addCell($this->getColW(2), 0, $debit->account->city->state->name, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(4), 0, 'State Code', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(5), 0, ':', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(6), 0,$state_code, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(7), 0, $cr_dr.'.Note Dt', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(8), 0, ':', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(9), 0, $this->gst_docs['party_doc_date'], 0, 'L', 1, 'T', 10, '');
        $this->addHLine();
        $this->addVLine($this->lm + 126);
        $this->pdf->ln(1);
        // dd($this->gst_docs);
        $sale = '';
        if($this->gst_docs['fyear']){
            $comp_id = session()->get('company_id');
            $database = config('database.data_name').$comp_id.'_'.$this->gst_docs['fyear'];
            $sale = DB::table($database. '.invoices')
                    ->where('invoices.invoice_no',$this->gst_docs['bill_no'])->first();
        }

        // $sale = Invoice::where('invoice_no',$this->gst_docs['bill_no'])->first();
        // dd($sale->invoice_no);
        $this->addCell($this->getColW(1,6), 0, '', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(7), 0, 'Invoice No.', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(8), 0, ':', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(9), 0, $sale ? $sale->invoice_no : '', 0, 'L', 1, 'T', 10, '');
        $this->pdf->ln(1);
        $this->addCell($this->getColW(1,6), 0, '', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(7), 0, 'Invoice Date', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(8), 0, ':', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(9), 0, $sale ? $sale->invoice_date :'', 0, 'L', 1, 'T', 10, '');

        $this->pdf->ln(1);
        $this->addCell($this->getColW(1), 0, 'GR No.', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(2), 0, ':', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(3,6), 0, $this->gst_docs['gr_no'], 0, 'L', 0, 'T', 10, '');

        $this->addCell($this->getColW(7), 0, 'GR Date', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(8), 0, ':', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(9), 0, $this->gst_docs['gr_date'], 0, 'L', 1, 'T', 10, '');

        $this->pdf->ln(1);
        $this->addCell($this->getColW(1), 0, 'Transport', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(2), 0, ':', 0, 'L', 0, 'T', 10, 'B');
        $transport = $this->gst_docs['transport'] ? $this->gst_docs['transport']['name'] : '';
        $this->addCell($this->getColW(3,6), 0, $transport, 0, 'L', 0, 'T', 10, '');

        $this->addCell($this->getColW(7), 0, 'Vehicle No.', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(8), 0, ':', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(9), 0, $this->gst_docs['veh_no'], 0, 'L', 1, 'T', 10, '');
        $this->pdf->ln(1);
        $this->addHLine();
        $this->GetYPos(true);
        $this->addCols([10, 60, 25, 25, 20, 20,15]);
        $this->pdf->ln(1);
        $this->addMCell($this->getColW(1), $this->lineHeight,  'Sr.No.' , 0, 'C', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(2), $this->lineHeight,  'Particullars', 0, 'C', 0, 'T', 9,'B');
        $this->addMCell($this->getColW(3), $this->lineHeight,  'HSN' , 0, 'C', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(4), $this->lineHeight,  'Qty.' , 0, 'C', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(5), $this->lineHeight,  'Weight' , 0, 'C', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(6), $this->lineHeight,  'Rate' , 0, 'C', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(7), $this->lineHeight,  'GST%', 0, 'C', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(8), $this->lineHeight,  'Amount', 0, 'C', 1, 'T', 9, 'B');
        $this->pdf->ln(1);

        $this->addHLine();
        // $this->GetYPos(true);
        $this->hight = $this->getNetHeight();
        $this->detailHeight = $this->hight - $pdf->getY() - $this->footerHeight;
        $this->secHeight = 0;

    }




    private function details($title = '')
    {

        $count = 1;
        foreach ($this->gst_docs['details'] as $key=>$detail) {
            $this->addDetail($detail,$count);
            $count++;
        }
        $this->drawBox($this->pdf->getY(), false);
    }

    private function addDetail($detail_det,$count)
    {
        $gst_docs = $this->gst_docs;
        $this->detHeight = 0;
        $gst_prec = 0;
        $gst_detail = TransGstDetail::where('vcode',$gst_docs->vcode)->where('det_id',$detail_det['id'])->get();
        foreach ( $gst_detail as $gst) {
            $gst_prec += $gst->gst_rate;
        }

        $item_name = $detail_det['item'] ? $detail_det['item']['item_name'] :'';
        $brand_name = $detail_det['brand'] ? $detail_det['brand']['name'] :'';
        $packing_name = $detail_det['packing'] ? $detail_det['packing']['name'] :'';
        $this->testpdf->addMCell($this->getColW(2), $this->lineHeight,  $item_name . ' '. $brand_name .' ',  $packing_name, 0, 'L', 0, 'T', 0, '');
        if ($this->secHeight + $this->testpdf->testHeight > $this->detailHeight){
            $this->startNewPage();
        }
        $item_name = $item_name . ' '. $brand_name .' '.  $packing_name;
        $this->addMCell($this->getColW(1), 0, $count, 0, 'C', 0, 'T', 9, '');
        $this->addMCell($this->getColW(2), 0, $item_name, 0, 'L', 0, 'L', 9, '');
        $this->addMCell($this->getColW(3), 0, $detail_det['item']['hsn_code'], 0, 'C', 0, 'T', 9, '');
        $this->addMCell($this->getColW(4), 0, $detail_det['qty'], 0, 'R', 0, 'T', 9, '');

        $this->addMCell($this->getColW(5), 0, $detail_det['weight'], 0, 'R', 0, 'T', 9, '');
        $this->addMCell($this->getColW(6), 0, $detail_det['rate'], 0, 'R', 0, 'T', 9, '');
        $this->addMCell($this->getColW(7), 0, $gst_prec, 0, 'R', 0, 'T', 9, '');
        $this->addMCell($this->getColW(8), 0, $detail_det['amount'], 0, 'R', 0, 'T', 9, '');

        $this->total_qty += $detail_det['qty'];
        $this->total_weight += $detail_det['weight'];
        $this->total_amount += $detail_det['amount'];

        $this->pdf->ln($this->detHeight);
        $this->secHeight += $this->detHeight;

    }



    private function footer($type = "R")
    {
        $gst_docs = $this->gst_docs;
        if ($this->secHeight > $this->detailHeight) {
            $this->drawBox($this->pdf->getY(), false);
            $this->pdf->Line($this->lm, $this->pdf->GetY(), $this->pageWidth + $this->lm, $this->pdf->GetY());
            $this->startNewPage('N');
        }

        if ($this->detailHeight - $this->secHeight > 0) {
            $this->pdf->ln($this->detailHeight - $this->secHeight);
        }

        $this->drawBox($this->pdf->getY(), false);
        $this->addHLine();

        if ($type == "E") {
            // $this->checkEndFooterHeight();
            $this->GetYPos(true);
            $this->addCols([10, 60, 25, 25, 20, 20,15]);
            $this->addMCell($this->getColW(1,3), $this->lineHeight, 'Total', 0, 'C', 0, 'T', 9, 'B');
            $this->addCell($this->getColW(4), $this->lineHeight, myRound($this->total_qty), 0, 'R', 0, 'T', 9, 'B');
            $this->addCell($this->getColW(5), $this->lineHeight, myRound($this->total_weight,3), 0, 'R', 1, 'T', 9, 'B');
            // $this->addCell($this->getColW(6), $this->lineHeight, '', 0, 'R', 0, 'T', 9, 'B');
            // $this->addCell($this->getColW(7), $this->lineHeight, '', 0, 'R', 0, 'T', 9, 'B');
            // $this->addCell($this->getColW(8), $this->lineHeight, myRound($this->total_amount ? $this->total_amount :'0.00'), 0, 'R', 1, 'T', 9, 'B');


            $this->drawBox($this->pdf->getY(), false);
            $this->addHLine();
            $this->GetYPos(true);
            $this->addCols([175]);
            $this->addMCell($this->getColW(1), $this->lineHeight, 'Total ', 0, 'R', 0, 'T', 9, '');
            $this->addMCell($this->getColW(2), $this->lineHeight, myRound($this->total_amount ? $this->total_amount :'0.00'), 0, 'R', 1, 'T', 10, 'B');
            // dd($gst_docs);
            $gst_details = TransGstDetail::gst_details($gst_docs->vcode);
            $gst_amount = 0;
            // dd($gst_details);
            $i = 0;
            foreach ($gst_details as $g) {
            //   echo($i .$g->gst_name);
                $this->addCell($this->getColW(1), $this->lineHeight,'Add '.$g->gst_name." @" . $g->gst_rate . ' On ' . $g->gst_on . '' . ':', 0, 'R', 0, 'T', 10, '');
                $this->addCell($this->getColW(2), $this->lineHeight, number_format($g->gst_value, 2, '.', ''), 0, 'R', 1, 'L', 10, 'B');
                $i++;
                $gst_amount += $g->gst_value;
            }

            $this->addCell($this->getColW(1), $this->lineHeight, "GST Total:", 0, 'R', 0, 'T', 10, '');
            $this->addCell($this->getColW(2), $this->lineHeight, number_format($gst_amount, 2, '.', ''), 0, 'R', 1, 'T', 10, '');
            $this->drawBox($this->pdf->getY(), false);
            $this->addHLine();
            $this->GetYPos(true);
            $this->addCols([150,25]);
            $grand_tot = $this->total_amount+$gst_amount;
            $this->addCell($this->getColW(1), $this->lineHeight, 'Amount In words : '.figToWord($grand_tot), 0, 'L', 0, 'T', 10, 'B');
            $this->addCell($this->getColW(2), $this->lineHeight,'Grand Total :', 0, 'R', 0, 'T', 10, 'B');
            $this->addCell($this->getColW(3), $this->lineHeight,  number_format($gst_amount, 2, '.', ''), 0, 'R', 0, 'T', 10, 'B');
            $this->pdf->ln(4);
            $this->drawBox($this->pdf->getY(), false);

            $this->addHLine();
            $this->addCols([17, 40, 60, 60]);
            $this->pdf->ln(2);
            $this->addCell($this->getColW(1), $this->lineHeight, "Remarks : ", 0, 'L', 0, 'T', 10, 'B');
            $this->addMCell($this->getColW(2, 4), $this->lineHeight, $gst_docs->remarks, 0, 'L', 1, 'T', 10, '');
            $this->pdf->ln(2);
            $this->addCell($this->getColW(1), $this->lineHeight, "Terms & Conditions :- ", 0, 'L', 0, 'T', 10, 'B');
            $this->addCell($this->getColW(2, 5), $this->lineHeight, "FOR " .  $this->company['company_name'], 0, 'R', 0, 'T', 10, '');

            if ($this->pageno != 1) {
                $this->drawBorder();
            }
        } else {
            $this->addCols([170]);
            $this->addCell($this->getColW(1), $this->lineHeight, "Continued.....", 0, 'R', 1, 'T', 10, '');
        }

        $this->addCols([20]);

        $currentPage = $this->pdf->getPage();
        $totalPages = $this->pdf->getNumPages();
        // $this->addCell(0, $this->lineHeight, "FOR " .  $this->company['company_name'], 0, 'R', 1, 'T', 10, 'B');
        $this->pdf->ln(20);
        $this->addCell(0, $this->lineHeight, 'Authorised Signatory', 0, 'R', 1, 'T', 10, 'B');
        $footerText = "Page {$currentPage} of {$totalPages}";
        $this->pdf->SetY(-10);
        $this->addCell($this->getColW(1), $this->lineHeight, 'E.&.O.E.' , 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(2), $this->lineHeight,  $footerText , 0, 'R', 1, 'T', 8, 'B');
        $this->drawBorder($this->border_gap);
    }


    private function startNewPage($footer = 'Y')
    {
        // $this->footerHeight = 20;
        if ($footer == 'Y')
            $this->footer();
        $this->secHeight = 0;
        $this->pageno++;
        $this->addNewPage("P", "A4");
        $this->header();

    }
}
