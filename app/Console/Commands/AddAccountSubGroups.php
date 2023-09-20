<?php

namespace App\Console\Commands;

use App\Models\Accounts\AccountGroup;
use App\Models\Accounts\AccountSubGroup;
use Illuminate\Console\Command;

class AddAccountSubGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accountsubgroups:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {


        $this->addAccountGroup(['sub_group_name' => 'Administative Expenses','old_sub_group_name' => 'Administative Expenses', 'parent_group' => 'Other Expenses','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Balance With Govt. Authorities','old_sub_group_name' => 'Balance With Govt. Authorities', 'parent_group' => 'Other Current Assets','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Bank Accounts','old_sub_group_name' => 'Bank Accounts', 'parent_group' => 'Financial Assets(C)','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Borrowings (Current)','old_sub_group_name' => 'Borrowings (Current)', 'parent_group' => 'Financial Liabilities (CL)','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Current Accounts','old_sub_group_name' => 'Current Accounts', 'parent_group' => 'Cash and Cash Equivalents','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Deposits','old_sub_group_name' => 'Deposits', 'parent_group' => 'Borrowings (long term)','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Depreciation','old_sub_group_name' => 'Depreciation', 'parent_group' => 'Expenses','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Direct Expenses','old_sub_group_name' => 'Direct Expenses', 'parent_group' => 'Purchase Accounts','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Employee Benefit Expenses','old_sub_group_name' => 'Employee Benefit Expenses', 'parent_group' => 'Expenses','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Equity Share capital','old_sub_group_name' => 'Equity Share capital', 'parent_group' => 'Equity','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Finance Costs','old_sub_group_name' => 'Finance Costs', 'parent_group' => 'Expenses','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Fixed Deposits','old_sub_group_name' => 'Fixed Deposits', 'parent_group' => 'Cash and Cash Equivalents','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'General Expenses','old_sub_group_name' => 'General Expenses', 'parent_group' => 'Other Expenses','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Intangible Assets','old_sub_group_name' => 'Intangible Assets', 'parent_group' => 'Non-Current Assets','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Inter Units','old_sub_group_name' => 'Inter Units', 'parent_group' => 'Inter Units','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Inventories','old_sub_group_name' => 'Inventories', 'parent_group' => 'Current Assets','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Investment Property','old_sub_group_name' => 'Investment Property', 'parent_group' => 'Non-Current Assets','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Investments (Current)','old_sub_group_name' => 'Investments (Current)', 'parent_group' => 'Financial Assets(C)','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Loans & Advances','old_sub_group_name' => 'Loans & Advances', 'parent_group' => 'Financial Assets(C)','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Manufacturing Expenses','old_sub_group_name' => 'Manufacturing Expenses', 'parent_group' => 'Other Expenses','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Non-Current Investments','old_sub_group_name' => 'Non-Current Investments', 'parent_group' => 'Financial Assets(NC)','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Other Equity','old_sub_group_name' => 'Other Equity', 'parent_group' => 'Equity','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Other financial Liabilities (Current)','old_sub_group_name' => 'Other financial Liabilities (Current)', 'parent_group' => 'Financial Liabilities (CL)','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Other financial Liabilities (Non Current)','old_sub_group_name' => 'Other financial Liabilities (Non Current)', 'parent_group' => 'Financial Liabilities (NCL)','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Other Income','old_sub_group_name' => 'Other Income', 'parent_group' => 'Income','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Other Non current Assets','old_sub_group_name' => 'Non-Current Assets', 'parent_group' => 'Non-Current Assets','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Other Operating Revenue','old_sub_group_name' => 'Other Operating Revenue', 'parent_group' => 'Revenue From Operations','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Property, Plant & Equipment','old_sub_group_name' => 'Property, Plant & Equipment', 'parent_group' => 'Non-Current Assets','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Provisions (CL)','old_sub_group_name' => 'Provisions (CL)', 'parent_group' => 'Current Liabilities','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Provisions (NCL)','old_sub_group_name' => 'Provisions (NCL)', 'parent_group' => 'Non-Current Liabilities','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Selling Expenses','old_sub_group_name' => 'Selling Expenses', 'parent_group' => 'Other Expenses','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Statutory Payables/Dues','old_sub_group_name' => 'Statutory Payables/Dues', 'parent_group' => 'Other Current Liabilities','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Stock Transfers (Purchase)','old_sub_group_name' => 'Stock Transfers (Purchase)', 'parent_group' => 'Purchase Accounts','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Stock Transfers (Sales)','old_sub_group_name' => 'Stock Transfers (Sales)', 'parent_group' => 'Sale of Products/Services','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Term Loans-From Banks','old_sub_group_name' => 'Term Loans-From Banks', 'parent_group' => 'Borrowings (long term)','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Term Loans-From Others','old_sub_group_name' => 'Term Loans-From Others', 'parent_group' => 'Borrowings (long term)','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Trade Payables','old_sub_group_name' => 'Trade Payables', 'parent_group' => 'Financial Liabilities (CL)','party_applicable'=>'Y']);
        $this->addAccountGroup(['sub_group_name' => 'Trade Receivables','old_sub_group_name' => 'Trade Receivables', 'parent_group' => 'Financial Assets(C)','party_applicable'=>'Y']);
        $this->addAccountGroup(['sub_group_name' => 'Sale Of Products','old_sub_group_name' => 'Sale Of Products', 'parent_group' => 'Sale Of Products','party_applicable'=>'N']);
        $this->addAccountGroup(['sub_group_name' => 'Services And Purchase','old_sub_group_name' => 'Services And Purchase', 'parent_group' => 'Services And Purchase','party_applicable'=>'N']);
        echo 'All Account Sub Groups updated Successfully!' . PHP_EOL;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    private function addAccountGroup($data)
    {
        $account_sub_group = AccountSubGroup::where('sub_group_name', $data['sub_group_name'])->first();
        if(!$account_sub_group){
            $account_sub_group = AccountSubGroup::where('sub_group_name', $data['old_sub_group_name'])->first();
        }
        if(!$account_sub_group){
            $account_sub_group = new AccountSubGroup();
        }
        $parent_group = null;
        if($data['parent_group'] != ''){
            $parent_group = AccountGroup::where('group_name', $data['parent_group'])->first();
            if(!$parent_group ){
                echo 'Please Add Account Groups first for Group : '.$data['sub_group_name'] .'!' . PHP_EOL;
                return;
            }
            $account_sub_group->ac_group_id = $parent_group->id;
        }
        $account_sub_group->sub_group_name = $data['sub_group_name'];
        $account_sub_group->party_applicable = $data['party_applicable'];
        $account_sub_group->save();
    }
}
