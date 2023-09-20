<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\Masters\Company;

class CompanyMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companymigrate
        {--force : Force the operation to run when in production.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the company databases migrations';

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
     * @return mixed
     */
    public function handle()
    {
        // dd($this->laravel->basePath());
        // dd($this->options());
        // return;
        $arguments = [];
        foreach ($this->arguments() as $index => $value) {
            $arguments['--' . $index] = $value;
        }
        foreach ($this->options() as $index => $value) {
            $arguments['--' . $index] = $value;
        }
        unset($arguments['--command']);
        // dd($arguments);
        // $exitCode = $this->call('migrate', $arguments + [
        //     '--database' => getCompDbConn(true),
        //     '--path' => 'database/company'
        // ]);
        $companies = Company::select('shared_comp_id')->distinct()->get();
        foreach ($companies as $comp) {
            $db = env('DB_DATABASE', 'bclbti_') . $comp->shared_comp_id . '_' . 'company';
            $this->call('migrate', $arguments + [
                '--database' => getCompanyDbConnFromDb($db),
                '--path' => 'database/company'
            ]);
        }
    }
}
