<?php

use App\Http\Controllers\Backend\HomeController;
use App\Http\Controllers\Backend\TaxMstController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Backend\HsnController;
use App\Http\Controllers\Backend\ExpHeadMasterController;
use App\Http\Controllers\Backend\UomConversionController;
use App\Http\Controllers\Backend\UomController;
use App\Http\Controllers\Backend\ItemController;
use App\Http\Controllers\Backend\SiteTypeController;
use App\Http\Controllers\Backend\SiteController;
use App\Http\Controllers\Backend\CompanyMasterController;
use App\Http\Controllers\Backend\DesignationController;
use App\Http\Controllers\Backend\VendorController;
use App\Http\Controllers\Backend\UnitMasterController;
use App\Http\Controllers\Backend\ClientController;
use App\Http\Controllers\Backend\CreateJobController;
use App\Http\Controllers\Backend\ItemOpeningBalanceController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\SaleRateController;
use App\Http\Controllers\Backend\PurchaseRateMasterController;
use App\Http\Controllers\Backend\UserMasterController;
use App\Http\Controllers\Backend\RoleMasterController;
use App\Http\Controllers\Backend\UserRoleMappingController;
use App\Http\Controllers\Backend\PurchaseOrderController;
use App\Http\Controllers\Backend\UserPermissionController;
use App\Http\Controllers\Backend\NavController;
use App\Http\Controllers\Backend\JobAdvanceController;
use App\Http\Controllers\Backend\JobInvoiceController;
use App\Http\Controllers\Backend\ChallanController;
use App\Http\Controllers\Backend\InvoicePaymentController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\SalesReportController;
use App\Http\Controllers\Backend\StockTransferController;
use App\Http\Controllers\Backend\ViewDesignerController;
use App\Http\Controllers\Backend\ViewPrinterDetailsController;



use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // print_r('ok');
    // die;
    return view('auth.login');
});

// Route::get('/dashboard', function () {
//     return view('backend.index');
// })->middleware(['auth', 'verified'])->name('index');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
// Regestar page
// Logout
Route::get('logout', [HomeController::class, 'adminLogout'])->name('asmin.logout');

