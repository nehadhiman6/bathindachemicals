<?php

use Inertia\Inertia;
use App\Models\Masters\TdsSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Models\Masters\BranchBankAccount;
use App\Http\Controllers\Items\GstController;
use App\Http\Controllers\Items\ItemController;
use App\Http\Controllers\Users\RoleController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Items\StoreController;
use App\Http\Controllers\Yearly\BillController;
use App\Http\Controllers\Items\VatCstController;
use App\Http\Controllers\Masters\BankController;
use App\Http\Controllers\Masters\CityController;
use App\Http\Controllers\Other\SelectController;
use App\Http\Controllers\Masters\BrandController;
use App\Http\Controllers\Masters\IfscsController;
use App\Http\Controllers\Masters\StateController;
use App\Http\Controllers\Items\ItemUnitController;
use App\Http\Controllers\Masters\BranchController;
use App\Http\Controllers\Yearly\VoucherController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\PayTermController;
use App\Http\Controllers\Company\FixRateController;
use App\Http\Controllers\Items\ItemGroupController;
use App\Http\Controllers\Masters\CompanyController;
use App\Http\Controllers\Masters\CountryController;
use App\Http\Controllers\Masters\PackingController;
use App\Http\Controllers\Masters\CategoryController;
use App\Http\Controllers\Masters\DistrictController;
use App\Http\Controllers\Users\UserBranchController;
use App\Http\Controllers\Yearly\ParameterController;
use App\Http\Controllers\Yearly\SaleOrderController;
use App\Http\Controllers\GstDocs\DebitNoteController;
use App\Http\Controllers\Users\UserCompanyController;
use App\Http\Controllers\Account\TypeMasterController;
use App\Http\Controllers\GstDocs\CreditNoteController;
use App\Http\Controllers\Masters\TdsSectionController;
use App\Http\Controllers\Yearly\SaleInvoiceController;
use App\Http\Controllers\Company\ItemFreightController;
use App\Http\Controllers\Company\SaleContractController;
use App\Http\Controllers\Masters\BranchPrefixController;
use App\Http\Controllers\Masters\BrandCategoryController;
use App\Http\Controllers\Masters\TermConditionController;
use App\Http\Controllers\Yearly\AccountOpeningController;
use App\Http\Controllers\Masters\PackingFormulaController;
use App\Http\Controllers\Masters\RateDifferenceController;
use App\Http\Controllers\Report\Sale\SaleReportController;
use App\Http\Controllers\Users\ReportUserBranchController;
use App\Http\Controllers\Account\LowerTdsSettingController;
use App\Http\Controllers\Resources\SharedResourceController;
use App\Http\Controllers\Yearly\SaleOrderDispatchController;
use App\Http\Controllers\Masters\BranchBankAccountController;
use App\Http\Controllers\MultiPurpose\MultiPurposeController;
use App\Http\Controllers\Resource\AttachmentSharedController;
use App\Http\Controllers\Resources\CompanyResourceController;
use App\Http\Controllers\Attachment\SharedAttachmentController;
use App\Http\Controllers\Attachment\YearlyAttachmentController;
use App\Http\Controllers\Attachment\CompanyAttachmentController;
use App\Http\Controllers\Company\PurchaseOrderController;
use App\Http\Controllers\Masters\BagController;
use App\Http\Controllers\Masters\ChequeSettingController;
use App\Http\Controllers\Masters\KandaController;
use App\Http\Controllers\Report\Sale\SaleFormatReportController;
use App\Http\Controllers\Report\Accounting\LedgerReportController;
use App\Http\Controllers\Report\Accounting\TrailBalanceReportController;
use App\Http\Controllers\Report\Sale\ContractBookingReportController;
use App\Http\Controllers\Report\Sale\PendingContractReportController;
use App\Http\Controllers\Report\Sale\ContractExecutionReportController;
use App\Http\Controllers\Yearly\TransferController;
use App\Models\Yearly\Transfer;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    if (Auth::check()) {
        return Inertia::render('Dashboard',[
            'csrf' =>csrf_token()
        ]);
    } else {
        return Inertia::render('Auth/Login');
    }
})->middleware(['auth']);


Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard',[
            'csrf' =>csrf_token()
        ]);
    })->name('dashboard');
});

