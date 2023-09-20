<?php

namespace App\Console\Commands;

use App\Models\Masters\Prefix;
use Illuminate\Console\Command;

class AddPrefix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prefix:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->addPrefix(['prefix' => '','type'=>'sale_contract','label'=>'Sale Contract']);
        $this->addPrefix(['prefix' => '','type'=>'sale_order','label'=>'Sale Order']);
        $this->addPrefix(['prefix' => '','type'=>'sale_order_dispatch','label'=>'Sale Order Dispatch']);
        $this->addPrefix(['prefix' => '','type'=>'voucher','label'=>'Voucher']);
        $this->addPrefix(['prefix' => '','type'=>'gst_invoice','label'=>'GST Invoice']);
        $this->addPrefix(['prefix' => '','type'=>'vat_invoice','label'=>'VAT Invoice']);
        $this->addPrefix(['prefix' => '','type'=>'retail_invoice','label'=>'Retail Invoice']);
        $this->addPrefix(['prefix' => '','type'=>'high_seas_sale','label'=>'High Seas Invoice']);
        $this->addPrefix(['prefix' => '','type'=>'sale_against_bond','label'=>'Sale Against Bond']);
        $this->addPrefix(['prefix' => '','type'=>'debit_note_purchase','label'=>'Debit Note Purchase']);
        $this->addPrefix(['prefix' => '','type'=>'credit_note_purchase','label'=>'Credit Note Purchase']);
        $this->addPrefix(['prefix' => '','type'=>'debit_note_sale','label'=>'Debit Note Sale']);
        $this->addPrefix(['prefix' => '','type'=>'credit_note_sale','label'=>'Credit Note Sale']);
        $this->addPrefix(['prefix' => '','type'=>'issue_transfer','label'=>'Issue Transfer']);
        $this->addPrefix(['prefix' => '','type'=>'receipt_transfer','label'=>'Receipt Transfer']);
        $this->addPrefix(['prefix' => '','type'=>'purchase_order','label'=>'Purchase Order']);


        echo 'Prefix updated!' . PHP_EOL;

    }

    private function addPrefix($prefix_data)
    {
        $r = Prefix::updateOrCreate(['type' => $prefix_data['type']], $prefix_data);
    }
}