// Tax Master Route
Route::get('/index', [HomeController::class, 'index'])->name('index.page');
// Tax Master Route
Route::middleware(['auth'])->group(function () {
    // Tax Master
    Route::controller(TaxMstController::class)->group(function () {
        Route::get('tax-master', 'index')->name('tax-mst.index');
        Route::post('tax-master', 'submit')->name('submit.tax');
        Route::get('get-tax/{id}', 'getTaxData')->name('get.tax');
        Route::post('/tax/update', 'updateTax')->name('update.tax');
        Route::post('/check-tax-name', 'checkTaxName')->name('check.tax.name');
        Route::put('/change-status/{id}', 'changeStatus')->name('change.status');
    });
    // Exp Head Master
    Route::controller(ExpHeadMasterController::class)->group(function () {
        Route::get('exp-head', 'index')->name('exp-head.index');
        Route::post('exp-head/store', 'store')->name('store.exp');
        Route::put('/update-status/{id}', 'updateStatus')->name('update.status');
        Route::get('get-exp-head-data/{id}', 'getExpData')->name('get.exp');
        Route::post('/exp/update', 'updateExp')->name('update.exp');
        Route::post('/check-head-name', 'checkHeadName')->name('check.head.name');
    });
    // Uom Conversion
    Route::controller(UomConversionController::class)->group(function () {
        Route::get('uom-conversion', 'index')->name('uom-conver.index');
        Route::post('store/uom-conversion', 'store')->name('store.uom-con');
        Route::get('get-uom-conversion/{id}', 'getUomConver')->name('get.uom.conver');
        Route::post('uom-conversion/update', 'updateExp')->name('update.uom.conver');
        Route::post('/check-uom-conversion', 'checkUomConversion');
        Route::post('/check-uom-duplicate', 'checkUOMDuplicate');
    });
    // HSN Master
    Route::controller(HsnController::class)->group(function () {
        Route::get('/hsn-master', 'index')->name('hsn.index');
        Route::post('/hsn-master',  'store')->name('hsn.store');
        Route::put('/hsn/update/{id}', 'update')->name('hsn.update');
        Route::get('/hsn/change-status/{id}', 'changeStatus')->name('hsnc.changeStatus');
        Route::get('/hsn/edit/{id}', 'edit')->name('hsn.edit');
        Route::post('/check-hsn-code', 'checkHsnCode')->name('check.hsn.code');
    });
    //UOM Master
    Route::controller(UomController::class)->group(function () {
        Route::get('/uom-master', 'index')->name('uom.index');
        Route::post('uom-master',  'store')->name('uom.store');
        Route::put('/uom/update/{id}', 'update')->name('uom.update');
        Route::get('/uom/change-status/{id}', 'changeStatus')->name('uom.changeStatus');
        Route::get('/uom/edit/{id}', 'edit')->name('uom.edit');
        Route::post('/check-uom-name', 'checkUomName')->name('check.uom.name');
        Route::post('/check-uom-code', 'checkUomCode')->name('check.uom.code');
    });
    //Item Master
    Route::controller(ItemController::class)->group(function () {
        Route::get('/item-master', 'index')->name('item.index');
        Route::post('item-master', 'store')->name('item.store');
        Route::put('/item/update/{id}', 'update')->name('item.update');
        Route::get('/item/change-status/{id}', 'changeStatus')->name('item.changeStatus');
        Route::get('/item/edit/{id}', 'edit')->name('item.edit');
        Route::post('/check-item-duplicate',  'checkItemDuplicate');
        Route::post('/check-item-name-unique',  'checkItemNameUnique');
    });
    //Designation
    Route::controller(DesignationController::class)->group(function () {
        Route::get('/designation-master', 'index')->name('designation.index');
        Route::post('designation-master', 'store')->name('designation.store');
        Route::put('/designation/update/{id}', 'update')->name('designation.update');
        Route::get('/designation/change-status/{id}', 'changeStatus')->name('designation.changeStatus');
        Route::get('/designation/edit/{id}', 'edit')->name('designation.edit');
        Route::post('/check-designation-duplicate', 'checkDuplicateDesignation');
    });
    //Role master
    Route::controller(RoleMasterController::class)->group(function () {
        Route::get('/role-master', 'index')->name('role.index');
        Route::post('role-master', 'store')->name('role.store');
        Route::put('/role/update/{id}', 'update')->name('role.update');
        Route::get('/role/change-status/{id}', 'changeStatus')->name('role.changeStatus');
        Route::get('/role/edit/{id}', 'edit')->name('role.edit');
        Route::post('/check-role-name', 'checkRoleName');
    });
    //vendor master
    Route::controller(VendorController::class)->group(function () {
        Route::get('/vendor-master', 'index')->name('vendor.index');
        Route::post('vendor-master', 'store')->name('vendor.store');
        Route::put('/vendor/update/{id}', 'update')->name('vendor.update');
        Route::get('/vendor/change-status/{id}', 'changeStatus')->name('vendor.changeStatus');
        Route::get('/vendor/edit/{id}', 'edit')->name('vendor.edit');
        Route::get('/districts/{stateId}', 'getDistrictsByState');
        Route::get('/districts/{state_id}', 'getDistricts')->name('districts.fetch');
        Route::post('/check-product-date-unique',  'checkProductDateUnique');
    });

    //Client master
    Route::controller(ClientController::class)->group(function () {
        Route::get('/client-master', 'index')->name('client.index');
        Route::post('client-master', 'store')->name('client.store');
        Route::put('/client/update/{id}', 'update')->name('client.update');
        Route::get('/client/change-status/{id}', 'changeStatus')->name('client.changeStatus');
        Route::get('/client/edit/{id}', 'edit')->name('client.edit');
        Route::get('/districts/{stateId}', 'getDistrictsByState');
        Route::get('/districts/{state_id}', 'getDistricts')->name('districts.fetch');
    });

    //product master
    Route::controller(ProductController::class)->group(function () {
        Route::get('/product-master', 'index')->name('product.index');
        Route::post('/product-master',  'store')->name('product.store');
        Route::put('/product/update/{id}', 'update')->name('product.update');
        Route::get('/product/change-status/{id}', 'changeStatus')->name('product.changeStatus');
        Route::get('/product/edit/{id}', 'edit')->name('product.edit');
        Route::post('/check-product-name', 'checkProductName');
    });
    //sale rate master
    Route::controller(SaleRateController::class)->group(function () {
        Route::get('/salerate-master', 'index')->name('sale.index');
        Route::post('/salerate-master',  'store')->name('sale.store');
        Route::put('/salerate/update/{id}', 'update')->name('sale.update');
        Route::get('/salerate/change-status/{id}', 'changeStatus')->name('sale.changeStatus');
        Route::get('/salerate/edit/{id}', 'edit')->name('sale.edit');
        Route::get('/salerate/uom/{productId}', 'getUOMName');
        Route::post('/check-product-date-unique', 'checkProductDateUnique');
    });


    //user master
    Route::controller(UserMasterController::class)->group(function () {
        Route::get('/user-master', 'index')->name('user.index');
        Route::post('/user-master',  'store')->name('user.store');
        Route::put('/user/update/{id}', 'update')->name('user.update');
        Route::get('/user/change-status/{id}', 'changeStatus')->name('user.changeStatus');
        Route::get('/user/edit/{id}/{state}', 'edit')->name('user.edit');
        Route::get('/district_fetch/{stateId}', 'getDistrictsByState');
        // Route::get('/get-districts/{stateId}', 'getDistricts')->name('districts.fetch');
    });
    //User Role Mapping
    Route::controller(UserRoleMappingController::class)->group(function () {
        Route::get('/role-mapping-master', 'index')->name('rolemapping.index');
        Route::post('/role-mapping-master',  'store')->name('rolemapping.store');
        Route::put('/role-mapping/update/{id}', 'update')->name('role-mapping.update');

        Route::get('/role-mapping/edit/{id}', 'edit')->name('role-mapping.edit');
    });

    //Item opening Balance
    Route::controller(ItemOpeningBalanceController::class)->group(function () {
        Route::get('/item-opening-master', 'index')->name('itemopening.index');
        Route::post('/item-opening-master',  'store')->name('itemopening.store');
        Route::put('/item-opening-master/update/{id}', 'update')->name('item-opening-master.update');
        Route::get('/get-uom/{itemId}', 'getUOM');
        Route::get('/item-opening-master/edit/{id}', 'edit')->name('item-opening-master.edit');
    });

    // Site Type Dorkar nai ata
    Route::controller(SiteTypeController::class)->group(function () {
        Route::get('/site-type-master', 'index')->name('site-type.index');
        Route::post('store/site-type-master', 'storeSite')->name('store.type-master');
        Route::put('/stie-type/update-status/{id}', 'updateStatus')->name('site.update.status');
        Route::get('get-site-type-master/{id}', 'getSiteType')->name('get.sitetype-master');
        Route::post('/site-master/update', 'updateSiteMaster')->name('update.site-master');
    });
    // Site Dorkar nai ata
    Route::controller(SiteController::class)->group(function () {
        Route::get('/site-master', 'index')->name('site.index');
    });
    // Company Master
    Route::controller(CompanyMasterController::class)->group(function () {
        Route::get('/company-master', 'index')->name('company.index');
        Route::post('/company-master', 'addStore')->name('company.add');
        Route::get('/districts/{state_id}', 'getDistricts')->name('districts.fetch');
        Route::get('/company/edit/{id}', 'edit')->name('company.edit');
        Route::post('/update-company-master', 'updateCompany')->name('update.company');
        Route::post('/check-company-duplicate', 'checkDuplicateCompany');
    });
    // Unit Master
    Route::controller(UnitMasterController::class)->group(function () {
        Route::get('/unit-master', 'index')->name('unit.index');
        Route::get('/get-districts/{state_id}', 'getDistricts')->name('districts.data');
        Route::post('/add-unit-master', 'addunit')->name('unit.store');
        Route::get('/company-master/change-status/{id}', 'unitchangeStatus')->name('unit-master.changeStatus');
        Route::get('/get-unit-data/{id}', 'getUnitData')->name('get-unit.master');
        Route::get('/edit-districts/{stateId}', 'editDistricts')->name('get-district.master');
        Route::post('/submit-data', 'submitData')->name('update.unit-master');
        Route::get('/check-unit-unique', 'checkUnitUnique')->name('unit.checkUnique');
    });
    // Purchase Rate Master
    Route::controller(PurchaseRateMasterController::class)->group(function () {
        Route::get('/purchase-rate-master', 'index')->name('purchase-rate.index');
        Route::get('/item-name', 'changeItem')->name('change-item');
        Route::post('/purchase-store', 'storePurchase')->name('store.purchase-item');
        Route::get('/get-item-data/{id}', 'getItemData')->name('get-item-data');
        // Route::get('/change-item', 'editchangeItem')->name('edit-change-item');
        Route::post('/update-purchase-item', 'updatePurchaseData')->name('update.purchase-data');
        // In web.php (or api.php depending on where you handle it)

        Route::post('/check-item-date-unique', 'checkItemDateUnique');
    });
});




