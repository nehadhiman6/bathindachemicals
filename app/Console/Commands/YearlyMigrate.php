<?php

namespace App\Console\Commands;

use App\Models\Masters\Company;
use Illuminate\Console\Command;

class YearlyMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yearmigrate
        {--force : Force the operation to run when in production.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the year databases migrations';

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
        $arguments = [];
        foreach ($this->arguments() as $index => $value) {
            $arguments['--' . $index] = $value;
        }
        foreach ($this->options() as $index => $value) {
            $arguments['--' . $index] = $value;
        }
        unset($arguments['--command']);
        $companies = Company::get();
        foreach ($companies as $comp) {
            $db = env('DB_DATABASE', 'bclbti_') . $comp->id . '_' . $comp->year;
            $this->call('migrate', $arguments + [
                '--database' => getYearlyDbConnFromDb($db),
                '--path' => 'database/yearly'
            ]);
        }
    }
}