Route::get('countries-list', [CountryController::class, 'countriesList']);
Route::get('states-list', [StateController::class, 'statesList']);
Route::get('cities-list', [CityController::class, 'citiesList']);
Route::get('districts-list', [DistrictController::class, 'districtsList']);
Route::get('companies-list', [CompanyController::class, 'companiesList']);
Route::get('branches-list', [BranchController::class, 'branchesList']);
Route::get('banks-list', [BankController::class, 'banksList']);
Route::get('ifscs-list', [IfscsController::class, 'ifscsList']);
Route::get('pay-terms-list', [PayTermController::class, 'payTermsList']);
Route::get('type-masters-list', [TypeMasterController::class, 'typeMastersList']);
Route::get('accounts-list', [AccountController::class, 'accountsList']);
Route::get('stores-list', [StoreController::class, 'storesList']);
Route::post('bill-reference-keys/{acid}/filtered', [BillController::class, 'filteredBillAccountKeys']);
Route::get('other-accounts', [AccountController::class, 'getOtherAccount']);
Route::get('item-units-list', [ItemUnitController::class, 'itemUnitsList']);
Route::get('item-groups-list', [ItemGroupController::class, 'itemGroupList']);
Route::get('items-list', [ItemController::class, 'itemsList']);
Route::get('gsts-list', [GstController::class, 'gstsList']);
Route::post('gsts/{id}/rate', [GstController::class, 'changeGstRate']);
Route::get('vat-cst-list', [VatCstController::class, 'vatCstList']);
Route::get('sale-contracts-list', [SaleContractController::class, 'saleContractList']);
Route::get('packings-list', [PackingController::class, 'packingsList']);
Route::get('brands-list', [BrandController::class, 'brandsList']);
Route::get('party-categories-list', [CategoryController::class, 'partyCategoryList']);
Route::get('brands-categories-list', [BrandCategoryController::class, 'brandsCategoryList']);
Route::get('packing-formula-list', [PackingFormulaController::class, 'packingFormulaList']);
Route::get('rate-difference-list', [RateDifferenceController::class, 'rateDifferenceList']);
Route::get('sale-orders-list', [SaleOrderController::class, 'saleOrderList']);
Route::get('item-freights-list', [ItemFreightController::class, 'itemFreightsList']);
Route::get('sale-order-dispatches-list', [SaleOrderDispatchController::class, 'saleOrderDispatchesList']);
Route::get('fix-rates-list', [FixRateController::class, 'fixRatesList']);

Route::get('vouchers-list', [VoucherController::class, 'voucherList']);
Route::get('term-conditions-list', [TermConditionController::class, 'termConditionsList']);
Route::get('invoices-list', [SaleInvoiceController::class, 'saleInvoiceList']);
Route::get('branch-bank-accounts-list', [BranchBankAccountController::class, 'branchBankAccountsList']);
Route::get('tds-section-list', [TdsSectionController::class, 'tdsSectionList']);
Route::get('bags-list', [BagController::class, 'bagsList']);
Route::get('kanda-list', [KandaController::class, 'KandaList']);
Route::get('cheque-setting-list', [ChequeSettingController::class, 'chequeSettingList']);
Route::get('purchase-order-list', [PurchaseOrderController::class, 'purchaseOrderList']);

Route::get('branches-skip', [BranchController::class, 'addSkipBranches']);
Route::get('get-branches/{id}', [BranchController::class, 'getSkipBranches']);

