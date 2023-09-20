<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeCompanyMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:companymigration {name : The name of the migration.}
        {--create= : The table to be created.}
        {--table= : The table to migrate.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file for company databases';

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
            if ($value && $index != 'name') {
                $arguments['--' . $index] = $value;
            }
        }

        foreach ($this->options() as $index => $value) {
            if ($value) {
                $arguments['--' . $index] = $value;
            }
        }
        unset($arguments['--command']);
        // dd($arguments);
        $exitCode = $this->call('make:migration', $arguments + [
            'name' => $this->argument('name'),
            '--path' => 'database/company'
        ]);
    }
}
