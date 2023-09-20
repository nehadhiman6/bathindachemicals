<?php

namespace App\Console\Commands;

use App\Models\Masters\Company;
use Illuminate\Console\Command;

class RollbackYearlyMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:rollbackyear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last year databases migration';

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
        $companies = Company::get();
        foreach ($companies as $comp) {
            $db = env('DB_DATABASE', 'prime_') . $comp->id . '_' . $comp->year;
            $this->call('migrate:rollback',  [
                '--database' => getYearlyDbConnFromDb($db),
                '--path' => 'database/yearly'
            ]);
        }

    }
}
