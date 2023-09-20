<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Auth\Role;
class AddRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:check';

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
        $this->addRole(['name' => 'Admin']);
        echo 'Roles updated!' . PHP_EOL;


    }


    private function addRole($role)
    {
        $r = Role::updateOrCreate(['name' => $role['name']]);
    }
}
