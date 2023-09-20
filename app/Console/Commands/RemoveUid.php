<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Yearly\Uid;
use App\Models\Masters\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveUid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uid:check';

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
        $date = yesterday();
        $date = getDateFormat($date, 'ymd');
        $companies = Company::get();
        foreach ($companies as $comp) {
            $db = env('DB_DATABASE', 'bathindachemicals') . $comp->id . '_' . $comp->year;
            $yearly_db = getYearlyDbConnFromDb($db);
            Uid::on($yearly_db)->where( 'created_at', '<', $date)->delete();
        }
    }
}
