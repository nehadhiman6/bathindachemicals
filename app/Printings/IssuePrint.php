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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IssuePrint extends PrintPdf
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
    protected $issue = null;
    protected $sale_order_dispatches = [];
    protected $det_cols =[];
    protected $total_qty = 0;
    protected $total_weight = 0;
    protected $total_discount = 0;
    protected $total_amount = 0;
    protected $amt_without_gst = 0;
    protected $term_conditions = null;
    protected $gst_rates = [];

    public function makepdf($issue)
    {
        $this->issue = $issue;
        $this->issue->load([
            'branch_from.city.district.state','branch_to.company','transport','details.item'
            ,'details.item_unit','details.packing'
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
        $this->footerHeight = 40;
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
        $title = 'STOCK TRANSFER DELIVERY CHALLAN';
        $branch_id = getCurrentBranchId();

        // getHeaderPrint($title,$branch_id,$this->company,$this);

        $branch = Branch::find($this->issue['branch_id_from']);
        $width= 0;
         $arr = explode(",",$branch->skip_add_branches);
        $new_array= [];
        foreach ($arr as $key => $value) {
            $new_array[$value] = $value;
        }
        // dd($pdf);
        // $pdf->$pdf->ln(2);
        $this->addCell($width, 0,$title , 0, 'C', 1, '', 12, 'B', false, false, true);
        $this->pdf->ln(2);
        $this->addHLine();
        $this->addCell($width, 0,strtoupper($branch['name']), 0, 'C', 1, '', 12, 'B', false, false, true);
        if(!array_key_exists($this->issue['branch_id_from'],$new_array)){
            if($branch['address_1'] && $branch['address_1'] != '') {
                $this->addCell($width, 0, $branch['address_1'] , 0, 'C', 1, '', 9, '', false, false, true);
            }
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
        $this->addCols([20,5,25,20,5,25,20,5,25,20,5]);
        $this->addCell($this->getColW(1), 0,'Challan No', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(2), 0,':', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(3,6), 0, $this->issue->iss_doc_no_print, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(7), 0,'Transport', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(8), 0, ':', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(9,12), 0, $this->issue->transport ?$this->issue->transport->name : '' , 0, 'L', 1, 'T', 10, '');


        $this->addCell($this->getColW(1), 0,'Challan Date', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(2), 0,':', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(3,6), 0, $this->issue->iss_date, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(7), 0,'Vehicle No', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(8), 0, ':', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(9,12), 0, $this->issue->vehical_no , 0, 'L', 1, 'T', 10, '');

        $this->addCell($this->getColW(1), 0,'State', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(2), 0,':', 0, 'L', 0, 'T', 10, '');
        $issue_state = $this->issue['transport']['account_detail'] && $this->issue['transport']['account_detail']['city']
        &&  $this->issue['transport']['account_detail']['city']['district'] &&  $this->issue['transport']['account_detail']['city']['district']['state']
        ?   $this->issue['transport']['account_detail']['city']['district']['state']:'';
        $state_name = $issue_state ? $issue_state['name']:'';
        $state_code = $issue_state ? $issue_state['state_code']:'';
        $this->addCell($this->getColW(3), 0, $state_name, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(4), 0,'Code', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(5), 0, ':', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(6), 0, $state_code, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(7), 0,'GR No.', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(8), 0, ':', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(9), 0, $this->issue->gr_no , 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(10), 0,'GR Date', 0, 'L', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(11), 0, ':', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(12), 0, $this->issue->gr_date, 0, 'L', 1, 'T', 10, '');
        $this->addVLine($this->lm + 100);


        $this->pdf->ln(1);
        $this->GetYPos(true);
        $this->addHLine();
        $this->pdf->ln(2);
        $this->addCols([30,5,50,20,5]);
        $this->addCell($this->getColW(1), 0,'Name', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(2), 0,':', 0, 'L', 0, 'T', 10, '');
        // dd($this->issue);
        $this->addCell($this->getColW(3,6), 0, $this->issue['branch_to']['name'], 0, 'L', 1, 'T', 10, '');
        $this->pdf->ln(2);
            $add = $this->issue['branch_to']['address_1'];
            $add .= $this->issue['branch_to']['address_2'] != '' ?', '.$this->issue['branch_to']['address_2'] :'';
            $add .= $this->issue['branch_to']['address_3'] != '' ?', '.$this->issue['branch_to']['address_3'] :'';
        $this->addCell($this->getColW(1), 0,'Address', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(2), 0,':', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(3,6), 0, $add, 0, 'L', 1, 'T', 10, '');
        $this->pdf->ln(2);
        $this->addCell($this->getColW(1), 0,'GSTIN', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(2), 0,':', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(3,6), 0, $this->issue['branch_to']['company']['gst_number'], 0, 'L', 1, 'T', 10, '');
        $this->pdf->ln(2);
        $issue_state = $this->issue['branch_to'] && $this->issue['branch_to']['city']
        &&  $this->issue['branch_to']['city']['district'] &&  $this->issue['branch_to']['city']['district']['state']
        ?   $this->issue['branch_to']['city']['district']['state']:'';
        $state_name = $issue_state ? $issue_state['name']:'';
        $state_code = $issue_state ? $issue_state['state_code']:'';
        $this->addCell($this->getColW(1), 0,'State', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(2), 0,':', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(3), 0, $state_name, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(4), 0,'State Code', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(5), 0,':', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(6), 0, $state_code, 0, 'L', 1, 'T', 10, '');
        $this->pdf->ln(2);
        $this->addHLine();
        $this->GetYPos(true);
        $this->addCols([15,60,25,25,25,25]);
        $this->pdf->ln(1);
        $this->addMCell($this->getColW(1), $this->lineHeight,  'Sr. No.' , 0, 'C', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(2), $this->lineHeight,  'Name of Product/Service ', 0, 'C', 0, 'T', 9,'B');
        $this->addMCell($this->getColW(3), $this->lineHeight,  'HSN/ SAC' , 0, 'C', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(4), $this->lineHeight,  'Qty.' , 0, 'C', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(5), $this->lineHeight,  'Weight' , 0, 'C', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(6), $this->lineHeight,  'Rate' , 0, 'C', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(7), $this->lineHeight,  'Approx Value', 0, 'C', 1, 'T', 9, 'B');
        // $this->pdf->ln($this->detHeight);
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
        foreach ($this->issue['details'] as $key=>$detail) {
            $this->addDetail($detail,$count);
            $count++;
        }
        $this->drawBox($this->pdf->getY(), false);
    }

    private function addDetail($detail_det,$count)
    {
        $this->detHeight = 0;
        $item_name = $detail_det['item'] ? $detail_det['item']['item_name'] :'';
        $this->testpdf->addMCell($this->getColW(2), $this->lineHeight,  $item_name , 0, 'L', 0, 'T', 0, '');
        if ($this->secHeight + $this->testpdf->testHeight > $this->detailHeight){
            $this->startNewPage();
        }

        $this->addMCell($this->getColW(1), 0, $count, 0, 'C', 0, 'T', 9, '');
        $this->addMCell($this->getColW(2), 0, $item_name, 0, 'L', 0, 'L', 9, '');
        $this->addMCell($this->getColW(3), 0, $detail_det['item']['hsn_code'], 0, 'C', 0, 'T', 9, '');
        $this->addMCell($this->getColW(4), 0, $detail_det['qty'], 0, 'R', 0, 'T', 9, '');

        $this->addMCell($this->getColW(5), 0, $detail_det['weight'], 0, 'R', 0, 'T', 9, '');
        $this->addMCell($this->getColW(6), 0, $detail_det['rate'], 0, 'R', 0, 'T', 9, '');
        $this->addMCell($this->getColW(7), 0, $detail_det['amount'], 0, 'R', 0, 'T', 9, '');

        $this->total_qty += $detail_det['qty'];
        $this->total_weight += $detail_det['weight'];
        $this->total_amount += $detail_det['amount'];

        $this->pdf->ln($this->detHeight);
        $this->secHeight += $this->detHeight;

    }



    private function footer($type = "R")
    {
        $issue = $this->issue;
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
            $this->addCols([15,60,25,25,25,25]);
            $this->addMCell($this->getColW(1,3), $this->lineHeight, 'Total', 0, 'C', 0, 'T', 9, 'B');
            $this->addCell($this->getColW(4), $this->lineHeight, myRound($this->total_qty), 0, 'R', 0, 'T', 9, 'B');
            $this->addCell($this->getColW(5), $this->lineHeight, myRound($this->total_weight,3), 0, 'R', 0, 'T', 9, 'B');
            $this->addCell($this->getColW(6), $this->lineHeight, '', 0, 'R', 0, 'T', 9, 'B');
            $this->addCell($this->getColW(7), $this->lineHeight, myRound($this->total_amount ? $this->total_amount :'0.00'), 0, 'R', 1, 'T', 9, 'B');


            $this->drawBox($this->pdf->getY(), false);
            $this->addHLine();

            $this->GetYPos(true);

            $this->addCols([32,90,50]);
            $this->addMCell($this->getColW(1), $this->lineHeight, 'Amount In words : ', 0, 'L', 0, 'T', 9, '');
            $this->addMCell($this->getColW(2), $this->lineHeight, figToWord($this->total_amount ), 0, 'L', 0, 'T', 9, 'B');
            $this->addMCell($this->getColW(3), $this->lineHeight, 'Approx Value :', 0, 'R', 0, 'T', 9, 'B');
            $this->addMCell($this->getColW(4), $this->lineHeight,number_format($this->total_amount, 2, ".", '') , 0, 'R', 1, 'T', 9, 'B');
            if ($this->pageno != 1) {
                $this->drawBorder();
            }
        } else {
            $this->addCols([170]);
            $this->addCell($this->getColW(1), $this->lineHeight, "Continued.....", 0, 'R', 1, 'T', 10, '');
        }

        $this->addHLine();
        $this->addCols([20]);

        $currentPage = $this->pdf->getPage();
        $totalPages = $this->pdf->getNumPages();
        $this->addCell(0, $this->lineHeight, "FOR " .  $this->company['company_name'], 0, 'R', 1, 'T', 10, 'B');
        $this->pdf->ln(15);
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

    // private function getFooterHight() {
    //     $hight = 35;
    //     $hight += $this->invoice['ethanol_bill'] == 'Y' ? 25:15;
    //     $hight += count($this->term_conditions) > 0 ? (count($this->term_conditions)+1)*5:0;
    //     $hight += count($this->gst_rates)*5;
    //     $hight += $this->getHight(abs($this->invoice['freight']));
    //     $hight += $this->getHight(abs($this->invoice['discount_amt']));
    //     $hight += $this->getHight(abs($this->invoice['export_fee']));
    //     $hight += $this->getHight(abs($this->invoice['tcs_amount']));
    //     return $hight;
    // }

}
