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

use App\Models\Accounts\Account;
use App\Models\Masters\Packing;
use App\Models\Masters\TermCondition;
use App\Models\Yearly\Invoice;
use App\Models\Yearly\SaleOrder;
use App\Models\Yearly\SaleOrderDispatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleInvoiceVatPrint extends PrintPdf
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
    protected $invoice = null;
    protected $sale_order_dispatches = [];
    protected $det_cols =[];
    protected $total_qty = 0;
    protected $total_weight = 0;
    protected $total_discount = 0;
    protected $total_amount = 0;
    protected $amt_without_gst = 0;
    protected $end_footer = 70;
    protected $total_pl_qty = 0;
    protected $term_conditions = null;


    public function makepdf($invoice)
    {
        $this->invoice = $invoice;
        $this->invoice->load([
            'branch',
            'client',
            'bill_party.account_detail.city.district.state',
            'ship_party.account_detail.city.district.state',
            'transport',
            'sale_order.sale_order_details.sale_contract',
            'ifsc.bank',
            'invoice_details.item.secondary_unit',
            'invoice_details.item_unit',
            'invoice_details.packing',
            'invoice_details.brand',
            'invoice_details.gst',
            'invoice_details.vat_cst',
            'cash_books',
            'trans_gst_details',
            'trans_vat_details',
            'bl_details',
        ]);
        $this->term_conditions = TermCondition::where('type',$invoice['invoice_type'])->orderBy('sno','ASC')->get();
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
        $this->footerHeight = $this->getFooterHight();
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
        // $this->pdf->ln(4);
        $width = 0;
        $this->addCell($width, 0,$this->invoice['invoice_type'] == 'vat_invoice' ? 'VAT INVOICE':'RETAIL INVOICE', 0, 'C', 1, '', 10, 'B', false, false, true);
        $this->addHLine();

        $this->pdf->ln(2);

        $bill_city = $this->invoice['bill_party']['account_detail'] && $this->invoice['bill_party']['account_detail']['city']
        ?  $this->invoice['bill_party']['account_detail']['city']['name'] :'';

        $bill_state = $this->invoice['bill_party']['account_detail'] && $this->invoice['bill_party']['account_detail']['city']
        && $this->invoice['bill_party']['account_detail']['city']['district'] && $this->invoice['bill_party']['account_detail']['city']['district']['state']
        ?  $this->invoice['bill_party']['account_detail']['city']['district']['state']['name'] :'';

        $ship_city = $this->invoice['ship_party']['account_detail'] && $this->invoice['ship_party']['account_detail']['city']
        ?  $this->invoice['ship_party']['account_detail']['city']['name'] :'';

        $ship_state = $this->invoice['ship_party']['account_detail'] && $this->invoice['ship_party']['account_detail']['city']
        && $this->invoice['ship_party']['account_detail']['city']['district'] && $this->invoice['ship_party']['account_detail']['city']['district']['state']
        ?  $this->invoice['ship_party']['account_detail']['city']['district']['state']['name'] :'';


        $this->addCols([40,60,40,60]);
        $this->addCell($this->getColW(1), 0, 'TIN No. ', 0, 'L', 0, 'T', 8, 'B');
        $this->addCell($this->getColW(2), 0, ': '.$this->invoice['branch']['tin'] , 0, 'L', 0, 'T', 8, '');
        $this->addCell($this->getColW(3), 0, 'PAN No. ', 0, 'L', 0, 'T', 8, 'B');
        $this->addCell($this->getColW(4), 0, ': '.$this->company['pan_number'], 0, 'L', 1, 'T', 8, '');

        $this->addCell($this->getColW(1), 0, 'CST No. ', 0, 'L', 0, 'T', 8, 'B');
        $this->addCell($this->getColW(2), 0, ': ', 0, 'L', 0, 'T', 8, '');
        $this->addCell($this->getColW(3), 0, 'VAT No.  ', 0, 'L', 0, 'T', 8, 'B');
        $this->addCell($this->getColW(4), 0, ': ' .$this->invoice['branch']['vat'], 0, 'L', 1, 'T', 8, '');

        $this->addCell($this->getColW(1), 0, 'Excise Regn. No.  ', 0, 'L', 0, 'T', 8, 'B');
        $this->addCell($this->getColW(2), 0, ': ', 0, 'L', 0, 'T', 8, '');
        // $this->addCell($this->getColW(3), 0, 'Division  ', 0, 'L', 0, 'T', 8, 'B');
        // $this->addCell($this->getColW(4), 0, ': ', 0, 'L', 1, 'T', 8, '');


        $this->addCell($this->getColW(1), 0, 'GSTIN No. ', 0, 'L', 0, 'T', 8, 'B');
        $this->addCell($this->getColW(2), 0, ': ' .$this->company['gst_number'], 0, 'L', 1, 'T', 8, '');
        // $this->addCell($this->getColW(3), 0, 'Commissionerate : ', 0, 'L', 0, 'T', 8, 'B');
        // $this->addCell($this->getColW(4), 0, ': '. $this->company['company_name'], 0, 'L', 1, 'T', 8, '');

        $this->addCell($this->getColW(1), 0, 'CIN No. ', 0, 'L', 0, 'T', 8, 'B');
        $this->addCell($this->getColW(2), 0, ': '.$this->company['cin_number'], 0, 'L', 0, 'T', 8, '');
        $this->addCell($this->getColW(3), 0, 'FSSAI Lic No.  ', 0, 'L', 0, 'T', 8, 'B');
        $this->addCell($this->getColW(4), 0, ': '.$this->invoice['branch']['fssai'], 0, 'L', 1, 'T', 8, '');

        $this->pdf->ln(2);
        $this->addHLine();
        $this->GetYPos(true);
        // $address = $this->invoice['branch']['address_1'] ? $this->invoice['branch']['address_1'] :'';
        // $address .= $this->invoice['branch']['address_2'] ? $this->invoice['branch']['address_2'] :'';
        // $address .= $this->invoice['branch']['address_3'] ? $this->invoice['branch']['address_3'] :'';

        $this->addCell($width, 0,strtoupper($this->invoice['branch']['name']), 0, 'C', 1, '', 12, 'B', false, false, true);
        if($this->invoice['branch']['address_1'] && $this->invoice['branch']['address_1'] != '') {
            $this->addCell($width, 0, $this->invoice['branch']['address_1'] , 0, 'C', 1, '', 9, '', false, false, true);
        }
        if($this->invoice['branch']['address_2'] && $this->invoice['branch']['address_2'] != '') {
            $this->addCell($width, 0, $this->invoice['branch']['address_2'] , 0, 'C', 1, '', 9, '', false, false, true);
        }
        if($this->invoice['branch']['address_3'] && $this->invoice['branch']['address_3'] != '') {
            $this->addCell($width, 0, $this->invoice['branch']['address_3'] , 0, 'C', 1, '', 9, '', false, false, true);
        }
        $this->addCell($width, 0, 'Phone No. :'.  $this->invoice['branch']['phone'] .' Fax No.: '.$this->invoice['branch']['fax'] , 0, 'C', 1, '', 9, '', false, false, true);
        $this->addCell($width, 0, 'Email ID. :'.$this->invoice['branch']['email'] , 0, 'C', 1, '', 9, '', false, false, true);




        // $this->addCell($this->getColW(1), 0, 'PAN No.: '.$this->company['pan_number'], 0, 'L', 0, 'T', 8, '');
        // $this->addCell($this->getColW(2), 0, '', 0, 'L', 0, 'T', 8, '');
        // $this->addCell($this->getColW(3), 0, $this->company['office_address'], 0, 'L', 1, 'T', 8, '');
        // $this->addCell($this->getColW(1), 0, 'TAN No: '.$this->invoice['branch']['tan'], 0, 'L', 0, 'T', 8, '');
        // $this->addCell($this->getColW(2), 0, 'CIN No. : ', 0, 'L', 0, 'T', 8, 'B');
        // $this->addCell($this->getColW(3), 0, $this->company['cin_number'], 0, 'L', 1, 'T', 8, '');
        // $this->addCell($this->getColW(1), 0, 'FSSAI Lic No. : '.$this->invoice['branch']['fssai'], 0, 'L', 0, 'T', 8, '');
        // $this->addCell($this->getColW(2), 0, 'Phone No. :', 0, 'L', 0, 'T', 8, 'B');
        // $this->addCell($this->getColW(3), 0,  $this->invoice['branch']['phone'] .' Fax No.: '.$this->invoice['branch']['fax'], 0, 'L', 1, 'T', 8, '');
        // $this->addCell($this->getColW(1), 0, '', 0, 'L', 0, 'T', 8, '');
        // $this->addCell($this->getColW(2), 0, 'Email ID. :', 0, 'L', 0, 'T', 8, 'B');
        // $this->addCell($this->getColW(3), 0, $this->invoice['branch']['email'], 0, 'L', 1, 'T', 8, '');
        $this->pdf->ln(1);
        $this->GetYPos(true);
        $this->addHLine();

        // $this->pdf->Line(5, $this->lastY, 162 + $this->lm, $this->lastY);

        $this->addCols([40, 60, 40, 60]);
        $this->addCell($this->getColW(1), 0,'Invoice No. : ', 0, 'L', 0, 'T', 9, 'B');
        $this->addCell($this->getColW(2), 0, $this->invoice->invoice_no, 0, 'L', 0, 'T', 9, '');
        $this->addCell($this->getColW(3), 0,'L-38 Number : ', 0, 'L', 0, 'T', 9, 'B');
        $this->addCell($this->getColW(4), 0, $this->invoice->l_38_no, 0, 'L', 1, 'T', 9, '');


        $this->addCell($this->getColW(1), 0, 'Invoice Date :', 0, 'L', 0, 'T', 9, 'B');
        $this->addCell($this->getColW(2), 0, $this->invoice->invoice_date, 0, 'L', 0, 'T', 9, '');
        $this->addCell($this->getColW(3), 0,'L-38 Date : ', 0, 'L', 0, 'T', 9, 'B');
        $this->addCell($this->getColW(4), 0,  $this->invoice->l_38_date, 0, 'L', 1, 'T', 9, '');

        $this->addCell($this->getColW(1), 0, 'Permit No. :', 0, 'L', 0, 'T', 9, 'B');
        $this->addCell($this->getColW(2), 0, $this->invoice->permit_no, 0, 'L', 0, 'T', 9, '');
        $this->addCell($this->getColW(3), 0,'GR No. : ', 0, 'L', 0, 'T', 9, 'B');
        $this->addCell($this->getColW(4), 0,  $this->invoice ? $this->invoice->gr_lr_no:'', 0, 'L', 1, 'T', 9, '');

        $this->addCell($this->getColW(1), 0, 'Permit Date :', 0, 'L', 0, 'T', 9, 'B');
        $this->addCell($this->getColW(2), 0, $this->invoice->permit_date, 0, 'L', 0, 'T', 9, '');
        $this->addCell($this->getColW(3), 0,'GR Date : ', 0, 'L', 0, 'T', 9, 'B');
        $this->addCell($this->getColW(4), 0,  $this->invoice ? $this->invoice->gr_lr_date:'', 0, 'L', 1, 'T', 9, '');

        $this->addCell($this->getColW(1), 0, 'Excise Pass no. :', 0, 'L', 0, 'T', 9, 'B');
        $this->addCell($this->getColW(2), 0, $this->invoice->excise_pass_no, 0, 'L', 0, 'T', 9, '');
        $this->addCell($this->getColW(3), 0,'Vehicle No. : ', 0, 'L', 0, 'T', 9, 'B');
        $this->addCell($this->getColW(4), 0,  $this->invoice ? $this->invoice->vehical_no:'', 0, 'L', 1, 'T', 9, '');

        $this->addCell($this->getColW(1), 0, 'Excise Pass Date :', 0, 'L', 0, 'T', 9, 'B');
        $this->addCell($this->getColW(2), 0, $this->invoice->excise_pass_date, 0, 'L', 0, 'T', 9, '');
        $this->addCell($this->getColW(3), 0,'Dispatched By : ', 0, 'L', 0, 'T', 9, 'B');
        $transpor_type = str_replace("_", " ", ucwords($this->invoice->transport_type));
        $this->addCell($this->getColW(4), 0,  $transpor_type, 0, 'L', 1, 'T', 9, '');

        // $this->addCell($this->getColW(5), 0,  $this->invoice ?  'Date:'.$this->invoice->gr_lr_date:'', 0, 'L', 1, 'T', 9, '');


        // $this->addCell($this->getColW(1), 0, 'State Code:', 0, 'L', 0, 'T', 9, 'B');
        // $this->addCell($this->getColW(2), 0, $this->invoice->invoice_date, 0, 'L', 0, 'T', 9, '');
        // $this->addCell($this->getColW(3), 0,'Delivery Type : ', 0, 'L', 0, 'T', 9, 'B');
        // $this->addCell($this->getColW(4), 0,  $this->invoice ? $this->invoice->delivery_terms:'', 0, 'L', 1, 'T', 9, '');
        // $this->addCell($this->getColW(5), 0,  'NEFT/RTGS', 0, 'L', 1, 'T', 9, '');

        $this->addHLine();

        $this->addVLine($this->lm + 100);

        $this->GetYPos(true);
        $cols = [40,60,40,60];
        $this->addCols($cols);
        $this->addMCell($this->getColW(1,2), 0, 'Name & Address of Buyer', 0, 'C', 0, 'T', 10, 'B');
        $this->addMCell($this->getColW(3,4), 0, 'Name & Address of Consignee', 0, 'C', 1, 'T', 10, 'B');
        $this->addHLine();
        $this->pdf->ln( 1);
        $this->addMCell($this->getColW(1,2), 0, $this->invoice['bill_party'] ?  $this->invoice['bill_party']['name'] :'' , 0, 'L', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(3,4), 0, $this->invoice['ship_party'] ?  $this->invoice['ship_party']['name'] :'' , 0, 'L', 1, 'T', 9, 'B');
        $bill_address = '';
        if($this->invoice['bill_party'] && $this->invoice['bill_party']['account_detail']){
            if($this->invoice['bill_party']['account_detail']['add1']){
                $bill_address .= $this->invoice['bill_party']['account_detail']['add1'] ;
            }
            if($this->invoice['bill_party']['account_detail']['add2']){
                $bill_address .= ', '. $this->invoice['bill_party']['account_detail']['add2'];
            }
            if($this->invoice['bill_party']['account_detail']['add3']){
                $bill_address .= ', '. $this->invoice['bill_party']['account_detail']['add3'];
            }
        }

        $bill_address .=  $bill_city ? ', '. $bill_city :'';
        // $bill_address .=  $bill_state?', '.$bill_state:'';

        $ship_address = '';


        if($this->invoice['ship_party'] && $this->invoice['ship_party']['account_detail']){
            if($this->invoice['ship_party']['account_detail']['add1']){
                $ship_address .= $this->invoice['ship_party']['account_detail']['add1'];
            }
            if($this->invoice['ship_party']['account_detail']['add2']){
                $ship_address .= ', '. $this->invoice['ship_party']['account_detail']['add2'];
            }
            if($this->invoice['ship_party']['account_detail']['add3']){
                $ship_address .= ', '. $this->invoice['ship_party']['account_detail']['add3'];
            }
        }
        $ship_address .=  $ship_city ? ', '. $ship_city :'';
        // $ship_address .=  $ship_state?', '.$ship_state:'';

        $this->addMCell($this->getColW(1,2), 0,   $bill_address, 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3,4), 0,   $ship_address, 0, 'L', 1, 'T', 9, '');

        $this->addMCell($this->getColW(1,2), 0,   $bill_state, 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3,4), 0,   $ship_state, 0, 'L', 1, 'T', 9, '');
        $this->addMCell($this->getColW(1), 0, 'Buyer\'s Order No and Date. ', 0, 'L', 0, 'T', 8, 'B');
        $date_time =  Carbon::createFromFormat('Y-m-d H:s:i',  $this->invoice['created_at'])->format('d-m-Y h:m A');
        $this->addMCell($this->getColW(2), 0, $this->invoice['sale_order'] ? ': ' . $this->invoice['sale_order']['sale_order_no'] . ' & '.$this->invoice['sale_order']['sale_order_date']:': ', 0, 'L', 0, 'T', 8, '');
        $this->addMCell($this->getColW(3), 0, 'Date and Time of Invoice', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0, ': '.$date_time, 0, 'L', 1, 'T', 8, '');
        $this->addMCell($this->getColW(1), 0, 'Buyer\'s Phone No. ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0, ': ' . $this->invoice['bill_party']['account_detail']['phone_no'], 0, 'L', 0, 'T', 8, '');
        $this->addMCell($this->getColW(3), 0, 'Cons\'s Phone No.  ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0, ': ' . $this->invoice['ship_party']['account_detail']['phone_no'], 0, 'L', 1, 'T', 8, '');

        $this->addMCell($this->getColW(1), 0, 'Buyer\'s VAT No. ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0, ': ' . $this->invoice['bill_party']['account_detail']['vat_no'], 0, 'L', 0, 'T', 8, '');
        $this->addMCell($this->getColW(3), 0, 'Cons\'s TIN No.  ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0, ': ' . $this->invoice['ship_party']['account_detail']['vat_no'], 0, 'L', 1, 'T', 8, '');


        $this->addMCell($this->getColW(1), 0, 'Buyer\'s CST No. ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0, ': ' . $this->invoice['bill_party']['account_detail']['cst_no'], 0, 'L', 0, 'T', 8, '');
        $this->addMCell($this->getColW(3), 0, 'Cons\'s CST No.  ', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0, ': ' . $this->invoice['ship_party']['account_detail']['cst_no'], 0, 'L', 1, 'T', 8, '');

        // $this->addMCell($this->getColW(1), 0, 'State ', 0, 'L', 0, 'T', 8, 'B');
        // $this->addMCell($this->getColW(2), 0, ': ' . $bill_state, 0, 'L', 1, 'T', 9, '');
        // $this->addMCell($this->getColW(2), 0, ': ' . $bill_state, 0, 'L', 1, 'T', 9, '');



        // $this->addHLine();
        $this->addVLine($this->lm + 100);
        $this->GetYPos(true);
        $this->addCols([10,60,20,20,20,20,30,20]);

        $this->addMCell($this->getColW(1), $this->lineHeight,  'Sr. No.' , 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(2), $this->lineHeight,  'Name of Product/Service ' .chr(10)  .chr(10) , 1, 'C', 0, 'T', 9,'B',1);
        $this->addMCell($this->getColW(3), $this->lineHeight,  'HSN/ SAC ' .chr(10).chr(10), 1, 'C', 0, 'T', 9, 'B',1);
        // $this->addMCell($this->getColW(4), $this->lineHeight,  'Unit'.chr(10)  .chr(10) , 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(4), $this->lineHeight,  'Quantity'.chr(10)  .chr(10) , 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(5), $this->lineHeight,  'Weight'.chr(10)  .chr(10) , 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(6), $this->lineHeight,  'Rate' .chr(10)  .chr(10), 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(7), $this->lineHeight,  'Unit' .chr(10)  .chr(10)  , 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(8), $this->lineHeight,  'Amount (Rs.)' .chr(10)  , 1, 'C', 1 ,'T', 9, 'B',1);
        // $this->pdf->ln($this->detHeight);
        $this->addHLine();
        $this->GetYPos(true);
        $this->hight = $this->getNetHeight();
        $this->detailHeight = $this->hight - $pdf->getY() - $this->footerHeight;
        $this->secHeight = 0;

    }

    private function getSaleContractNumbers(){
        $contract_nos = [];
        $sale_order = $this->invoice['sale_order'];
        if($sale_order &&  $sale_order['sale_order_details']){
            foreach( $sale_order['sale_order_details'] as $sale_order_det){
                if($sale_order_det && $sale_order_det['sale_contract']){
                    $contract_no = $sale_order_det['sale_contract']['contract_no'];
                    array_push($contract_nos , $contract_no);
                }
            }
        }
        return joinArrayByCommaSepratedString($contract_nos);

    }


    private function details($title = '')
    {

        $count = 1;

        foreach ($this->invoice['invoice_details'] as $key=>$invoice_detail) {
            $last = $key+1 == count($this->invoice['invoice_details'] );
            $this->addDetail($invoice_detail, $count,$last );
            $count++;
        }
        $this->drawBox($this->pdf->getY(), false);
        // $this->addHLine();

    }

    private function addDetail($invoice_det,$count,$is_last=false)
    {
        $this->detHeight = 0;
        $item_name = $invoice_det['item'] ? $invoice_det['item']['item_name'] :'';
        $brand_name = $invoice_det['brand'] ? $invoice_det['brand']['name'] :'';
        $packing_name = $invoice_det['packing'] ? $invoice_det['packing']['name'] :'';
        // $this->ethanol_remarks = $this->ethanol_remarks != 'Y' ? ($invoice_det['item'] ? $invoice_det['item']['ethanol_reamrks'] :''): $this->ethanol_remarks;
        $this->testpdf->addMCell($this->getColW(2), $this->lineHeight,  $item_name . ' '. $brand_name .' ',  $packing_name, 0, 'L', 0, 'T', 0, '');

        if($is_last && ($this->secHeight + $this->testpdf->testHeight)  > $this->detailHeight){
            $this->startNewPage();
        }
        else if ($this->secHeight + $this->testpdf->testHeight > $this->detailHeight){
            $this->startNewPage();
        }
        $this->addMCell($this->getColW(1), 0, $count, 0, 'C', 0, 'T', 9, '');
        // dd($item_name . ' '. $brand_name .' ',  $packing_name);
        $item_name = $item_name . ' '. $brand_name .' '.  $packing_name;
        $this->addMCell($this->getColW(2), 0,   $item_name, 0, 'L', 0, 'L', 9, '');
        $this->addMCell($this->getColW(3), 0, $invoice_det['item']['hsn_code'], 0, 'C', 0, 'T', 9, '');
        $this->addMCell($this->getColW(4), 0, $invoice_det['qty'], 0, 'R', 0, 'T', 9, '');
        $this->addMCell($this->getColW(5), 0,  $invoice_det['weight'] ? $invoice_det['weight']/100 :'', 0, 'R', 0, 'T', 9, '');
        $this->addMCell($this->getColW(6), 0, $invoice_det['rate'], 0, 'R', 0, 'T', 9, '');
        $this->addMCell($this->getColW(7), 0, $invoice_det['item_unit'] ?  $invoice_det['item_unit']['unit_name']:'', 0, 'C', 0, 'T', 8, '');
        $this->addMCell($this->getColW(8), 0, $invoice_det['basic_amount'] ? $invoice_det['basic_amount'] : '0.00', 0, 'R', 0, 'T', 9, '');


        if(count($invoice_det['item']['secondary_unit']) > 0){
            $item_pl =$invoice_det['item']['secondary_unit'][0]['multiplier'] * $invoice_det['qty'];
            $this->total_pl_qty += $item_pl;
        }
        $this->total_qty += $invoice_det['qty'];
        $this->total_weight += $invoice_det['weight']/100;
        $this->total_amount += $invoice_det['basic_amount'];
        $this->total_discount += $invoice_det['discount'];
        $this->amt_without_gst += $invoice_det['amt_without_gst'];

        // return;
        // $total_qty = 0;
        // foreach($dispatch['packings'] as $key=>$packing){
        //     $total_qty +=  $packing['qty'] ?  $packing['qty']:0;
        //     $this->addMCell($this->getColW($count++), 0, $packing['qty'] > 0 ? number_format($packing['qty']):'', 0, 'C', 0, 'T', 12, '');
        // }
        // $this->addMCell($this->getColW($count++), 0, $total_qty, 0, 'C', 0, 'T', 12, '');
        $this->pdf->ln($this->detHeight+1);
        $this->secHeight += $this->detHeight+1;

    }

    private function checkEndFooterHeight(){
        if ($this->secHeight + $this->end_footer > $this->detailHeight) {
             $this->drawBorder($this->border_gap);
            $this->drawBox($this->pdf->getY(), false);
            $this->pdf->Line($this->lm, $this->pdf->GetY(), $this->pageWidth + $this->lm, $this->pdf->GetY());
            $this->startNewPage('N');
        }
    }

    private function footer($type = "R")
    {
        $invoice = $this->invoice;
        if ($this->secHeight > $this->detailHeight) {
            $this->drawBox($this->pdf->getY(), false);
            $this->pdf->Line($this->lm, $this->pdf->GetY(), $this->pageWidth + $this->lm, $this->pdf->GetY());
            $this->startNewPage('N');
        }

        if ($this->detailHeight - $this->secHeight > 0) {
            $this->pdf->ln($this->detailHeight - $this->secHeight);
        }
        if(strlen($this->invoice['remarks']) > 0){
            $this->addMCell($this->getColW(1), $this->lineHeight, '', 0, 'L',0, 'T', 8, 'B');
            $this->addMCell(200-$this->getColW(1), $this->lineHeight, $this->invoice['remarks'], 0, 'L', 1, 'T', 8, 'B');
        }
        $this->drawBox($this->pdf->getY(), false);
        $this->addHLine();
        if ($type == "E") {

            // $this->checkEndFooterHeight();
            $this->GetYPos(true);
            $this->addCols([90,20,20,20,30,20]);
            $this->addMCell($this->getColW(1), $this->lineHeight, 'Total ', 0, 'C', 0, 'T', 8, 'B');
            $this->addCell($this->getColW(2), $this->lineHeight, number_format($this->total_qty, 2, ".", ''), 0, 'R', 0, 'T', 8, 'B');
            $this->addCell($this->getColW(3), $this->lineHeight, number_format($this->total_weight, 2, ".", ''), 0, 'R', 0, 'T', 8, 'B');
            $this->addCell($this->getColW(4), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');
            $this->addCell($this->getColW(5), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');
            $this->addCell($this->getColW(6), $this->lineHeight, number_format($this->total_amount ? $this->total_amount :'0.00', 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            $this->drawBox($this->pdf->getY(), false);

            $this->addHLine();
            $this->GetYPos(true);

            $this->addCols([32,78,50,20,20]);
            $this->addMCell($this->getColW(1), $this->lineHeight, 'Amount In words : ', 0, 'L', 0, 'T', 8, '');
            $this->addMCell($this->getColW(2), $this->lineHeight, figToWord($this->invoice['net_amt'] ), 0, 'L', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(3), $this->lineHeight, 'PL : ' . $this->total_pl_qty, 0, 'C', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(4), $this->lineHeight, 'Total :', 0, 'R', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(5), $this->lineHeight,number_format($this->amt_without_gst, 2, ".", '') , 0, 'R', 1, 'T', 8, 'B');

            $this->GetYPos(true);
            $this->addCols([32,78,70,20]);
            if(abs($this->invoice['freight'])  > 0){
                $term_f = $this->invoice['freight'] > 0 ? 'Add ' :'Less ';
                $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(3), $this->lineHeight, $term_f .'Freight :', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight,  number_format(abs($this->invoice['freight']), 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            }

            if(abs($this->invoice['discount_amt'])){
                $term_d = $this->invoice['discount_amt'] > 0 ? 'Add ' :'Less ';
                $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(3), $this->lineHeight, $term_d .'Discount :', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight,  number_format(abs($this->invoice['discount_amt']), 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            }

            if($this->invoice['add_excise_amount']> 0){
                $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
                $pl =  $this->invoice['add_excise_amount']/$this->invoice['add_excise_per_pl'];

                $this->addMCell($this->getColW(3), $this->lineHeight, 'Add Excise @' . $this->invoice['add_excise_per_pl'] . ' On ' .$pl. ' :', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight,  number_format(abs($this->invoice['add_excise_amount']), 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            }
            $term_f = $this->invoice['freight'] > 0 ? 'Add ' :'Less ';

            if(abs($this->invoice['freight']) > 0){
                $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(3), $this->lineHeight, $term_f .'Freight :', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight,  number_format(abs($this->invoice['freight']), 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            }
            if(abs($this->invoice['freight_amount']) > 0){
                $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(3), $this->lineHeight, 'Freight Per Case @'.  $this->invoice['freight_per_case'].' :', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight,  number_format(($this->invoice['freight_amount']), 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            }
            $vat_details = $this->invoice['trans_vat_details'];
            // dd($vat_details );
            foreach($vat_details as $vat_detail){
                $term = $vat_detail['vat_cst'] == 'C' ? 'CST' :'VAT';
                $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(3), $this->lineHeight, 'Add '.$term.' @ '.$vat_detail['vat_cst_rate'].'% On '.number_format($vat_detail['vat_cst_on'], 2, ".", '') .' :', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight, number_format($vat_detail['vat_cst_amt'], 2, ".", '' ) , 0, 'R', 1, 'T', 8, 'B');

                $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
                if(floatval($vat_detail['surcharge_amt']) != 0) {
                    $this->addMCell($this->getColW(3), $this->lineHeight, 'Surcharge on '.number_format($vat_detail['vat_cst_amt'], 2, ".", '' ).' @ '.$vat_detail['surcharge_rate'].'% :', 0, 'R', 0, 'T', 8, 'B');
                    $this->addMCell($this->getColW(4), $this->lineHeight, number_format($vat_detail['surcharge_amt'], 2, ".", '' ) , 0, 'R', 1, 'T', 8, 'B');
                }
            }

            if($this->invoice['less_excise_amount']> 0){
                $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
                $pl =  $this->invoice['less_excise_amount']/$this->invoice['less_excise_per_pl'];
                $this->addMCell($this->getColW(3), $this->lineHeight, 'Less Excise @' . $this->invoice['less_excise_per_pl'] . ' On ' .$pl. ' :', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight,  number_format(abs($this->invoice['less_excise_amount']), 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            }

            if(abs($this->invoice['tcs_amount']) > 0){
                $this->addMCell($this->getColW(1), $this->lineHeight,'', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(3), $this->lineHeight, $this->invoice['tcs_per'] ?  'TCS @' .$this->invoice['tcs_per'] .' %' : 'TCS @ 0.00% :'  , 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight, number_format($this->invoice['tcs_amount'], 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            }
            if(abs($this->invoice['round_off']) > 0){
                $this->addMCell($this->getColW(1), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(3), $this->lineHeight, 'Round Off :', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight, number_format($this->invoice['round_off'], 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            }
         $this->addMCell($this->getColW(1), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(3), $this->lineHeight, 'Bill Amount :', 0, 'R', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(4), $this->lineHeight,$this->invoice['net_amt'] , 0, 'R', 1, 'T', 8, 'B');

            $this->addHLine();
            // $this->pdf->ln(1);
            $this->GetYPos(true);
            $this->addCols([100,100]);

            $str = "Certified that  the particulars given avobe are true and correct and the amount indicated represents the price actually charged and that there";
            $str .= ' is no flow of additional consideration directly or indirectly from the buyer.';
            $remarks = $this->getEthanolRemarks();
            $this->detHeight = 0;
            $this->addMCell($this->getColW(1), $this->lineHeight, $str, 0, 'L', 0, 'T', 8, '');
            $this->addMCell($this->getColW(2), $this->lineHeight,$remarks != '' ? 'Remarks if Any : ':'',  0, 'L', 0, 'T', 8, 'B');
            // $this->pdf->ln($this->detHeight+2);
            // $this->pdf->ln(2);
            if($remarks != '') {
                $this->addCell($this->getColW(1), $this->lineHeight, '',  0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, $remarks,  0, 'L', 0, 'T', 8, '');
            }
            $this->pdf->ln($this->detHeight);
            $this->drawBox($this->pdf->getY(), false);
            $this->addHLine();
            if(count($this->term_conditions) > 0) {
                $this->addCell($this->getColW(1,4), $this->lineHeight, "Terms & Conditions :", 0, 'L', 1, 'T', 10, 'B');
                foreach($this->term_conditions as $key=>$term_condition){
                    $this->addTermandConditions($term_condition, $key+1);
                }
                $this->addHLine();
            }
            $this->GetYPos(true);
            $this->addCols([200]);
            // $this->pdf->ln(2);
            // $this->addCell($this->getColW(1), $this->lineHeight, "FOR " .  $this->company['company_name'], 0, 'R', 1, 'T', 7, 'B');

            // $this->pdf->SetY(-15);
            $this->pdf->ln(1);
            $this->GetYPos(true);
            $this->addCell(0, $this->lineHeight, "FOR " .  $this->company['company_name'] , 0, 'R', 1, 'T', 7, 'B');
            $this->pdf->ln(15);
            $this->addCols([66.6,66.6,66.6]);
            $this->addCell($this->getColW(1), $this->lineHeight, "Prepared By: ", 0, 'L', 0, 'T', 7, 'B');
            $this->addCell($this->getColW(2), $this->lineHeight, "Checked By", 0, 'C', 0, 'T', 7, 'B');
            $this->addCell($this->getColW(3), $this->lineHeight, 'Authorised Signatory', 0, 'R', 1, 'T', 7, 'B');
            if ($this->pageno != 1) {
                $this->drawBorder();
            }
        } else {
            $this->addCols([170]);
            $this->addCell($this->getColW(1), $this->lineHeight, "Continued.....", 0, 'R', 1, 'T', 10, '');
        }

        // $this->addHLine();
        $this->addCols([20]);
        //$this->pdf->ln(10);
        // dd($vo->updatedby);

        // $this->pdf->ln(40);
        // $this->addCell($this->getColW(1), $this->lineHeight, "Prepared By: ", 0, 'L', 0, 'T', 10, 'B');
        // $this->addCell($this->getColW(2), $this->lineHeight, $invoice->updatedby ? $invoice->updatedby->name :'', 0, 'L', 0, 'T', 10, '');
        // $this->addCell($this->getColW(3), $this->lineHeight, "", 0, 'L', 0, 'T', 10, 'B');
        // $this->addCell($this->getColW(4), $this->lineHeight, 'Authorised Signatory', 0, 'R', 0, 'T', 10, 'B');
        //$this->pdf->ln(5);
        // $this->pdf->SetY(-10);
        $currentPage = $this->pdf->getPage();
        $totalPages = $this->pdf->getNumPages();
        $footerText = "Page {$currentPage} of {$totalPages}";
        // $this->pdf->SetY(-10);
        // $this->addCell(0, $this->lineHeight,  $footerText , 0, 'C', 0, 'T', 7, 'B');
        $this->drawBorder($this->border_gap);
    }


    private function addTermandConditions($term, $count){
        $this->detHeight = 0;
        $this->testpdf->addMCell($this->getColW(2), $this->lineHeight,  $term['terms'], 0, 'L', 0, 'T', 0, '');
        if ($this->secHeight + $this->testpdf->testHeight > $this->detailHeight){
            $this->startNewPage();
        }
        $this->addMCell($this->getColW(1), 0, $count.'. '. $term['terms'], 0, 'L', 0, 'T', 7, '');
        $this->pdf->ln($this->detHeight+1);
        $this->secHeight += $this->detHeight;
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

    private function getFooterHight() {
        $hight = 28;
        $hight += $this->invoice->ethanol_bill == 'Y' ? 40:15;
        $hight += count($this->term_conditions) > 0 ? (count($this->term_conditions)+1)*5:0;
        $hight += $this->getHight(abs($this->invoice['freight']));
        $hight += $this->getHight(abs($this->invoice['freight']));
        $hight += $this->getHight(abs($this->invoice['discount_amt']));
        $hight += $this->getHight(abs($this->invoice['add_excise_amount']));
        $hight += $this->getHight(abs($this->invoice['freight']));
        $hight += $this->getHight(abs($this->invoice['freight_amount']));
        $hight += $this->getHight(abs($this->invoice['less_excise_amount']));
        $hight += $this->getHight(abs($this->invoice['tcs_amount']));
        $hight += $this->getHight(abs($this->invoice['round_off']));
        $hight += $this->getHight(count($this->invoice['trans_vat_details']));
        return $hight;
    }

    private function getHight($amount) {
        $hight = 0;
        if($amount > 0) {
            $hight = 5;
        }
        return $hight;
    }

    private function getEthanolRemarks() {
        $remarks = '';
        if($this->invoice['ethanol_bill'] == 'Y') {
            $remarks = addString($remarks,'1. Ethanol Category:',chr(10));
            $remarks = addString($remarks,'2. Excise Certificate No.:',chr(10));
            $remarks = addString($remarks,'3. Denaturant: ',chr(10));
            $remarks = addString($remarks,'   a. Crotonaldehde :',chr(10));
            $remarks = addString($remarks,'   b. Denatonium Saccaride:',chr(10));
            $remarks = addString($remarks,'   c. Denatonium Benzoate:',chr(10));
            $remarks = addString($remarks,'4. Tanker Seal No.:',chr(10));
        }
        return $remarks;
    }
}
