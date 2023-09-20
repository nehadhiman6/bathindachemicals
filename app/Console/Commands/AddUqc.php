<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Items\Uqc;

class AddUqc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uqc:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $this->addUqc(['uqc' => 'BAL', 'uqc_name' => 'BALE', 'uqc_disp_name' => 'BAL-BALE ']);
        $this->addUqc(['uqc' => 'BDL', 'uqc_name' => 'BUNDLES', 'uqc_disp_name' => 'BDL-BUNDLES ']);
        $this->addUqc(['uqc' => 'BKL', 'uqc_name' => 'BUCKLES', 'uqc_disp_name' => 'BKL-BUCKLES ']);
        $this->addUqc(['uqc' => 'BOU', 'uqc_name' => 'BILLION OF UNITS ', 'uqc_disp_name' => 'BOU-BILLION OF UNITS ']);
        $this->addUqc(['uqc' => 'BOX', 'uqc_name' => 'BOX', 'uqc_disp_name' => 'BOX-BOX ']);
        $this->addUqc(['uqc' => 'BTL', 'uqc_name' => 'BOTTLES', 'uqc_disp_name' => 'BTL-BOTTLES']);
        $this->addUqc(['uqc' => 'BUN', 'uqc_name' => 'BUNCHES', 'uqc_disp_name' => 'BUN-BUNCHES ']);
        $this->addUqc(['uqc' => 'CAN', 'uqc_name' => 'CAN', 'uqc_disp_name' => 'CAN-CANS ']);
        $this->addUqc(['uqc' => 'CBM', 'uqc_name' => 'CUBIC METERS ', 'uqc_disp_name' => 'CBM-CUBIC METERS ']);
        $this->addUqc(['uqc' => 'CCM', 'uqc_name' => 'CUBIC CENTIMETERS', 'uqc_disp_name' => 'CCM-CUBIC CENTIMETERS ']);
        $this->addUqc(['uqc' => 'CMS', 'uqc_name' => 'CENTIMETERS', 'uqc_disp_name' => 'CMS-CENTIMETERS']);
        $this->addUqc(['uqc' => 'CTN', 'uqc_name' => 'CARTONS', 'uqc_disp_name' => 'CTN-CARTONS']);
        $this->addUqc(['uqc' => 'DOZ', 'uqc_name' => 'DOZENS', 'uqc_disp_name' => 'DOZ-DOZENS']);
        $this->addUqc(['uqc' => 'DRM', 'uqc_name' => 'DRUMS', 'uqc_disp_name' => 'DRM-DRUMS']);
        $this->addUqc(['uqc' => 'GGK', 'uqc_name' => 'GREAT GROSS', 'uqc_disp_name' => 'GGK-GREAT GROSS']);
        $this->addUqc(['uqc' => 'GMS', 'uqc_name' => 'GRAMMES', 'uqc_disp_name' => 'GMS-GRAMMES ']);
        $this->addUqc(['uqc' => 'GRS', 'uqc_name' => 'GROSS', 'uqc_disp_name' => 'GRS-GROSS ']);
        $this->addUqc(['uqc' => 'GYD', 'uqc_name' => 'GROSS YARDS', 'uqc_disp_name' => 'GYD-GROSS YARDS ']);
        $this->addUqc(['uqc' => 'KGS', 'uqc_name' => 'KILOGRAMS', 'uqc_disp_name' => 'KGS-KILOGRAMS ']);
        $this->addUqc(['uqc' => 'KLR', 'uqc_name' => 'KILOLITRE', 'uqc_disp_name' => 'KLR-KILOLITRE']);
        $this->addUqc(['uqc' => 'KME', 'uqc_name' => 'KILOMETRE', 'uqc_disp_name' => 'KME-KILOMETRE']);
        $this->addUqc(['uqc' => 'MLT', 'uqc_name' => 'MILILITRE', 'uqc_disp_name' => 'MLT-MILILITRE']);
        $this->addUqc(['uqc' => 'MTR', 'uqc_name' => 'METERS', 'uqc_disp_name' => 'MTR-METERS']);
        $this->addUqc(['uqc' => 'MTS', 'uqc_name' => 'METRIC TON', 'uqc_disp_name' => 'MTS-METRIC TON']);
        $this->addUqc(['uqc' => 'NOS', 'uqc_name' => 'NUMBERS', 'uqc_disp_name' => 'NOS-NUMBERS']);
        $this->addUqc(['uqc' => 'PAC', 'uqc_name' => 'PACKS', 'uqc_disp_name' => 'PAC-PACKS']);
        $this->addUqc(['uqc' => 'PCS', 'uqc_name' => 'PIECES', 'uqc_disp_name' => 'PCS-PIECES']);
        $this->addUqc(['uqc' => 'PRS', 'uqc_name' => 'PAIRS', 'uqc_disp_name' => 'PRS-PAIRS']);
        $this->addUqc(['uqc' => 'QTL', 'uqc_name' => 'QUINTAL', 'uqc_disp_name' => 'QTL-QUINTAL']);
        $this->addUqc(['uqc' => 'ROL', 'uqc_name' => 'ROLLS', 'uqc_disp_name' => 'ROL-ROLLS']);
        $this->addUqc(['uqc' => 'SET', 'uqc_name' => 'SETS', 'uqc_disp_name' => 'SET-SETS']);
        $this->addUqc(['uqc' => 'SQF', 'uqc_name' => 'SQUARE FEET', 'uqc_disp_name' => 'SQF-SQUARE FEET']);
        $this->addUqc(['uqc' => 'SQM', 'uqc_name' => 'SQUARE METERS', 'uqc_disp_name' => 'SQM-SQUARE METERS']);
        $this->addUqc(['uqc' => 'SQY', 'uqc_name' => 'SQUARE YARDS', 'uqc_disp_name' => 'SQY-SQUARE YARDS']);
        $this->addUqc(['uqc' => 'TBS', 'uqc_name' => 'TABLETS', 'uqc_disp_name' => 'TBS-TABLETS']);
        $this->addUqc(['uqc' => 'TGM', 'uqc_name' => 'TEN GROSS', 'uqc_disp_name' => 'TGM-TEN GROSS']);
        $this->addUqc(['uqc' => 'THD', 'uqc_name' => 'THOUSANDS', 'uqc_disp_name' => 'THD-THOUSANDS']);
        $this->addUqc(['uqc' => 'TON', 'uqc_name' => 'TONNES', 'uqc_disp_name' => 'TON-TONNES']);
        $this->addUqc(['uqc' => 'TUB', 'uqc_name' => 'TUBES', 'uqc_disp_name' => 'TUB-TUBES']);
        $this->addUqc(['uqc' => 'UGS', 'uqc_name' => 'US GALLONS', 'uqc_disp_name' => 'UGS-US GALLONS']);
        $this->addUqc(['uqc' => 'UNT', 'uqc_name' => 'UNITS', 'uqc_disp_name' => 'UNT-UNITS']);
        $this->addUqc(['uqc' => 'YDS', 'uqc_name' => 'YARDS', 'uqc_disp_name' => 'YDS-YARDS']);
        $this->addUqc(['uqc' => 'OTH', 'uqc_name' => 'OTHERS', 'uqc_disp_name' => 'OTH-OTHERS']);

        echo 'UQC saved' . PHP_EOL;
    }
    private function addUqc($data)
    {
        $uqc = Uqc::firstOrNew(['uqc' => $data['uqc']]);
        $uqc->uqc = $data['uqc'];
        $uqc->uqc_name = $data['uqc_name'];
        $uqc->uqc_disp_name = $data['uqc_disp_name'];
        $uqc->save();
    }
}