Route::post('bill-detail-add', [VoucherController::class, 'billDetailsAdd']);
Route::get('vouchers-detail-list', [VoucherController::class, 'voucherDetailList']);
Route::get('vouchers-details', [VoucherController::class, 'voucherDetailIndex']);

Route::get('lower-tds-setting-list', [LowerTdsSettingController::class, 'lowerTdsSettingList']);

Route::get('get-bill-no', [DebitNoteController::class, 'getBillNo']);
Route::get('debit-note-print/{id}', [DebitNoteController::class, 'getDebitNotePrint']);
Route::get('debit-note-list', [DebitNoteController::class, 'debitNotList']);
Route::get('credit-note-print/{id}', [DebitNoteController::class, 'getDebitNotePrint']);
Route::get('credit-note-list', [CreditNoteController::class, 'creditNotList']);

Route::get('get-slip-weight/{slip_no}', [TransferController::class, 'getSlipWt']);
Route::get('issue-print/{id}', [TransferController::class, 'getTransferPrint']);
Route::get('issue-list/{type}', [TransferController::class, 'transferList']);
Route::get('receipt-print/{id}', [TransferController::class, 'getTransferPrint']);
Route::get('receipt-list/{type}', [TransferController::class, 'transferList']);
Route::get('posting-detail/{vcode}', [LedgerReportController::class, 'postingDetails']);



Route::post('current-branch',[MultiPurposeController::class,'setCurrentSessionBranch']);
Route::get('current-branch',[MultiPurposeController::class,'getCurrentSessionBranch'])->name('current-branch');
Route::get('party-category-packing/{acid}',[MultiPurposeController::class,'getPartyCategoryPacking']);
Route::post('sale-orders-detail',[MultiPurposeController::class,'getSaleOrderDetail']);
Route::post('sale-order-packs-rate',[MultiPurposeController::class,'getSaleOrderPacksRate']);
Route::post('sale-order-foc-packings',[MultiPurposeController::class,'getSaleOrderFocPacking']);
Route::get('sale-order-client/{acid}',[MultiPurposeController::class,'getSaleOrderClient']);
Route::get('get-amount',[MultiPurposeController::class,'getCashInHand']);
Route::get('get-account-balance', [MultiPurposeController::class, 'getAccountBalance']);

// purchase Order
Route::post('qty-extended-add', [PurchaseOrderController::class, 'qtyExtendedAdd']);
Route::post('delivery-extended', [PurchaseOrderController::class, 'delExtendedAdd']);


Route::post('dispatch-packings',[SaleOrderDispatchController::class,'getPackings']);
Route::delete('shared-attachments/{id}', [SharedAttachmentController::class, 'deleteAttachment']);
Route::get('shared-attachments-thumbnail/{id}', [SharedAttachmentController::class, 'getAttachmentThumbnail']);

Route::post('shared-resources/{type}',[SharedResourceController::class,'saveResources']);
Route::get('shared-resources/{type}/{id}',[SharedResourceController::class,'showResources']);

Route::delete('company-attachments/{id}', [CompanyAttachmentController::class, 'deleteAttachment']);
Route::get('company-attachments-thumbnail/{id}', [CompanyAttachmentController::class, 'getAttachmentThumbnail']);

Route::post('company-resources/{type}',[CompanyResourceController::class,'saveResources']);
Route::get('company-resources/{type}/{id}',[CompanyResourceController::class,'showResources']);

Route::delete('yearly-attachments/{id}', [YearlyAttachmentController::class, 'deleteAttachment']);
Route::get('yearly-attachments-thumbnail/{id}', [YearlyAttachmentController::class, 'getAttachmentThumbnail']);

Route::post('yearly-resources/{type}',[YearlyAttachmentController::class,'saveResources']);
Route::get('yearly-resources/{type}/{id}',[YearlyAttachmentController::class,'showResources']);

