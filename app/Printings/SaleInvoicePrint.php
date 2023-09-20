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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleInvoicePrint extends PrintPdf
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
    protected $term_conditions = null;
    protected $gst_rates = [];

    public function makepdf($invoice)
    {
        $this->invoice = $invoice;
        $this->invoice->load([
            'branch.city.district.state',
            'client',
            'bill_party.account_detail.city',
            'ship_party.account_detail.city',
            'transport',
            'sale_order.sale_order_details.sale_contract',
            'ifsc.bank',
            'invoice_details.item',
            'invoice_details.item_unit',
            'invoice_details.packing',
            'invoice_details.brand',
            'invoice_details.gst',
            'invoice_details.vat_cst',
            'cash_books',
            'trans_gst_details',
            'bl_details',
        ]);
        $this->term_conditions = TermCondition::where('type',$invoice['invoice_type'])->orderBy('sno','ASC')->get();
        $gst_details = $this->invoice['trans_gst_details'];
        $this->gst_rates = [];
        foreach($gst_details as $gst){
            $key = $gst['gst_name'].' @ '.myround($gst['gst_rate']);
            if(array_key_exists($key,$this->gst_rates)) {
                $this->gst_rates[$key]['gst_on'] += $gst['gst_on'];
                $this->gst_rates[$key]['gst_value'] += $gst['gst_value'];
            } else {
                $this->gst_rates[$key] = ['gst_on' => $gst['gst_on'],'gst_value' => $gst['gst_value']];
            }
        }
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
        $title = $this->invoice['invoice_type'] == 'gst_invoice' ? 'TAX INVOICE':
                ($this->invoice['invoice_type'] == 'high_seas_sale' ? 'HIGH SEAS SALE INVOICE':
                ($this->invoice['invoice_type'] == 'sale_against_bond' ? 'SALE AGAINST BOND INVOICE':''));
        $this->addCell($width, 0,'TAX INVOICE' , 0, 'C', 1, '', 10, 'B', false, false, true);
        $this->addHLine();
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

        $this->pdf->ln(2);
        $this->addHLine();
        $this->GetYPos(true);
        $this->pdf->ln(2);

        $this->addCols([88,20,68,40]);
        $this->addCell($this->getColW(1), 0, 'GSTIN : '.$this->company['gst_number'], 0, 'L', 0, 'T', 8, '');
        $this->addCell($this->getColW(2), 0, 'CIN No. : ', 0, 'L', 0, 'T', 8, 'B');
        $this->addCell($this->getColW(3), 0, $this->company['cin_number'], 0, 'L', 1, 'T', 8, '');
        $style = array(
            'border' => 1,
            'vpadding' => '1',
            'hpadding' => '1',
            'fgcolor' => array(0,0,0),
            // 'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
               // QRCODE,L : QR-CODE Low error correction
        $this->pdf->write2DBarcode('www.tcpdf.org', 'QRCODE,L', 167, 20.5, 40, 40, $style);

        $this->addCell($this->getColW(1), 0, 'PAN No.: '.$this->company['pan_number'], 0, 'L', 0, 'T', 8, '');
        $this->addCell($this->getColW(2), 0, 'Phone No. :', 0, 'L', 0, 'T', 8, 'B');
        $this->addCell($this->getColW(3), 0,  $this->invoice['branch']['phone'] .' Fax No.: '.$this->invoice['branch']['fax'], 0, 'L', 1, 'T', 8, '');
        $this->addCell($this->getColW(1), 0, 'TAN No: '.$this->invoice['branch']['tan'], 0, 'L', 0, 'T', 8, '');
        $this->addCell($this->getColW(2), 0, 'Email ID. :', 0, 'L', 0, 'T', 8, 'B');
        $this->addCell($this->getColW(3), 0, $this->invoice['branch']['email'], 0, 'L', 1, 'T', 8, '');
        $this->addCell($this->getColW(1), 0, 'FSSAI Lic No. : '.$this->invoice['branch']['fssai'], 0, 'L', 1, 'T', 8, '');
        $this->pdf->ln(1);
        $this->GetYPos(true);
        $this->pdf->Line(5, $this->lastY, 162 + $this->lm, $this->lastY);

        $this->addCols([ 34.6, 34.6, 34.6, 30, 36]);
        $this->addCell($this->getColW(1), 0,'Invoice No. : ', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(2), 0, $this->invoice->invoice_no, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(3), 0,'Mode of Transport. : ', 0, 'L', 0, 'T', 10, '');
        $transpor_type = str_replace("_", " ", ucwords($this->invoice->transport_type));
        $this->addCell($this->getColW(4), 0, $transpor_type, 0, 'L', 1, 'T', 10, '');

        $this->addCell($this->getColW(1), 0, 'Invoice Date :', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(2), 0, $this->invoice->invoice_date, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(3), 0,'Vehicle No. : ', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(4), 0,  $this->invoice ? $this->invoice->vehical_no:'', 0, 'L', 1, 'T', 10, '');

        $this->addCell($this->getColW(1), 0, 'Date of Supply :', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(2), 0, $this->invoice->invoice_date, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(3), 0,'Gr No. : ', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(4), 0,  $this->invoice ? $this->invoice->gr_lr_no:'', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(5), 0,  $this->invoice ?  'Date : '.$this->invoice->gr_lr_date:'', 0, 'L', 1, 'T', 10, '');


        $this->addCell($this->getColW(1), 0, 'Supply Type:', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(2), 0, $this->invoice['supply_type'], 0, 'L', 0, 'T', 10, '');

        $this->addCell($this->getColW(3), 0,'Delivery Type : ', 0, 'L', 0, 'T', 10, '');

        $delivery_terms = $this->invoice ? ( $this->invoice->delivery_terms == 'F'? 'FOR':
        ($this->invoice->delivery_terms == 'M'? 'EX-MILL': ($this->invoice->delivery_terms == 'K'? ' EX-KANDLA':''))):'';
        $this->addCell($this->getColW(4), 0,  $delivery_terms, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(5), 0,  'NEFT/RTGS', 0, 'L', 1, 'T', 10, '');



        $state =  $this->invoice['branch'] && $this->invoice['branch']['city'] && $this->invoice['branch']['city']['district']&&
        $this->invoice['branch']['city']['district']['state'] ? $this->invoice['branch']['city']['district']['state']:'';

        $state_code =  $state ?$state['state_code'] :'';
        $state_name =  $state ?$state['name'] :'';
        $city_name = $this->invoice['branch'] && $this->invoice['branch']['city'] ? $this->invoice['branch']['city']['name']:'';


        $bill_state = $this->invoice['bill_party']['account_detail'] && $this->invoice['bill_party']['account_detail']['city']
        && $this->invoice['bill_party']['account_detail']['city']['district'] && $this->invoice['bill_party']['account_detail']['city']['district']['state']
        ?  $this->invoice['bill_party']['account_detail']['city']['district']['state']:'';
        $ship_state = $this->invoice['ship_party']['account_detail'] && $this->invoice['ship_party']['account_detail']['city']
        && $this->invoice['ship_party']['account_detail']['city']['district'] && $this->invoice['ship_party']['account_detail']['city']['district']['state']
        ?  $this->invoice['ship_party']['account_detail']['city']['district']['state']:'';


        $bill_state_name = $bill_state ? $bill_state['name']:'';
        $bill_state_code = $bill_state ? $bill_state['state_code']:'';
        $ship_state_name = $ship_state ? $ship_state['name']:'';
        $ship_state_code = $ship_state ? $ship_state['state_code']:'';

        $bill_city_name = $this->invoice['bill_party']['account_detail'] && $this->invoice['bill_party']['account_detail']['city']
        ?  $this->invoice['bill_party']['account_detail']['city']['name']:'';
        $ship_city_name = $this->invoice['ship_party']['account_detail'] && $this->invoice['ship_party']['account_detail']['city']
        ?  $this->invoice['ship_party']['account_detail']['city']['name']:'';


        $this->addCell($this->getColW(1), 0, 'State Name:', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(2), 0, $bill_state_name, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(3), 0, 'State Code:', 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(4), 0, $bill_state_code, 0, 'L', 0, 'T', 10, '');
        $this->addCell($this->getColW(5), 0,  'Place of Supply : ' . $bill_city_name , 0, 'L', 1, 'T', 10, '');


        $this->addHLine();
        $this->pdf->ln(1);
        $this->GetYPos(true);

        $this->detHeight = 0;
        $this->addCols([12,42,11,16,37,8,14,35,9,16]);
        $this->addMCell($this->getColW(1), 0, 'PO No.:', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0, $this->invoice['client_po_no'], 0, 'L', 0, 'T', 8, '');
        $this->addMCell($this->getColW(3), 0, 'Dated:', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0, $this->invoice['client_po_date'], 0, 'L', 0, 'T', 8, '');
        $this->addMCell($this->getColW(5), 0, 'Reverse Charges Applicable:', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(6), 0, $this->invoice['reverse_charge_applicable'] == 'Y' ? 'Yes':($this->invoice['reverse_charge_applicable'] =='N'? 'No':''), 0, 'L', 0, 'T', 8, '');
        $this->addMCell($this->getColW(7), 0, 'D-20 No.:', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(8), 0, $this->invoice['d20_no'], 0, 'L', 0, 'T', 8, '');
        $this->addMCell($this->getColW(9), 0, 'Date:', 0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(10), 0, $this->invoice['d20_date'] , 0, 'L', 0, 'T', 8, '');
        $this->pdf->ln($this->detHeight);
        $this->GetYPos(true);

        $this->detHeight = 0;
        $this->addCols([28,75,18,43,20,16]);
        $sale_contract_nos = $this->getSaleContractNumbers();
        $this->addMCell($this->getColW(1), 0,  'Sale Contract Nos.:',0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0,  $sale_contract_nos ,0, 'L', 0, 'T', 8, '');
        $this->addMCell($this->getColW(3), 0,  'SAP PO No.:',0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0,  $this->invoice['sap_po_no'] ,0, 'L', 0, 'T', 8, '');
        $this->addMCell($this->getColW(5), 0,  'SAP PO Date:',0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(6), 0,  $this->invoice['sap_po_date'] ,0, 'L', 0, 'T', 8, '');

        $this->pdf->ln( $this->detHeight);

        $this->addHLine();
        $this->GetYPos(true);
        $this->addMCell($this->getColW(1), 0,  'IRN : ' ,0, 'L', 1, 'T', 8, 'B');
        $this->pdf->ln( 1);
        $this->addHLine();
        $this->addCols([50,50,50,50]);
        $this->addMCell($this->getColW(1), 0,  'ACK No. : ' ,0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(2), 0,  ' ' ,0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(3), 0,  'ACK Date : ' ,0, 'L', 0, 'T', 8, 'B');
        $this->addMCell($this->getColW(4), 0,  '' ,0, 'L', 1, 'T', 8, 'B');
        $this->pdf->ln( 1);
        $this->addHLine();


        $this->GetYPos(true);
        $cols = [20,80,20,80];
        $this->addCols($cols);
        $this->addMCell($this->getColW(1,2), 0, 'Details of Receiver/Billed To', 0, 'C', 0, 'T', 10, 'B');
        $this->addMCell($this->getColW(3,4), 0, 'Details of Consignee/Shipped to', 0, 'C', 1, 'T', 10, 'B');
        $this->addHLine();
        $this->pdf->ln( 1);
        $this->addMCell($this->getColW(1), 0, 'Name ', 0, 'L', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(2), 0, $this->invoice['bill_party'] ? ': ' . $this->invoice['bill_party']['name'] :'' , 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3), 0, 'Name ', 0, 'L', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(4), 0, $this->invoice['ship_party'] ? ': ' . $this->invoice['ship_party']['name'] :'' , 0, 'L', 1, 'T', 9, '');
        $this->addMCell($this->getColW(1), 0, 'Address ', 0, 'L', 0, 'T', 9, 'B');
        $bill_address = '';
        if($this->invoice['bill_party'] && $this->invoice['bill_party']['account_detail']){
            if($this->invoice['bill_party']['account_detail']['add1']){
                $bill_address .= $this->invoice['bill_party']['account_detail']['add1'];
            }
            if($this->invoice['bill_party']['account_detail']['add2']){
                $bill_address .= $this->invoice['bill_party']['account_detail']['add2'];
            }
            if($this->invoice['bill_party']['account_detail']['add3']){
                $bill_address .= $this->invoice['bill_party']['account_detail']['add3'];
            }
        }
        $ship_address = '';
        if($this->invoice['ship_party'] && $this->invoice['ship_party']['account_detail']){
            if($this->invoice['ship_party']['account_detail']['add1']){
                $ship_address .= $this->invoice['ship_party']['account_detail']['add1'];
            }
            if($this->invoice['ship_party']['account_detail']['add2']){
                $ship_address .= $this->invoice['ship_party']['account_detail']['add2'];
            }
            if($this->invoice['ship_party']['account_detail']['add3']){
                $ship_address .= $this->invoice['ship_party']['account_detail']['add3'];
            }
        }
        $this->addMCell($this->getColW(2), 0,  ': ' . $bill_address, 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3), 0, 'Address ', 0, 'L', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(4), 0,  ': ' . $ship_address, 0, 'L', 1, 'T', 9, '');
        $this->addMCell($this->getColW(1), 0, 'GSTIN ', 0, 'L', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(2), 0, ': ' . $this->invoice['bill_party']['account_detail']['gst_no'], 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3), 0, 'GSTIN ', 0, 'L', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(4), 0, ': ' . $this->invoice['ship_party']['account_detail']['gst_no'], 0, 'L', 1, 'T', 9, '');

        $this->addMCell($this->getColW(1), 0, 'City ', 0, 'L', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(2), 0, ': ' . $bill_city_name, 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3), 0, 'City ', 0, 'L', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(4), 0, ': ' . $ship_city_name, 0, 'L', 1, 'T', 9, '');

        $cols = [20,45,20,15,20,45,20,15];
        $this->addCols($cols);


        $this->addMCell($this->getColW(1), 0, 'State ', 0, 'L', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(2), 0, ': ' . $bill_state_name, 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(3), 0, 'State Code : ', 0, 'L', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(4), 0, $bill_state_code, 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(5), 0, 'State ', 0, 'L', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(6), 0, ': ' . $ship_state_name, 0, 'L', 0, 'T', 9, '');
        $this->addMCell($this->getColW(7), 0, 'State Code : ', 0, 'L', 0, 'T', 9, 'B');
        $this->addMCell($this->getColW(8), 0, $ship_state_code, 0, 'L', 1, 'T', 9, '');
        // $this->pdf->ln(1);

        // $this->addHLine();
        $this->addVLine($this->lm + 100);
        $this->GetYPos(true);
        $this->addCols([10,40,15,15,20,20,20,20,20,20]);

        $this->addMCell($this->getColW(1), $this->lineHeight,  'Sr. No.' , 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(2), $this->lineHeight,  'Name of Product/Service ' .chr(10)  .chr(10) , 1, 'C', 0, 'T', 9,'B',1);
        $this->addMCell($this->getColW(3), $this->lineHeight,  'HSN/ SAC' , 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(4), $this->lineHeight,  'Unit'.chr(10)  .chr(10) , 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(5), $this->lineHeight,  'Qty.'.chr(10)  .chr(10) , 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(6), $this->lineHeight,  'Weight'.chr(10)  .chr(10) , 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(7), $this->lineHeight,  'Rate' .chr(10)  .chr(10), 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(8), $this->lineHeight,  'Amount (Rs.)' .chr(10)  , 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(9), $this->lineHeight,  'Less Discount' , 1, 'C', 0, 'T', 9, 'B',1);
        $this->addMCell($this->getColW(10), $this->lineHeight,  'Taxable Value (Rs.)' , 1, 'C', 1, 'T', 9, 'B',1);
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
                    if(!in_array($contract_no, $contract_nos, true)){
                        array_push($contract_nos , $contract_no);
                    }
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
        $this->testpdf->addMCell($this->getColW(2), $this->lineHeight,  $item_name . ' '. $brand_name .' ',  $packing_name, 0, 'L', 0, 'T', 0, '');

        if($is_last && ($this->secHeight + $this->testpdf->testHeight)  > $this->detailHeight){
            $this->startNewPage();
        }
        else if ($this->secHeight + $this->testpdf->testHeight > $this->detailHeight){
            $this->startNewPage();
        }

        $item_name = $item_name . ' '. $brand_name .' '.  $packing_name;
        $this->addMCell($this->getColW(1), 0, $count, 0, 'C', 0, 'T', 9, '');
        $this->addMCell($this->getColW(2), 0, $item_name, 0, 'L', 0, 'L', 9, '');
        $this->addMCell($this->getColW(3), 0, $invoice_det['item']['hsn_code'], 0, 'C', 0, 'T', 9, '');
        $this->addMCell($this->getColW(4), 0, $invoice_det['item_unit'] ?  $invoice_det['item_unit']['unit_name']:'', 0, 'C', 0, 'T', 8, '');
        $this->addMCell($this->getColW(5), 0, $invoice_det['qty'], 0, 'R', 0, 'T', 9, '');
        $wt = $invoice_det['weight'];
        if($this->invoice->packed_loose == 'packed') {
            $wt = $invoice_det['weight']/100;
        }
        $this->addMCell($this->getColW(6), 0, $wt, 0, 'R', 0, 'T', 9, '');
        $this->addMCell($this->getColW(7), 0, $invoice_det['rate'], 0, 'R', 0, 'T', 9, '');
        $this->addMCell($this->getColW(8), 0, $invoice_det['basic_amount'] ? $invoice_det['basic_amount'] : '0.00', 0, 'R', 0, 'T', 9, '');
        $this->addMCell($this->getColW(9), 0, $invoice_det['discount'] ? $invoice_det['discount']  : '0.00', 0, 'R', 0, 'T', 9, '');
        $this->addMCell($this->getColW(10), 0, $invoice_det['amt_without_gst'] ?  $invoice_det['amt_without_gst']:'0.00', 0, 'R', 0, 'T', 9, '');


        $this->total_qty += $invoice_det['qty'];
        $this->total_weight += $wt;
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
        if ($this->secHeight > $this->detailHeight) {
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
            $this->addCols([80,20,20,20,20,20,20]);
            $this->addMCell($this->getColW(1), $this->lineHeight, 'Total', 0, 'C', 0, 'T', 8, 'B');
            $this->addCell($this->getColW(2), $this->lineHeight, myRound($this->total_qty), 0, 'R', 0, 'T', 8, 'B');
            $this->addCell($this->getColW(3), $this->lineHeight, myRound($this->total_weight,3), 0, 'R', 0, 'T', 8, 'B');
            $this->addCell($this->getColW(4), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');
            $this->addCell($this->getColW(5), $this->lineHeight, myRound($this->total_amount ? $this->total_amount :'0.00'), 0, 'R', 0, 'T', 8, 'B');
            $this->addCell($this->getColW(6), $this->lineHeight,myRound( $this->total_discount ?$this->total_discount:'0.00') , 0, 'R', 0, 'T', 8, 'B');
            $this->addCell($this->getColW(7), $this->lineHeight, myRound($this->amt_without_gst), 0, 'R', 1, 'T', 8, 'B');

            $this->drawBox($this->pdf->getY(), false);
            $this->addHLine();

            $this->GetYPos(true);

            $this->addCols([32,78,70,20]);
            $this->addMCell($this->getColW(1), $this->lineHeight, 'Amount In words : ', 0, 'L', 0, 'T', 8, '');
            $this->addMCell($this->getColW(2), $this->lineHeight, figToWord($this->invoice['net_amt'] ), 0, 'L', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(3), $this->lineHeight, 'Total:', 0, 'R', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(4), $this->lineHeight,number_format($this->amt_without_gst, 2, ".", '') , 0, 'R', 1, 'T', 8, 'B');

            $term_f = $this->invoice['freight'] > 0 ? 'Add ' :'Less ';
            if(abs($this->invoice['freight']) > 0){
                $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(3), $this->lineHeight, $term_f .'Freight :', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight,  number_format(abs($this->invoice['freight']), 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            }

            $term_d = $this->invoice['discount_amt'] > 0 ? 'Add ' :'Less ';
            if(abs($this->invoice['discount_amt']) > 0){
                $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(3), $this->lineHeight, $term_d .'Discount :', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight,  number_format(abs($this->invoice['discount_amt']), 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            }
            $term_e = $this->invoice['export_fee'] > 0 ? 'Add ' :'Less ';
            if(abs($this->invoice['export_fee']) > 0){
                $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(3), $this->lineHeight,$term_e. 'Export Fee :', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight,  number_format(abs($this->invoice['export_fee']), 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            }
            foreach($this->gst_rates as $key => $gst){
                $this->addMCell($this->getColW(1), $this->lineHeight, ' ', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(3), $this->lineHeight, 'Add '.$key.'% On '.myround($gst['gst_on']) .' :', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight, myround($gst['gst_value']) , 0, 'R', 1, 'T', 8, 'B');
            }
             if(abs($this->invoice['tcs_per']) > 0){
                $this->addMCell($this->getColW(1), $this->lineHeight,'', 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');

                $this->addMCell($this->getColW(3), $this->lineHeight, $this->invoice['tcs_per'] ?  'TCS @' .$this->invoice['tcs_per'] .' %' : 'TCS @ 0.00%'  , 0, 'R', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(4), $this->lineHeight, number_format($this->invoice['tcs_amount'], 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');
            }
            $this->addMCell($this->getColW(1), $this->lineHeight, 'Bank Name : ', 0, 'L', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(2), $this->lineHeight,$this->invoice['ifsc'] &&  $this->invoice['ifsc']['bank'] ? $this->invoice['ifsc']['bank']['name'] :'', 0, 'L', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(3), $this->lineHeight, 'Round Off : ', 0, 'R', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(4), $this->lineHeight, number_format($this->invoice['round_off'], 2, ".", ''), 0, 'R', 1, 'T', 8, 'B');

            $this->addMCell($this->getColW(1), $this->lineHeight, 'Bank Account Number : ', 0, 'L', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(2), $this->lineHeight, $this->invoice['bank_account_number'] , 0, 'L', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(3), $this->lineHeight, 'Bill Amount :', 0, 'R', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(4), $this->lineHeight,$this->invoice['net_amt'] , 0, 'R', 1, 'T', 8, 'B');

            $this->addMCell($this->getColW(1), $this->lineHeight, 'Bank Branch IFSC : ', 0, 'L', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(2), $this->lineHeight, $this->invoice['ifsc']  ? $this->invoice['ifsc']['ifsc_code'] :'', 0, 'L', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(3), $this->lineHeight, '', 0, 'R', 0, 'T', 8, 'B');
            $this->addMCell($this->getColW(4), $this->lineHeight,'' , 0, 'R', 1, 'T', 8, 'B');
            $this->addHLine();
            $this->GetYPos(true);
            $this->addCols([100,100]);
            $remarks = $this->getEthanolRemarks();
            $str = "Certified that  the particulars given above are true and correct and the amount indicated represents the price actually charged and that there";
            $str .= ' is no flow of additional consideration directly or indirectly from the buyer.';
            $this->detHeight = 0;
            $this->addMCell($this->getColW(1), $this->lineHeight, $str, 0, 'L', 0, 'T', 8, '');
            $this->addMCell($this->getColW(2), $this->lineHeight,$remarks != '' ? 'Remarks if Any : ':'',  0, 'L', 1, 'T', 8, 'B');
            if($remarks != '') {
                $this->addCell($this->getColW(1), $this->lineHeight, '',  0, 'L', 0, 'T', 8, 'B');
                $this->addMCell($this->getColW(2), $this->lineHeight, $remarks,  0, 'L', 0, 'T', 8, 'B');
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


            // $this->pdf->SetY(-15);
            $this->pdf->ln(1);
            $this->addCell(0, $this->lineHeight, "FOR " .  $this->company['company_name'] , 0, 'R', 1, 'T', 7, 'B');
            $this->pdf->ln(15);
            $this->GetYPos(true);
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
        // $this->pdf->ln(2);
        $currentPage = $this->pdf->getPage();
        $totalPages = $this->pdf->getNumPages();
        // $this->addCell(0, $this->lineHeight, "FOR " .  $this->company['company_name'], 0, 'R', 1, 'T', 7, 'B');
        $footerText = "Page {$currentPage} of {$totalPages}";
        $this->pdf->SetY(-10);
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
        $hight = 35;
        $hight += $this->invoice['ethanol_bill'] == 'Y' ? 25:15;
        $hight += count($this->term_conditions) > 0 ? (count($this->term_conditions)+1)*5:0;
        $hight += count($this->gst_rates)*5;
        $hight += $this->getHight(abs($this->invoice['freight']));
        $hight += $this->getHight(abs($this->invoice['discount_amt']));
        $hight += $this->getHight(abs($this->invoice['export_fee']));
        $hight += $this->getHight(abs($this->invoice['tcs_amount']));
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
            $remarks = addString($remarks,'1. Ethanol Category : '.$this->invoice['ethanol_category'],chr(10));
            $remarks = addString($remarks,'2. Excise Certificate No.: '.$this->invoice['excise_certificate'],chr(10));
            $remarks = addString($remarks,'3. Denaturant : '.$this->invoice['denaturant'],chr(10));
            $remarks = addString($remarks,'   a. Crotonaldehde : '.$this->invoice['crotonaldehde'],chr(10));
            $remarks = addString($remarks,'   b. Denatonium Saccaride : '.$this->invoice['denatonium_saccaride'],chr(10));
            $remarks = addString($remarks,'   c. Denatonium Benzoate : '.$this->invoice['denatonium_benzoate'],chr(10));
            $remarks = addString($remarks,'4. Tanker Seal No.: '.$this->invoice['tanker_seal_no'],chr(10));
        }
        return $remarks;
    }

}
