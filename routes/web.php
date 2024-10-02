<?php

use App\Http\Controllers\AdditionalInvoiceController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\GeneralAccountController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\OpeningBalanceController;
use App\Http\Controllers\ProductUnitMeasureController;
use App\Http\Controllers\ProformaInvoiceController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\InterBankController;
use App\Http\Controllers\ReturnDebitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BankBranchController;
use App\Http\Controllers\ExpenseItemController;
use App\Http\Controllers\PaymentModeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BranchProductPriceController;
use App\Http\Controllers\MisController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\InterStoreTransferController;
use App\Http\Controllers\InterSiteTransferController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CreditLimitController;
use App\Http\Controllers\PurchaseGRNController;
use App\Http\Controllers\PurchaseRequestController;
use App\Models\Supplier;
use App\Http\Controllers\ReportController;
use Faker\Provider\Miscellaneous;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleHasPermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CashMovementController;
use App\Http\Controllers\LoanCollectorController;
use App\Models\LoanCollector;
use App\Http\Controllers\LoanPaymentController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductExpireSettingController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\DebitNoteController;


/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider within a group which | contains the "web" middleware group. Now create something great! | */
Auth::routes();
Route::get('/', function () {
    return view('auth/login');
});

Route::middleware('auth')->group(function () {


    Route::prefix('opening-balance')->group(function(){
            Route::match(['GET','POST'],'/customer-balance', [OpeningBalanceController::class,'customerBalance'])->name('opening-balance.customer.balance');
            Route::match(['GET','POST'],'/limit', [OpeningBalanceController::class,'customerLimit'])->name('opening-balance.customer.limit');
            Route::match(['GET','POST'],'/inventory-valuation', [OpeningBalanceController::class,'inventoryValuation'])->name('opening-balance.inventory.valuation');
            Route::match(['GET','POST'],'/account-ledger', [OpeningBalanceController::class,'accountLedger'])->name('opening-balance.account.ledger');
            Route::match(['GET','POST'],'/supplier-balance', [OpeningBalanceController::class,'supplierBalance'])->name('opening-balance.supplier.balance');
    });


    Route::group(['prefix' => 'transaction'], function () {
        Route::group(
            ['prefix' => 'stock_adjustment'],
            function () {
                Route::get('/index', [StockAdjustmentController::class, 'index'])->name('stock_adjustments.index');
                Route::get('/create', [StockAdjustmentController::class, 'create'])->name('stock_adjustments.create');
                Route::get('/show/{stockAdjustment}', [StockAdjustmentController::class, 'show'])->name('stock_adjustments.show');
                Route::post('/store', [StockAdjustmentController::class, 'store'])->name('stock_adjustments.store');
                Route::get('/edit/{stockAdjustment}', [StockAdjustmentController::class, 'edit'])->name('stock_adjustments.edit');
                Route::put('/update/{stockadjustment}', [StockAdjustmentController::class, 'update'])->name('stock_adjustments.update');
                Route::delete('/delete/{stockadjustment}', [StockAdjustmentController::class, 'destroy'])->name('stock_adjustments.destroy');
                Route::get('/print/{stockAdjustment}', [StockAdjustmentController::class, 'print'])->name('stock_adjustments.print');
                Route::post('/post/{stockAdjustment}', [StockAdjustmentController::class, 'post'])->name('stock_adjustments.post');
                Route::post('/delete/{stockAdjustment}', [StockAdjustmentController::class, 'delete'])->name('stock_adjustments.delete');
                Route::put('/cart/update', [StockAdjustmentController::class, 'updateCart'])->name('stock_adjustments.cart.update');
            }
        );
    });
    Route::group(['prefix' => 'settings'], function () {
        Route::group(
            ['prefix' => 'banks'],
            function () {
                Route::get('/index', [BankController::class, 'index'])->name('banks.index');
                Route::get('/create', [BankController::class, 'create'])->name('banks.create');
                Route::get('/show/{bank}', [BankController::class, 'show'])->name('banks.show');
                Route::post('/store', [BankController::class, 'store'])->name('banks.store');
                Route::get('/edit/{bank}', [BankController::class, 'edit'])->name('banks.edit');
                Route::put('/update/{bank}', [BankController::class, 'update'])->name('banks.update');
                Route::delete('/delete/{bank}', [BankController::class, 'destroy'])->name('banks.destroy');
            }
        );
        Route::group(
            ['prefix' => 'categories'],
            function () {
                Route::get('/index', [CategoryController::class, 'index'])->name('categories.index');
                Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
                Route::get('/show/{category}', [CategoryController::class, 'show'])->name('categories.show');
                Route::post('/store', [CategoryController::class, 'store'])->name('categories.store');
                Route::get('/edit/{category}', [CategoryController::class, 'edit'])->name('categories.edit');
                Route::put('/update/{category}', [CategoryController::class, 'update'])->name('categories.update');
                Route::delete('/delete/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

                //Import Categories
                Route::get('/import/form', [CategoryController::class, 'importForm'])->name('categories.import.form');
                Route::post('/import', [CategoryController::class, 'import'])->name('categories.import');
            }
        );

        Route::group(
            ['prefix' => 'bank_branches'],
            function () {
                Route::get('/index', [BankBranchController::class, 'index'])->name('bank_branches.index');
                Route::get('/create', [BankBranchController::class, 'create'])->name('bank_branches.create');
                Route::get('/show/{bankbranch}', [BankBranchController::class, 'show'])->name('bank_branches.show');
                Route::post('/store', [BankBranchController::class, 'store'])->name('bank_branches.store');
                Route::get('/edit/{bankbranch}', [BankBranchController::class, 'edit'])->name('bank_branches.edit');
                Route::put('/update/{bankbranch}', [BankBranchController::class, 'update'])->name('bank_branches.update');
                Route::delete('/delete/{bankbranch}', [BankBranchController::class, 'destroy'])->name('bank_branches.destroy');
            }
        );

        Route::group(
            ['prefix' => 'branches'],
            function () {
                Route::get('/index', [BranchController::class, 'index'])->name('branches.index');
                Route::get('/create', [BranchController::class, 'create'])->name('branches.create');
                Route::get('/show/{branch}', [BranchController::class, 'show'])->name('branches.show');
                Route::post('/store', [BranchController::class, 'store'])->name('branches.store');
                Route::get('/edit/{branch}', [BranchController::class, 'edit'])->name('branches.edit');
                Route::put('/update/{branch}', [BranchController::class, 'update'])->name('branches.update');
                Route::delete('/delete/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');

                //Import Branches
                Route::get('/import/form', [BranchController::class, 'importForm'])->name('branches.import.form');
                Route::post('/import', [BranchController::class, 'import'])->name('branches.import');
            }
        );
        Route::group(
            ['prefix' => 'stores'],
            function () {
                Route::get('/index', [StoreController::class, 'index'])->name('stores.index');
                Route::get('/create', [StoreController::class, 'create'])->name('stores.create');
                Route::get('/show/{store}', [StoreController::class, 'show'])->name('stores.show');
                Route::post('/store', [StoreController::class, 'store'])->name('stores.store');
                Route::get('/edit/{store}', [StoreController::class, 'edit'])->name('stores.edit');
                Route::put('/update/{store}', [StoreController::class, 'update'])->name('stores.update');
                Route::delete('/delete/{store}', [StoreController::class, 'destroy'])->name('stores.destroy');

                //Import Stores
                Route::get('/import/form', [StoreController::class, 'importForm'])->name('stores.import.form');
                Route::post('/import', [StoreController::class, 'import'])->name('stores.import');
            }
        );
        Route::group(
            ['prefix' => 'products'],
            function () {

                Route::group(
                    ['prefix' => 'manage'],
                    function () {
                        Route::get('/index', [ProductController::class, 'index'])->name('products.index');
                        Route::get('/create', [ProductController::class, 'create'])->name('products.create');
                        Route::get('/show/{product}', [ProductController::class, 'show'])->name('products.show');
                        Route::post('/store', [ProductController::class, 'store'])->name('products.store');
                        Route::get('/edit/{product}', [ProductController::class, 'edit'])->name('products.edit');
                        Route::put('/update/{product}', [ProductController::class, 'update'])->name('products.update');
                        Route::delete('/delete/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
                        Route::match(['GET', 'POST'], '/update/purchase_price', [ProductController::class, 'purchasePrice'])->name('products.purchase_prices');

                        //Import Products
                        Route::get('/import/form', [ProductController::class, 'importForm'])->name('products.import.form');
                        Route::post('/import', [ProductController::class, 'import'])->name('products.import');
                    }
                );
                Route::group(
                    ['utm' => 'manage'],
                    function () {
                        Route::get('/index', [ProductUnitMeasureController::class, 'index'])->name('product_unit_measures.index');
                        Route::get('/create', [ProductUnitMeasureController::class, 'create'])->name('product_unit_measures.create');
                        Route::get('/show/{productunitmeasure}', [ProductUnitMeasureController::class, 'show'])->name('product_unit_measures.show');
                        Route::post('/store', [ProductUnitMeasureController::class, 'store'])->name('product_unit_measures.store');
                        Route::get('/edit/{productunitmeasure}', [ProductUnitMeasureController::class, 'edit'])->name('product_unit_measures.edit');
                        Route::put('/update/{productunitmeasure}', [ProductUnitMeasureController::class, 'update'])->name('product_unit_measures.update');
                        Route::delete('/delete/{productunitmeasure}', [ProductUnitMeasureController::class, 'destroy'])->name('product_unit_measures.destroy');

                        //Import Products
                        Route::get('/import/form', [ProductUnitMeasureController::class, 'importForm'])->name('product_unit_measures.import.form');
                        Route::post('/import', [ProductUnitMeasureController::class, 'import'])->name('product_unit_measures.import');
                    }
                );
                Route::group(
                    ['prefix' => 'prices'],
                    function () {
                        Route::get('/index', [BranchProductPriceController::class, 'index'])->name('branch_product_prices.index');
                        Route::get('/create', [BranchProductPriceController::class, 'create'])->name('branch_product_prices.create');
                        Route::get('/show/{BranchProductPrice}', [BranchProductPriceController::class, 'show'])->name('branch_product_prices.show');
                        Route::post('/store', [BranchProductPriceController::class, 'store'])->name('branch_product_prices.store');
                        Route::get('/edit/{BranchProductPrice}', [BranchProductPriceController::class, 'edit'])->name('branch_product_prices.edit');
                        Route::put('/update/{BranchProductPrice}', [BranchProductPriceController::class, 'update'])->name('branch_product_prices.update');
                        Route::delete('/delete/{BranchProductPrice}', [BranchProductPriceController::class, 'destroy'])->name('branch_product_prices.destroy');
                        Route::get('/update/cost_price', [BranchProductPriceController::class, 'editCostPrice'])->name('store_product_cost_prices.edit');
                        Route::post('/update/cost_price/store', [BranchProductPriceController::class, 'updateCostPrice'])->name('store_product_cost_prices.store');
                        Route::get('/cost/price', [MisController::class, 'getCostPrice'])->name('ajax.load.product.price'); //This is cost price
                        Route::get('/cost/selling_price', [MisController::class, 'getSellingPrice'])->name('ajax.load.product.selling_price');
                        Route::get('/selling/price', [MisController::class, 'getLastTwoSellingPrice'])->name('ajax.load.selling.price');

                        //Import Prices
                        Route::get('/import/form', [BranchProductPriceController::class, 'importForm'])->name('price.import.form');
                        Route::post('/import', [BranchProductPriceController::class, 'import'])->name('price.import');
                    }
                );
                Route::group(
                    ['prefix' => 'expiration'],
                    function () {
                        Route::get('/index', [ProductExpireSettingController::class, 'index'])->name('product_expire_settings.index');
                        Route::get('/create', [ProductExpireSettingController::class, 'create'])->name('product_expire_settings.create');
                        Route::get('/show/{productexpiresetting}', [ProductExpireSettingController::class, 'show'])->name('product_expire_settings.show');
                        Route::post('/store', [ProductExpireSettingController::class, 'store'])->name('product_expire_settings.store');
                        Route::get('/edit/{productexpiresetting}', [ProductExpireSettingController::class, 'edit'])->name('product_expire_settings.edit');
                        Route::put('/update/{productexpiresetting}', [ProductExpireSettingController::class, 'update'])->name('product_expire_settings.update');
                        Route::delete('/delete/{productexpiresetting}', [ProductExpireSettingController::class, 'destroy'])->name('product_expire_settings.destroy');
                    }
                );

                Route::get('/create/stock/balance', [BranchProductPriceController::class, 'openingBalance'])->name('stock_opening_balance.create');
                Route::post('/store/stock/balance', [BranchProductPriceController::class, 'storeStockBalance'])->name('stock_opening_balance.store');
            }
        );

    });

//completed authorization
    Route::group(['prefix' => 'expenses'], function () {
        Route::group(
            ['prefix' => 'items'],
            function () {
                Route::get('/index', [ExpenseItemController::class, 'index'])->name('expense_items.index');
                Route::get('/create', [ExpenseItemController::class, 'create'])->name('expense_items.create');
                Route::get('/show/{expenseitem}', [ExpenseItemController::class, 'show'])->name('expense_items.show');
                Route::post('/store', [ExpenseItemController::class, 'store'])->name('expense_items.store');
                Route::get('/edit/{expenseitem}', [ExpenseItemController::class, 'edit'])->name('expense_items.edit');
                Route::put('/update/{expenseitem}', [ExpenseItemController::class, 'update'])->name('expense_items.update');
                Route::delete('/delete/{expenseitem}', [ExpenseItemController::class, 'destroy'])->name('expense_items.destroy');
            }
        );
        Route::group(
            ['prefix' => 'expenditures'],
            function () {
                Route::get('/index', [ExpenseController::class, 'index'])->name('expenses.index');
                Route::post('/cart/create', [ExpenseController::class, 'addToCart'])->name('expenses.cart.create');
                Route::put('/cart/update', [ExpenseController::class, 'updateCart'])->name('expenses.cart.update');
                Route::delete('/cart/delete/{expense}', [ExpenseController::class, 'removeCart'])->name('expense.cart.remove');
                Route::get('/create', [ExpenseController::class, 'create'])->name('expenses.create');
                Route::get('/show/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
                Route::post('/store', [ExpenseController::class, 'store'])->name('expenses.store');
                Route::get('/edit/{expense}', [ExpenseController::class, 'edit'])->name('expenses.edit');
                Route::put('/update/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
                Route::delete('/delete/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
                Route::post('/search', [ExpenseController::class, 'search'])->name('expenses.search');
            }
        );
        Route::get('/today', [ExpenseController::class, 'today_expense'])->name('expense.today');
        Route::get('/month/{month?}', [ExpenseController::class, 'month_expense'])->name('expense.month');
        Route::get('/yearly/{year?}', [ExpenseController::class, 'yearly_expense'])->name('expense.yearly');
    });

    Route::group(['prefix' => 'cart'], function () {
        Route::get('/cart/list', [CartController::class, 'cartList'])->name('cart.list');
        Route::post('/cart', [CartController::class, 'addToCart'])->name('cart.store');
        Route::put('/update-cart', [CartController::class, 'updateCart'])->name('cart.update');
        Route::delete('/remove/{id}', [CartController::class, 'removeCart'])->name('cart.remove');
        Route::post('/clear', [CartController::class, 'clearAllCart'])->name('cart.clear');
    });

    Route::group(['prefix' => 'sales'], function () {
        //Orders
        Route::group(['prefix' => 'orders'], function () {
            Route::get('/create', [PosController::class, 'index'])->name('order.invoice.index');
            Route::post('/order-invoice/create', [InvoiceController::class, 'final_order_invoice'])->name('order.invoice.create');
            Route::get('/order-invoice/edit/{order}', [InvoiceController::class, 'editOrderInvoice'])->name('order.invoice.edit');
            Route::get('/order-invoice/link/{order}', [InvoiceController::class, 'linkOrderInvoice'])->name('order.invoice.linking');
            Route::put('/order-invoice/update/{order}', [InvoiceController::class, 'updateOrderInvoice'])->name('order.invoice.update');
            Route::get('/order-print/{order_id}', [InvoiceController::class, 'order_print'])->name('invoice.order_print');
            Route::get('/order-invoice/print/{customer_id}', [InvoiceController::class, 'print_order_invoice'])->name('order.invoice.print');
            Route::get('/order-invoice/show/{id}', [OrderController::class, 'order_invoice_show'])->name('order.invoice.show');
            Route::get('/', [OrderController::class, 'order_invoice_list'])->name('order.invoice.list');
            Route::delete('/order-invoice/delete/{order}', [OrderController::class, 'destroy_order_invoice'])->name('order.invoice.destroy');
            Route::post('/order-invoice/close/{order}', [OrderController::class, 'orderInvoiceClose'])->name('order.invoice.close');
            Route::post('/order-invoice/search', [OrderController::class, 'order_invoice_search'])->name('order.invoice.search');
            Route::put('/approve/order-invoice/{order}', [OrderController::class, 'approveOrderInvoice'])->name('order.invoice.approve');
        });

        //Invoices
        Route::group(['prefix' => 'invoice'], function () {
            Route::get('/', [OrderController::class, 'invoice_list'])->name('invoice.index');
            Route::post('/product/search', [OrderController::class, 'search'])->name('sales_products.search');
            Route::post('/invoice', [InvoiceController::class, 'final_invoice'])->name('invoice.create');
            Route::put('/invoice/update/{order}', [InvoiceController::class, 'updateInvoice'])->name('invoice.update');
            Route::get('/print/{customer_id}', [InvoiceController::class, 'print'])->name('invoice.print');
            Route::get('/print-with-vat/{customer_id}', [InvoiceController::class, 'printWithVat'])->name('invoice.print-with-vat');
            Route::post('/invoice-final', [InvoiceController::class, 'final_invoice'])->name('invoice.final_invoice');
            Route::get('/show/{id}', [OrderController::class, 'show'])->name('orders.show');
            //            Route::delete('/delete/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
            Route::post('/delete/{invoice}', [InvoiceController::class, 'delete'])->name('invoice.delete');
            Route::post('/post/{invoice}', [OrderController::class, 'post'])->name('invoice.post');
            Route::get('pos/edit/{order}', [PosController::class, 'edit'])->name('pos.edit');
            Route::get('pos/get_available_product/{storeproduct}', [OrderController::class, 'getAvailableQuantity'])->name('update.available.quantity');

        });

        //Proforma Invoice
        Route::group(['prefix' => 'proforma'], function () {
            Route::get('/create', [PosController::class, 'index'])->name('proforma.index');
            Route::post('/proforma/create', [ProformaInvoiceController::class, 'final_proformer'])->name('proformer.create');
            Route::get('/proforma/print/{order_id}', [ProformaInvoiceController::class, 'print_proformer'])->name('proformer.print');
            Route::get('/proforma/edit/{order}', [ProformaInvoiceController::class, 'editProformer'])->name('proformer.edit');
            Route::put('/proforma-invoice/update/{order}', [ProformaInvoiceController::class, 'UpdateProforma'])->name('proformer.update');
            Route::delete('/proforma-invoice/delete/{order}', [ProformaInvoiceController::class, 'destroy_proformer'])->name('proformer.destroy');
            Route::get('/proforma/show/{id}', [ProformaInvoiceController::class, 'proformer_show'])->name('proformer.show');
            Route::get('/', [ProformaInvoiceController::class, 'proformer_list'])->name('proformer.list');
            Route::post('/proforma/search', [ProformaInvoiceController::class, 'proformer_search'])->name('proformer.search');
            Route::post('/delete/{proforma}', [ProformaInvoiceController::class, 'delete'])->name('proformer.delete');
            Route::post('/close/{proforma}', [ProformaInvoiceController::class, 'close'])->name('proformer.close');

        });

        //Credit Notes
        Route::group(['prefix' => 'credit-note'], function () {
            Route::get('/', [CreditNoteController::class, 'creditNote'])->name('customers.credit.note');
            Route::get('/create/{order?}', [CreditNoteController::class, 'create'])->name('credit.note.create');
            Route::post('/store', [CreditNoteController::class, 'store'])->name('credit.note.store');
            Route::put('/update/{ledger}', [CreditNoteController::class, 'updateCreditnote'])->name('customers.credit.note.update');
            Route::delete('/delete/{ledger}', [CreditNoteController::class, 'deleteCreditNote'])->name('customers.credit.note.destroy');
            Route::post('/search', [CreditNoteController::class, 'searchCreditNote'])->name('customers.credit.note.search');
            Route::get('/load_invoices', [CreditNoteController::class, 'loadInvoices'])->name('load.order-invoices');
            Route::get('/load_cart', [CreditNoteController::class, 'loadToCart'])->name('load.order-cart');
            Route::post('/cart', [CreditNoteController::class, 'addToCart'])->name('credit.note.cart.store');
            Route::post('/update/cart', [CreditNoteController::class, 'updateCreditNoteCart'])->name('credit.note.cart.update');
            Route::post('/remove/{id}', [CreditNoteController::class, 'removeCart'])->name('credit.note.cart.remove');
            Route::get('/print/{credit_note}', [CreditNoteController::class, 'printCreditNoteReceipt'])->name('credit.note.print');
            Route::get('/show/{credit_note}', [CreditNoteController::class, 'show'])->name('credit.note.show');
            Route::post('/post/{credit_note}', [CreditNoteController::class, 'post'])->name('credit.note.post');
            Route::get('/edit/{credit_note}', [CreditNoteController::class, 'edit'])->name('credit.note.edit');
            Route::post('/delete/{credit_note}', [CreditNoteController::class, 'delete'])->name('credit.note.delete');
        });

        Route::get('pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('pos/whole-sale', [PosController::class, 'wholeSale'])->name('pos.whole.sale');

        Route::get('barcode/search/product', [PosController::class, 'barcodeSearch'])->name('barcode.search.product');
        Route::get('/waybill/order-print/{order_id}', [InvoiceController::class, 'waybill_print'])->name('waybill.order_print');
        Route::get('/pos/order-print/{order_id}', [InvoiceController::class, 'pos_print'])->name('pos.order_print');
        Route::get('/sales-today', [OrderController::class, 'today_sales'])->name('sales.today');
        Route::get('/sales-monthly/{month?}', [OrderController::class, 'monthly_sales'])->name('sales.monthly');
        Route::get('/sales-total', [OrderController::class, 'total_sales'])->name('sales.total');
        Route::get('load/store/products', [MisController::class, 'loadStoreProducts'])->name('ajax.load.store.products');

        Route::post('/product/verify', [OrderController::class, 'verify'])->name('sales_products.verify');
        Route::post('/transfer/to-user', [OrderController::class, 'transfer'])->name('transfer.sale.to.user');
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/customers/{id}', [OrderController::class, 'customer_order'])->name('orders.customer');
        Route::get('/download/{id}', [OrderController::class, 'download'])->name('orders.download');
        Route::get('/payment/print/{id}', [OrderController::class, 'printPayment'])->name('orders.payment.print');
        Route::post('/order/edit{order}', [OrderController::class, 'edit'])->name('orders.edit');
        Route::get('/load', [OrderController::class, 'load'])->name('orders.load');
        Route::get('/edit', [OrderController::class, 'loadEdit'])->name('orders.detail.edit');
        Route::delete('/destroy/item/{orderdetail}', [OrderController::class, 'removeItem'])->name('orders.detail.destroy');
        Route::put('/update/{orderdetail}', [OrderController::class, 'updateOrder'])->name('orders.update');
    });

    Route::group(
        ['prefix' => 'customers'],
        function () {
            Route::group(
                ['prefix' => 'manage/'],
                function () {
                    Route::get('/index', [CustomerController::class, 'index'])->name('customers.index');
                    Route::get('/create', [CustomerController::class, 'create'])->name('customers.create');
                    Route::get('/show/{customer}', [CustomerController::class, 'show'])->name('customers.show');
                    Route::post('/store', [CustomerController::class, 'store'])->name('customers.store');
                    Route::get('/edit/{customer}', [CustomerController::class, 'edit'])->name('customers.edit');
                    Route::post('/update/{customer}', [CustomerController::class, 'update'])->name('customers.update');
                    Route::delete('/delete/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

                    //Import Customers
                    Route::get('/import/form', [CustomerController::class, 'importForm'])->name('customers.import.form');
                    Route::post('/import', [CustomerController::class, 'import'])->name('customers.import');

                }
            );
            Route::get('/opening_balance', [CustomerController::class, 'createOpeningBalance'])->name('customers.create.opening_balance');
            Route::post('/opening_balance/store', [CustomerController::class, 'openingBalanceStore'])->name('opening_balance.store');

            Route::group(
                ['prefix' => 'create/credit_limits'],
                function () {
                    Route::get('/index', [CreditLimitController::class, 'index'])->name('credit_limits.index');
                    Route::get('/create', [CreditLimitController::class, 'create'])->name('credit_limits.create');
                    Route::get('/show/{creditlimit}', [CreditLimitController::class, 'show'])->name('credit_limits.show');
                    Route::post('/store', [CreditLimitController::class, 'store'])->name('credit_limits.store');
                    Route::get('/edit/{creditlimit}', [CreditLimitController::class, 'edit'])->name('credit_limits.edit');
                    Route::put('/update/{creditlimit}', [CreditLimitController::class, 'update'])->name('credit_limits.update');
                    Route::delete('/delete/{creditlimit}', [CreditLimitController::class, 'destroy'])->name('credit_limits.destroy');
                }
            );

            Route::get('/ledger', [CustomerController::class, 'generateCustomerLedger'])->name('customer.ledger');
            Route::get('/ledger/load', [CustomerController::class, 'loadLedger'])->name('ajax.customer.ledger');
            Route::get('/ledger/load/general', [CustomerController::class, 'loadGeneralCustomerLedger'])->name('ajax.general.customer.ledger');
            Route::get('/credit_limit', [CustomerController::class, 'getCustomerCreditLimit'])->name('ajax.load.customer.credit_limit');
            Route::get('/balance', [CustomerController::class, 'getCustomerBalance'])->name('ajax.load.customer.balance');
            Route::post('update/credit_limit', [CustomerController::class, 'updateCreditLimit'])->name('customers.update.credit_limit');
            Route::get('/print/ledger/{from_date}/{to_date}/{customer_id}', [CustomerController::class, 'printLedger'])->name('ajax.customer.print.ledger');
        }
    );
    Route::group(
        ['prefix' => '/receipt-payment'],
        function () {
            Route::get('/list', [ReceiptController::class, 'receipts'])->name('receipt.payments');
            Route::get('/create', [ReceiptController::class, 'createReciept'])->name('create.payment.reciept');
            Route::post('/store', [ReceiptController::class, 'payReciept'])->name('receipt.payment.store');
            Route::delete('/destroy/{ledger}', [ReceiptController::class, 'deleteReceipt'])->name('receipt.payment.destroy');
            Route::put('/update/{ledger}', [ReceiptController::class, 'updateReceipt'])->name('receipt.payment.update');
            Route::post('/search', [ReceiptController::class, 'search'])->name('receipt.payment.search');
            Route::get('/print/receipt/{payment}', [ReceiptController::class, 'printReceipt'])->name('receipt.payment.print');
            Route::get('/print/receipt/pos/{payment}', [ReceiptController::class, 'printPoSPaymentReceipt'])->name('receipt.payment.print.pos');
            Route::get('/load/payers', [ReceiptController::class, 'loadPayers'])->name('ajax.load.payers');
            Route::get('/reverse/{receipt}', [ReceiptController::class, 'reverse'])->name('receipt.payment.reverse');
            Route::get('/show/{receipt}', [ReceiptController::class, 'show'])->name('receipt.payment.show');
            Route::post('/post/{receipt}', [ReceiptController::class, 'post'])->name('receipt.payment.post');
            Route::post('/delete/{receipt}', [ReceiptController::class, 'delete'])->name('receipt.payment.delete');
        }
    );
    Route::group(
        ['prefix' => '/interbanks'],
        function () {
            Route::get('/list', [InterBankController::class, 'list'])->name('interbank.list');
            Route::get('/create', [InterBankController::class, 'create'])->name('create.interbank');
            Route::post('/store', [InterBankController::class, 'store'])->name('interbank.store');
            Route::delete('/destroy/{interbank}', [InterBankController::class, 'destroy'])->name('interbank.destroy');
            Route::put('/update/{interbank}', [InterBankController::class, 'update'])->name('interbank.update');
            Route::get('/edit/{interbank}', [InterBankController::class, 'edit'])->name('interbank.edit');
            Route::post('/search', [InterBankController::class, 'search'])->name('interbank.search');
            Route::get('/print/interbank/{interbank}', [InterBankController::class, 'print'])->name('interbank.print');
            Route::get('/print/interbank/pos/{interbank}', [InterBankController::class, 'printPos'])->name('interbank.print.pos');
            Route::get('/reverse/{interbank}', [InterBankController::class, 'reverse'])->name('interbank.reverse');
            Route::get('/show/{interbank}', [InterBankController::class, 'show'])->name('interbank.show');
            Route::post('/post/{interbank}', [InterBankController::class, 'post'])->name('interbank.post');
            Route::post('/delete/{interbank}', [InterBankController::class, 'delete'])->name('interbank.delete');

        }
    );
    Route::group(
        ['prefix' => '/payment-invoice'],
        function () {
            Route::get('/list', [PaymentController::class, 'payments'])->name('payments.list');
            Route::get('/create', [PaymentController::class, 'makePayment'])->name('create.payment');
            Route::post('/store', [PaymentController::class, 'pay'])->name('payment.store');
            Route::delete('/destroy/{ledger}', [PaymentController::class, 'deletePayment'])->name('payment.destroy');
            Route::put('/update/{ledger}', [PaymentController::class, 'updateReceipt'])->name('payment.update');
            Route::post('/search', [PaymentController::class, 'search'])->name('payment.search');
            Route::get('/print/payment/{payment}', [PaymentController::class, 'printPaymentReceipt'])->name('payment.print');
            Route::get('/print/payment/pos/{payment}', [PaymentController::class, 'printPoSPaymentReceipt'])->name('payment.print.pos');
            Route::get('/reverse/{payment}', [PaymentController::class, 'reverse'])->name('payment.reverse');
            Route::get('/show/{payment}', [PaymentController::class, 'show'])->name('payment.show');
            Route::post('/post/{payment}', [PaymentController::class, 'post'])->name('payment.post');
            Route::post('/delete/{payment}', [PaymentController::class, 'deletePayment'])->name('payment.delete');
        }
    );

    Route::group(
        ['prefix' => 'employees'],
        function () {
            Route::get('/index', [EmployeeController::class, 'index'])->name('employees.index');
            Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
            Route::get('/show/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
            Route::post('/store', [EmployeeController::class, 'store'])->name('employees.store');
            Route::get('/edit/{employee}', [EmployeeController::class, 'edit'])->name('employees.edit');
            Route::put('/update/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::delete('/delete/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        }
    );

    Route::group(
        ['prefix' => 'suppliers'],
        function () {
            Route::group(
                ['prefix' => 'manage'],
                function () {
                    Route::get('/index', [SupplierController::class, 'index'])->name('suppliers.index');
                    Route::get('/create', [SupplierController::class, 'create'])->name('suppliers.create');
                    Route::get('/show/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
                    Route::post('/store', [SupplierController::class, 'store'])->name('suppliers.store');
                    Route::get('/edit/{supplier}', [SupplierController::class, 'edit'])->name('suppliers.edit');
                    Route::put('/update/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
                    Route::delete('/delete/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
                    Route::get('/opening_balance', [SupplierController::class, 'createOpeningBalance'])->name('suppliers.create.opening_balance');
                    Route::post('/opening_balance/store', [SupplierController::class, 'openingBalanceStore'])->name('suppliers.opening_balance.store');
                    Route::get('/get-codes/{type}', [SupplierController::class, 'supplierCode'])->name('suppliers.code');
                    //Import Suppliers
                    Route::get('/import/form', [SupplierController::class, 'importForm'])->name('suppliers.import.form');
                    Route::post('/import', [SupplierController::class, 'import'])->name('suppliers.import');
                }
            );
            Route::group(
                ['prefix' => '/pay'],
                function () {
                    Route::get('/payment', [SupplierController::class, 'supplierPayments'])->name('suppliers.payments');
                    Route::get('/create', [SupplierController::class, 'createSupplierPayment'])->name('suppliers.payment.create');
                    Route::post('/store', [SupplierController::class, 'pay'])->name('suppliers.payment.store');
                    Route::put('/update/{ledger}', [SupplierController::class, 'updatePayment'])->name('suppliers.payment.update');
                    Route::get('/balance', [MisController::class, 'loadSupplierBalance'])->name('ajax.load.supplier.balance');
                    Route::delete('/delete/{ledger}', [SupplierController::class, 'deletePayment'])->name('suppliers.payment.destroy');
                    Route::post('/search', [SupplierController::class, 'searchPayment'])->name('suppliers.payment.search');
                    Route::get('/print/receipt/{payment}', [SupplierController::class, 'printPaymentReceipt'])->name('supplier.payment.print');
                    Route::get('/print/receipt/pos/{payment}', [SupplierController::class, 'printPoSPaymentReceipt'])->name('supplier.payment.print.pos');

                    //Debite Note
                    Route::get('/credit-note', [SupplierController::class, 'supplierCreditNote'])->name('suppliers.credit.note');
                    Route::get('/credit-note/create', [SupplierController::class, 'createCreditNote'])->name('suppliers.credit.note.create');
                    Route::post('/credit-note/store', [SupplierController::class, 'payCreditNote'])->name('suppliers.credit.note.store');
                    Route::put('/credit-note/update/{ledger}', [SupplierController::class, 'updateCreditnote'])->name('suppliers.credit.note.update');
                    Route::delete('/credit-note/delete/{ledger}', [SupplierController::class, 'deleteCreditNote'])->name('suppliers.credit.note.destroy');
                    Route::post('/credit-note/search', [SupplierController::class, 'searchCreditNote'])->name('suppliers.credit.note.search');
                    Route::get('/credit-note/print/receipt/{payment}', [SupplierController::class, 'printCreditNoteReceipt'])->name('supplier.credit.note.print');
                }
            );
            Route::get('/ledger', [SupplierController::class, 'generateSupplierLedger'])->name('supplier.ledger');
            Route::get('/ledger/load', [SupplierController::class, 'loadLedger'])->name('ajax.supplier.ledger');
            Route::get('/ledger/load/general', [SupplierController::class, 'loadGeneralSupplierLedger'])->name('ajax.general.supplier.ledger');
            Route::get('/print/ledger/{from_date}/{to_date}/{supplier_id}', [SupplierController::class, 'printLedger'])->name('ajax.supplier.print.ledger');

        }

    );
    Route::group(
        ['prefix' => 'users'],
        function () {
            Route::group(
                ['prefix' => 'manage', 'middleware' => 'auth'],
                function () {
                    Route::get('/index', [UserController::class, 'index'])->name('users.index');
                    Route::get('/create', [UserController::class, 'create'])->name('users.create');
                    Route::get('/show/{user}', [UserController::class, 'show'])->name('users.show');
                    Route::post('/store', [UserController::class, 'store'])->name('users.store');
                    Route::get('/edit/{user}', [UserController::class, 'edit'])->name('users.edit');
                    Route::put('/update/{user}', [UserController::class, 'update'])->name('users.update');
                    Route::delete('/delete/{user}', [UserController::class, 'destroy'])->name('users.destroy');
                    Route::match(['GET','POST'],'/reset-password/{user?}', [UserController::class, 'resetPassword'])->name('users.reset-password');
                    //Import Users
                    Route::get('/import/form', [UserController::class, 'importForm'])->name('users.import.form');
                    Route::post('/import', [UserController::class, 'import'])->name('users.import');

                    //Roles
                    Route::controller(RoleController::class)->group(
                        function () {
                        Route::get('role/view', 'index')->name('roles.index');
                        Route::get('role/show/{role}', 'show')->name('roles.show');
                        Route::get('role/create', 'create')->name('roles.create');
                        Route::post('role/store', 'store')->name('roles.store');
                        Route::get('role/edit/{role}', 'edit')->name('roles.edit');
                        Route::put('role/update/{role}', 'update')->name('roles.update');
                        Route::delete('role/delete/{role}', 'destroy')->name('roles.destroy');
                    }
                    );
                    Route::controller(PermissionController::class)->group(
                        function () {
                            Route::get('permission/view', 'index')->name('permissions.index');
                            Route::get('permission/show/{permission}', 'show')->name('permissions.show');
                            Route::get('permission/create', 'create')->name('permissions.create');
                            Route::post('permission/store', 'store')->name('permissions.store');
                            Route::get('permission/edit/{permission}', 'edit')->name('permissions.edit');
                            Route::put('permission/update/{permission}', 'update')->name('permissions.update');
                            Route::delete('permission/delete/{permission}', 'destroy')->name('permissions.destroy');
                        }
                    );
                    Route::get('/role/permission', [RoleHasPermissionController::class, 'index'])->name('role-permission');
                    Route::post('/role/permission/create', [RoleHasPermissionController::class, 'store'])->name('role-permission.store');
                    Route::get('/assign/permissions-to-roles', [RoleHasPermissionController::class, 'show'])->name('role-permission.show');
                    Route::any('/user/site/access/{user}', [UserController::class, 'userSiteAccess'])->name('user.site.access');
                    Route::get('/user/{user}/{status}/', [UserController::class, 'changeAccountStatus'])->name('user.account.status');
                    Route::post('/user/assign-role/{user}', [UserController::class, 'storeUserRole'])->name('user.store.role');
                    Route::post('/user/assign-permission', [UserController::class, 'storeUserPermission'])->name('user.store.permission');
                }
            );
            Route::controller(CompanyController::class)->group(
                function () {
                    Route::get('company/view', 'index')->name('companies.index');
                    Route::get('company/show/{company}', 'show')->name('companies.show');
                    Route::get('company/create', 'create')->name('companies.create');
                    Route::post('company/store', 'store')->name('companies.store');
                    Route::get('company/edit/{company}', 'edit')->name('companies.edit');
                    Route::put('company/update/{company}', 'update')->name('companies.update');
                    Route::delete('company/delete/{company}', 'destroy')->name('companies.destroy');
                }
            );
        }
    );

    Route::controller(ProfileController::class)->group(
        function () {
            Route::get('profile', 'index')->name('profile');
            Route::put('profile/update/{user}', 'updateProfile')->name('profile.update');
            Route::post('profile/picture/', 'uploadPhoto')->name('upload.profile.picture');
            Route::post('profile/change-password/{user}', 'changePassword')->name('account.change.password');
            Route::post('branch/switch/{user}', 'switchBranch')->name('branch.swtich');
        }
    );

    Route::group(['prefix' => 'loan'], function () {
        Route::group(
            ['prefix' => 'collector'],
            function () {
                Route::get('/index', [LoanCollectorController::class, 'index'])->name('loan_collectors.index');
                Route::get('/create', [LoanCollectorController::class, 'create'])->name('loan_collectors.create');
                Route::get('/show/{loancollector}', [LoanCollectorController::class, 'show'])->name('loan_collectors.show');
                Route::post('/store', [LoanCollectorController::class, 'store'])->name('loan_collectors.store');
                Route::get('/edit/{loancollector}', [LoanCollectorController::class, 'edit'])->name('loan_collectors.edit');
                Route::put('/update/{loancollector}', [LoanCollectorController::class, 'update'])->name('loan_collectors.update');
                Route::delete('/delete/{loancollector}', [LoanCollectorController::class, 'destroy'])->name('loan_collectors.destroy');
                Route::post('/search', [LoanCollectorController::class, 'search'])->name('loan_collectors.search');
            }
        );
        Route::group(
            ['prefix' => 'grant'],
            function () {
                Route::get('/index', [LoanController::class, 'index'])->name('loans.index');
                Route::get('/create', [LoanController::class, 'create'])->name('loans.create');
                Route::get('/show/{loan}', [LoanController::class, 'show'])->name('loans.show');
                Route::post('/store', [LoanController::class, 'store'])->name('loans.store');
                Route::get('/edit/{loan}', [LoanController::class, 'edit'])->name('loans.edit');
                Route::put('/update/{loan}', [LoanController::class, 'update'])->name('loans.update');
                Route::delete('/delete/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');
                Route::get('/balance', [MisController::class, 'loadLoanBalance'])->name('ajax.load.loan.collector.balance');
                Route::post('/search', [LoanCollectorController::class, 'search'])->name('loan_grants.search');
            }
        );
        Route::group(
            ['prefix' => 'payment'],
            function () {
                Route::get('/index', [LoanPaymentController::class, 'index'])->name('loan_payments.index');
                Route::get('/create', [LoanPaymentController::class, 'create'])->name('loan_payments.create');
                Route::get('/show/{loanpayment}', [LoanPaymentController::class, 'show'])->name('loan_payments.show');
                Route::post('/store', [LoanPaymentController::class, 'store'])->name('loan_payments.store');
                Route::get('/edit/{loanpayment}', [LoanPaymentController::class, 'edit'])->name('loan_payments.edit');
                Route::put('/update/{loanpayment}', [LoanPaymentController::class, 'update'])->name('loan_payments.update');
                Route::delete('/delete/{loanpayment}', [LoanPaymentController::class, 'destroy'])->name('loan_payments.destroy');
                Route::post('/search', [LoanPaymentController::class, 'search'])->name('loan_payments.search');
                Route::get('/print/{loanpayment}', [LoanPaymentController::class, 'print'])->name('loan_payments.print');
            }
        );
    });
    //Ajax

    Route::get('load/products', [MisController::class, 'loadproducts'])->name('ajax.loadproducts');
    Route::get('load/products/available', [MisController::class, 'loadAvailableProducts'])->name('ajax.load.available.products');
    Route::get('load/branches', [MisController::class, 'loadbranches'])->name('ajax.loadbranches');
    Route::get('load/stores', [MisController::class, 'loadStores'])->name('ajax.loadStores');
    Route::get('load/bank_account', [MisController::class, 'loadBankAccounts'])->name('ajax.loadBankAccounts');
    Route::get('load/account_name', [MisController::class, 'loadAccountName'])->name('ajax.load.account.name');
    Route::get('load/account_balance', [MisController::class, 'loadAccountBalance'])->name('ajax.load.account.balance');
    Route::get('load/customers', [MisController::class, 'loadCustomers'])->name('ajax.load.customers');
    Route::get('load/customer-orders', [MisController::class, 'loadCustomerOrders'])->name('ajax.load.customer-orders');
    Route::get('load/suppliers', [MisController::class, 'loadSuppliers'])->name('ajax.load.suppliers');
    Route::get('load/store/product', [MisController::class, 'loadStoreProducts'])->name('ajax.load.store-products');
    Route::get('load/store/product/availabe', [MisController::class, 'loadStoreProductQuantity'])->name('ajax.load.quantity.available');
    Route::get('load/supplier/invoices', [MisController::class, 'loadSupplierInvoices'])->name('ajax.loadSupplierInvoices');
    Route::get('load/customer/invoices', [MisController::class, 'loadCustomerUnPaidInvoices'])->name('ajax.loadCustomerInvoices');

    Route::group(
        ['prefix' => 'reports'],
        function () {
            //INtersite Stock Transfer Report
            Route::get('/intersite/transfer', [ReportController::class, 'intersiteTransfer'])->name('intersite.transfer.reports');
            Route::get('/intersite/transfer/load', [ReportController::class, 'loadIntersiteTransferReport'])->name('ajax.load.intersite.transfer.reports');
            Route::get('/intersite/transfer/print/{from_date}/{to_date}/{source_branch_id}/{destination_branch_id}/{category_id}/{product_id}', [ReportController::class, 'printIntersiteTransfer'])->name('ajax.print.intersite.transfer.reports');

            Route::get('/interstore/transfer', [ReportController::class, 'interstoreTransfer'])->name('interstore.transfer.reports');
            Route::get('/interstore/transfer/load', [ReportController::class, 'loadInterstoreTransferReport'])->name('ajax.load.interstore.transfer.reports');
            Route::get('/interstore/transfer/print/{from_date}/{to_date}/{company_id}/{branch_id}/{source_store_id}/{destination_store_id}/{category_id}/{product_id}', [ReportController::class, 'printInterstoreTransfer'])->name('ajax.print.interstore.transfer.reports');

            //Current Stock Report
            Route::get('/sc/current-stock', [ReportController::class, 'generateCurrentStock'])->name('current.stock.report');
            Route::get('/sc/current-stock/load', [ReportController::class, 'loadCurrentStock'])->name('ajax.current.stock.report');
            Route::get('/sc/current-stock/print/{company_id}/{branch_id}/{store_id}/{category_id}/{product_id}', [ReportController::class, 'printCurrentStock'])->name('ajax.current.stock.report.print');
            Route::post('/available-stock', [ReportController::class, 'printAvailableStock'])->name('stock.available');

            //Stock  History
            Route::get('/sc/stock/history', [ReportController::class, 'stockHistory'])->name('stock.history.reports');
            Route::get('/sc/stock/history/load', [ReportController::class, 'loadStockHistoryReport'])->name('ajax.load.stock.history.reports');
            Route::get('/sc/stock/history/print/{from_date}/{to_date}/{type}/{company_id}/{branch_id}/{store_id}/{category_id}/{product_id}', [ReportController::class, 'printStockHistory'])->name('ajax.print.stock.history.reports');

            //Store  Ledger
            Route::get('/sc/store-ledger/in', [ReportController::class, 'storeLedger'])->name('store.ledger.reports');
            Route::get('/sc/store-ledger/load', [ReportController::class, 'loadStoreLedger'])->name('ajax.load.store.ledger.reports');
            Route::get('/sc/stock-ledger/print/{company_id}/{branch_id}/{store_id}/{category_id}/{product_id}', [ReportController::class, 'printstoreLedger'])->name('ajax.print.store.ledger.reports');

            //Stock  Adjustment
            Route::get('/sc/stock-adjustment/in', [ReportController::class, 'stockAdjustment'])->name('stock.adjustment.reports');
            Route::get('/sc/stock-adjustment/load', [ReportController::class, 'loadStockAdjustment'])->name('ajax.load.stock.adjustment.reports');
            Route::get('/sc/stock-adjustment/print/{from_date}/{to_date}/{company_id}/{branch_id}/{store_id}/{category_id}/{product_id}', [ReportController::class, 'printStockAdjustment'])->name('ajax.print.stock.adjustment.reports');

            //Previous Stock Balances Report
            Route::get('/sc/stock/balances', [ReportController::class, 'previousStockBalance'])->name('stock.balances.report');
            Route::get('/sc/stock/balances/load', [ReportController::class, 'loadPreviousStockBalance'])->name('ajax.stock.balances.report');
            Route::get('/sc/stock/balances/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/', [ReportController::class, 'printPreviousStockBalance'])->name('ajax.stock.balances.report.print');

            //Stock Ledger Report
            Route::get('/sc/stock/ledger', [ReportController::class, 'stockLedger'])->name('stock.ledger.reports');
            Route::get('/sc/stock/ledger/load', [ReportController::class, 'loadStockLedger'])->name('ajax.load.stock.ledger.reports');
            Route::get('/sc/stock/ledger/print/{from_date}/{to_date}/{company_id}/{branch_id}/{store_id}/{product_id}/', [ReportController::class, 'printStockLedger'])->name('ajax.stock.ledger.report.print');

            //General Sales Report
            Route::get('/sa/sales/general', [ReportController::class, 'generalSaleReport'])->name('general.sales.report');
            Route::get('/sa/sales/general/load', [ReportController::class, 'loadGeneralSaleReport'])->name('ajax.general.sales.report');
            Route::get('/sa/sales/general/print/{from_date}/{to_date}/{company_id}/{branch_id}/{store_id}/{category_id}/{product_id}/{customer_id}/{type}', [ReportController::class, 'printGeneralSaleReport'])->name('ajax.general.sales.report.print');

            //Category Sales Report
            Route::get('/sa/sales/category', [ReportController::class, 'categorySaleReport'])->name('sales.report.by.category');
            Route::get('/sa/sales/category/load', [ReportController::class, 'loadCategorySaleReport'])->name('ajax.category.sales.report');
            Route::get('/sa/sales/category/print/{from_date}/{to_date}/{company_id}/{branch_id}/{category_id1}', [ReportController::class, 'printCategorySaleReport'])->name('ajax.category.sales.report.print');

             //Sales by Category by Site Report
             Route::get('/site/sales/category', [ReportController::class, 'categorySaleBySiteReport'])->name('sales.report.by.category.site');
             Route::get('/site/sales/category/load', [ReportController::class, 'loadCategorySaleBySiteReport'])->name('ajax.category.site.sales.report');
             Route::get('/site/sales/category/print/{from_date}/{to_date}/{company_id}/{branch_id}/{category_id1}', [ReportController::class, 'printCategorySaleBySiteReport'])->name('ajax.category.site.sales.report.print');

            //Staff Sales Report
            Route::get('/sa/sales/staff', [ReportController::class, 'staffSaleReport'])->name('staff.sales.report');
            Route::get('/sa/sales/staff/load', [ReportController::class, 'loadStaffSaleReport'])->name('ajax.staff.sales.report');
            Route::get('/sa/sales/staff/print/{from_date}/{to_date}/{company_id}/{branch_id}/{store_id}/{category_id}/{product_id}/{staff_id}', [ReportController::class, 'printStaffSaleReport'])->name('ajax.staff.sales.report.print');

             //Staff Sales Report
             Route::get('/sa/sales/relation_officer', [ReportController::class, 'staffRelationOfficerReport'])->name('relation_officer.report');
             Route::get('/sa/sales/relation_officer/load', [ReportController::class, 'loadRelationOfficerReport'])->name('ajax.relation_officer.sales.report');
             Route::get('/sa/sales/relation_officer/print/{from_date}/{to_date}/{company_id}/{branch_id}/{category_id}/{staff_id}', [ReportController::class, 'printRelationOfficerReport'])->name('ajax.relation_officer.report.print');
 
            //CUstoomer Sale with common names Report
            Route::get('/sa/sales/customer/sale/common-name', [ReportController::class, 'customerSaleReport'])->name('customer.sale.reports');
            Route::get('/sa/sales/customer/sale/common-name/load', [ReportController::class, 'loadCustomerSaleReport'])->name('ajax.load.customer.sale.reports');
            Route::get('/sa/sales/customer/sale/common-name/print/{from_date}/{to_date}/{company_id}/{branch_id}/{store_id}/{category_id}/{product_id}/{customer}/{matching}', [ReportController::class, 'printCustomerSaleReport'])->name('ajax.customer.sale.report.print');


            //Most Sold Item by Amount and by Quantity Report
            Route::get('/sa/sales/most/sold-item', [ReportController::class, 'mostSoldItemReport'])->name('most.sold.item.reports');
            Route::get('/sa/sales/most/sold-item/load', [ReportController::class, 'loadMostSoldItemReport'])->name('ajax.load.most.sold.item.reports');
            Route::get('/sa/sales/most/sold-item/print/{from_date}/{to_date}/{company_id}/{branch_id}/{type}/{number_limit}/', [ReportController::class, 'printMostSoldItemReport'])->name('ajax.most.sold.item.print');


            //Total Items Sold to Customer
            Route::get('/sa/sales/total-item', [ReportController::class, 'totalItemSoldReport'])->name('total.item.sold.report');
            Route::get('/sa/sales/total-item/load', [ReportController::class, 'loadItemSoldReport'])->name('ajax.total.item.sold.report');
            Route::get('/sa/sales/total-item/print/{company_id}/{branch_id}/{from_date}/{to_date}/{category_id}/{product_id}/{customer_id}', [ReportController::class, 'printItemSoldReport'])->name('ajax.total.item.sold.report.print');

            //Discount Granted Report
            Route::get('/sa/sales/discount-granted', [ReportController::class, 'trackDiscount'])->name('discount.granted.reports');
            Route::get('/sa/sales/discount-granted/load', [ReportController::class, 'loadTrackDiscount'])->name('ajax.load.discount.granted.reports');
            Route::get('/sa/sales/discount-granted/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/{customer_id}/{credit_walkedin}/{lower}/{upper}', [ReportController::class, 'printTrackDiscount'])->name('ajax.discount.granted.report.print');

            //Credit Note Report
            Route::get('/sa/sales/credit/note', [ReportController::class, 'creditNoteReport'])->name('credit.note.reports');
            Route::get('/sa/sales/credit/note/load', [ReportController::class, 'loadCreditNoteReport'])->name('ajax.load.credit.note.reports');
            Route::get('/sa/sales/credit/note/print/{from_date}/{to_date}/{company_id}/{branch_id}/{status}', [ReportController::class, 'printCreditNoteReport'])->name('ajax.credit.note.report.print');

            //List of Invoices Report
            Route::get('/sa/sales/list/invoices', [ReportController::class, 'invoiceReport'])->name('invoice.list.reports');
            Route::get('/sa/sales/list/invoices/load', [ReportController::class, 'loadInvoiceReport'])->name('ajax.load.invoice.list.reports');
            Route::get('/sa/sales/list/invoices/print/{from_date}/{to_date}/{company_id}/{branch_id}/{status}', [ReportController::class, 'printInvoiceReport'])->name('ajax.invoice.list.report.print');

            //List of Invoice Lines Report
            Route::get('/sa/sales/list/invoices/lines', [ReportController::class, 'invoiceLinesReport'])->name('invoice.lines.reports');
            Route::get('/sa/sales/list/invoices/lines/load', [ReportController::class, 'loadInvoiceLinesReport'])->name('ajax.load.invoice.lines.reports');
            Route::get('/sa/sales/list/invoices/lines/print/{from_date}/{to_date}/{branch_id}/{status}', [ReportController::class, 'printInvoiceLinesReport'])->name('ajax.invoice.lines.report.print');


            //List of Orders Report
            Route::get('/sa/sales/list/orders', [ReportController::class, 'orderReport'])->name('order.list.reports');
            Route::get('/sa/sales/list/orders/load', [ReportController::class, 'loadOrderReport'])->name('ajax.load.order.list.reports');
            Route::get('/sa/sales/list/orders/print/{from_date}/{to_date}/{company_id}/{branch_id}/{status}', [ReportController::class, 'printOrderReport'])->name('ajax.order.list.report.print');

            //List of Order Lines Report
            Route::get('/sa/sales/list/order/lines', [ReportController::class, 'orderLinesReport'])->name('order.lines.reports');
            Route::get('/sa/sales/list/order/lines/load', [ReportController::class, 'loadOrderLinesReport'])->name('ajax.load.order.lines.reports');
            Route::get('/sa/sales/list/order/lines/print/{from_date}/{to_date}/{company_id}/{branch_id}/{status}', [ReportController::class, 'printOrderLinesReport'])->name('ajax.order.lines.report.print');


            //Begin Customer Sales Analysis Report

            //Customer Debt Report
            Route::get('/ca/customer/debt', [ReportController::class, 'customerDebtReport'])->name('customer.total.debt.reports');
            Route::get('/ca/customer/debt/load', [ReportController::class, 'loadCustomerDebtReport'])->name('ajax.load.customer.total.debt.reports');
            Route::get('/ca/customer/debt/print/{from_date}/{to_date}/{customer}/', [ReportController::class, 'printCustomerDebtReport'])->name('ajax.customer.total.debt.report.print');

            //Customer Last Transaction Report
            Route::get('/ca/customer/last-transaction', [ReportController::class, 'lastTransaction'])->name('customer.last.transaction.reports');
            Route::get('/ca/customer/last-transaction/load', [ReportController::class, 'loadLastTransaction'])->name('ajax.load.customer.last.transaction.reports');
            Route::get('/ca/customer/last-transaction/print/{company_id}/{branch_id}/{customer}/', [ReportController::class, 'printLastTransaction'])->name('ajax.customer.last.transaction.report.print');


            //Customer Balance Detail Report
            Route::get('/ca/customer/ageing-report', [ReportController::class, 'ageingReport'])->name('customer.ageing.reports');
            Route::get('/ca/customer/ageing-report/load', [ReportController::class, 'loadAgeingReport'])->name('ajax.load.customer.ageing.reports');
            Route::get('/ca/customer/ageing-report/print/{from_date}/{to_date}/{company_id}/{branch_id}/{customer_id}', [ReportController::class, 'printAgeingReport'])->name('ajax.customer.ageing.report.print');

            //Customer List Report
            Route::get('/ca/customer/list', [ReportController::class, 'customerList'])->name('customer.list.reports');
            Route::get('/ca/customer/list/load', [ReportController::class, 'loadCustomerListReport'])->name('ajax.load.customer.list.reports');
            Route::get('/ca/customer/load/print/{company_id}/{branch_id}', [ReportController::class, 'printCustomerListReport'])->name('ajax.customer.list.report.print');

            //Customer with Credit Limit Report
            Route::get('/ca/customer/credit_limit/list', [ReportController::class, 'customerCreditLimit'])->name('customer.credit_limit.reports');
            Route::get('/ca/customer/credit_limit/list/load', [ReportController::class, 'loadCustomerCreditLimitReport'])->name('ajax.load.customer.credit_limit.reports');
            Route::get('/ca/customer/credit_limit/load/print/{company_id}/{branch_id}', [ReportController::class, 'printCustomerCreditLimitReport'])->name('ajax.customer.credit_limit.report.print');

            //Customer exceeded Credit Limit Report
            Route::get('/ca/customer/exceeded/credit_limit/list', [ReportController::class, 'customerExceededCreditLimit'])->name('customer.exceeded_credit_limit.reports');
            Route::get('/ca/customer/exceeded/credit_limit/list/load', [ReportController::class, 'loadCustomerExceededCreditLimitReport'])->name('ajax.load.customer.exceeded_credit_limit.reports');
            Route::get('/ca/customer/exceeded/credit_limit/load/print/{company_id}/{branch_id}', [ReportController::class, 'printCustomerExceededCreditLimitReport'])->name('ajax.customer.exceeded_credit_limit.report.print');


            //Purchases  Report
            Route::get('/inventory/invoice', [ReportController::class, 'purchaseInvoiceReport'])->name('purchase.invoice.report');
            Route::get('/inventory/invoice/load', [ReportController::class, 'loadPurchaseInvoiceReport'])->name('ajax.purchase.invoice.report');
            Route::get('/inventory/invoice/print/lines/{from_date}/{to_date}/{company_id}/{branch_id}/{supplier_id}/{status}', [ReportController::class, 'printPurchaseInvoiceReport'])->name('ajax.purchase.invoice.report.print');

            //Purchase Request  Report
            Route::get('/inventory/request', [ReportController::class, 'purchaseRequestReport'])->name('purchase.request.report');
            Route::get('/inventory/request/load', [ReportController::class, 'loadPurchaseRequestReport'])->name('ajax.purchase.request.report');
            Route::get('/inventory/request/print/{from_date}/{to_date}/{company_id}/{branch_id}/{category_id}/{product_id}/{supplier_id}/{status}', [ReportController::class, 'printPurchaseRequestReport'])->name('ajax.purchase.request.report.print');

            //Goods in Transit  Report
            Route::get('/inventory/gooods/in-transit', [ReportController::class, 'goodsInTransitReport'])->name('goods.in.transit.report');
            Route::get('/inventory/gooods/in-transit/load', [ReportController::class, 'loadGoodsInTransitReport'])->name('ajax.goods.in.transit.report');
            Route::get('/inventory/gooods/in-transit/print/{from_date}/{to_date}/{company_id}/{branch_id}/{status}', [ReportController::class, 'printGoodsInTransitReport'])->name('ajax.goods.in.transit.report.print');


            //Purchases  Lines Report
            Route::get('/inventory/invoice/lines', [ReportController::class, 'purchaseInvoiceLinesReport'])->name('purchase.invoice.lines.report');
            Route::get('/inventory/invoice/lines/load', [ReportController::class, 'loadPurchaseInvoiceLinesReport'])->name('ajax.purchase.invoice.lines.report');
            Route::get('/inventory/invoice/lines/print/lines/{from_date}/{to_date}/{company_id}/{branch_id}/{store_id}/{category_id}/{product_id}/{supplier_id}/{status}', [ReportController::class, 'printPurchaseInvoiceLinesReport'])->name('ajax.purchase.invoice.lines.report.print');

            //Additional Invoices Report
            Route::get('/inventory/additional/invoice', [ReportController::class, 'additionalInvoiceReport'])->name('additional.invoice.report');
            Route::get('/inventory/additional/invoice/load', [ReportController::class, 'loadAdditionalInvoiceReport'])->name('ajax.additional.invoice.report');
            Route::get('/inventory/additional/invoice/print/lines/{from_date}/{to_date}/{company_id}/{branch_id}/{supplier_id}/{status}', [ReportController::class, 'printAdditionalInvoiceReport'])->name('ajax.additional.invoice.report.print');

            //Return & Debit  Report
            Route::get('/inventory/returndebit', [ReportController::class, 'returnDebitReport'])->name('return.debit.report');
            Route::get('/inventory/returndebit/load', [ReportController::class, 'loadReturnDebitReport'])->name('ajax.load.return.debit.report');
            Route::get('/inventory/returndebit/print/{from_date}/{to_date}/{company_id}/{branch_id}/{status}', [ReportController::class, 'printReturnDebitReport'])->name('ajax.return.debit.report.print');

             //Product Expiring Date  Report
             Route::get('/inventory/expiring', [ReportController::class, 'expiryReport'])->name('expiry.date.report');
             Route::get('/inventory/expiring/load', [ReportController::class, 'loadExpiryReport'])->name('ajax.load.expiry.date.report');
             Route::get('/inventory/expiring/print/{from_date}/{to_date}/{company_id}/{branch_id}', [ReportController::class, 'printExpiryReport'])->name('ajax.expiry.date.report.print');


            //Purchase Check Report
            Route::get('/inventory/transaction/check', [ReportController::class, 'purchaseCheckReport'])->name('purchase.transaction.check.report');
            Route::get('/inventory/transaction/check/load', [ReportController::class, 'loadPurchaseCheckReport'])->name('ajax.purchase.transaction.check.report');
            Route::get('/inventory/transaction/check/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/{supplier_id}/{purchase_mode}', [ReportController::class, 'printPurchaseCheckReport'])->name('ajax.purchase.transaction.check.report.print');

            //Total Purchases Report
            Route::get('/pa/total/puchases/item', [ReportController::class, 'totalPurchaseItemReport'])->name('total.purchase.item.report');
            Route::get('/pa/total/puchases/item/load', [ReportController::class, 'loadTotalPurchaseItemReport'])->name('ajax.total.purchase.item.report');
            Route::get('/pa/total/puchases/item/print/{from_date}/{to_date}/{store_id}', [ReportController::class, 'printTotalPurchaseItemReport'])->name('ajax.total.purchase.item.report.print');

            Route::get('/activity/{user}', [ReportController::class, 'logs'])->name('users.logs');
            Route::get('/activity/load/logs', [ReportController::class, 'viewLogs'])->name('user.activity.logs');
            Route::get('/activity/load/logs/print/{from_date}/{to_date}/{user_id}', [ReportController::class, 'printLogs'])->name('user.activity.logs.print');

             //Purchase Request  Report
             Route::get('/product/valuation', [ReportController::class, 'productValuation'])->name('product.valuation.report');
             Route::get('/product/valuation/load', [ReportController::class, 'loadProductValuationReport'])->name('ajax.product.valuation.report');
             Route::post('/product/valuation/print/', [ReportController::class, 'printProductValuationReport'])->name('ajax.product.valuation.report.print');
            //User Ledger and Loans

            //Loan Balances
            Route::get('/us/user/balance', [ReportController::class, 'loanBalance'])->name('user.loan.balance.report');
            Route::get('/us/user/balance/load', [ReportController::class, 'loadLoanBalance'])->name('ajax.load.user.loan.balance.report');
            Route::get('/us/user/balance/print/{collector_id}', [ReportController::class, 'printLoanBalance'])->name('ajax.user.loan.balance.report.print');

            //Loan History
            Route::get('/us/user/loan/history', [ReportController::class, 'loanHistory'])->name('user.loan.history.report');
            Route::get('/us/user/loan/history/load', [ReportController::class, 'loadLoanHistory'])->name('ajax.load.user.loan.history.report');
            Route::get('/us/user/loan/history/print/{collector_id}', [ReportController::class, 'printLoanHistory'])->name('ajax.user.loan.history.report.print');

            //New Reports
            //AP/AR
            //Account Balances and Statements
            //Loan History
            Route::group(['prefix' => 'ap_ar'], function () {
                //Account balances
                Route::get('/account_balances', [ReportController::class, 'accountBalance'])->name('account.balance.report');
                Route::get('/account_balances/load', [ReportController::class, 'loadAccountBalance'])->name('ajax.load.account.balance.report');
                Route::get('/account_balances/print/{date}//{account_type}/{company_id}/{branch_id}', [ReportController::class, 'printAccountBalance'])->name('ajax.account.balance.report.print');

                //Account Statement
                Route::get('/account_statement', [ReportController::class, 'accountStatement'])->name('account.statement.report');
                Route::get('/account_statement/load', [ReportController::class, 'loadAccountStatement'])->name('ajax.load.account.statement.report');
                Route::get('/account_statement/print/{from_date}/{to_date}/{company_id}/{branch_id}/{payer_id}/{type}', [ReportController::class, 'printAccountStatement'])->name('ajax.account.statement.report.print');

                //Income Statement
                Route::get('/income_statement', [ReportController::class, 'incomeStatement'])->name('income.statement.report');
                Route::get('/income_statement/load', [ReportController::class, 'loadIncomeStatement'])->name('ajax.load.income.statement.report');
                Route::get('/income_statement/print/{from_month}/{to_month}/{income_year}/{company_id}/{branch_id}/{category_id1}/{category_id2}', [ReportController::class, 'printIncomeStatement'])->name('ajax.income.statement.report.print');

                //Trial Balance
                Route::get('/trial_balance', [ReportController::class, 'trialBalance'])->name('trial.balance.report');
                Route::get('/trial_balance/load', [ReportController::class, 'loadTrialBalance'])->name('ajax.load.trial.balance.report');
                Route::get('/trial_balance/print/{from}/{to}/{company_id}/{branch_id}', [ReportController::class, 'printTrialBalance'])->name('ajax.print.trial.balance.report');

                 //Balance Sheet
                 Route::get('/balance_sheet', [ReportController::class, 'balanceSheet'])->name('balance.sheet.report');
                 Route::get('/balance_sheet/load', [ReportController::class, 'loadBalanceSheet'])->name('ajax.load.balance.sheet.report');
                 Route::get('/balance_sheet/print/{to}/{company_id}/{branch_id}', [ReportController::class, 'printBalanceSheet'])->name('ajax.print.balance.sheet.report');


                //Cash Flow
                Route::get('/cash_flow', [ReportController::class, 'cashFlow'])->name('cash.flow.report');
                Route::get('/cash_flow/load', [ReportController::class, 'loadCashFlow'])->name('ajax.load.cash.flow.report');
                Route::get('/cash_flow/print/{from}/{to}/{company_id}/{branch_id}', [ReportController::class, 'printCashFlow'])->name('ajax.print.cash.flow.report');

                //Daily Remittance
                Route::get('/remittance', [ReportController::class, 'remittance'])->name('remittance.report');
                Route::get('/remittance/load', [ReportController::class, 'loadRemittance'])->name('ajax.load.remittance.report');
                Route::get('/remittance/print/{from}/{to}/{company_id}/{branch_id}/{payee_id}/{user_id}', [ReportController::class, 'printRemittance'])->name('ajax.print.remittance.report');

                //Document Status
                Route::get('/document_status', [ReportController::class, 'documentStatus'])->name('document.status.report');
                Route::get('/document_status/load', [ReportController::class, 'loadDocumentStatus'])->name('ajax.load.document.status.report');
                Route::get('/document_status/print/{from}/{to}/{company_id}/{branch_id}/{status}/{type}', [ReportController::class, 'printdocumentStatus'])->name('ajax.print.document.status.report');

            });

        }
    );
    Route::group(['prefix' => 'ap_ar_account'], function () {
        Route::group(
            ['prefix' => 'chart_of_accounts'],
            function () {
                Route::get('/index', [ChartOfAccountController::class, 'index'])->name('chart_of_accounts.index');
                Route::get('/create', [ChartOfAccountController::class, 'create'])->name('chart_of_accounts.create');
                Route::post('/store', [ChartOfAccountController::class, 'store'])->name('chart_of_accounts.store');
                Route::get('/edit/{chartofaccount}', [ChartOfAccountController::class, 'edit'])->name('chart_of_accounts.edit');
                Route::put('/update/{chartofaccount}', [ChartOfAccountController::class, 'update'])->name('chart_of_accounts.update');
                Route::delete('/delete/{chartofaccount}', [ChartOfAccountController::class, 'destroy'])->name('chart_of_accounts.destroy');

                //Import CoAs
                Route::get('/import/form', [ChartOfAccountController::class, 'importForm'])->name('chart_of_accounts.import.form');
                Route::post('/import', [ChartOfAccountController::class, 'import'])->name('chart_of_accounts.import');
            }
        );
        Route::group(
            ['prefix' => 'general_accounts'],
            function () {
                Route::get('/index', [GeneralAccountController::class, 'index'])->name('general_accounts.index');
                Route::get('/create', [GeneralAccountController::class, 'create'])->name('general_accounts.create');
                Route::post('/store', [GeneralAccountController::class, 'store'])->name('general_accounts.store');
                Route::get('/edit/{generalaccount}', [GeneralAccountController::class, 'edit'])->name('general_accounts.edit');
                Route::put('/update/{generalaccount}', [GeneralAccountController::class, 'update'])->name('general_accounts.update');
                Route::delete('/delete/{generalaccount}', [GeneralAccountController::class, 'destroy'])->name('general_accounts.destroy');

                Route::get('/import/form', [GeneralAccountController::class, 'importForm'])->name('general_accounts.import.form');
                Route::post('/import', [GeneralAccountController::class, 'import'])->name('general_accounts.import');
            }
        );

    });

    Route::prefix('journals')->group(function () {
        Route::get('/', [JournalController::class, 'index'])->name('journal.index');
        Route::post('/', [JournalController::class, 'store'])->name('journal.store');
        Route::get('/create', [JournalController::class, 'create'])->name('journal.create');
        Route::get('/show/{journal}', [JournalController::class, 'show'])->name('journal.show');
        Route::put('/update/{journal}', [JournalController::class, 'update'])->name('journal.update');
        Route::get('/post/{journal}', [JournalController::class, 'post'])->name('journal.post');
        Route::get('/reverse/{journal}', [JournalController::class, 'reverse'])->name('journal.reverse');
        Route::get('/print/{journal}', [JournalController::class, 'print'])->name('journal.print');
        Route::get('/edit/{journal}', [JournalController::class, 'edit'])->name('journal.edit');
        Route::get('/delete/{journal}', [JournalController::class, 'delete'])->name('journal.delete');
    });

    Route::prefix('inventory')->group(function () {

        // Route::prefix('purchases')->group(function () {
        //     Route::get('/', [App\Http\Controllers\Inventory\PurchaseController::class, 'index'])->name('inventories.purchases.index');
        //     Route::get('/create', [App\Http\Controllers\Inventory\PurchaseController::class, 'create'])->name('inventories.purchases.create');
        //     Route::post('/ajax/create', [App\Http\Controllers\Inventory\PurchaseController::class, 'createAjax'])->name('inventories.purchases.ajax.create');
        //     Route::post('/store', [App\Http\Controllers\Inventory\PurchaseController::class, 'store'])->name('inventories.purchases.store');
        // });
        //Debit Notes

        Route::group(['prefix' => 'additional-invoice'], function () {
            Route::get('/', [AdditionalInvoiceController::class, 'index'])->name('purchase.additional-invoice.index');
            Route::get('/create', [AdditionalInvoiceController::class, 'create'])->name('purchase.additional-invoice.create');
            Route::get('/edit/{invoice}', [AdditionalInvoiceController::class, 'edit'])->name('purchase.additional-invoice.edit');
            Route::get('/show/{invoice}', [AdditionalInvoiceController::class, 'show'])->name('purchase.additional-invoice.show');
            Route::post('/store', [AdditionalInvoiceController::class, 'store'])->name('purchase.additional-invoice.store');
            Route::post('/post/{invoice}', [AdditionalInvoiceController::class, 'post'])->name('purchase.additional-invoice.post');
            Route::post('/reverse/{invoice}', [AdditionalInvoiceController::class, 'reverse'])->name('purchase.additional-invoice.reverse');
            Route::get('/print/{invoice}', [AdditionalInvoiceController::class, 'print'])->name('purchase.additional-invoice.print');
            Route::post('/delete/{invoice}', [AdditionalInvoiceController::class, 'delete'])->name('purchase.additional-invoice.delete');

        });

        Route::group(['prefix' => 'debit-note'], function () {
            Route::get('/', [DebitNoteController::class, 'supplierDebitNote'])->name('suppliers.debit.note');
            Route::get('/create/{purchase?}', [DebitNoteController::class, 'createDebitNote'])->name('suppliers.debit.note.create');
            Route::post('/store', [DebitNoteController::class, 'payDebitNote'])->name('suppliers.debit.note.store');
            Route::put('/update/{ledger}', [DebitNoteController::class, 'updateDebitNote'])->name('suppliers.debit.note.update');
            Route::delete('/delete/{ledger}', [DebitNoteController::class, 'deleteDebitNote'])->name('suppliers.debit.note.destroy');
            Route::post('/search', [DebitNoteController::class, 'searchDebitNote'])->name('suppliers.debit.note.search');
            Route::get('/load-invoices', [DebitNoteController::class, 'loadInvoices'])->name('suppliers.load.order.invoices');
            Route::get('/load_cart', [DebitNoteController::class, 'loadToCart'])->name('suppliers.load.order.cart');
            Route::post('/cart', [DebitNoteController::class, 'addToCart'])->name('debit.note.cart.store');
            Route::put('/update-cart', [DebitNoteController::class, 'updateCart'])->name('debit.note.cart.update');
            Route::delete('/remove/{id}', [DebitNoteController::class, 'removeCart'])->name('debit.note.cart.remove');
            Route::get('/print/receipt/{debit_note}', [DebitNoteController::class, 'printDebitNoteReceipt'])->name('suppliers.debit.note.print');
            Route::post('/expense', [DebitNoteController::class, 'expense'])->name('update.purchases.expense.ajax.create');
        });

        //Return and Debit
        Route::group(
            ['prefix' => 'return-debit'],
            function () {
                Route::get('/', [ReturnDebitController::class, 'returnDebit'])->name('return.debit');
                Route::get('/create/{purchase?}', [ReturnDebitController::class, 'createReturnDebit'])->name('return.debit.create');
                Route::post('/store', [ReturnDebitController::class, 'storeReturnDebit'])->name('return.debit.store');
                Route::post('/post/{returndebit}', [ReturnDebitController::class, 'post'])->name('return.debit.post');
                Route::get('/show/{returndebit}', [ReturnDebitController::class, 'show'])->name('return.debit.show');
                Route::get('/edit/{returndebit}', [ReturnDebitController::class, 'edit'])->name('return.debit.edit');
                Route::put('/update/{returndebit}', [ReturnDebitController::class, 'updateReturnDebit'])->name('return.debit.update');
                Route::delete('/delete/{returndebit}', [ReturnDebitController::class, 'deletReturnDebit'])->name('return.debit.destroy');
                Route::post('/search', [ReturnDebitController::class, 'searchReturnDebit'])->name('return.debit.search');
                Route::get('/load_invoices', [ReturnDebitController::class, 'loadInvoices'])->name('load.order.invoices');
                Route::get('/load_cart', [ReturnDebitController::class, 'loadToCart'])->name('load.order.cart');
                Route::post('/cart', [ReturnDebitController::class, 'addToCart'])->name('return.debit.cart.store');
                Route::get('/update-cart', [ReturnDebitController::class, 'updateCart'])->name('return.debit.cart.update');
                Route::delete('/remove/{id}', [ReturnDebitController::class, 'removeCart'])->name('return.debit.cart.remove');
                Route::get('/print/receipt/{returnDebit}', [ReturnDebitController::class, 'printReturnDebitReceipt'])->name('return.debit.print');
            }
        );
        Route::group(
            ['prefix' => 'purchases/grn'],
            function () {
                Route::get('/index', [PurchaseGRNController::class, 'index'])->name('purchases.index');
                Route::get('/create', [PurchaseGRNController::class, 'create'])->name('purchases.create');
                Route::get('/show/{purchase}', [PurchaseGRNController::class, 'show'])->name('purchases.show');
                Route::post('/store', [PurchaseGRNController::class, 'store'])->name('purchases.store');
                Route::get('/edit/{purchase}', [PurchaseGRNController::class, 'edit'])->name('purchases.edit');
                Route::put('/update/{purchase}', [PurchaseGRNController::class, 'update'])->name('purchases.update');
                Route::put('/purchase/update-cart', [PurchaseGRNController::class, 'updateCart'])->name('purchase.cart.update');
                Route::delete('/delete/{purchase}', [PurchaseGRNController::class, 'destroy'])->name('purchases.destroy');
                Route::post('/cart', [PurchaseGRNController::class, 'addToCart'])->name('purchases.cart.store');
                Route::delete('/remove/{id}', [PurchaseGRNController::class, 'removeCart'])->name('purchases.cart.remove');
                Route::post('/clear', [PurchaseGRNController::class, 'clearAllCart'])->name('purchases.cart.clear');
                Route::get('/print/{purchase}', [PurchaseGRNController::class, 'printInvoice'])->name('purchase.print');
                Route::post('/waybill/{purchase}', [PurchaseGRNController::class, 'generateWaybill'])->name('purchase.generate.waybill');
                Route::get('/print/waybill/{purchase}', [PurchaseGRNController::class, 'printWaybill'])->name('purchase.waybill.print');
                Route::post('/search', [PurchaseGRNController::class, 'search'])->name('purchases.search');
                Route::post('/expense', [PurchaseGRNController::class, 'expense'])->name('purchases.expense.ajax.create');
                Route::delete('/expense/delete/{expense}', [PurchaseGRNController::class, 'deleteExpense'])->name('delete.purchase.expense');
                Route::post('/approve/{purchase}', [PurchaseGRNController::class, 'approve'])->name('purchase.approve');
                Route::post('/post/{purchase}', [PurchaseGRNController::class, 'post'])->name('purchase.post');
            }
        );
        Route::group(['prefix' => 'purchases/request'], function () {
            Route::get('/index', [PurchaseRequestController::class, 'index'])->name('purchases.request.index');
            Route::get('/create', [PurchaseRequestController::class, 'create'])->name('purchases.request.create');
            Route::get('/show/{purchase}', [PurchaseRequestController::class, 'show'])->name('purchases.request.show');
            Route::post('/store', [PurchaseRequestController::class, 'store'])->name('purchases.request.store');
            Route::get('/edit/{purchase}', [PurchaseRequestController::class, 'edit'])->name('purchases.request.edit');
            Route::put('/update/{purchase}', [PurchaseRequestController::class, 'update'])->name('purchases.request.update');
            Route::put('/purchase/update-cart', [PurchaseRequestController::class, 'updateCart'])->name('purchase.request.cart.update');
            Route::delete('/delete/{purchase}', [PurchaseRequestController::class, 'destroy'])->name('purchases.request.destroy');
            Route::post('/cart', [PurchaseRequestController::class, 'addToCart'])->name('purchases.request.cart.store');
            Route::delete('/remove/{id}', [PurchaseRequestController::class, 'removeCart'])->name('purchases.request.cart.remove');
            Route::post('/clear', [PurchaseRequestController::class, 'clearAllCart'])->name('purchases.request.cart.clear');
            Route::get('/print/{purchase}', [PurchaseRequestController::class, 'printInvoice'])->name('purchase.request.print');
            Route::post('/waybill/{purchase}', [PurchaseRequestController::class, 'generateWaybill'])->name('purchase.request.generate.waybill');
            Route::get('/print/waybill/{purchase}', [PurchaseRequestController::class, 'printWaybill'])->name('purchase.request.waybill.print');
            Route::post('/search', [PurchaseRequestController::class, 'search'])->name('purchases.request.search');
            Route::post('/close/{purchase}', [PurchaseRequestController::class, 'close'])->name('purchase.request.close');
            Route::post('/approve/{purchase}', [PurchaseRequestController::class, 'link'])->name('purchase.request.link');
        });

        Route::group(
            ['prefix' => 'interstore/transfer'],
            function () {
                Route::get('/index', [InterStoreTransferController::class, 'index'])->name('interstore.index');
                Route::post('/search', [InterStoreTransferController::class, 'search'])->name('interstore.search');
                Route::get('/create', [InterStoreTransferController::class, 'create'])->name('interstore.create');
                Route::get('/show/{transferproduct}', [InterStoreTransferController::class, 'show'])->name('interstore.show');
                Route::post('/store', [InterStoreTransferController::class, 'store'])->name('interstore.store');
                Route::get('/edit/{transferproduct}', [InterStoreTransferController::class, 'edit'])->name('interstore.edit');
                Route::put('/update/{transferproduct}', [InterStoreTransferController::class, 'update'])->name('interstore.update');
                Route::delete('/delete/{transferproduct}', [InterStoreTransferController::class, 'destroy'])->name('interstore.destroy');
                Route::post('/cart', [InterStoreTransferController::class, 'addToCart'])->name('interstore.cart');
                Route::delete('/remove/{id}', [InterStoreTransferController::class, 'removeCart'])->name('interstore.cart.remove');
                Route::post('/clear', [InterStoreTransferController::class, 'clearAllCart'])->name('interstore.cart.clear');
                Route::get('/print/{interstoreTransfer}', [InterStoreTransferController::class, 'print'])->name('interstore.print');

            }
        );
        Route::group(['prefix' => 'intersite/transfer'], function () {
            Route::get('/index', [InterSiteTransferController::class, 'index'])->name('intersite.index');
            Route::get('/received', [InterSiteTransferController::class, 'received'])->name('intersite.received');
            Route::post('/search', [InterSiteTransferController::class, 'search'])->name('intersite.search');
            Route::get('/create', [InterSiteTransferController::class, 'create'])->name('intersite.create');
            Route::get('/show/{intersiteTransfer}', [InterSiteTransferController::class, 'show'])->name('intersite.show');
            Route::post('/store', [InterSiteTransferController::class, 'store'])->name('intersite.store');
            Route::get('/edit/{intersite}', [InterSiteTransferController::class, 'edit'])->name('intersite.edit');

            Route::post('/cart', [InterSiteTransferController::class, 'addToCart'])->name('intersite.cart');
            Route::delete('/remove/{id}', [InterSiteTransferController::class, 'removeCart'])->name('intersite.cart.remove');
            Route::post('/clear', [InterSiteTransferController::class, 'clearAllCart'])->name('intersite.cart.clear');
            Route::get('/print/{intersite}', [InterSiteTransferController::class, 'printStockTransfer'])->name('intersite.print');
            Route::post('/delete/{intersite}', [InterSiteTransferController::class, 'delete'])->name('intersite.delete');
            Route::post('/post/{intersite}', [InterSiteTransferController::class, 'post'])->name('intersite.post');
            Route::post('/receive/{intersite}', [InterSiteTransferController::class, 'receive'])->name('intersite.receive');
            Route::post('/add-to-store', [InterSiteTransferController::class, 'addToStore'])->name('intersite.add-to-store');
        });

    });

    //inventory

    Route::get('/sample/report', [App\Http\Controllers\HomeController::class, 'sampleReport'])->name('sample-report');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('customer/search', 'CustomerController@search')->name('customer.search');
    Route::get('/notification', [NotificationController::class, 'notify'])->name('notification');
    Route::post('/notification/send', [NotificationController::class, 'send'])->name('notification.send');

    Route::get('generate/product_code', [MisController::class, 'generateProductCode'])->name('generate.productCode');
    Route::get('generate/customer_code', [MisController::class, 'nextCustomerCode'])->name('generate.customerCode');
    Route::prefix('ajax')->group(function () {
        Route::get('cart/load', [CartController::class, 'loadCartItem'])->name('ajax.cart.load');
        Route::get('cart/add', [CartController::class, 'addCartItem'])->name('ajax.cart.add');
        Route::get('cart/update/{id}', [CartController::class, 'updateCartItem'])->name('ajax.cart.update');
        Route::get('cart/delete/{id}', [CartController::class, 'deleteCartItem'])->name('ajax.cart.delete');
        Route::get('cart/change-store/{id}', [CartController::class, 'changeStoreCartItem'])->name('ajax.cart.change-store');
    });


});
