<?php

use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\GeneralAccountController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\InterBankController;
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


/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider within a group which | contains the "web" middleware group. Now create something great! | */
Auth::routes();
Route::get('/', function () {
    return view('auth/login');
});

Route::middleware('auth')->group(function () {
    Route::group(['prefix' => 'transaction'], function () {
        Route::group(
            ['prefix' => 'stock_adjustment'],
            function () {
                Route::get('/index', [StockAdjustmentController::class, 'index'])->name('stock_adjustments.index');
                Route::get('/create', [StockAdjustmentController::class, 'create'])->name('stock_adjustments.create');
                Route::get('/show/{stockadjustment}', [StockAdjustmentController::class, 'show'])->name('stock_adjustments.show');
                Route::post('/store', [StockAdjustmentController::class, 'store'])->name('stock_adjustments.store');
                Route::get('/edit/{stockadjustment}', [StockAdjustmentController::class, 'edit'])->name('stock_adjustments.edit');
                Route::put('/update/{stockadjustment}', [StockAdjustmentController::class, 'update'])->name('stock_adjustments.update');
                Route::delete('/delete/{stockadjustment}', [StockAdjustmentController::class, 'destroy'])->name('stock_adjustments.destroy');
                Route::post('/cart', [StockAdjustmentController::class, 'addToCart'])->name('stock_adjustments.cart');
                Route::delete('/remove/{id}', [StockAdjustmentController::class, 'removeCart'])->name('stock_adjustments.cart.remove');
                Route::post('/clear', [StockAdjustmentController::class, 'clearAllCart'])->name('stock_adjustments.cart.clear');
                Route::get('/print/{refno}', [StockAdjustmentController::class, 'printStockAdjustment'])->name('stock_adjustments.print');
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
            ['prefix' => 'payment_modes'],
            function () {
                Route::get('/index', [PaymentModeController::class, 'index'])->name('payment_modes.index');
                Route::get('/create', [PaymentModeController::class, 'create'])->name('payment_modes.create');
                Route::get('/show/{paymentmode}', [PaymentModeController::class, 'show'])->name('payment_modes.show');
                Route::post('/store', [PaymentModeController::class, 'store'])->name('payment_modes.store');
                Route::get('/edit/{paymentmode}', [PaymentModeController::class, 'edit'])->name('payment_modes.edit');
                Route::put('/update/{paymentmode}', [PaymentModeController::class, 'update'])->name('payment_modes.update');
                Route::delete('/delete/{paymentmode}', [PaymentModeController::class, 'destroy'])->name('payment_modes.destroy');
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
            }
        );
        Route::group(
            ['prefix' => 'bank_accounts'],
            function () {
                Route::get('/index', [BankAccountController::class, 'index'])->name('bank_accounts.index');
                Route::get('/create', [BankAccountController::class, 'create'])->name('bank_accounts.create');
                Route::get('/show/{bankaccount}', [BankAccountController::class, 'show'])->name('bank_accounts.show');
                Route::post('/store', [BankAccountController::class, 'store'])->name('bank_accounts.store');
                Route::get('/edit/{bankaccount}', [BankAccountController::class, 'edit'])->name('bank_accounts.edit');
                Route::put('/update/{bankaccount}', [BankAccountController::class, 'update'])->name('bank_accounts.update');
                Route::delete('/delete/{bankaccount}', [BankAccountController::class, 'destroy'])->name('bank_accounts.destroy');
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

                        Route::get('/import', [BranchProductPriceController::class, 'importForm'])->name('price.import.form');
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
    Route::group(['prefix' => 'sales'], function () {
        Route::get('/cart/list', [CartController::class, 'cartList'])->name('cart.list');
        Route::post('/cart', [CartController::class, 'addToCart'])->name('cart.store');
        Route::put('/update-cart', [CartController::class, 'updateCart'])->name('cart.update');
        Route::delete('/remove/{id}', [CartController::class, 'removeCart'])->name('cart.remove');
        Route::post('/clear', [CartController::class, 'clearAllCart'])->name('cart.clear');

        Route::get('pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('pos/whole-sale', [PosController::class, 'wholeSale'])->name('pos.whole.sale');
        Route::get('pos/edit/{order}', [PosController::class, 'edit'])->name('pos.edit');
        Route::get('barcode/search/product', [PosController::class, 'barcodeSearch'])->name('barcode.search.product');

        Route::get('proformer', [PosController::class, 'index'])->name('proformer.index');
        Route::get('order_invoice', [PosController::class, 'index'])->name('order.invoice.index');

        Route::post('/invoice', [InvoiceController::class, 'final_invoice'])->name('invoice.create');
        Route::post('/preformer/create', [InvoiceController::class, 'final_proformer'])->name('proformer.create');
        Route::post('/order-invoice/create', [InvoiceController::class, 'final_order_invoice'])->name('order.invoice.create');
        Route::put('/invoice/update/{order}', [InvoiceController::class, 'updateInvoice'])->name('invoice.update');
        Route::get('/print/{customer_id}', [InvoiceController::class, 'print'])->name('invoice.print');
        Route::get('/order-print/{order_id}', [InvoiceController::class, 'order_print'])->name('invoice.order_print');
        Route::get('/proformer/print/{customer_id}', [InvoiceController::class, 'print_proformer'])->name('proformer.print');
        Route::get('/order-invoice/print/{customer_id}', [InvoiceController::class, 'print_order_invoice'])->name('order.invoice.print');
        Route::post('/invoice-final', [InvoiceController::class, 'final_invoice'])->name('invoice.final_invoice');
        Route::get('/waybill/order-print/{order_id}', [InvoiceController::class, 'waybill_print'])->name('waybill.order_print');
        Route::get('/pos/order-print/{order_id}', [InvoiceController::class, 'pos_print'])->name('pos.order_print');

        Route::get('/sales-today', [OrderController::class, 'today_sales'])->name('sales.today');
        Route::get('/sales-monthly/{month?}', [OrderController::class, 'monthly_sales'])->name('sales.monthly');
        Route::get('/sales-total', [OrderController::class, 'total_sales'])->name('sales.total');

        Route::get('load/store/products', [MisController::class, 'loadStoreProducts'])->name('ajax.load.store.products');

        Route::post('/product/search', [OrderController::class, 'search'])->name('sales_products.search');
        Route::post('/proformer/search', [OrderController::class, 'proformer_search'])->name('proformer.search');
        Route::post('/order-invoice/search', [OrderController::class, 'order_invoice_search'])->name('order.invoice.search');
        Route::post('/product/verify', [OrderController::class, 'verify'])->name('sales_products.verify');

        Route::post('/transfer/to-user', [OrderController::class, 'transfer'])->name('transfer.sale.to.user');


    });
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/show/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/proformer/show/{id}', [OrderController::class, 'proformer_show'])->name('proformer.show');
        Route::get('/order-invoice/show/{id}', [OrderController::class, 'order_invoice_show'])->name('order.invoice.show');
        Route::get('/customers/{id}', [OrderController::class, 'customer_order'])->name('orders.customer');
        Route::get('/approved', [OrderController::class, 'approved_order'])->name('orders.approved');
        Route::get('/proformer', [OrderController::class, 'proformer_list'])->name('proformer.list');
        Route::get('/order-invoice', [OrderController::class, 'order_invoice_list'])->name('order.invoice.list');
        Route::delete('/delete/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::delete('/order-invoice/delete/{order}', [OrderController::class, 'destroy_order_invoice'])->name('order.invoice.destroy');
        Route::delete('/proformer-invoice/delete/{order}', [OrderController::class, 'destroy_proformer'])->name('proformer.destroy');
        Route::get('/download/{id}', [OrderController::class, 'download'])->name('orders.download');
        Route::get('/payment/print/{id}', [OrderController::class, 'printPayment'])->name('orders.payment.print');
        Route::post('/order/edit{order}', [OrderController::class, 'edit'])->name('orders.edit');
        Route::get('/load/', [OrderController::class, 'load'])->name('orders.load');
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
                    Route::put('/update/{customer}', [CustomerController::class, 'update'])->name('customers.update');
                    Route::delete('/delete/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

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
            Route::post('/post/{receipt}', [ReceiptController::class, 'post'])->name('receipt.payment.post');
        }
    );
    Route::group(
        ['prefix' => '/interbanks'],
        function () {
            Route::get('/list', [InterBankController::class, 'list'])->name('interbank.list');
            Route::get('/create', [InterBankController::class, 'create'])->name('create.interbank');
            Route::post('/store', [InterBankController::class, 'store'])->name('interbank.store');
            Route::delete('/destroy/{interbank}', [InterBankController::class, 'destroy'])->name('interbank.destroy');
            Route::get('/delete/{interbank}', [InterBankController::class, 'destroy'])->name('interbank.delete');
            Route::put('/update/{interbank}', [InterBankController::class, 'update'])->name('interbank.update');
            Route::get('/edit/{interbank}', [InterBankController::class, 'edit'])->name('interbank.edit');
            Route::post('/search', [InterBankController::class, 'search'])->name('interbank.search');
            Route::get('/print/interbank/{interbank}', [InterBankController::class, 'print'])->name('interbank.print');
            Route::get('/print/interbank/pos/{interbank}', [InterBankController::class, 'printPos'])->name('interbank.print.pos');
            Route::get('/reverse/{interbank}', [InterBankController::class, 'reverse'])->name('interbank.reverse');
        }
    );
    Route::group(
        ['prefix' => '/payment-invoice'],
        function () {
            Route::get('/list', [PaymentController::class, 'payments'])->name('payments.list');
            Route::get('/create', [PaymentController::class, 'makePayment'])->name('create.payment');
            Route::post('/store', [PaymentController::class, 'pay'])->name('payment.store');
            Route::delete('/destroy/{ledger}', [PaymentController::class, 'deletePayment'])->name('payment.destroy');
            Route::post('/delete/{payment}', [PaymentController::class, 'delete'])->name('payment.delete');
            Route::put('/update/{ledger}', [PaymentController::class, 'updateReceipt'])->name('payment.update');
            Route::post('/search', [PaymentController::class, 'search'])->name('payment.search');
            Route::get('/print/payment/{payment}', [PaymentController::class, 'printPaymentReceipt'])->name('payment.print');
            Route::get('/print/payment/pos/{payment}', [PaymentController::class, 'printPoSPaymentReceipt'])->name('payment.print.pos');
            Route::get('/reverse/{payment}', [PaymentController::class, 'reverse'])->name('payment.reverse');
            Route::post('/post/{payment}', [PaymentController::class, 'post'])->name('payment.post');
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

                    //Credit Note
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
                    //Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
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

    Route::group(['prefix' => 'cash'], function () {
        Route::group(
            ['prefix' => 'movement'],
            function () {
                Route::get('/index', [CashMovementController::class, 'index'])->name('cash_movements.index');
                Route::get('/create', [CashMovementController::class, 'create'])->name('cash_movements.create');
                Route::get('/show/{cashmovement}', [CashMovementController::class, 'show'])->name('cash_movements.show');
                Route::post('/store', [CashMovementController::class, 'store'])->name('cash_movements.store');
                Route::get('/edit/{cashmovement}', [CashMovementController::class, 'edit'])->name('cash_movements.edit');
                Route::put('/update/{cashmovement}', [CashMovementController::class, 'update'])->name('cash_movements.update');
                Route::delete('/delete/{cashmovement}', [CashMovementController::class, 'destroy'])->name('cash_movements.destroy');


                Route::get('/deposit/create', [CashMovementController::class, 'createDeposit'])->name('deposits.create');
                Route::get('/deposit/show/{deposit}', [CashMovementController::class, 'showDeposit'])->name('deposits.show');
                Route::post('/deposit/store', [CashMovementController::class, 'storeDeposit'])->name('deposits.store');
                Route::get('/deposit/edit/{deposit}', [CashMovementController::class, 'editDeposit'])->name('deposits.edit');
                Route::put('/deposit/update/{deposit}', [CashMovementController::class, 'updateDeposit'])->name('deposits.update');
                Route::delete('/deposit/delete/{deposit}', [CashMovementController::class, 'destroyDeposit'])->name('deposits.destroy');

                Route::get('/withdraw/create', [CashMovementController::class, 'createWithdraw'])->name('withdraw.create');
                Route::get('/withdraw/show/{withdraw}', [CashMovementController::class, 'showWithdraw'])->name('withdraw.show');
                Route::post('/withdraw/store', [CashMovementController::class, 'storeWithdraw'])->name('withdraw.store');
                Route::get('/withdraw/edit/{withdraw}', [CashMovementController::class, 'editWithdraw'])->name('withdraw.edit');
                Route::put('/withdraw/update/{withdraw}', [CashMovementController::class, 'updateWithdraw'])->name('withdraw.update');
                Route::delete('/withdraw/delete/{withdraw}', [CashMovementController::class, 'destroyWithdraw'])->name('withdraw.destroy');

                Route::get('/print/{cashmovement}', [CashMovementController::class, 'print'])->name('cash_movements.print');

                Route::post('/search', [CashMovementController::class, 'search'])->name('cash_movements.search');
            }
        );
    });

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
    Route::get('load/suppliers', [MisController::class, 'loadSuppliers'])->name('ajax.load.suppliers');
    Route::get('load/store/product', [MisController::class, 'loadStoreProducts'])->name('ajax.load.store-products');
    Route::get('load/store/product/availabe', [MisController::class, 'loadStoreProductQuantity'])->name('ajax.load.quantity.available');
    Route::get('load/supplier/invoices', [MisController::class, 'loadSupplierInvoices'])->name('ajax.loadSupplierInvoices');
    Route::get('load/customer/invoices', [MisController::class, 'loadCustomerUnPaidInvoices'])->name('ajax.loadCustomerInvoices');


    Route::group(
        ['prefix' => 'reports'],
        function () {
            //Stock Transfer Report
            Route::get('/stock/transfer', [ReportController::class, 'stockTransfer'])->name('stock.transfer.reports');
            Route::get('/stock/transfer/load', [ReportController::class, 'loadStockTransferReport'])->name('ajax.load.stock.transfer.reports');
            Route::get('/stock/transfer/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/{from_to}', [ReportController::class, 'printStockTransfer'])->name('ajax.print.stock.transfer.reports');

            //Bank Ledger Report
            Route::get('/be/bank-ledger', [ReportController::class, 'generateBankLedger'])->name('bank.ledger');
            Route::get('/be/bank-ledger/load', [ReportController::class, 'loadBankLedger'])->name('ajax.bank.ledger');
            Route::get('/be/bank-ledger/print/{from_date}/{to_date}/{bank_id}', [ReportController::class, 'printBankLedger'])->name('ajax.bank.print.ledger');

            //Bank Deposit Report
            Route::get('/be/bank-deposit', [ReportController::class, 'generateBankDeposit'])->name('bank.deposit.report');
            Route::get('/be/bank-desposit/load', [ReportController::class, 'loadBankDeposit'])->name('ajax.bank.deposit.report');
            Route::get('/be/bank-deposit/print/{from_date}/{to_date}', [ReportController::class, 'printBankDeposit'])->name('ajax.bank.deposit.report.print');

            //Bank Withdaw Report
            Route::get('/be/bank-withdraw', [ReportController::class, 'generateBankWithdraw'])->name('bank.withdraw.report');
            Route::get('/be/bank-withdraw/load', [ReportController::class, 'loadBankWithdraw'])->name('ajax.bank.withdraw.report');
            Route::get('/be/bank-withdraw/print/{from_date}/{to_date}', [ReportController::class, 'printBankWithdraw'])->name('ajax.bank.withdraw.report.print');

            //Bank Withdaw Report
            Route::get('/be/cash-transfer', [ReportController::class, 'generateCashTransfer'])->name('cash.transfer.report');
            Route::get('/be/cash-transfer/load', [ReportController::class, 'loadCashTransfer'])->name('ajax.cash.transfer.report');
            Route::get('/be/cash-transfer/print/{from_date}/{to_date}/{user_id}', [ReportController::class, 'printCashTransfer'])->name('ajax.cash.trasnfer.report.print');

            //Bank Withdaw Report
            Route::get('/be/bank-balance', [ReportController::class, 'generateBankBalance'])->name('bank.balance.report');
            Route::get('/be/bank-balance/print', [ReportController::class, 'printBankBalance'])->name('bank.balance.report.print');

            //Cheque Collected Report
            Route::get('/be/cheque-collected', [ReportController::class, 'generateChequeCollected'])->name('cheque.collected.report');
            Route::get('/cheque-collected/load', [ReportController::class, 'loadChequeCollected'])->name('ajax.cheque.collected.report');
            Route::get('/cheque-collected/print/{from_date}/{to_date}', [ReportController::class, 'printChequeCollected'])->name('ajax.cheque.collected.report.print');

            //Consolidated Expense Report
            Route::get('/consolidated-expense', [ReportController::class, 'generateConsolidatedExpense'])->name('consolidated.expense.report');
            Route::get('/be/consolidated-expense/load', [ReportController::class, 'loadConsolidatedExpense'])->name('ajax.consolidated.expense.report');
            Route::get('/be/consolidated-expense/print/{from_date}/{to_date}/{item_id}', [ReportController::class, 'printConsolidatedExpense'])->name('ajax.consolidated.expense.report.print');

            //Expense Item Report
            Route::get('/be/expense-item', [ReportController::class, 'generateExpenseItem'])->name('expense.item.report');
            Route::get('/be/expense-item/load', [ReportController::class, 'loadExpenseItem'])->name('ajax.expense.item.report');
            Route::get('/be/expense-item/print/{from_date}/{to_date}/{item_id}', [ReportController::class, 'printExpenseItem'])->name('ajax.expense.item.report.print');

            //Daily Report
            Route::get('/be/daily-report', [ReportController::class, 'dailyReport'])->name('daily.report');
            Route::get('/be/daily-report/load', [ReportController::class, 'loadDailyReport'])->name('ajax.load.daily.report');
            Route::get('/be/daily-report/print/{from_date}/{to_date}', [ReportController::class, 'printDailyReport'])->name('ajax.daily.report.print');

            //Current Stock Report
            Route::get('/sc/current-stock', [ReportController::class, 'generateCurrentStock'])->name('current.stock.report');
            Route::get('/sc/current-stock/load', [ReportController::class, 'loadCurrentStock'])->name('ajax.current.stock.report');
            Route::get('/sc/current-stock/print/{store_id}/{category_id}/{product_id}', [ReportController::class, 'printCurrentStock'])->name('ajax.current.stock.report.print');
            Route::post('/available-stock', [ReportController::class, 'printAvailableStock'])->name('stock.available');

            //Stock  In Report (Purchase)
            Route::get('/sc/stock/in', [ReportController::class, 'stockIn'])->name('stock.in.reports');
            Route::get('/sc/stock/in/load', [ReportController::class, 'loadStockInReport'])->name('ajax.load.stock.in.reports');
            Route::get('/sc/stock/in/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/', [ReportController::class, 'printStockIn'])->name('ajax.print.stock.in.reports');

            //Store  Ledger
            Route::get('/sc/store-ledger/in', [ReportController::class, 'storeLedger'])->name('store.ledger.reports');
            Route::get('/sc/store-ledger/load', [ReportController::class, 'loadStoreLedger'])->name('ajax.load.store.ledger.reports');
            Route::get('/sc/stock-ledger/print/{store_id}/{category_id}/{product_id}', [ReportController::class, 'printstoreLedger'])->name('ajax.print.store.ledger.reports');

            //Stock  Adjustment
            Route::get('/sc/stock-adjustment/in', [ReportController::class, 'stockAdjustment'])->name('stock.adjustment.reports');
            Route::get('/sc/stock-adjustment/load', [ReportController::class, 'loadStockAdjustment'])->name('ajax.load.stock.adjustment.reports');
            Route::get('/sc/stock-adjustment/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}', [ReportController::class, 'printStockAdjustment'])->name('ajax.print.stock.adjustment.reports');

            //Previous Stock Balances Report
            Route::get('/sc/stock/balances', [ReportController::class, 'previousStockBalance'])->name('stock.balances.report');
            Route::get('/sc/stock/balances/load', [ReportController::class, 'loadPreviousStockBalance'])->name('ajax.stock.balances.report');
            Route::get('/sc/stock/balances/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/', [ReportController::class, 'printPreviousStockBalance'])->name('ajax.stock.balances.report.print');

            //Stock Ledger Report
            Route::get('/sc/stock/ledger', [ReportController::class, 'stockLedger'])->name('stock.ledger.reports');
            Route::get('/sc/stock/ledger/load', [ReportController::class, 'loadStockLedger'])->name('ajax.load.stock.ledger.reports');
            Route::get('/sc/stock/ledger/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/', [ReportController::class, 'printStockLedger'])->name('ajax.stock.ledger.report.print');

            //General Sales Report
            Route::get('/sa/sales', [ReportController::class, 'generalSaleReport'])->name('general.sales.report');
            Route::get('/sa/sales/load', [ReportController::class, 'loadGeneralSaleReport'])->name('ajax.general.sales.report');
            Route::get('/sa/sales/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/{customer_id}/{payment_mode}/{credit_walkedin}', [ReportController::class, 'printGeneralSaleReport'])->name('ajax.general.sales.report.print');

            //Staff Sales Report
            Route::get('/sa/sales/staff', [ReportController::class, 'staffSaleReport'])->name('staff.sales.report');
            Route::get('/sa/sales/staff/load', [ReportController::class, 'loadStaffSaleReport'])->name('ajax.staff.sales.report');
            Route::get('/sa/sales/staff/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/{staff_id}/{payment_mode}/', [ReportController::class, 'printStaffSaleReport'])->name('ajax.staff.sales.report.print');

            //CUstoomer Sale with common names Report
            Route::get('/sa/customer/sale/common-name', [ReportController::class, 'customerSaleReport'])->name('customer.sale.reports');
            Route::get('/sa/customer/sale/common-name/load', [ReportController::class, 'loadCustomerSaleReport'])->name('ajax.load.customer.sale.reports');
            Route::get('/sa/customer/sale/common-name/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/{payment_mode}/{customer}/{credit_walkedin}/{matching}/', [ReportController::class, 'printCustomerSaleReport'])->name('ajax.customer.sale.report.print');

            //Debtpr Balance Report
            Route::get('/sa/debtor/balance', [ReportController::class, 'debtorBalanceReport'])->name('debtor.balance.reports');
            Route::get('/sa/debtor/balance/load', [ReportController::class, 'loadDebtorBalanceReport'])->name('ajax.load.debtor.balance.reports');
            Route::get('/sa/debtor/balance/print/{from_date}/{to_date}/{customer}/', [ReportController::class, 'printDebtorBalanceReport'])->name('ajax.debtor.balance.report.print');

            //Most Sold Item by Amount Report
            Route::get('/sa/most/sold-item', [ReportController::class, 'mostSoldItemReport'])->name('most.sold.item.reports');
            Route::get('/sa/most/sold-item/load', [ReportController::class, 'loadMostSoldItemReport'])->name('ajax.load.most.sold.item.reports');
            Route::get('/sa/most/sold-item/print/{from_date}/{to_date}/{number_limit}/', [ReportController::class, 'printMostSoldItemReport'])->name('ajax.most.sold.item.print');

            //Most Sold Item by Quantity Report
            Route::get('/sa/most/sold-item/qty', [ReportController::class, 'mostSoldItemQuantityReport'])->name('most.sold.item.quantity.reports');
            Route::get('/sa/most/sold-item/qty/load', [ReportController::class, 'loadSoldItemQuantityReport'])->name('ajax.load.most.sold.item.quantity.reports');
            Route::get('/sa/most/sold-item/qty/print/{from_date}/{to_date}/{number_limit}/', [ReportController::class, 'printSoldItemQuantityReport'])->name('ajax.most.sold.item.quantity.print');

            //Total Items Sold to Customer
            Route::get('/sa/total-item', [ReportController::class, 'totalItemSoldReport'])->name('total.item.sold.report');
            Route::get('/sa/total-item/load', [ReportController::class, 'loadItemSoldReport'])->name('ajax.total.item.sold.report');
            Route::get('/sa/total-item/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/{customer_id}/{credit_walkedin}', [ReportController::class, 'printItemSoldReport'])->name('ajax.total.item.sold.report.print');

            //Discount Granted Report
            Route::get('/sa/discount-granted', [ReportController::class, 'trackDiscount'])->name('discount.granted.reports');
            Route::get('/sa/discount-granted/load', [ReportController::class, 'loadTrackDiscount'])->name('ajax.load.discount.granted.reports');
            Route::get('/sa/discount-granted/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/{customer_id}/{credit_walkedin}/{lower}/{upper}', [ReportController::class, 'printTrackDiscount'])->name('ajax.discount.granted.report.print');


            //Begin Customer Sales Analysis Report

            //Customer Debt Report
            Route::get('/ca/customer/debt', [ReportController::class, 'customerDebtReport'])->name('customer.total.debt.reports');
            Route::get('/ca/customer/debt/load', [ReportController::class, 'loadCustomerDebtReport'])->name('ajax.load.customer.total.debt.reports');
            Route::get('/ca/customer/debt/print/{from_date}/{to_date}/{customer}/', [ReportController::class, 'printCustomerDebtReport'])->name('ajax.customer.total.debt.report.print');


            //Customer Balance Detail Report
            Route::get('/ca/customer/balance-details', [ReportController::class, 'customerBalanceDetailReport'])->name('customer.balance.details.reports');
            Route::get('/ca/customer/balance-details/load', [ReportController::class, 'loadCustomerBalanceDetailReport'])->name('ajax.load.customer.balance.details.reports');
            Route::get('/ca/customer/balance-details/print/{from_date}/{to_date}/{customer}/', [ReportController::class, 'printCustomerBalanceDetailReport'])->name('ajax.customer.balance.details.report.print');

            //Customer Last Transaction Report
            Route::get('/ca/customer/last-transaction', [ReportController::class, 'lastTransaction'])->name('customer.last.transaction.reports');
            Route::get('/ca/customer/last-transaction/load', [ReportController::class, 'loadLastTransaction'])->name('ajax.load.customer.last.transaction.reports');
            Route::get('/ca/customer/last-transaction/print/{from_date}/{to_date}/{customer}/', [ReportController::class, 'printLastTransaction'])->name('ajax.customer.last.transaction.report.print');

            //Customer Payment Report
            Route::get('/ca/customer/payment', [ReportController::class, 'customerPaymentReport'])->name('customer.payment.reports');
            Route::get('/ca/customer/payment/load', [ReportController::class, 'loadCustomerPaymentReport'])->name('ajax.load.customer.payment.reports');
            Route::get('/ca/customer/payment/print/{from_date}/{to_date}/{customer}/', [ReportController::class, 'printCustomerPaymentReport'])->name('ajax.customer.payment.report.print');

            //Debtor Payment Overdue Report
            Route::get('/ca/debtor/payment-overdue', [ReportController::class, 'debtorPaymentOverDueReport'])->name('customer.payment.overdue.reports');
            Route::get('/ca/debtor/payment-overdue/load', [ReportController::class, 'loadDebtorPaymentOverDueReport'])->name('ajax.load.customer.payment.overdue.reports');
            Route::get('/ca/debtor/payment-overdue/print/{from_date}/{to_date}/{customer}/', [ReportController::class, 'printDebtorPaymentOverDueReport'])->name('ajax.customer.payment.overdue.report.print');

            //Deleted Sales Report
            Route::get('/be/deleted-sales', [ReportController::class, 'deletedSaleReport'])->name('deleted.sales.reports');
            Route::get('/be/deleted-sales/load', [ReportController::class, 'loadDeletedSaleReport'])->name('ajax.load.deleted.sales.reports');
            Route::get('/be/deleted-sales/print/{from_date}/{to_date}/{customer}/', [ReportController::class, 'printDeletedSaleReport'])->name('ajax.deleted.sales.report.print');


            //Customer Balance Detail Report
            Route::get('/ca/customer/ageing-report', [ReportController::class, 'ageingReport'])->name('customer.ageing.reports');
            Route::get('/ca/customer/ageing-report/load', [ReportController::class, 'loadAgeingReport'])->name('ajax.load.customer.ageing.reports');
            Route::get('/ca/customer/ageing-report/print/{from_date}/{to_date}/{customer_id}/', [ReportController::class, 'printAgeingReport'])->name('ajax.customer.ageing.report.print');

            //Begin Supplier Ledger Analysis
            //Supplier Balance Detail Report
            Route::get('/sp/supplier/running-balance', [ReportController::class, 'supplierBalanceReport'])->name('supplier.balance.reports');
            Route::get('/sp/supplier/running-balance/load', [ReportController::class, 'loadSupplierBalanceReport'])->name('ajax.load.supplier.balance.reports');
            Route::get('/sp/supplier/running-balance/print/{from_date}/{to_date}/{supplier_id}/', [ReportController::class, 'printSupplierBalanceReport'])->name('ajax.supplier.balance.report.print');

            //Supplier Debt Report
            Route::get('/ca/supplier/debt', [ReportController::class, 'supplierDebtReport'])->name('supplier.total.debt.reports');
            Route::get('/ca/supplier/debt/load', [ReportController::class, 'loadSupplierDebtReport'])->name('ajax.load.supplier.total.debt.reports');
            Route::get('/ca/supplier/debt/print/{from_date}/{to_date}/{supplier_id}/', [ReportController::class, 'printSupplierDebtReport'])->name('ajax.supplier.total.debt.report.print');

            //Credit Note Report
            Route::get('/ca/credit/note', [ReportController::class, 'creditNoteReport'])->name('credit.note.reports');
            Route::get('/ca/credit/note/load', [ReportController::class, 'loadCreditNoteReport'])->name('ajax.load.credit.note.reports');
            Route::get('/ca/credit/note/print/{from_date}/{to_date}/{supplier_id}/', [ReportController::class, 'printCreditNoteReport'])->name('ajax.credit.note.report.print');

            //Begin Purchase Anaysis
            //Customer Payment Report
            Route::get('/pa/supplier/payment', [ReportController::class, 'supplierPaymentReport'])->name('supplier.payment.reports');
            Route::get('/pa/supplier/payment/load', [ReportController::class, 'loadSupplierPaymentReport'])->name('ajax.load.supplier.payment.reports');
            Route::get('/pa/supplier/payment/print/{from_date}/{to_date}/{supplier_id}/', [ReportController::class, 'printSupplierPaymentReport'])->name('ajax.supplier.payment.report.print');

            //Purchases  Report
            Route::get('/pa/puchases/transaction', [ReportController::class, 'purchasesReport'])->name('supplier.transaction.report');
            Route::get('/pa/puchases/transaction/load', [ReportController::class, 'loadPurchasesReport'])->name('ajax.supplier.transaction.report');
            Route::get('/pa/puchases/transaction/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/{supplier_id}/{purchase_mode}', [ReportController::class, 'printPurchasesReport'])->name('ajax.supplier.transaction.report.print');

            //Purchase Check Report
            Route::get('/pa/puchases/transaction/check', [ReportController::class, 'purchaseCheckReport'])->name('purchase.transaction.check.report');
            Route::get('/pa/puchases/transaction/check/load', [ReportController::class, 'loadPurchaseCheckReport'])->name('ajax.purchase.transaction.check.report');
            Route::get('/pa/puchases/transaction/check/print/{from_date}/{to_date}/{store_id}/{category_id}/{product_id}/{supplier_id}/{purchase_mode}', [ReportController::class, 'printPurchaseCheckReport'])->name('ajax.purchase.transaction.check.report.print');

            //Total Purchases Report
            Route::get('/pa/total/puchases/item', [ReportController::class, 'totalPurchaseItemReport'])->name('total.purchase.item.report');
            Route::get('/pa/total/puchases/item/load', [ReportController::class, 'loadTotalPurchaseItemReport'])->name('ajax.total.purchase.item.report');
            Route::get('/pa/total/puchases/item/print/{from_date}/{to_date}/{store_id}', [ReportController::class, 'printTotalPurchaseItemReport'])->name('ajax.total.purchase.item.report.print');

            Route::get('/activity/{user}', [ReportController::class, 'logs'])->name('users.logs');
            Route::get('/activity/load/logs', [ReportController::class, 'viewLogs'])->name('user.activity.logs');
            Route::get('/activity/load/logs/print/{from_date}/{to_date}/{user_id}', [ReportController::class, 'printLogs'])->name('user.activity.logs.print');

            //User Ledger and Loans

            //Loan Balances
            Route::get('/us/user/balance', [ReportController::class, 'loanBalance'])->name('user.loan.balance.report');
            Route::get('/us/user/balance/load', [ReportController::class, 'loadLoanBalance'])->name('ajax.load.user.loan.balance.report');
            Route::get('/us/user/balance/print/{collector_id}', [ReportController::class, 'printLoanBalance'])->name('ajax.user.loan.balance.report.print');

            //Loan History
            Route::get('/us/user/loan/history', [ReportController::class, 'loanHistory'])->name('user.loan.history.report');
            Route::get('/us/user/loan/history/load', [ReportController::class, 'loadLoanHistory'])->name('ajax.load.user.loan.history.report');
            Route::get('/us/user/loan/history/print/{collector_id}', [ReportController::class, 'printLoanHistory'])->name('ajax.user.loan.history.report.print');
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

                Route::get('/import', [ChartOfAccountController::class, 'importForm'])->name('chart_of_accounts.import.form');
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

                Route::get('/import', [GeneralAccountController::class, 'importForm'])->name('general_accounts.import.form');
                Route::post('/import', [GeneralAccountController::class, 'import'])->name('general_accounts.import');
            }
        );

    });

    Route::prefix('journals')->group(function () {
        Route::get('/', [JournalController::class, 'index'])->name('journal.index');
        Route::post('/', [JournalController::class, 'store'])->name('journal.store');
        Route::get('/create', [JournalController::class, 'create'])->name('journal.create');
        Route::get('/show/{journal}', [JournalController::class, 'show'])->name('journal.show');
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
            }
        );
        Route::group(
            ['prefix' => 'purchases/request'],
            function () {
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
                Route::post('/expense', [PurchaseRequestController::class, 'expense'])->name('purchases.request.expense.ajax.create');
                Route::delete('/expense/delete/{expense}', [PurchaseRequestController::class, 'deleteExpense'])->name('delete.purchase.request.expense');
                Route::post('/approve/{purchase}', [PurchaseRequestController::class, 'approve'])->name('purchase.request.approve');
            }
        );
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
                Route::get('/print/{transfer_id}', [InterStoreTransferController::class, 'printStockTransfer'])->name('interstore.print');

            }
        );
        Route::group(
            ['prefix' => 'intersite/transfer'],
            function () {
                Route::get('/index', [InterSiteTransferController::class, 'index'])->name('intersite.index');
                Route::post('/search', [InterSiteTransferController::class, 'search'])->name('intersite.search');
                Route::get('/create', [InterSiteTransferController::class, 'create'])->name('intersite.create');
                Route::get('/show/{intersiteTransfer}', [InterSiteTransferController::class, 'show'])->name('intersite.show');
                Route::post('/store', [InterSiteTransferController::class, 'store'])->name('intersite.store');
                Route::get('/edit/{intersiteTransfer}', [InterSiteTransferController::class, 'edit'])->name('intersite.edit');
                Route::put('/update/{intersiteTransfer}', [InterSiteTransferController::class, 'update'])->name('intersite.update');
                Route::put('/approve/{intersiteTransfer}', [InterSiteTransferController::class, 'approve'])->name('intersite.approve');
                Route::delete('/delete/{intersiteTransfer}', [InterSiteTransferController::class, 'destroy'])->name('intersite.destroy');
                Route::post('/cart', [InterSiteTransferController::class, 'addToCart'])->name('intersite.cart');
                Route::delete('/remove/{id}', [InterSiteTransferController::class, 'removeCart'])->name('intersite.cart.remove');
                Route::post('/clear', [InterSiteTransferController::class, 'clearAllCart'])->name('intersite.cart.clear');
                Route::get('/print/{transfer_id}', [InterSiteTransferController::class, 'printStockTransfer'])->name('intersite.print');

            }
        );

    });

    //inventory

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('customer/search', 'CustomerController@search')->name('customer.search');
    Route::get('/notification', [NotificationController::class, 'notify'])->name('notification');
    Route::post('/notification/send', [NotificationController::class, 'send'])->name('notification.send');


    Route::get('generate/product_code', [MisController::class, 'generateProductCode'])->name('generate.productCode');
    Route::get('generate/customer_code', [MisController::class, 'nextCustomerCode'])->name('generate.customerCode');


});
