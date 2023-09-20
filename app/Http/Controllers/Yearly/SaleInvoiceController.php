<?php

namespace App\Http\Controllers\Yearly;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleInvoiceRequest;
use App\Models\Accounts\Account;
use App\Models\Company\SaleContract;
use App\Models\Company\SaleContractDetail;
use App\Models\Items\Item;
use App\Models\Items\ItemUnit;
use App\Models\Masters\Brand;
use App\Models\Masters\Packing;
use App\Models\Yearly\Invoice;
use App\Models\Yearly\Parameter;
use App\Models\Yearly\SaleOrder;
use App\Printings\SaleInvoicePrint;
use App\Printings\SaleInvoiceVatPrint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SaleInvoiceController extends Controller
{
    public function index(Request $request){
        if (Gate::denies('sale-invoices')) {
            return deny();
        }

        return Inertia::render('ProjectComponents/Sale/Invoice/InvoiceList',[
            'sale_order_id' => 0,
            'invoice' => 0,
            // 'transport_types' => getTransportTypes(),
            // 'branch' => getCurrentBranch(),
            // 'tcs_liquor' => Parameter::getParaValue('tcs_rate_liqour')
        ]);
    }

    public function saleInvoiceList (Request $request){
        if (Gate::denies('sale-invoices')) {
            return deny();
        }
        $count = Invoice::count();
        $filteredCount = $count;

        $invoices = Invoice::leftJoin(getSharedDb().'.accounts','accounts.id','=','invoices.client_id');

        if ($searchStr = $request->input('search.value')) {
            $invoices = $invoices->where('accounts.name', 'like', "%{$searchStr}%");
        }

        $invoices = $invoices->where('invoices.branch_id',getCurrentBranchId());
        $asc_desc = $request->order[0]['dir'];
        $field_name = 'invoices.'. $request->columns[$request->order[0]['column']]['data'];

        if($request->columns[$request->order[0]['column']]['data'] =='client_name'){
            $field_name = 'accounts.name';
        }

        if($field_name){
            $invoices = $invoices->orderBy($field_name, $asc_desc);
        }
        else{
            $invoices = $invoices->orderBy('invoices.id','DESC');
        }
        $invoices = $invoices->take($request->length);
        $filteredCount = $invoices->count();
        if ($request->start > 0) {
            $invoices->skip($request->start);
        }
        $invoices = $invoices->groupBy('invoices.id')->with(['sale_order'])
        ->select('invoices.*','accounts.name as client_name')->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $invoices,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function show(Request $request,$sale_order_id){
        if (Gate::denies('sale-invoices-add')) {
            return deny();
        }

        $delivery_terms = [];
        $sale_order = SaleOrder::where('id',$sale_order_id)->first();
        $account = Account::findOrFail($sale_order->bill_party_id);
        $credit_limit = $account->getAccountLimit();
        $balance = getAccountBalanceLimit($account['id']);

        if($sale_order){
            $sale_order->load([
                'sale_order_details',
                'client.account_yearly',
                'bill_party',
                'ship_party',
                'transport',
                'sale_order_details.sale_order_packs.brand',
                'sale_order_details.item',
                'sale_order_details.sale_contract',
                'sale_order_details.gst.gst_types.details',
                'sale_order_details.vat_cst',
                'sale_order_details.foc_item',
                'sale_order_details.foc_packing',
                'sale_order_details.foc_brand',
                'sale_order_details.sale_order_packs.packing'
            ]);
            if($sale_order && $sale_order['sale_order_details'][0]['item_id']){
                $item = Item::findOrFail($sale_order && $sale_order['sale_order_details'][0]['item_id']);
                if($item['gst_id'] > 0){
                    $sale_order['type'] ='gst';
                }
                else if($item['vat_cst_id'] > 0){
                    $sale_order['type'] ='vat';
                }
            }

            $det_items = [];
            $sale_contract = null;
            foreach($sale_order['sale_order_details'] as $sale_order_det){
                if(!$sale_contract) {
                    $sale_contract = SaleContract::where('id',$sale_order_det->sale_contract_id)->first();
                    if($sale_contract){
                        if (!in_array($sale_contract->delivery_terms, $delivery_terms)) {
                            $delivery_terms[] = $sale_contract->delivery_terms == 'F' ? 'For':($sale_contract->delivery_terms == 'M'? 'EX-MILL':($sale_contract->delivery_terms == 'K'?'EX-KANDLA':''));
                        }
                    }
                }
                if($sale_order['packed_loose'] == 'loose'){
                    $item = Item::where('id',$sale_order_det['item']['id'])->with(['gst.gst_types.details','vat_cst','secondary_unit'])->first();
                    $sale_contract_detail_unit_ids = SaleContractDetail::where('sale_contract_id',$sale_order_det->sale_contract_id)->where('item_id',$item)->pluck('unit_id')->toArray();
                    $unit  = ItemUnit::where('id',$item['item_unit_id'])->first();
                    $packing  = Packing::where('id',$sale_order_det['packing_id'])->first();
                    if(count($sale_contract_detail_unit_ids) > 0){
                        $unit  = ItemUnit::whereIn('id',$sale_contract_detail_unit_ids)->first();
                    }
                    $item = [
                        'item' => $sale_order_det['item'],
                        'item_unit'=> $unit,
                        'brand'=> null,
                        'packing'=>$packing,
                        'qty' =>  $sale_order_det['qty'],
                        'weight' =>  $sale_order_det['weight'],
                        'rate_on' =>  $sale_order_det['rate_on'],
                        'rate' =>  $sale_order_det['final_rate'],
                        'freight' =>  $sale_order_det['freight'],
                        'packing_cost' =>  $sale_order_det['packing_cost'],
                        'discount' =>  $sale_order_det['discount'],
                        'gst_id' => $item['gst_id'],
                        'gst_vat_id' => $item['vat_cst_id'],
                        'gst' => $item['gst'],
                        'vat_cst' => $item['vat_cst'],
                        'gst_vat_amount' => $sale_order_det['gst_vat_amount'],
                        'net_amount' => $sale_order_det['net_amount'],
                        'basic_amount' =>$sale_order_det['basic_amount']
                    ];

                   $det_items[] = $item;
                }
                else{
                    foreach($sale_order_det['sale_order_packs'] as $pack){
                        $item = Item::where('id',$sale_order_det['item']['id'])->with(['gst.gst_types.details','vat_cst','secondary_unit'])->first();
                        $sale_contract_detail_unit_ids = SaleContractDetail::where('sale_contract_id',$sale_order_det->sale_contract_id)->where('item_id',$item)->pluck('unit_id')->toArray();
                        $unit  = ItemUnit::where('id',$item['item_unit_id'])->first();
                        if(count($sale_contract_detail_unit_ids) > 0){
                            $unit  = ItemUnit::whereIn('id',$sale_contract_detail_unit_ids)->first();
                        }
                        $tot_freight = $sale_order_det['freight'];
                        $item = [
                            'item' => $sale_order_det['item'],
                            'item_unit'=> $unit,
                            'brand'=> $pack['brand'],
                            'brand_id'=> $pack['brand_id'],
                            'packing_id'=> $pack['packing_id'],
                            'packing'=> $pack['packing'],
                            'qty' =>  $pack['qty'],
                            'weight' =>  $pack['weight'],
                            'rate_on' =>  'Q',
                            'discount' =>  $pack['discount'],
                            'rate' =>  $pack['net_rate'],
                            'freight' =>  $sale_order_det['freight']/count($sale_order_det['sale_order_packs']),
                            'packing_cost' =>  $sale_order_det['packing_cost']/count($sale_order_det['sale_order_packs']),
                            'gst_id' => $item['gst_id'],
                            'gst_vat_id' => $item['vat_cst_id'],
                            'gst' => $item['gst'],
                            'vat_cst' => $item['vat_cst'],
                            'gst_vat_amount' => $sale_order_det['gst_vat_amount'],
                            'net_amount' => $pack['net_amount'],
                            'basic_amount' =>$pack['amt_without_gst']
                        ];

                        $det_items[] = $item;
                    }

                    if($sale_order_det->foc_item_id > 0){
                        //FREE ITEm
                        $foc_item = Item::where('id',$sale_order_det->foc_item_id)->with(['gst','vat_cst'])->first();
                        $unit  = ItemUnit::where('id',$foc_item['item_unit_id'])->first();
                        $brand  = Brand::findOrFail($sale_order_det['foc_brand_id']);
                        $packing  = Packing::findOrFail($sale_order_det['foc_packing_id']);
                        $item = [
                            'item' =>$foc_item,
                            'item_unit'=> $unit,
                            'brand'=> $brand,
                            'qty' =>  $sale_order_det['foc_qty'],
                            'weight' =>  $sale_order_det['foc_weight'],
                            'packing_id'=> $packing['id'],
                            'packing'=> $packing,
                            'rate_on' =>  '',
                            'rate' => '',
                            'freight' => '',
                            'packing_cost' => '',
                            'discount' =>  0,
                            'gst_id' =>0,
                            'gst_vat_id' =>0,
                            'gst' => null,
                            'vat_cst' => null,
                            'gst_vat_amount' => 0,
                            'net_amount' => 0,
                        ];
                        $det_items[] = $item;

                    }
                }
            }
            $sale_order['item_details'] = $det_items;
        }
        $sale_order['sale_contract'] = $sale_contract;

        $delivery_terms = array_unique($delivery_terms);

        // Convert array to comma-separated string
        $delivery_terms = implode(',', $delivery_terms);
        return Inertia::render('ProjectComponents/Sale/Invoice/InvoiceList',[
            'sale_order' => $sale_order,
            'transport_types' => getTransportTypes(),
            'branch' => getCurrentBranch(),
            'credit_limit'=>$credit_limit,
            'balance'=>$balance,
            'delivery_terms'=>$delivery_terms,
        ]);
    }

    public function store(SaleInvoiceRequest $request){
        return $request->save();
    }

    public function update(SaleInvoiceRequest $request){
        return $request->save();
    }

    public function edit(Request $request,$id){
        if (Gate::denies('sale-invoices-modify') || Gate::denies('sale-invoices-view')) {
            return deny();
        }
        $sale_invoice = Invoice::whereId($id)->with([
            'invoice_details.item',
            'invoice_details.packing',
            'invoice_details.brand',
            'invoice_details',
            'client.account_yearly',
            'bill_party',
            'ship_party',
            'transport',
            'sale_order',
            'invoice_details.item.gst',
            'invoice_details.item.vat_cst',
            'invoice_details.brand',
            'invoice_details.item_unit',
            'invoice_details.item.secondary_unit',
            'ifsc',
            'invoice_details.gst.gst_types.details',
            'invoice_details.vat_cst',
            'invoice_details.packing',
            'account_number.ifsc'
        ])->first();


        $account = Account::findOrFail($sale_invoice->bill_party_id);
        $credit_limit = $account->getAccountLimit();
        $balance = getAccountBalanceLimit($sale_invoice->bill_party_id);


        return reply(true, [
            'sale_invoice' => $sale_invoice,
            'credit_limit' => $credit_limit,
            'balance'=>$balance
        ]);
    }

    public function getSaleInvoicePrint(Request $request,$id){
        if (Gate::denies('sale-invoices-print')) {
            return deny();
        }
        $invoice = Invoice::findOrFail($id);

        if($invoice->invoice_type == 'vat_invoice'){
            $print = new SaleInvoiceVatPrint();
            $pdf = $print->makepdf($invoice);
            $pdf->Output("SaleInvoice_".$invoice['invoice_no'].".pdf", 'I');
        }
        else{
            $print = new SaleInvoicePrint();
            $pdf = $print->makepdf($invoice);
            $pdf->Output("SaleInvoice_".$invoice['invoice_no'].".pdf", 'I');
        }
        exit();
    }
}