Route::resources([
    'users' => UserController::class,
    'roles' => RoleController::class,
    'countries' => CountryController::class,
    'states' => StateController::class,
    'cities' => CityController::class,
    'districts' => DistrictController::class,
    'companies' => CompanyController::class,
    'branches' => BranchController::class,
    'banks' => BankController::class,
    'ifscs' => IfscsController::class,
    'pay-terms'=> PayTermController::class,
    'clients'=> TypeMasterController::class,
    'vendors'=> TypeMasterController::class,
    'party-sub-groups'=>TypeMasterController::class,
    'gl-sub-groups'=>TypeMasterController::class,
    'limit-sub-groups'=>TypeMasterController::class,
    'accounts'=> AccountController::class,
    'opening' =>AccountOpeningController::class,
    'stores'=> StoreController::class,
    'item-units'=> ItemUnitController::class,
    'main-groups'=> ItemGroupController::class,
    'sub-groups'=> ItemGroupController::class,
    'other-sub-groups'=>ItemGroupController::class,
    'items'=> ItemController::class,
    'gsts'=> GstController::class,
    'vat-cst'=> VatCstController::class,
    'sale-contracts'=> SaleContractController::class,
    'oil-packings'=> PackingController::class,
    'loose-packings'=> PackingController::class,
    'liquor-packings'=> PackingController::class,
    'parameters'=> ParameterController::class,
    'branch-prefixes'=> BranchPrefixController::class,
    'user-companies'=>UserCompanyController::class,
    'user-branches'=>UserBranchController::class,
    'report-user-branches'=>ReportUserBranchController::class,
    'brands'=>BrandController::class,
    'party-categories'=> CategoryController::class,
    'brands-categories'=> BrandCategoryController::class,
    'categories-brands'=> BrandCategoryController::class,
    'rate-differences'=>RateDifferenceController::class,
    'packing-formulas'=>PackingFormulaController::class,
    'sale-orders'=>SaleOrderController::class,
    'item-freights'=>ItemFreightController::class,
    'fix-rates'=>FixRateController::class,
    'sale-order-dispatch' => SaleOrderDispatchController::class,
    'shared-attachments'=> SharedAttachmentController::class,
    'company-attachments'=> CompanyAttachmentController::class,
    'yearly-attachments'=> YearlyAttachmentController::class,
    'sale-invoices' => SaleInvoiceController::class,
    'vouchers' => VoucherController::class,
    'term-conditions' => TermConditionController::class,
    'sale-report'=> SaleReportController::class,
    'ledger-report'=> LedgerReportController::class,
    'contract-booking-report'=> ContractBookingReportController::class,
    'contract-execution-report'=> ContractExecutionReportController::class,
    'pending-contract-report'=> PendingContractReportController::class,
    'sale-format-report'=> SaleFormatReportController::class,
    'branch-bank-accounts'=> BranchBankAccountController::class,
    'tds-section'=> TdsSectionController::class,
    'lower-tds-setting'=> LowerTdsSettingController::class,
    'debit-note'=> DebitNoteController::class,
    'credit-note'=> CreditNoteController::class,
    'issue'=> TransferController::class,
    'receipt'=> TransferController::class,
    'bags'=> BagController::class,
    'kanda'=> KandaController::class,
    'trial-balance-report'=> TrailBalanceReportController::class,
    'cheque-setting'=> ChequeSettingController::class,
    'purchase-orders'=> PurchaseOrderController::class,


]);



