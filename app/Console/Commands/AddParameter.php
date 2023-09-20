<?php

namespace App\Console\Commands;

use App\Models\Masters\Company;
use App\Models\Yearly\Parameter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Years;

class AddParameter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parameters:check';
    protected $yearly_db = null;


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks & Add Parameter in the Database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companies = Company::get();
        foreach ($companies as $comp) {
            $db = env('DB_DATABASE', 'bathindachemicals_') . $comp->id . '_' . $comp->year;
            $this->yearly_db = getYearlyDbConnFromDb($db);
            $this->addParameter(['s_no' => '-1', 'para_name' => 'sale_contract_pack_id', 'para_disp_name' => 'Default Sale Contract Packing ', 'para_type' => 'numeric', 'para_nature' => 'Packing Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '-1', 'para_name' => 'freight_ac_id', 'para_disp_name' => 'Freight Account ', 'para_type' => 'numeric', 'para_nature' => 'Gl A/c Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '-1', 'para_name' => 'freight_item_id', 'para_disp_name' => 'Freight Item ', 'para_type' => 'numeric', 'para_nature' => 'Item Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '4', 'para_name' => 'brokerage_per_nag', 'para_disp_name' => 'Brokerage per Nag ', 'para_type' => 'String', 'para_nature' => 'Normal', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'No'], 0);
            $this->addParameter(['s_no' => '5', 'para_name' => 'brokerage_per_quintal', 'para_disp_name' => 'Brokerage per Qunital', 'para_type' => 'String', 'para_nature' => 'Normal', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'No'], 0);
            $this->addParameter(['s_no' => '6', 'para_name' => 'brokerage_per_drum', 'para_disp_name' => 'Brokerage per Drum ', 'para_type' => 'String', 'para_nature' => 'Normal', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'No'], 0);
            $this->addParameter(['s_no' => '5', 'para_name' => 'brokerage_per_quintal_unit', 'para_disp_name' => 'Brokerage Qunital Unit(for brakage rate in sale contract)', 'para_type' => 'numeric', 'para_nature' => 'Item Unit Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '6', 'para_name' => 'brokerage_per_drum_unit', 'para_disp_name' => 'Brokerage Drum Unit(for brakage rate in sale contract) ', 'para_type' => 'numeric', 'para_nature' => 'Item Unit Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '7', 'para_name' => 'tcs_rate', 'para_disp_name' => 'TCS  Rate', 'para_type' => 'decimal', 'para_nature' => 'Normal', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'No'], 0);
            $this->addParameter(['s_no' => '8', 'para_name' => 'tcs_rate_liqour', 'para_disp_name' => 'TCS  Rate Liqour', 'para_type' => 'decimal', 'para_nature' => 'Normal', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'No'], 0);
            $this->addParameter(['s_no' => '9', 'para_name' => 'tcs_ac_id', 'para_disp_name' => 'TCS Account ', 'para_type' => 'numeric', 'para_nature' => 'Gl A/c Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '10', 'para_name' => 'tcs_amount_on', 'para_disp_name' => 'TCS Account on ', 'para_type' => 'numeric', 'para_nature' => 'Normal', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'No'], 0);
            $this->addParameter(['s_no' => '11', 'para_name' => 'tcs_ac_id_liquor', 'para_disp_name' => 'TCS Account For Liquor', 'para_type' => 'numeric', 'para_nature' => 'Gl A/c Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '12', 'para_name' => 'ac_id_roff', 'para_disp_name' => 'Round Off Account', 'para_type' => 'numeric', 'para_nature' => 'Gl A/c Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '13', 'para_name' => 'ac_id_freight_gst', 'para_disp_name' => 'Freight A/c(GST)', 'para_type' => 'numeric', 'para_nature' => 'Gl A/c Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '14', 'para_name' => 'ac_id_freight_vat', 'para_disp_name' => 'Freight A/c(VAT)', 'para_type' => 'numeric', 'para_nature' => 'Gl A/c Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '15', 'para_name' => 'ac_id_export_fee', 'para_disp_name' => 'Export Fee A/c', 'para_type' => 'numeric', 'para_nature' => 'Gl A/c Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '16', 'para_name' => 'ac_id_barcode_fee', 'para_disp_name' => 'Bar Code Fee A/c', 'para_type' => 'numeric', 'para_nature' => 'Gl A/c Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '17', 'para_name' => 'ac_id_excise_duty', 'para_disp_name' => 'Excise Duty A/c', 'para_type' => 'numeric', 'para_nature' => 'Gl A/c Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '18', 'para_name' => 'ac_id_court_fee', 'para_disp_name' => 'Court Fees A/c', 'para_type' => 'numeric', 'para_nature' => 'Gl A/c Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
            $this->addParameter(['s_no' => '-1', 'para_name' => 'test_unit', 'para_disp_name' => 'Test Item Unit', 'para_type' => 'numeric', 'para_nature' => 'Item Unit Help', 'para_length' => '0', 'para_decimals' => '0', 'mandatory' => 'Yes'], 0);
        }

        echo 'parameters added' . PHP_EOL;
    }

    private function addParameter($parameter, $para_value = '')
    {

        Parameter::on($this->yearly_db)->where('para_disp_name','Need for Key Generation ignore this')->delete();
        $r = Parameter::on($this->yearly_db)->where('para_name', $parameter['para_name'])->first();
        if($r && $parameter['s_no'] == -1) {
            Parameter::on($this->yearly_db)->where('para_name', $parameter['para_name'])->delete();
        } else {
            if ($r) {
                $r = Parameter::on($this->yearly_db)->updateOrCreate(['para_name' => $parameter['para_name']], $parameter);
            } else {
                $r = Parameter::on($this->yearly_db)->updateOrCreate(['para_name' => $parameter['para_name']], $parameter);
                if ($para_value != '') {
                    $r->para_value = $para_value;
                }
                $r->save();
            }
        }
    }
}
