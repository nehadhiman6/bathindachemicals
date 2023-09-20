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
use App\Models\Yearly\SaleOrder;
use App\Models\Yearly\SaleOrderDispatch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleOrderDispatchPrint extends PrintPdf
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
    protected $dispatch = null;
    protected $sale_order_dispatches = [];
    protected $det_cols =[];



    public function makepdf($dispatch)
    {
        $this->dispatch = $dispatch;
        $this->dispatch->load('sale_order_dispatches.packing', 'sale_order_dispatches.sale_order','sale_order_dispatches.item', 'sale_order_dispatches.brand');

        if( $this->dispatch &&  count($this->dispatch['sale_order_dispatches']) > 0){
            $this->sale_order = $this->dispatch['sale_order_dispatches'][0]['sale_order'];
        }
        $this->company = getCom();

        $this->testpdf = new TestPdf();
        $this->testpdf->makePdf("L", "A4");
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
        $this->footerHeight = 10;
        do {
            $this->pageno++;
            $this->nextpage = "N";
            $this->addNewPage("L", "A4");
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
        $this->pdf->ln(4);
        $width = 0;
        $this->addCell($width, 0, $this->company->company_name, 0, 'C', 1, '', 15, 'B', false, false, true);
       $this->addCell($width, $this->lineHeight, $this->company->office_address, 0, 'C', 1, 'T', 10, 'T', false, false, true);
        $this->pdf->ln(2);
        $this->addHLine();
        $this->GetYPos(true);
        $this->pdf->ln(2);

        $this->addCols([110,110]);
        $this->addCell($this->getColW(1), 0, 'Dispatch Date :' . $this->dispatch->dispatch_date, 0, 'L', 0, 'T', 10, '');
        // $this->addCell($this->getColW(2), 0, $this->sale_order->bill_party['name'], 0, 'L', 0, 'T', 12, 'B');
        $this->addCell($this->getColW(3), 0,' SALE Dispatch No. : '. $this->dispatch->dispatch_advise_no, 0, 'L', 0, 'T', 10, 'B');

        $this->addCell($this->getColW(1), 0,  $this->sale_order ? 'Vehicle No. : ' . $this->sale_order->vehical_no:'', 0, 'R', 1, 'T', 12, '');
        $this->pdf->ln(1);
        $this->GetYPos(true);

        $packings = $this->getSaleDispatchData();

        // dd($this->sale_order_dispatches);

        $cols = [25,25,25,25,25];
        $pack_col = 147/count($packings);

        for($i= 0;$i<count($packings);$i++){
            $cols[] = $pack_col;
        }
        $cols[] = 15;

        $this->det_cols = $cols;
        $count = 1;

        $this->addCols($cols);
        $this->addMCell($this->getColW($count++), 0, 'Sale Order No.', 0, 'C', 0, 'T', 12, 'B');
        $this->addMCell($this->getColW($count++), 0, 'Party', 0, 'C', 0, 'T', 12, 'B');
        $this->addMCell($this->getColW($count++), 0, 'Destination', 0, 'C', 0, 'T', 12, 'B');
        $this->addMCell($this->getColW($count++), 0, 'Product', 0, 'C', 0, 'T', 12, 'B');
        $this->addMCell($this->getColW($count++), 0, 'Brand', 0, 'C', 0, 'T', 12, 'B');


        foreach($packings  as $key=>$packing){
            $this->addMCell($this->getColW($count++), 0, $packing['name'], 0, 'C', 0 , 'T', 10, 'B');
        }
        $this->addMCell($this->getColW($count++), 0, 'Total', 0, 'C', 1, 'T', 12, 'B');

        $this->pdf->ln(10);
        $this->addHLine();
        $this->hight = $this->getNetHeight();
        // dd( $this->hight - $pdf->getY() - $this->footerHeight);
        $this->detailHeight = $this->hight - $pdf->getY() - $this->footerHeight;
        $this->secHeight = 0;
        // $this->drawBox(0,true,true);
    }

    private function getSaleDispatchData(){
        $dispatch_id = $this->dispatch['id'];
        $sale_order_packings = SaleOrderDispatch::where('dispatch_id',$dispatch_id)->pluck('packing_id')->toArray();
        $this->packings = Packing::whereIn('id',$sale_order_packings)->orderBy('order_no')->get();
        $packings =  $this->packings;

        $data = SaleOrderDispatch::where('dispatch_id',$dispatch_id)->select('destination', 'brand_id','item_id','sale_order_id')
            ->groupBy('destination', 'brand_id','item_id','sale_order_id')
            ->with(['brand','item','sale_order'])
            ->get();


        $this->sale_order_dispatches = [];

        foreach ($data as $row) {
            $destination = $row->destination;
            $brandId = $row->brand_id;
            $brand_name= $row->brand ?$row->brand['name']:'' ;
            $item_name= $row->item ?$row->item['item_name']:'' ;
            $item_id= $row->item ?$row->item['id']:'' ;
            $sale_order_id= $row->sale_order ?$row->sale_order['id']:'' ;
            $sale_order_no= $row->sale_order ?$row->sale_order['sale_order_no']:'' ;

            $bill_party ='';
            $bill_party_id = $row->sale_order ? $row->sale_order['bill_party_id']:'' ;
            if($bill_party_id > 0){
                $account = Account::findOrFail($bill_party_id);
                $bill_party = $account ? $account['name']:'';
            }
            $packingsData = [];

            foreach ($packings as $packing) {
                $qty = SaleOrderDispatch::where('dispatch_id', $dispatch_id)
                    ->where('destination', $destination)
                    ->where('brand_id', $brandId)
                    ->where('sale_order_id', $sale_order_id)
                    ->where('packing_id', $packing['id'])
                    ->where('item_id', $item_id)
                    ->sum('qty');

                $packingsData[] = [
                    'packing_id' => $packing['id'],
                    'packing_name' => $packing['name'],
                    'qty' => $qty
                ];
            }

            $this->sale_order_dispatches[] = [
                'sale_order_no' => $sale_order_no,
                'bill_party' => $bill_party,
                'destination' => $destination,
                'brand_id' => $brandId,
                'brand_name'=>$brand_name,
                'item_id'=>$item_id,
                'item_name'=>$item_name,
                'packings' => $packingsData
            ];
        }

        return $packings;
    }


    private function details($title = '')
    {

        foreach ($this->sale_order_dispatches as $sale_order_dispatch) {
            $this->addDetail($sale_order_dispatch);
            $this->drawBox(0,true,true);
        }
        $this->addHLine();
        $sumQuantities = SaleOrderDispatch::where('dispatch_id',$this->dispatch['id'])
            ->groupBy('packing_id')
            ->selectRaw('packing_id,SUM(qty) as total_qty')
            ->pluck('total_qty', 'packing_id');

        $this->addMCell($this->getColW(1,5), $this->lineHeight, 'TOTAL', 0, 'C', 0, 'T', 12, 'B');
        $count = 6;
        $total_qty = 0;
        $a = 1;
        foreach ($this->packings as $packing) {
            // echo($a++);
            $total_qty += $sumQuantities[$packing['id']];
            $this->addMCell($this->getColW($count++), $this->lineHeight, number_format($sumQuantities[$packing['id']]) , 0, 'C', 0, 'T', 12, 'B');
        }
        $this->addMCell($this->getColW($count++), $this->lineHeight, $total_qty, 0, 'C', 1, 'T', 12, 'B');
        $this->drawBox(0,true,true);
    }

    private function addDetail($dispatch)
    {
        $this->detHeight = 0;
        $this->testpdf->addMCell($this->getColW(2), $this->lineHeight,  $dispatch['bill_party'], 0, 'L', 0, 'T', 0, '');
        if ($this->secHeight + $this->testpdf->testHeight > $this->detailHeight)
        $this->startNewPage();

        $count = 1;
        $this->addMCell($this->getColW($count++), 0, $dispatch['sale_order_no'], 0, 'C', 0, 'T', 12, '');
        $this->addMCell($this->getColW($count++), 0, $dispatch['bill_party'], 0, 'C', 0, 'L', 12, '');
        $this->addMCell($this->getColW($count++), 0, $dispatch['destination'], 0, 'C', 0, 'T', 12, '');
        $this->addMCell($this->getColW($count++), 0, $dispatch['item_name'], 0, 'C', 0, 'T', 12, '');
        $this->addMCell($this->getColW($count++), 0, $dispatch['brand_name'], 0, 'C', 0, 'T', 12, '');
        $total_qty = 0;
        foreach($dispatch['packings'] as $key=>$packing){
            $total_qty +=  $packing['qty'] ?  $packing['qty']:0;
            $this->addMCell($this->getColW($count++), 0, $packing['qty'] > 0 ? number_format($packing['qty']):'', 0, 'C', 0, 'T', 12, '');
        }
        $this->addMCell($this->getColW($count++), 0, $total_qty, 0, 'C', 0, 'T', 12, '');
        $this->pdf->ln($this->detHeight);
        $this->secHeight += $this->detHeight;
    }

    private function footer($type = "R")
    {

    }




    private function startNewPage($footer = 'Y')
    {
        // $this->footerHeight = 20;
        if ($footer == 'Y')
            $this->footer();
        $this->secHeight = 0;
        $this->pageno++;
        $this->addNewPage("L", "A4");
        $this->header();
    }
}
