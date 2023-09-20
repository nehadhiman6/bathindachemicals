<?php

namespace App\Console\Commands;

use App\Models\Accounts\AccountGroup;
use Illuminate\Console\Command;

class AddAccountGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accountgroups:check';

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


    public function handle()
    {



        $this->addAccountGroup(['group_name' => 'Non-Current Assets','old_group_name' => 'Non-Current Assets', 'under' => 'Primary', 'nature' => 'Assets', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Financial Assets(NC)','old_group_name' => 'Financial Assets(NC)', 'under' => 'Primary', 'nature' => 'Assets', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Current Assets','old_group_name' => 'Current Assets', 'under' => 'Primary', 'nature' => 'Assets', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Financial Assets(C)','old_group_name' => 'Financial Assets(C)', 'under' => 'Primary', 'nature' => 'Assets', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Cash and Cash Equivalents','old_group_name' => 'Cash and Cash Equivalents', 'under' => 'Primary', 'nature' => 'Assets', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Other Current Assets','old_group_name' => 'Other Current Assets', 'under' => 'Primary', 'nature' => 'Assets', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Inter Units','old_group_name' => 'Inter Units', 'under' => 'Primary', 'nature' => 'Assets', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Equity & Liabilities','old_group_name' => 'Equity & Liabilities', 'under' => 'Primary', 'nature' => 'Liabilities', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Equity','old_group_name' => 'Equity', 'under' => 'Primary', 'nature' => 'Liabilities', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Liabilities','old_group_name' => 'Liabilities', 'under' => 'Primary', 'nature' => 'Liabilities', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Non-Current Liabilities','old_group_name' => 'Non-Current Liabilities', 'under' => 'Primary', 'nature' => 'Liabilities', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Current Liabilities','old_group_name' => 'Current Liabilities', 'under' => 'Primary', 'nature' => 'Liabilities', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Financial Liabilities (NCL)','old_group_name' => 'Financial Liabilities (NCL)', 'under' => 'Primary', 'nature' => 'Liabilities', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Financial Liabilities (CL)','old_group_name' => 'Financial Liabilities (CL)', 'under' => 'Primary', 'nature' => 'Liabilities', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Other Current Liabilities','old_group_name' => 'Other Current Liabilities', 'under' => 'Primary', 'nature' => 'Liabilities', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Borrowings (long term)','old_group_name' => 'Borrowings (long term)', 'under' => 'Primary', 'nature' => 'Liabilities', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Income','old_group_name' => 'Income', 'under' => 'Primary', 'nature' => 'Income', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Revenue From Operations','old_group_name' => 'Revenue From Operations', 'under' => 'Primary', 'nature' => 'Income', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Sale of Products/Services','old_group_name' => 'Sale of Products/Services', 'under' => 'Primary', 'nature' => 'Income', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Expenses','old_group_name' => 'Expenses', 'under' => 'Primary', 'nature' => 'Expenses', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Raw Material Cost','old_group_name' => 'Raw Material Cost', 'under' => 'Primary', 'nature' => 'Expenses', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Purchase Accounts','old_group_name' => 'Purchase Accounts', 'under' => 'Primary', 'nature' => 'Expenses', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Other Expenses','old_group_name' => 'Other Expenses', 'under' => 'Primary', 'nature' => 'Expenses', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Sale Of Products','old_group_name' => 'Sale Of Products', 'under' => 'Primary', 'nature' => 'Expenses', 'affect_gp' => 'N']);
        $this->addAccountGroup(['group_name' => 'Services And Purchase','old_group_name' => 'Services And Purchase', 'under' => 'Primary', 'nature' => 'Expenses', 'affect_gp' => 'N']);



        echo 'All Account Groups updated Successfully!' . PHP_EOL;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    private function addAccountGroup($data)
    {
        $account_group = AccountGroup::where('group_name', $data['group_name'])->first();
        if(!$account_group){
            $account_group = AccountGroup::where('group_name', $data['old_group_name'])->first();
        }
        if(!$account_group){
            $account_group = new AccountGroup();
        }
        $account_group->group_name = $data['group_name'];
        $account_group->under = $data['under'];
        $account_group->nature = $data['nature'];
        $account_group->affect_gp = $data['affect_gp'];
        $account_group->save();
    }
}