//Selects
Route::post('login-companies/filtered', [SelectController::class, 'loginCompaniesFiltered']);
Route::post('login-branches/filtered', [SelectController::class, 'loginBranchesFiltered']);
Route::post('companies/filtered', [SelectController::class, 'companiesFiltered']);
Route::post('cities/filtered', [SelectController::class, 'citiesFiltered']);
Route::post('states/filtered', [SelectController::class, 'statesFiltered']);
Route::post('ifscs/filtered', [SelectController::class, 'ifscFiltered']);
Route::post('ifscs/filtered', [SelectController::class, 'ifscFiltered']);
Route::post('account-groups/filtered', [SelectController::class, 'accountGroupFiltered']);
Route::post('account-sub-groups/filtered', [SelectController::class, 'accountSubGroupFiltered']);
Route::post('pay-terms/filtered', [SelectController::class, 'payTermsFiltered']);
Route::post('type-masters/filtered', [SelectController::class, 'typeMasterFiltered']);
Route::post('branches/filtered', [SelectController::class, 'branchesFiltered']);
Route::post('report-branches/filtered', [SelectController::class, 'reportBranchesFiltered']);
Route::post('districts/filtered', [SelectController::class, 'districtsFiltered']);
Route::post('banks/filtered', [SelectController::class, 'banksFiltered']);
Route::post('accounts/filtered', [SelectController::class, 'accountsFiltered']);
Route::post('limit-accounts/filtered', [SelectController::class, 'limitAccountsFiltered']);
Route::post('account-sub-groups-others/filtered', [SelectController::class, 'accountSubGroupOtherFiltered']);
Route::post('uqcs/filtered', [SelectController::class, 'uqcsFiltered']);
Route::post('item-groups/filtered', [SelectController::class, 'itemGroupsFiltered']);
Route::post('stores/filtered', [SelectController::class, 'storesFiltered']);
Route::post('item-units/filtered', [SelectController::class, 'itemUnitsFiltered']);
Route::post('gsts/filtered', [SelectController::class, 'gstFiltered']);
Route::post('vat-cst/filtered', [SelectController::class, 'vatCstFiltered']);
Route::post('items/filtered', [SelectController::class, 'itemsFiltered']);
Route::post('packings/filtered', [SelectController::class, 'packingsFiltered']);
Route::post('packings/filtered/{type}', [SelectController::class, 'packingsLoosePackedFiltered']);
Route::post('party-categories/filtered', [SelectController::class, 'partyCategoriesFiltered']);
Route::post('brands/filtered', [SelectController::class, 'brandsFiltered']);
Route::post('sale-contracts/filtered', [SelectController::class, 'saleContractFiltered']);
Route::post('sale-contract-items/filtered', [SelectController::class, 'saleContractItemsFiltered']);
Route::post('items-sale-contracts/filtered/{item_id}/{client_id}', [SelectController::class, 'itemSaleContractFiltered']);
Route::post('sale-orders/filtered', [SelectController::class, 'saleOrdersFiltered']);
Route::post('sale-orders-dispatch/filtered', [SelectController::class, 'saleOrdersDispatchFiltered']);
Route::post('bank-accounts/filtered',[SelectController::class,'getBankAccounts']);
Route::post('tds-section/filtered',[SelectController::class,'getTdsSection']);

Route::post('get-branches',[MultiPurposeController::class,'getBranches']);
Route::post('item-freight',[MultiPurposeController::class,'getItemFreight']);
Route::post('item-fix-rates',[MultiPurposeController::class,'getItemFixRate']);
Route::get('initial-data/{type}',[MultiPurposeController::class,'getFormInitialData']);

Route::get('item/{id}/details', [ItemController::class, 'getItemDetails']);

//PRINTS

Route::get('sale-dispatch-print/{sale_order_id}', [SaleOrderDispatchController::class, 'getSaleOrderDispatchPrint']);
Route::get('sale-invoice-print/{invoice_id}', [SaleInvoiceController::class, 'getSaleInvoicePrint']);
Route::get('voucher-print/{id}', [VoucherController::class, 'VoucherPrint']);


// roles

Route::get('roles/{role_id}/permissions', [RoleController::class, 'showPermissions']);
Route::get('roles/{role_id}/add', [RoleController::class, 'addRole']);
Route::post('roles/{role_id}/permissions', [RoleController::class, 'savePermissions']);

Route::get('users-list', [UserController::class, 'userslist']);
Route::get('roles-list', [RoleController::class, 'roleslist']);

// Reports

