<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use  App\Models\Auth\UserRole;
use  App\Models\Auth\User;
use  App\Models\Auth\PermissionGroup;
use  App\Models\Auth\Permission;
use App\Models\Auth\Role;

class AddPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:check';

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
        $this->addPermission(['name' => 'roles-permissions', 'label' => 'Allow users to add roles and their permissions', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'manage-users', 'label' => 'Allow one to add/update user', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'users', 'label' => 'Allow to see all users', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'modify-user-password', 'label' => 'Allow one to update user password', 'opt_group' => 'Users & Rights']);

        $this->addPermission(['name' => 'roles', 'label' => 'Allow to see all roles', 'opt_group' => 'Users & Rights']);
        $this->addPermission(['name' => 'role-modify', 'label' => 'Allow to add/update role', 'opt_group' => 'Users & Rights']);

        $this->addPermission(['name' => 'cities', 'label' => 'Allow to see all cities', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'cities-add', 'label' => 'Allow to to add cities', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'cities-modify', 'label' => 'Allow to update cities', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'districts', 'label' => 'Allow to see all districts', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'districts-add', 'label' => 'Allow to add districts', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'districts-modify', 'label' => 'Allow to update districts', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'states', 'label' => 'Allow to see all states', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'states-add', 'label' => 'Allow to add states', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'states-modify', 'label' => 'Allow to update states', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'countries', 'label' => 'Allow to see all countries', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'countries-add', 'label' => 'Allow to add countries', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'countries-modify', 'label' => 'Allow to update countries', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'companies', 'label' => 'Allow to see all companies', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'companies-add', 'label' => 'Allow to add companies', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'companies-modify', 'label' => 'Allow to update companies', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'branches', 'label' => 'Allow to see all branches', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'branches-add', 'label' => 'Allow to add branches', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'branches-modify', 'label' => 'Allow to update branches', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'skip-branches', 'label' => 'Allow to branches to skip header line one', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'banks', 'label' => 'Allow to see all banks', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'banks-add', 'label' => 'Allow to add banks', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'banks-modify', 'label' => 'Allow to update banks', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'ifscs', 'label' => 'Allow to see all ifscs', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'ifscs-add', 'label' => 'Allow to add ifscs', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'ifscs-modify', 'label' => 'Allow to update ifscs', 'opt_group' => 'Masters']);


        $this->addPermission(['name' => 'oil-packings', 'label' => 'Allow to see all Oil packings', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'oil-packings-add', 'label' => 'Allow to  add Oil packings', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'oil-packings-modify', 'label' => 'Allow to update Oil packings', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'loose-packings', 'label' => 'Allow to see all Loose packings', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'loose-packings-add', 'label' => 'Allow to add Loose packings', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'loose-packings-modify', 'label' => 'Allow to update Loose packings', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'liquor-packings', 'label' => 'Allow to see all Liquor packings', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'liquor-packings-add', 'label' => 'Allow to add Liquor packings', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'liquor-packings-modify', 'label' => 'Allow to update Liquor packings', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'parameters', 'label' => 'Allow to see all parameters', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'parameters-modify', 'label' => 'Allow to add/update parameters', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'party-categories', 'label' => 'Allow to see all party-categories', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'party-categories-add', 'label' => 'Allow to add party-categories', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'party-categories-modify', 'label' => 'Allow to update party-categories', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'brands', 'label' => 'Allow to see all brands', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'brands-add', 'label' => 'Allow to see add brands', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'brands-modify', 'label' => 'Allow to update brands', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'brand-categories', 'label' => 'Allow to view the list of brands across all categories and the list of categories for all brands', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'brand-categories-modify', 'label' => 'Allow to add/update brands categories', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'branch-bank-accounts', 'label' => 'Allow to view the list of all bank accounts', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'branch-bank-accounts-add', 'label' => 'Allow to add bank accounts', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'branch-bank-accounts-modify', 'label' => 'Allow to update bank accounts', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'branch-prefixes', 'label' => 'Allow to see all branch-prefixes', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'branch-prefixes-add', 'label' => 'Allow to add branch-prefixes', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'branch-prefixes-modify', 'label' => 'Allow to update branch-prefixes', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'item-freights', 'label' => 'Allow to see all item freights', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'item-freights-add', 'label' => 'Allow to add item freights', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'item-freights-modify', 'label' => 'Allow to update item freights', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'item-freights-view', 'label' => 'Allow to see item freights', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'fix-rates', 'label' => 'Allow to see all fix rates', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'fix-rates-add', 'label' => 'Allow to add fix rates', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'fix-rates-modify', 'label' => 'Allow to update fix rates', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'fix-rates-view', 'label' => 'Allow to see fix rates', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'term-conditions', 'label' => 'Allow to see all terms and conditions list', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'term-conditions-add', 'label' => 'Allow to add term conditions', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'term-conditions-modify', 'label' => 'Allow to update term conditions', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'tds-section', 'label' => 'Allow to see all tds section', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'tds-section-add', 'label' => 'Allow to add tds section', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'tds-section-modify', 'label' => 'Allow to update tds section', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'bags', 'label' => 'Allow to see all bags', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'bags-add', 'label' => 'Allow to add bags', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'bags-modify', 'label' => 'Allow to update bags', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'kanda', 'label' => 'Allow to see all Kanda', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'kanda-add', 'label' => 'Allow to add Kanda', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'kanda-modify', 'label' => 'Allow to update Kanda', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'cheque-setting', 'label' => 'Allow to see all Cheque Setting', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'cheque-setting-add', 'label' => 'Allow to add Cheque Setting', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'cheque-setting-modify', 'label' => 'Allow to update Cheque Setting', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'accounts', 'label' => 'Allow to see all accounts', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'accounts-add', 'label' => 'Allow to add accounts', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'accounts-modify', 'label' => 'Allow to update accounts', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'accounts-attachment', 'label' => 'Allow to add account attachments', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'accounts-view', 'label' => 'Allow to see account view', 'opt_group' => 'Accounts']);

        $this->addPermission(['name' => 'other-accounts', 'label' => 'Allow to see all other-accounts', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'other-accounts-add', 'label' => 'Allow to add other-accounts', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'other-accounts-modify', 'label' => 'Allow to update other-accounts', 'opt_group' => 'Accounts']);

        $this->addPermission(['name' => 'accounts-opening', 'label' => 'Allow to add Opening in accounts', 'opt_group' => 'Accounts']);

        $this->addPermission(['name' => 'lower-tds-setting', 'label' => 'Allow to see all lower tds setting(accounts)', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'lower-tds-setting-add', 'label' => 'Allow to add lower tds setting(accounts)', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'lower-tds-setting-modify', 'label' => 'Allow to update lower tds setting(accounts)', 'opt_group' => 'Accounts']);

        $this->addPermission(['name' => 'party-sub-groups', 'label' => 'Allow to see all party sub groups', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'party-sub-groups-add', 'label' => 'Allow to add party sub groups', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'party-sub-groups-modify', 'label' => 'Allow to update party sub groups', 'opt_group' => 'Accounts']);

        $this->addPermission(['name' => 'gl-sub-groups', 'label' => 'Allow to see all GL sub groups', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'gl-sub-groups-add', 'label' => 'Allow to add GL sub groups', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'gl-sub-groups-modify', 'label' => 'Allow to update GL sub groups', 'opt_group' => 'Accounts']);

        $this->addPermission(['name' => 'limit-sub-groups', 'label' => 'Allow to see all Limit sub groups', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'limit-sub-groups-add', 'label' => 'Allow to add Limit sub groups', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'limit-sub-groups-modify', 'label' => 'Allow to update Limit sub groups', 'opt_group' => 'Accounts']);

        $this->addPermission(['name' => 'clients', 'label' => 'Allow to see all clients', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'clients-add', 'label' => 'Allow to add clients', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'clients-modify', 'label' => 'Allow to update clients', 'opt_group' => 'Accounts']);

        $this->addPermission(['name' => 'vendors', 'label' => 'Allow to see all vendors', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'vendors-add', 'label' => 'Allow to add vendors', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'vendors-modify', 'label' => 'Allow to update vendors', 'opt_group' => 'Accounts']);

        $this->addPermission(['name' => 'pay-terms', 'label' => 'Allow to see all pay-terms', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'pay-terms-add', 'label' => 'Allow to add pay-terms', 'opt_group' => 'Accounts']);
        $this->addPermission(['name' => 'pay-terms-modify', 'label' => 'Allow to update pay-terms', 'opt_group' => 'Accounts']);

        $this->addPermission(['name' => 'items', 'label' => 'Allow to see all items', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'items-add', 'label' => 'Allow to add items', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'items-modify', 'label' => 'Allow to update items', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'items-view', 'label' => 'Allow to only items view', 'opt_group' => 'Items']);

        $this->addPermission(['name' => 'main-groups', 'label' => 'Allow to see all main-groups', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'main-groups-add', 'label' => 'Allow to add main-groups', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'main-groups-modify', 'label' => 'Allow to update main-groups', 'opt_group' => 'Items']);

        $this->addPermission(['name' => 'sub-groups', 'label' => 'Allow to see all sub groups', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'sub-groups-add', 'label' => 'Allow to add sub groups', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'sub-groups-modify', 'label' => 'Allow to update sub groups', 'opt_group' => 'Items']);

        $this->addPermission(['name' => 'other-sub-groups', 'label' => 'Allow to see all other sub groups', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'other-sub-groups-add', 'label' => 'Allow to add other sub groups', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'other-sub-groups-modify', 'label' => 'Allow to update other sub groups', 'opt_group' => 'Items']);

        $this->addPermission(['name' => 'item-units', 'label' => 'Allow to see all item units', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'item-units-add', 'label' => 'Allow to add item units', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'item-units-modify', 'label' => 'Allow to update item units', 'opt_group' => 'Items']);

        $this->addPermission(['name' => 'stores', 'label' => 'Allow to see all stores', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'stores-add', 'label' => 'Allow to add stores', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'stores-modify', 'label' => 'Allow to update stores', 'opt_group' => 'Items']);

        $this->addPermission(['name' => 'gsts', 'label' => 'Allow to see all gsts', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'gsts-add', 'label' => 'Allow to add gsts', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'gsts-modify', 'label' => 'Allow to update gsts', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'gst-change-rates', 'label' => 'Allow to change GST rates', 'opt_group' => 'Items']);

        $this->addPermission(['name' => 'vat-cst', 'label' => 'Allow to see all VAT CST', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'vat-cst-add', 'label' => 'Allow to add VAT CST', 'opt_group' => 'Items']);
        $this->addPermission(['name' => 'vat-cst-modify', 'label' => 'Allow to update VAT CST', 'opt_group' => 'Items']);

        $this->addPermission(['name' => 'rate-differences', 'label' => 'Allow to see all rate differences', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'rate-differences-add', 'label' => 'Allow to add rate-differences', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'rate-differences-modify', 'label' => 'Allow to update rate-differences', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'rate-differences-view', 'label' => 'Allow to only view rate-differences', 'opt_group' => 'Masters']);

        $this->addPermission(['name' => 'packing-formulas', 'label' => 'Allow to see all packing formulas', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'packing-formulas-add', 'label' => 'Allow to add packing formulas', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'packing-formulas-modify', 'label' => 'Allow to update packing formulas', 'opt_group' => 'Masters']);
        $this->addPermission(['name' => 'packing-formulas-view', 'label' => 'Allow to only view packing formulas', 'opt_group' => 'Masters']);


        $this->addPermission(['name' => 'sale-contracts', 'label' => 'Allow to see all Sale Contract', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-contracts-add', 'label' => 'Allow to add Sale Contract', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-contracts-modify', 'label' => 'Allow to update Sale Contract', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-contracts-attachment', 'label' => 'Allow to update Sale Contract', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-contracts-view', 'label' => 'Allow to only Sale Contract view', 'opt_group' => 'Sales']);

        $this->addPermission(['name' => 'sale-orders', 'label' => 'Allow to see all Sale Orders', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-orders-add', 'label' => 'Allow to add Sale Orders', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-orders-modify', 'label' => 'Allow to update Sale Orders', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-orders-view', 'label' => 'Allow to only Sale Orders view', 'opt_group' => 'Sales']);

        $this->addPermission(['name' => 'sale-order-dispatches', 'label' => 'Allow to see all Sale Order Dispatch', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-order-dispatches-add', 'label' => 'Allow to add Sale Orders Dispatch', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-order-dispatches-modify', 'label' => 'Allow to update Sale Orders Dispatch', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-order-dispatch-print', 'label' => 'Allow to print Sale Orders Dispatch', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-order-dispatches-view', 'label' => 'Allow to only Sale Orders Dispatch view', 'opt_group' => 'Sales']);

        $this->addPermission(['name' => 'sale-invoices', 'label' => 'Allow to see all Sale invoice', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-invoices-add', 'label' => 'Allow to add Sale invoice', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-invoices-modify', 'label' => 'Allow to update Sale invoice', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-invoices-print', 'label' => 'Allow to update Sale invoice Print', 'opt_group' => 'Sales']);
        $this->addPermission(['name' => 'sale-invoices-view', 'label' => 'Allow to only Sale invoice view', 'opt_group' => 'Sales']);

        $this->addPermission(['name' => 'receipt-vouchers', 'label' => 'Allow to see all receipt vouchers', 'opt_group' => 'Vouchers']);
        $this->addPermission(['name' => 'receipt-vouchers-add', 'label' => 'Allow to add receipt vouchers', 'opt_group' => 'Vouchers']);
        $this->addPermission(['name' => 'receipt-vouchers-modify', 'label' => 'Allow to update receipt vouchers', 'opt_group' => 'Vouchers']);

        $this->addPermission(['name' => 'payment-vouchers', 'label' => 'Allow to see all payment vouchers', 'opt_group' => 'Vouchers']);
        $this->addPermission(['name' => 'payment-vouchers-add', 'label' => 'Allow to add payment vouchers', 'opt_group' => 'Vouchers']);
        $this->addPermission(['name' => 'payment-vouchers-modify', 'label' => 'Allow to update payment vouchers', 'opt_group' => 'Vouchers']);

        $this->addPermission(['name' => 'journal-vouchers', 'label' => 'Allow to see all journal vouchers', 'opt_group' => 'Vouchers']);
        $this->addPermission(['name' => 'journal-vouchers-add', 'label' => 'Allow to add journal vouchers', 'opt_group' => 'Vouchers']);
        $this->addPermission(['name' => 'journal-vouchers-modify', 'label' => 'Allow to update journal vouchers', 'opt_group' => 'Vouchers']);


        $this->addPermission(['name' => 'interest-vouchers', 'label' => 'Allow to see all interest vouchers', 'opt_group' => 'Vouchers']);
        $this->addPermission(['name' => 'interest-vouchers-add', 'label' => 'Allow to add interest vouchers', 'opt_group' => 'Vouchers']);
        $this->addPermission(['name' => 'interest-vouchers-modify', 'label' => 'Allow to update interest vouchers', 'opt_group' => 'Vouchers']);

        $this->addPermission(['name' => 'transfer-vouchers', 'label' => 'Allow to see all transfer vouchers', 'opt_group' => 'Vouchers']);
        $this->addPermission(['name' => 'transfer-vouchers-add', 'label' => 'Allow to add transfer vouchers', 'opt_group' => 'Vouchers']);
        $this->addPermission(['name' => 'transfer-vouchers-modify', 'label' => 'Allow to update transfer vouchers', 'opt_group' => 'Vouchers']);
        $this->addPermission(['name' => 'transfer-vouchers-print', 'label' => 'Allow to transfer vouchers Print', 'opt_group' => 'Vouchers']);
        $this->addPermission(['name' => 'vouchers-bill-detail', 'label' => 'Allow to vouchers bill Mismatch Refrence detail ', 'opt_group' => 'Vouchers']);

        $this->addPermission(['name' => 'debit-note', 'label' => 'Allow to see all Debit Note', 'opt_group' => 'Gst Documents']);
        $this->addPermission(['name' => 'debit-note-add', 'label' => 'Allow to add Debit Note', 'opt_group' => 'Gst Documents']);
        $this->addPermission(['name' => 'debit-note-modify', 'label' => 'Allow to update Debit Note', 'opt_group' => 'Gst Documents']);
        $this->addPermission(['name' => 'debit-note-print', 'label' => 'Allow to Debit Note Print', 'opt_group' => 'Gst Documents']);

        $this->addPermission(['name' => 'credit-note', 'label' => 'Allow to see all Credit Note', 'opt_group' => 'Gst Documents']);
        $this->addPermission(['name' => 'credit-note-add', 'label' => 'Allow to add Credit Note', 'opt_group' => 'Gst Documents']);
        $this->addPermission(['name' => 'credit-note-modify', 'label' => 'Allow to update Credit Note', 'opt_group' => 'Gst Documents']);
        $this->addPermission(['name' => 'credit-note-print', 'label' => 'Allow to Credit Note Print', 'opt_group' => 'Gst Documents']);

        $this->addPermission(['name' => 'issue', 'label' => 'Allow to see all issue Transfer', 'opt_group' => 'Transfers']);
        $this->addPermission(['name' => 'issue-add', 'label' => 'Allow to add issue Transfer', 'opt_group' => 'Transfers']);
        $this->addPermission(['name' => 'issue-modify', 'label' => 'Allow to update issue Transfer', 'opt_group' => 'Transfers']);
        $this->addPermission(['name' => 'issue-print', 'label' => 'Allow to issue print', 'opt_group' => 'Transfers']);

        $this->addPermission(['name' => 'receipt', 'label' => 'Allow to see all receipt Transfer', 'opt_group' => 'Transfers']);
        $this->addPermission(['name' => 'receipt-add', 'label' => 'Allow to add receipt Transfer', 'opt_group' => 'Transfers']);
        $this->addPermission(['name' => 'receipt-modify', 'label' => 'Allow to update receipt Transfer', 'opt_group' => 'Transfers']);
        $this->addPermission(['name' => 'receipt-print', 'label' => 'Allow to receipt Transfer Print', 'opt_group' => 'Transfers']);

        $this->addPermission(['name' => 'purchase-order', 'label' => 'Allow to see all purchase order', 'opt_group' => 'Purchases']);
        $this->addPermission(['name' => 'purchase-order-add', 'label' => 'Allow to add purchase order', 'opt_group' => 'Purchases']);
        $this->addPermission(['name' => 'purchase-order-modify', 'label' => 'Allow to update purchase order', 'opt_group' => 'Purchases']);
        $this->addPermission(['name' => 'qty-extend-order', 'label' => 'Allow to qty extended in purchase order', 'opt_group' => 'Purchases']);
        $this->addPermission(['name' => 'del-extend-order', 'label' => 'Allow to delivery extended Date in purchase order', 'opt_group' => 'Purchases']);


        $this->addPermission(['name' => 'sale-report', 'label' => 'Grant permission to access Sale report', 'opt_group' => 'Reports']);
        $this->addPermission(['name' => 'ledger-report', 'label' => 'Grant permission to access Ledger report', 'opt_group' => 'Reports']);
        $this->addPermission(['name' => 'contract-booking-report', 'label' => 'Grant permission to access Contract Booking Report', 'opt_group' => 'Reports']);
        $this->addPermission(['name' => 'contract-execution-report', 'label' => 'Grant permission to access Contract Execution Report', 'opt_group' => 'Reports']);
        $this->addPermission(['name' => 'pending-contract-report', 'label' => 'Grant permission to access Pending Contract Report', 'opt_group' => 'Reports']);
        $this->addPermission(['name' => 'sale-format-report', 'label' => 'Grant permission to access Sale Format Report', 'opt_group' => 'Reports']);
        $this->addPermission(['name' => 'trial-balance-report', 'label' => 'Grant permission to access Trial Balance Report', 'opt_group' => 'Reports']);


        $this->addRole(['name' => 'Admin']);
        $this->addUser(['name' => 'Infoway', 'email' => 'infowayit@infowayindia.com', 'email_verified_at' => today(), 'password' => bcrypt(123456)]);
        echo 'Permissions updated!' . PHP_EOL;
    }



    private function addPermission($permission)
    {
        $opt_group_id = 0;
        if (isset($permission['opt_group'])) {
            $g = PermissionGroup::updateOrCreate(['opt_group' => $permission['opt_group']]);
            $opt_group_id = $g->id;
        }

        $p = Permission::updateOrCreate(['name' => $permission['name']], $permission + ['opt_group_id' => $opt_group_id]);
    }

    private function addUser($user_data)
    {
        $user = User::where('email', $user_data['email'])->first();
        if (!$user) {
            $user = User::create($user_data);
        }
        $role = Role::where('name', 'Admin')->first();
        $user_role =  UserRole::updateOrCreate(['user_id' => $user['id'], 'role_id' => $role['id']]);
    }


    private function addRole($role)
    {
        $user = Role::updateOrCreate(['name' => $role['name']], $role);
    }
}