// Create Job
Route::middleware(['auth'])->group(function () {
    Route::controller(CreateJobController::class)->group(function () {
        Route::get('/job-index', 'jobindex')->name('job-page.index');
        Route::get('/job-create', 'index')->name('job-create.index');
        Route::get('/get-client-contacts/{id}', 'getClientContacts');
        Route::get('/get-uom-rate/{id}', 'getUomAndRate');
        Route::post('store-create-job', 'stroreCreateJob')->name('store.create-job');
        Route::get('/view-job-details/{id}', 'jobProductDetails')->name('view.job-details');
        Route::get('/edit-create-job/{id}', 'editCreateJob')->name('edit.create-job');
        Route::get('/get-invoice-data', 'getInvoiceData')->name('get.invoice.data');
        Route::post('/update-jb-create', 'updateCreateJob')->name('update.create-job');
        Route::get('/job-view/{id}/approve-reject', 'approveReject')->name('job.approve');
        Route::get('assign-desiner/{id}', 'assignDesigner')->name('assign.desiner');
        Route::get('assign-printer/{id}', 'assignPrinter')->name('assign.printer');
        Route::post('get-designer-details', 'desinerDetails')->name('designer.details');
        Route::post('get-printer-details', 'printerDetails')->name('printer.details');
        Route::post('submit-assign-data', 'submitData')->name('submit.data');
        Route::post('submit-printer-data', 'submitPrinterData')->name('submit.printer-data');
    });
});
//Purchase order
Route::controller(PurchaseOrderController::class)->group(function () {
    Route::get('/purchase-order-index', 'orderList')->name('purchaseOrder.index');
    Route::get('/purchase-order', 'orderCreate')->name('purchase-order.create_order');
    Route::post('purchase-order', 'store')->name('purchaseOrder.store');
    Route::get('/get-detail-by-item/{itemId}/{vendorId}', 'getDetailsByItem');
    Route::get('/purchase-order-view/{id}', 'orderView')->name('purchaseOrder.view');
    Route::get('/purchase-order/{id}/approve-reject', 'approveReject')->name('purchaseOrder.approve');
    Route::post('/save-remarks-for-approve-reject', 'saveRemarks');
    //GRN
    Route::get('/grn-index', 'grnList')->name('grn.index');
    Route::get('/grn-generate', 'grnCreate')->name('grnGenerate.create_grn');
    Route::post('grn-generate', 'grnSave')->name('grnGenerate.saveGrn');
    Route::get('/get-order-details-for-grn/{id}', 'getOrderDetails');
});
//stock tranfer
Route::controller(StockTransferController::class)->group(function () {
    Route::get('/create-stock-transfer', 'createStockTranfer')->name('stock-transfer.create');
    Route::get('/get-items-for-unit/{unitId}', 'getItemsForUnit')->name('items.forUnit');
    Route::get('/get-stock-details-from-unit/{itemId}/{fromUnit}', 'getStock');
    Route::post('create-stock-transfer', 'saveStockTransfer')->name('stockTransfer.saveData');
    Route::get('/stock-transfer-index', 'transferList')->name('stockTransfer.index');
});



