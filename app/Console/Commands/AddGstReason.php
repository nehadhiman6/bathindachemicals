<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Masters\GstReason;

class AddGstReason extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gstreason:check';

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
        $this->addGstReason(['reason_code' => '01', 'reason_name' => 'Sales Return', 'reason_disp_name' => '01-Sales Return']);
        $this->addGstReason(['reason_code' => '02', 'reason_name' => 'Post Sale Discount', 'reason_disp_name' => '02-Post sale discount']);
        $this->addGstReason(['reason_code' => '03', 'reason_name' => 'Deficiency in services', 'reason_disp_name' => '03-Deficiency in service']);
        $this->addGstReason(['reason_code' => '04', 'reason_name' => 'Correction in Invoice', 'reason_disp_name' => '04-Correction in invoice']);
        $this->addGstReason(['reason_code' => '05', 'reason_name' => 'Change in POS', 'reason_disp_name' => '05-Change in POS']);
        $this->addGstReason(['reason_code' => '06', 'reason_name' => 'Finalization of Provisional assessment', 'reason_disp_name' => '06-Finalization of Provisional assessment']);
        $this->addGstReason(['reason_code' => '07', 'reason_name' => 'Others', 'reason_disp_name' => '07-Others']);
    }

    private function addGstReason($data)
    {
        $reason = GstReason::firstOrNew(['reason_code' => $data['reason_code']]);
        $reason->reason_code = $data['reason_code'];
        $reason->reason_name = $data['reason_name'];
        $reason->reason_disp_name = $data['reason_disp_name'];
        $reason->save();
    }
}
