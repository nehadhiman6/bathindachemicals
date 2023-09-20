<?php

namespace App\Console\Commands;

use App\Models\Masters\Company;
use Illuminate\Console\Command;

class RollbackCompanyMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:rollbackcompany';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last company databases migration';

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
        $this->call('migrate:rollback', [
            '--database' => getCompDbConn(true),
            '--path' => 'database/company'
        ]);
    }
}
