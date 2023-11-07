@php $r = request(); @endphp
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <img alt="Photo" src="{{ asset('assets/backend/img/logo5.png') }}" class="brand-image img-sqauare elevation-2"
            alt="User Image">
        <span class="brand-text font-weight-light"
            style="color:#FFF;font-size:20px;text-shadow: 2px 2px 4px #000000;font-weight:900;

        "><b>{{ app_name('short', 'uppercase') }}</b></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @if (Auth::check() && file_exists(public_path() . '/staffpics/' . Auth::user()->photo))
                    <img alt="Photo" src="{{ url('staffpics/' . Auth::user()->photo_url) }}"
                        class="img-circle elevation-2" alt="User Image">
                @else
                    <img alt="Photo" src="{{ asset('staffpics/man.png') }}" class="img-circle elevation-2"
                        alt="User Image">
                @endif
            </div>
            <div class="info">
                <a href="{{ route('home') }}" class="d-block">@auth {{ Auth::user()->name }} @endif
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item has-treeview">
                    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
                        <i class="nav-icon ion-ios-home-outline"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
<!--                <li class="nav-item has-treeview {{ Request::is('transaction/*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::is('transaction/*') ? 'active' : '' }}">
                        <i class="ion-android-list"></i>
                        <p>
                            Transactions
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('verify.invoice')
                            <li class="nav-item">
                                <a href="{{ route('orders.approved') }}"
                                    class="nav-link {{ Request::is('orders/approved') ? 'active' : '' }}">
                                    <i class="nav-icon ion-cash"></i>
                                    <p>Verify/Invoice</p>
                                </a>
                            </li>
                        @endcan
                        @can('view.expenditure')
                            <li class="nav-item">
                                <a href="{{ route('expenses.index') }}"
                                    class="nav-link {{ Request::is('expenses/expenditures*') ? 'active' : '' }}">
                                    <i class="ion-card"></i>
                                    <p>Expenditure</p>
                                </a>
                            </li>
                        @endcan
                        @can('view.deposit.withdraw')
                            <li class="nav-item">
                                <a href="{{ route('cash_movements.create') }}"
                                    class="nav-link {{ Request::is('cash/movement/*') ? 'active' : '' }}">
                                    <i class="ion-social-euro-outline"></i>
                                    <p>Bank Deposit & Withdrawal</p>
                                </a>
                            </li>
                        @endcan
                        @can('make.bank.deposit')
                            <li class="nav-item">
                                <a href="{{ route('deposits.create') }}"
                                    class="nav-link {{ Request::is('cash/movement/*') ? 'active' : '' }}">
                                    <i class="ion-jet"></i>
                                    <p>Bank Deposit</p>
                                </a>
                            </li>
                        @endcan
                        @can('make.bank.withdraw')
                            <li class="nav-item">
                                <a href="{{ route('withdraw.create') }}"
                                    class="nav-link {{ Request::is('cash/movement/*') ? 'active' : '' }}">
                                    <i class="ion-model-s"></i>
                                    <p>Bank Withdraw</p>
                                </a>
                            </li>
                        @endcan
                        @can('view.daily.sale')
                            <li class="nav-item">
                                <a href="{{ route('orders.approved') }}"
                                    class="nav-link {{ Request::is('orders/approved') ? 'active' : '' }}">
                                    <i class="nav-icon ion-cash"></i>
                                    <p>Daily Sales</p>
                                </a>
                            </li>
                        @endcan
                        @can('view.stock.transfer')
                            <li class="nav-item">
                                <a href="{{ route('interstore.index') }}"
                                    class="nav-link {{ Request::is('products/transfer/*') ? 'active' : '' }}">
                                    <i class="ion-ios-fastforward"></i>
                                    <p>Stock Transfer</p>
                                </a>
                            </li>
                        @endcan
                        @can('view.item.purchase')
                            <li class="nav-item">
                                <a href="{{ route('purchases.index') }}"
                                    class="nav-link {{ Request::is('products/purchases/*') ? 'active' : '' }}">
                                    <i class="ion-ios-pricetag"></i>
                                    <p>Purchases</p>
                                </a>
                            </li>
                        @endcan
                        @can('view.creditor.payment')
                            <li class="nav-item">
                                <a href="{{ route('suppliers.payments') }}"
                                    class="nav-link {{ Request::is('suppliers/pay/*') ? 'active' : '' }}">
                                    <i class="ion-social-angular-outline"></i>
                                    <p>Supplier Payments</p>
                                </a>
                            </li>
                        @endcan
                        @can('view.loan.granted')
                            <li class="nav-item">
                                <a href="{{ route('loans.index') }}"
                                    class="nav-link {{ Request::is('loan/grant/*') ? 'active' : '' }}">
                                    <i class="ion-social-sass"></i>
                                    <p>Grant Loan</p>
                                </a>
                            </li>
                        @endcan
                        @can('view.loan.payment')
                            <li class="nav-item">
                                <a href="{{ route('loan_payments.index') }}"
                                    class="nav-link {{ Request::is('loan/payment/*') ? 'active' : '' }}">
                                    <i class="ion-volume-mute"></i>
                                    <p>Loan Payment</p>
                                </a>
                            </li>
                        @endcan
                        @can('view.credit.note')
                            <li class="nav-item">
                                <a href="{{ route('suppliers.credit.note') }}"
                                    class="nav-link {{ Request::is('suppliers/pay/credit-note/*') ? 'active' : '' }}">
                                    <i class="ion-volume-mute"></i>
                                    <p>Credit Note</p>
                                </a>
                            </li>
                        @endcan
                        @can('view.stock.adjustment')
                            <li class="nav-item">
                                <a href="{{ route('stock_adjustments.index') }}"
                                    class="nav-link {{ Request::is('transaction/stock_adjustment/*') ? 'active' : '' }}">
                                    <i class="ion-ios-plus-outline"></i>
                                    <p>Stock Adjustment</p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>-->
                <li class="nav-item has-treeview {{ Request::is('transaction/*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::is('transaction/*') ? 'active' : '' }}">
                        <i class="ion-android-list"></i>
                        <p>
                            AP-AR Accounting
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('receipt.payments')}}"
                               class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Receipts</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('payments.list')}}"
                               class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Payments</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('interbank.list')}}"
                               class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Interbanks</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('journal.index') }}"
                               class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Journals</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview {{ Request::is('transaction/*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::is('transaction/*') ? 'active' : '' }}">
                        <i class="ion-android-list"></i>
                        <p>
                            Sales
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('order.invoice.list') }}" class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Orders</p>
                            </a>
                        </li>
                        @can('make.daily.sale')
                        <li class="nav-item">
                            <a href="{{ route('orders.approved') }}"
                               class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Invoice (POS)</p>
                            </a>
                        </li>
                        @endcan
                        <li class="nav-item">
                            <a href="{{ route('proformer.list') }}"
                               class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Proforma Invoice</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('customers.credit.note')}}"
                               class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Credit Note</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview {{ Request::is('transaction/*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::is('transaction/*') ? 'active' : '' }}">
                        <i class="ion-android-list"></i>
                        <p>
                            Purchases
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('purchases.request.index') }}" class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Purchases (Request)</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('purchases.index') }}" class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Purchases (GRN)</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('intersite.index')}}"
                               class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Intersite Transfer</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('interstore.index')}}"
                               class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Interstore Transfer</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('stock_adjustments.index')}}"
                               class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Stock Adjustment</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('customers.return.debit')}}"
                               class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Return and Debit</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('suppliers.debit.note')}}"
                               class="nav-link ">
                                <i class="ion-card"></i>
                                <p>Debit Note</p>
                            </a>
                        </li>

                    </ul>
                </li>
<!--
                @can('view.daily.sale.report')
                    <li class="nav-item">
                        <a href="{{ route('daily.report') }}"
                            class="nav-link {{ Request::is('*/daily-report*') ? 'active' : '' }}">
                            <i class="ion-code-working"></i>
                            <p>Daily Report</p>
                        </a>
                    </li>
                @endcan-->

                @can('menu.setting')
                    <li class="nav-item has-treeview {{ Request::is('settings/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('settings/*') ? 'active' : '' }}">
                            <i class="nav-icon ion-android-settings"></i>
                            <p>
                                Settings
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('view.staff')
                                <li class="nav-item">
                                    <a href="{{ route('employees.index') }}"
                                        class="nav-link {{ Request::is('employees/*') ? 'active' : '' }}">
                                        <i class="ion-person-stalker"></i>
                                        <p>Manage Staff</p>
                                    </a>
                                </li>
                            @endcan
                            @can('add.customer')
                                <li class="nav-item">
                                    <a href="{{ route('customers.index') }}"
                                       class="nav-link {{ Request::is('customers/manage/*') ? 'active' : '' }}">
                                        <i class="ion-ios-sunny-outline"></i>
                                        <p>Manage Customers</p>
                                    </a>
                                </li>
                            @endcan
                            <li class="nav-item">
                                <a href="{{ route('suppliers.index') }}"
                                   class="nav-link {{ Request::is('suppliers/manage/*') ? 'active' : '' }}">
                                    <i class="ion-ios-sunny-outline"></i>
                                    <p>Manage Suppliers</p>
                                </a>
                            </li>
                            @can('view.product.group')
                                <li class="nav-item">
                                    <a href="{{ route('categories.index') }}"
                                        class="nav-link {{ Request::is('settings/categories/*') ? 'active' : '' }}">
                                        <i class="ion-code-working"></i>
                                        <p>Manage Product Categories</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view.product')
                                <li class="nav-item">
                                    <a href="{{ route('products.index') }}"
                                        class="nav-link {{ Request::is('settings/products/manage/*') ? 'active' : '' }}">
                                        <i class="ion-ios-cart-outline"></i>
                                        <p>Manage Products</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view.product.price')
                                <li class="nav-item">
                                    <a href="{{ route('branch_product_prices.create') }}"
                                        class="nav-link {{ Request::is('settings/products/manage/prices*') ? 'active' : '' }}">
                                        <i class="ion-ios-cart-outline"></i>
                                        <p>Product Selling Prices</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('products.purchase_prices') }}"
                                        class="nav-link {{ Request::is('settings/products/manage/purchase_price*') ? 'active' : '' }}">
                                        <i class="ion-ios-cart-outline"></i>
                                        <p>Product Purchase Prices</p>
                                    </a>
                                </li>
<!--                                <li class="nav-item">
                                    <a href="{{ route('product_expire_settings.index') }}"
                                        class="nav-link {{ Request::is('settings/products/manage/expiration*') ? 'active' : '' }}">
                                        <i class="ion-ios-cart-outline"></i>
                                        <p>Product Expire Settings</p>
                                    </a>
                                </li>-->
                            @endcan

                            {{--@can('view.bank')
                                <li class="nav-item">
                                    <a href="{{ route('banks.index') }}"
                                        class="nav-link {{ Request::is('settings/banks*') ? 'active' : '' }}">
                                        <i class="ion-social-windows-outline"></i>
                                        <p>Manage Banks</p>
                                    </a>
                                </li>
                            @endcan--}}
                            {{-- @can('view.bank.branch')
                                <li class="nav-item">
                                    <a href="{{ route('bank_branches.index') }}"
                                        class="nav-link {{ Request::is('settings/bank_branches*') ? 'active' : '' }}">
                                        <i class="ion-ios-cog"></i>
                                        <p>Manage Bank Branches</p>
                                    </a>
                                </li>
                            @endcan
                            <li class="nav-item">
                                                                                                                <a href="{{ route('payment_modes.index') }}"
                                                                                                                    class="nav-link {{ Request::is('settings/payment_modes/*') ? 'active' : '' }}">
                                                                                                                    <i class="ion-jet"></i>
                                                                                                                    <p>Manage Payment Mode</p>
                                                                                                                </a>
                                                                                                            </li> --}}
                            @can('view.office.branch')
                                <li class="nav-item">
                                    <a href="{{ route('branches.index') }}"
                                        class="nav-link {{ Request::is('settings/branches/*') ? 'active' : '' }}">
                                        <i class="ion-network"></i>
                                        <p>Manage Branches</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view.store')
                                <li class="nav-item">
                                    <a href="{{ route('stores.index') }}"
                                        class="nav-link {{ Request::is('settings/stores/*') ? 'active' : '' }}">
                                        <i class="ion-briefcase"></i>
                                        <p>Manage Stores</p>
                                    </a>
                                </li>
                            @endcan

                            <li class="nav-item">
                                <a href="{{route('chart_of_accounts.index')}}"
                                   class="nav-link ">
                                    <i class="ion-card"></i>
                                    <p>Manage Chart of Accounts</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('general_accounts.index')}}"
                                   class="nav-link ">
                                    <i class="ion-card"></i>
                                    <p>Manage General Accounts</p>
                                </a>
                            </li>