//role permission by Aritra

Route::get('/permissions', [UserPermissionController::class, 'showPermissionsPage'])->name('permissions.page');
Route::get('/get-role-list', [UserPermissionController::class, 'getRoleList'])->name('get.role.list');
Route::post('/get-permissions', [UserPermissionController::class, 'getPermissions'])->name('get.permissions');
Route::post('/update-permissions', [UserPermissionController::class, 'updatePermissions'])->name('update.permissions');
Route::post('/update-single-permission', [UserPermissionController::class, 'updateSinglePermission'])->name('update.single.permission');


Route::get('/show-top-nav', [NavController::class, 'showTopNavPage'])->name('showTopNavPage');
Route::get('/get-sidebar-data', [NavController::class, 'sideNavCall'])->name('get-sidebar.data');

//advance payment for job number
Route::get('/job-view/{id}', [JobAdvanceController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{id}/advances', [JobAdvanceController::class, 'storeAdvance'])->name('advances.store');

//Job invoice

Route::controller(JobInvoiceController::class)->group(function () {
    Route::get('/job-invoice/{id}', 'getDetails')->name('jobInvoice.get_details');
    Route::get('/cash-job-invoice/{id}', 'getDetailsForCashJob')->name('jobInvoice.cash_job_get_details');
    Route::get('/get-tax-by-client-id/{clientId}/{hsnId}', 'getTax');
    Route::post('/submit-job-invoice/{id}', 'submit')->name('job.submit');
    Route::post('/submit-cash-job-invoice/{id}', 'cashJobSubmit')->name('cashjob.submit');
    Route::get('/generate-job-invoice-pdf/{id}', 'generatePdf')->name('generate.pdf');
});

//challan generate for job number
Route::get('challan/create/{job_id}', [ChallanController::class, 'create'])->name('challan.create');
Route::post('challan/store', [ChallanController::class, 'store'])->name('challan.store');
Route::get('challans/report/{id}', [ChallanController::class, 'generateReport'])->name('challans.report');

//Invoice Outstanding Payment
Route::get('invoice-payment/{jobInvoiceId}', [InvoicePaymentController::class, 'showForm'])->name('invoice.payment.form');
Route::post('invoice-payment', [InvoicePaymentController::class, 'store'])->name('invoice.payment.store');

Route::get('/sales-report', [SalesReportController::class, 'index'])->name('sales.report');

// Designer View Controller
Route::middleware(['auth'])->group(function () {
    Route::controller(ViewDesignerController::class)->group(function () {
        Route::get('/designer-view', 'index')->name('designer.view');
        Route::get('/designer-view-edit/{id}', 'view')->name('designer.view.edit');
        // web.php
        Route::post('/designer-update', 'update')->name('designer.update');
    });

    Route::controller(ViewPrinterDetailsController::class)->group(function () {
        Route::get('/printer-view', 'index')->name('printer.view');
        Route::get('/printer-view-edit/{id}', 'view')->name('printer.view.edit');
        Route::post('/printer-update', 'update')->name('printer.update');
    });
});