<!--                            <li class="nav-item">
                                <a href="{{ route('bank_accounts.index') }}"
                                   class="nav-link {{ Request::is('settings/bank_accounts/*') ? 'active' : '' }}">
                                    <i class="ion-ios-pricetag-outline"></i>
                                    <p>Manage Chart of Accounts</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('bank_accounts.index') }}"
                                   class="nav-link {{ Request::is('settings/bank_accounts/*') ? 'active' : '' }}">
                                    <i class="ion-ios-pricetag-outline"></i>
                                    <p>Manage General Accounts</p>
                                </a>
                            </li>-->

                            @can('view.bank.account')
                                <li class="nav-item">
                                    <a href="{{ route('bank_accounts.index') }}"
                                        class="nav-link {{ Request::is('settings/bank_accounts/*') ? 'active' : '' }}">
                                        <i class="ion-ios-pricetag-outline"></i>
                                        <p>Manage Bank Accounts</p>
                                    </a>
                                </li>
                            @endcan
                            {{--@can('view.loan.collector')
                                <li class="nav-item">
                                    <a href="{{ route('loan_collectors.index') }}"
                                        class="nav-link {{ Request::is('settings/loan/collector/*') ? 'active' : '' }}">
                                        <i class="ion-code-working"></i>
                                        <p>Manage Loan Collectors</p>
                                    </a>
                                </li>
                            @endcan--}}
                            @can('view.product')
                                <li class="nav-item">
                                    <a href="{{ route('companies.index') }}"
                                        class="nav-link {{ Request::is('company/*') ? 'active' : '' }}">
                                        <i class="ion-person-stalker"></i>
                                        <p>Manage Company</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan
                @can('view.report')
                    <li class="nav-item has-treeview {{ Request::is('reports/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('reports/*') ? 'active' : '' }}">
                            <i class="ion-ios-timer-outline"></i>
                            <p>
                                Report
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('bank.expense.report')
                                <li class="nav-item has-treeview {{ Request::is('reports/be/*') ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is('reports/be/*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            Bank & Expenses
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('expense.item.report') }}"
                                                class="nav-link {{ Request::is('reports/be/expense-item*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Expense Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('consolidated.expense.report') }}"
                                                class="nav-link {{ Request::is('reports/be/consolidated-expense*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Cons. Expense Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('bank.ledger') }}"
                                                class="nav-link {{ Request::is('reports/be/bank-ledger*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Bank Ledger</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('bank.deposit.report') }}"
                                                class="nav-link {{ Request::is('reports/be/bank-deposit*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Bank Deposit</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('bank.withdraw.report') }}"
                                                class="nav-link {{ Request::is('reports/be/bank-withdraw*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Bank Withdraw</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('cash.transfer.report') }}"
                                                class="nav-link {{ Request::is('reports/be/cash-transfer*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Cash Withdrawn Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('bank.balance.report') }}"
                                                class="nav-link {{ Request::is('reports/be/bank-balance*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Bank Balances Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('cheque.collected.report') }}"
                                                class="nav-link {{ Request::is('reports/be/cheque-collected*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Cheque Collected Report</p>
                                            </a>
                                        </li>

                                    </ul>
                                </li>
                            @endcan
                            {{-- <li class="nav-item">
                            <a href="{{ route('expense.today') }}"
                                class="nav-link {{ Request::is('expenses/today') ? 'active' : '' }}">
                                <i class="ion-clock"></i>
                                <p>Today Expense</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('expense.month') }}"
                                class="nav-link {{ Request::is('expenses/month*') ? 'active' : '' }}">
                                <i class="ion-calendar"></i>
                                <p>Monthly Expense</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('expense.yearly') }}"
                                class="nav-link {{ Request::is('expenses/yearly*') ? 'active' : '' }}">
                                <i class="ion-clock"></i>
                                <p>Yearly Expense</p>
                            </a>
                        </li>- --}}
                            @can('stock.control.report')
                                <li class="nav-item has-treeview {{ Request::is('reports/sc/stock*') ? 'menu-open' : '' }}">
                                    <a href="#"
                                        class="nav-link {{ Request::is('reports/sc/stock*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            Stock Control
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('stock.balances.report') }}"
                                                class="nav-link {{ Request::is('reports/sc/stock/balances*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Previous Stock Balances</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('current.stock.report') }}"
                                                class="nav-link {{ Request::is('reports/sc/current-stock*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Current Stock</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('stock.transfer.reports') }}"
                                                class="nav-link {{ Request::is('reports/sc/stock/transfer*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Stock Transfer Reports</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('stock.in.reports') }}"
                                                class="nav-link {{ Request::is('reports/sc/stock/in*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Stock In Reports</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('store.ledger.reports') }}"
                                                class="nav-link {{ Request::is('reports/sc/store-ledger/*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Store Ledger Reports</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('stock.ledger.reports') }}"
                                                class="nav-link {{ Request::is('reports/sc/stock/ledger/*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Stock Ledger Reports</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('stock.adjustment.reports') }}"
                                                class="nav-link {{ Request::is('reports/sc/stock-adjustment/*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Stock Adjustment Reports</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                            @can('sale.cash.analysis.report')
                                <li class="nav-item has-treeview {{ Request::is('reports/sa/sales*') ? 'menu-open' : '' }}">
                                    <a href="#"
                                        class="nav-link {{ Request::is('reports/sa/sales*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            Sales and Cash Analysis
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('general.sales.report') }}"
                                                class="nav-link {{ Request::is('reports//sa/sales/*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Sales Transaction Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('customer.sale.reports') }}"
                                                class="nav-link {{ Request::is('reports//sa/sales/*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Customer Sales With Common Name</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('debtor.balance.reports') }}"
                                                class="nav-link {{ Request::is('reports//sa/debtor/balance*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Debtor Balance Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('most.sold.item.reports') }}"
                                                class="nav-link {{ Request::is('reports//sa/most/sold-item*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Most Sold Item</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('most.sold.item.quantity.reports') }}"
                                                class="nav-link {{ Request::is('reports//sa/most/sold-item/qty*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Most Sold Item by Quantity</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('staff.sales.report') }}"
                                                class="nav-link {{ Request::is('reports//sa/sales/staff*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Staff Sales Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('total.item.sold.report') }}"
                                                class="nav-link {{ Request::is('reports//sa/sales/staff*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Total Items Sold to Customers</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('discount.granted.reports') }}"
                                                class="nav-link {{ Request::is('reports//sa/discount-granted*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Track Discount Granted</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                            @can('customer.ledger.analysis.report')
                                <li class="nav-item has-treeview {{ Request::is('reports/sa/sales*') ? 'menu-open' : '' }}">
                                    <a href="#"
                                        class="nav-link {{ Request::is('reports/sa/sales*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            Customer Ledger Analysis
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('customer.ledger') }}"
                                                class="nav-link {{ Request::is('customers/ledger*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Customer Ledger Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('customer.total.debt.reports') }}"
                                                class="nav-link {{ Request::is('reports/ca/customer/debt*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Customer Total Debts</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('customer.balance.details.reports') }}"
                                                class="nav-link {{ Request::is('reports/ca/customer/balance-details*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Customer Balance Details</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('customer.last.transaction.reports') }}"
                                                class="nav-link {{ Request::is('reports/ca/customer/last-transaction*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Customer Last Transaction Date</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('customer.payment.reports') }}"
                                                class="nav-link {{ Request::is('reports/ca/customer/payment*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Customer Payment Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('customer.payment.overdue.reports') }}"
                                                class="nav-link {{ Request::is('reports/ca/customer/payment-overdue*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Debt Payment Overdue Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('deleted.sales.reports') }}"
                                                class="nav-link {{ Request::is('reports/ca/deleted/deleted-sales*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Deleted Items Sold Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('customer.ageing.reports') }}"
                                                class="nav-link {{ Request::is('reports//ca/customer/ageing-report*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Ageing Report</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                            @can('supplier.ledger.analysis.report')
                                <li
                                    class="nav-item has-treeview {{ Request::is('reports/sp/supplier*') ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is('reports/sa/*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            Supplier Ledger Analysis
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('supplier.balance.reports') }}"
                                                class="nav-link {{ Request::is('supplier/running-balance*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Supplier Running Balance</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('supplier.ledger') }}"
                                                class="nav-link {{ Request::is('reports/sp/*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Supplier Ledger Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('supplier.total.debt.reports') }}"
                                                class="nav-link {{ Request::is('reports/sp/supplier/debt*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Supplier Debt Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('credit.note.reports') }}"
                                                class="nav-link {{ Request::is('reports/sp/credit/note*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Credit Note Report</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                            @can('purchase.analysis.report')
                                <li
                                    class="nav-item has-treeview {{ Request::is('reports/pa/supplier*') ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is('reports/pa/*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            Purchase Analysis
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('supplier.payment.reports') }}"
                                                class="nav-link {{ Request::is('reports/pa/supplier/payment*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Supplier Payment Balance</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('supplier.transaction.report') }}"
                                                class="nav-link {{ Request::is('reports/pa/supplier/transaction*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Purchases Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('purchase.transaction.check.report') }}"
                                                class="nav-link {{ Request::is('reports/pa/purchase/transaction/check*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Purchase Transaction Check</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('total.purchase.item.report') }}"
                                                class="nav-link {{ Request::is('reports/pa/total/purchases/item*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Sum Purchased Items</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                            @can('user.ledger.and.loan')
                                <li class="nav-item has-treeview {{ Request::is('reports/us/user*') ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is('reports/us/*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            User Ledger and Loans
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('user.loan.balance.report') }}"
                                                class="nav-link {{ Request::is('reports/us/user/ledger*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>User's Ledger</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('user.loan.balance.report') }}"
                                                class="nav-link {{ Request::is('reports/us/loan/balance*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Loan Balances</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('user.loan.history.report') }}"
                                                class="nav-link {{ Request::is('reports/pa/loan/history*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Loan History</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                            @can('user.ledger.and.loan')
                                <li class="nav-item has-treeview {{ Request::is('reports/ap_ar*') ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is('reports/ap_ar*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            AP/AR
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('account.balance.report') }}"
                                                class="nav-link {{ Request::is('reports/us/user/ledger*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Account Balances/Statements</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('user.loan.balance.report') }}"
                                                class="nav-link {{ Request::is('reports/us/loan/balance*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Trial Balance</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('user.loan.history.report') }}"
                                                class="nav-link {{ Request::is('reports/pa/loan/history*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Income Statement</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('user.loan.history.report') }}"
                                                class="nav-link {{ Request::is('reports/pa/loan/history*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Balance Sheet</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('user.loan.history.report') }}"
                                                class="nav-link {{ Request::is('reports/pa/loan/history*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Cash Flow</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('user.loan.history.report') }}"
                                                class="nav-link {{ Request::is('reports/pa/loan/history*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Financial Statistics</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('user.loan.history.report') }}"
                                                class="nav-link {{ Request::is('reports/pa/loan/history*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>User Daily Remittance</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('user.loan.history.report') }}"
                                                class="nav-link {{ Request::is('reports/pa/loan/history*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Receipt/Payment/Interbank & Journal list</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
