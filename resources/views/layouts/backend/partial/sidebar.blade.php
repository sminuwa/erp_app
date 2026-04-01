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
        <div class="sidebar-search mb-3">
            <div class="input-group">
                <input type="text" id="sidebar-search" class="form-control" placeholder="Search menu..."
                    style="background-color: #343a40; color: #fff; border: 1px solid #4b545c;">
                <div class="input-group-append">
                    <span class="input-group-text" style="background-color: #343a40; border: 1px solid #4b545c;">
                        <i class="fa fa-search"></i>
                    </span>
                </div>
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
                @can('menu.accounting')
                    <li class="nav-item has-treeview {{ Request::is('transaction/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('transaction/*') ? 'active' : '' }}">
                            <i class="ion-android-list"></i>
                            <p>
                                AP-AR Accounting
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('receipt.payments')
                                <li class="nav-item">
                                    <a href="{{ route('receipt.payments') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Receipts</p>
                                    </a>
                                </li>
                            @endcan
                            @can('payments.list')
                                <li class="nav-item">
                                    <a href="{{ route('payments.list') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Payments</p>
                                    </a>
                                </li>
                            @endcan
                            @can('interbank.list')
                                <li class="nav-item">
                                    <a href="{{ route('interbank.list') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Interbanks</p>
                                    </a>
                                </li>
                            @endcan
                            @can('journal.index')
                                <li class="nav-item">
                                    <a href="{{ route('journal.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Journals</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('menu.sale')
                    <li class="nav-item has-treeview {{ Request::is('sales/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('sales/*') ? 'active' : '' }}">
                            <i class="ion-android-list"></i>
                            <p>
                                Sales
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            @can('order.invoice.list')
                                <li class="nav-item">
                                    <a href="{{ route('order.invoice.list') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Orders</p>
                                    </a>
                                </li>
                            @endcan
                            @can('invoice.index')
                                <li class="nav-item">
                                    <a href="{{ route('invoice.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Invoice (POS)</p>
                                    </a>
                                </li>
                            @endcan
                            @can('proformer.list')
                                <li class="nav-item">
                                    <a href="{{ route('proformer.list') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Proforma Invoice</p>
                                    </a>
                                </li>
                            @endcan
                            @can('customers.credit.note')
                                <li class="nav-item">
                                    <a href="{{ route('customers.credit.note') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Credit Note</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan
                @can('menu.purchase')
                    <li class="nav-item has-treeview {{ Request::is('transaction/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('transaction/*') ? 'active' : '' }}">
                            <i class="ion-android-list"></i>
                            <p>
                                Purchases
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('purchases.request.index')
                                <li class="nav-item">
                                    <a href="{{ route('purchases.request.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Purchase (Request)</p>
                                    </a>
                                </li>
                            @endcan
                            @can('purchases.index')
                                <li class="nav-item">
                                    <a href="{{ route('purchases.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Purchase Invoice/GRN</p>
                                    </a>
                                </li>
                            @endcan
                            @can('purchase.additional-invoice.index')
                                <li class="nav-item">
                                    <a href="{{ route('purchase.additional-invoice.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Additional Invoice</p>
                                    </a>
                                </li>
                            @endcan
                            @can('intersite.index')
                                <li class="nav-item">
                                    <a href="{{ route('intersite.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Intersite Transfer</p>
                                    </a>
                                </li>
                            @endcan
                            @can('interstore.index')
                                <li class="nav-item">
                                    <a href="{{ route('interstore.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Interstore Transfer</p>
                                    </a>
                                </li>
                            @endcan
                            @can('stock_adjustments.index')
                                <li class="nav-item">
                                    <a href="{{ route('stock_adjustments.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Stock Adjustment</p>
                                    </a>
                                </li>
                            @endcan
                            @can('return.debit')
                                <li class="nav-item">
                                    <a href="{{ route('return.debit') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Return and Debit</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan

                @can('menu.setting')
                    <li class="nav-item has-treeview {{ Request::is('settings/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('settings/*') ? 'active' : '' }}">
                            <i class="nav-icon ion-android-settings"></i>
                            <p>Settings <i class="right fa fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('employees.index')
                                <li class="nav-item">
                                    <a href="{{ route('employees.index') }}"
                                        class="nav-link {{ Request::is('employees/*') ? 'active' : '' }}">
                                        <i class="ion-person-stalker"></i>
                                        <p>Manage Staff</p>
                                    </a>
                                </li>
                            @endcan
                            @can('customers.index')
                                <li class="nav-item">
                                    <a href="{{ route('customers.index') }}"
                                        class="nav-link {{ Request::is('customers/manage/*') ? 'active' : '' }}">
                                        <i class="ion-ios-sunny-outline"></i>
                                        <p>Manage Customers</p>
                                    </a>
                                </li>
                            @endcan
                            @can('suppliers.index')
                                <li class="nav-item">
                                    <a href="{{ route('suppliers.index') }}"
                                        class="nav-link {{ Request::is('suppliers/manage/*') ? 'active' : '' }}">
                                        <i class="ion-ios-sunny-outline"></i>
                                        <p>Manage Suppliers</p>
                                    </a>
                                </li>
                            @endcan
                            @can('categories.index')
                                <li class="nav-item">
                                    <a href="{{ route('categories.index') }}"
                                        class="nav-link {{ Request::is('settings/categories/*') ? 'active' : '' }}">
                                        <i class="ion-code-working"></i>
                                        <p>Manage Product Categories</p>
                                    </a>
                                </li>
                            @endcan
                            @can('products.index')
                                <li class="nav-item">
                                    <a href="{{ route('products.index') }}"
                                        class="nav-link {{ Request::is('settings/products/manage/*') ? 'active' : '' }}">
                                        <i class="ion-ios-cart-outline"></i>
                                        <p>Manage Products</p>
                                    </a>
                                </li>
                            @endcan
                            @can('product_unit_measures.index')
                                <li class="nav-item">
                                    <a href="{{ route('product_unit_measures.index') }}"
                                        class="nav-link {{ Request::is('settings/products/manage/unit/*') ? 'active' : '' }}">
                                        <i class="ion-ios-cart-outline"></i>
                                        <p>Manage UTM</p>
                                    </a>
                                </li>
                            @endcan
                            @can('branch_product_prices.create')
                                <li class="nav-item">
                                    <a href="{{ route('branch_product_prices.create') }}"
                                        class="nav-link {{ Request::is('settings/products/manage/prices*') ? 'active' : '' }}">
                                        <i class="ion-ios-cart-outline"></i>
                                        <p>Product Selling Prices</p>
                                    </a>
                                </li>
                            @endcan
                            @can('products.purchase_prices')
                                <li class="nav-item">
                                    <a href="{{ route('products.purchase_prices') }}"
                                        class="nav-link {{ Request::is('settings/products/manage/purchase_price*') ? 'active' : '' }}">
                                        <i class="ion-ios-cart-outline"></i>
                                        <p>Product Purchase Prices</p>
                                    </a>
                                </li>
                            @endcan

                            @can('branches.index')
                                <li class="nav-item">
                                    <a href="{{ route('branches.index') }}"
                                        class="nav-link {{ Request::is('settings/branches/*') ? 'active' : '' }}">
                                        <i class="ion-network"></i>
                                        <p>Manage Branches</p>
                                    </a>
                                </li>
                            @endcan
                            @can('stores.index')
                                <li class="nav-item">
                                    <a href="{{ route('stores.index') }}"
                                        class="nav-link {{ Request::is('settings/stores/*') ? 'active' : '' }}">
                                        <i class="ion-briefcase"></i>
                                        <p>Manage Stores</p>
                                    </a>
                                </li>
                            @endcan
                            @can('chart_of_accounts.index')
                                <li class="nav-item">
                                    <a href="{{ route('chart_of_accounts.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Manage Chart of Accounts</p>
                                    </a>
                                </li>
                            @endcan
                            @can('general_accounts.index')
                                <li class="nav-item">
                                    <a href="{{ route('general_accounts.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Manage GL Accounts</p>
                                    </a>
                                </li>
                            @endcan
                            @can('companies.index')
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
                @can('menu.report')
                    <li class="nav-item has-treeview {{ Request::is('reports/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('reports/*') ? 'active' : '' }}">
                            <i class="ion-ios-timer-outline"></i>
                            <p>Report <i class="right fa fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('submenu.stock.control')
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
                                        <!--                                        <li class="nav-item">
                                                                                                                                            <a href="{{-- {{ route('stock.balances.report') }} --}}"
                                                                                                                                                class="nav-link {{-- {{ Request::is('reports/sc/stock/balances*') ? 'active' : '' }} --}}">
                                                                                                                                                <i class="ion-code-working"></i>
                                                                                                                                                <p>Previous Stock Balances</p>
                                                                                                                                            </a>
                                                                                                                                        </li>-->
                                        <li class="nav-item">
                                            <a href="{{ route('current.stock.report') }}"
                                                class="nav-link {{ Request::is('reports/sc/current-stock*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Current Stock</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('intersite.transfer.reports') }}"
                                                class="nav-link {{ Request::is('reports/sc/intersite/transfer*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Intersite Transfer Reports</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('interstore.transfer.reports') }}"
                                                class="nav-link {{ Request::is('reports/sc/interstore/transfer*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Interstore Transfer Reports</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('stock.history.reports') }}"
                                                class="nav-link {{ Request::is('reports/sc/stock/history*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Stock History Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('store.ledger.reports') }}"
                                                class="nav-link {{ Request::is('reports/sc/store-ledger/*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Store Quantity Reports</p>
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
                            @can('submenu.sale.report')
                                <li class="nav-item has-treeview {{ Request::is('reports/sa/sales*') ? 'menu-open' : '' }}">
                                    <a href="#"
                                        class="nav-link {{ Request::is('reports/sa/sales*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            Sales Reports
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('general.sales.report') }}"
                                                class="nav-link {{ Request::is('reports/sa/sales/general*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>General Sales Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('sales.report.by.category') }}"
                                                class="nav-link {{ Request::is('reports/sa/sales/category*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Sales by Category</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('sales.report.by.category.site') }}"
                                                class="nav-link {{ Request::is('reports/site/sales/category*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Sales by Category by Site</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('customer.sale.reports') }}"
                                                class="nav-link {{ Request::is('reports/sa/sales/customer/sale/common-name*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Sales With Common Name</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('most.sold.item.reports') }}"
                                                class="nav-link {{ Request::is('reports/sa/most/sold-item*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Most Sold Item</p>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="{{ route('staff.sales.report') }}"
                                                class="nav-link {{ Request::is('reports/sa/sales/staff*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Staff Sales Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('relation_officer.report') }}"
                                                class="nav-link {{ Request::is('reports/sa/sales/relation_officer*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>RO Sales Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('total.item.sold.report') }}"
                                                class="nav-link {{ Request::is('reports/sa/sales/staff*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Total Items Sold</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('credit.note.reports') }}"
                                                class="nav-link {{ Request::is('reports/sa/sales/staff*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Credit Notes</p>
                                            </a>
                                            <a href="{{ route('credit.note.lines.reports') }}"
                                                class="nav-link {{ Request::is('reports/sa/sales/staff*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Credit Notes Lines</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('invoice.list.reports') }}"
                                                class="nav-link {{ Request::is('reports/sa/sales/invoice*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Invoices List</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('invoice.lines.reports') }}"
                                                class="nav-link {{ Request::is('reports/sa/sales/invoice/lines*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Invoices Lines</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('order.list.reports') }}"
                                                class="nav-link {{ Request::is('reports/sa/sales/order*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Orders List</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('order.lines.reports') }}"
                                                class="nav-link {{ Request::is('reports/sa/sales/order/lines*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Orders Lines</p>
                                            </a>
                                        </li>

                                    </ul>
                                </li>
                            @endcan
                            @can('submenu.customer.report')
                                <li
                                    class="nav-item has-treeview {{ Request::is('reports/ca/customer*') ? 'menu-open' : '' }}">
                                    <a href="#"
                                        class="nav-link {{ Request::is('reports/ca/customer*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            Customer Reports
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('customer.last.transaction.reports') }}"
                                                class="nav-link {{ Request::is('reports/ca/customer/last-transaction*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Last Transaction Date</p>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="{{ route('customer.ageing.reports') }}"
                                                class="nav-link {{ Request::is('reports//ca/customer/ageing-report*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Ageing Report</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('customer.credit_limit.reports') }}"
                                                class="nav-link {{ Request::is('reports/ca/customer/credit_limit*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>With Credit Limit</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('customer.exceeded_credit_limit.reports') }}"
                                                class="nav-link {{ Request::is('reports/ca/customer/exceeded_credit_limit*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Exceeded Credit Limit</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('customer.list.reports') }}"
                                                class="nav-link {{ Request::is('reports/ca/customer/list*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Customer List</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('best.performing.Customers.reports') }}"
                                                class="nav-link {{ Request::is('reports/sa/customer/best/performing-customer*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>Best Performing Customers</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('relation.officer.with.customers') }}"
                                                class="nav-link {{ Request::is('reports/sa/relation-officers*') ? 'active' : '' }}">
                                                <i class="ion-code-working"></i>
                                                <p>RO with Customers</p>
                                            </a>
                                        </li>

                                    </ul>
                                </li>
                            @endcan
                            @can('submenu.customer.report')
                                <li class="nav-item has-treeview {{ Request::is('reports/supplier*') ? 'menu-open' : '' }}">
                                    <a href="#"
                                        class="nav-link {{ Request::is('reports/supplier*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            Supplier Reports
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('supplier.list.reports') }}"
                                                class="nav-link {{ Request::is('reports/supplier/list*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Supplier List</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan

                            @can('submenu.inventory.report')
                                <li class="nav-item has-treeview {{ Request::is('reports/inventory*') ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is('reports/pa/*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            Inventory
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('product.valuation.report') }}"
                                                class="nav-link {{ Request::is('reports/inventory/product/valuation*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Inventory Valuation</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('product.list.reports') }}"
                                                class="nav-link {{ Request::is('reports/inventory/product*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Product List</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('expiry.date.report') }}"
                                                class="nav-link {{ Request::is('reports/inventory/expiring*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Expiry Date Tracking</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('goods.in.transit.report') }}"
                                                class="nav-link {{ Request::is('reports/inventory/goods/in-transit*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Goods in Transit</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('purchase.request.report') }}"
                                                class="nav-link {{ Request::is('reports/inventory/request*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Purchase Requests</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('purchase.invoice.report') }}"
                                                class="nav-link {{ Request::is('reports/inventory/invoice*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Invoice/GRN</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('purchase.invoice.lines.report') }}"
                                                class="nav-link {{ Request::is('reports/inventory/invoice/lines*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Invoices/GRN Lines</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('return.debit.report') }}"
                                                class="nav-link {{ Request::is('reports/inventory/returndebit*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Return & Debit</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('additional.invoice.report') }}"
                                                class="nav-link {{ Request::is('reports/inventory/additional/invoice*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Additional Invoices</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('user_entries.report') }}"
                                                class="nav-link {{ Request::is('reports/inventory/user_entries*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>User Entries</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('backdated.report') }}"
                                                class="nav-link {{ Request::is('reports/inventory/user_entries/backdated*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Backdated/Postdated Entries</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('slow.overstayed.report') }}"
                                                class="nav-link {{ Request::is('reports/inventory/slow/overstay*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Slow and overstayed</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                            {{-- @can('submenu.user.ledger.and.loan')
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
                            @endcan --}}
                            @can('submenu.accounting.report')
                                <li class="nav-item has-treeview {{ Request::is('reports/ap_ar*') ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is('reports/ap_ar*') ? 'active' : '' }}">
                                        <i class="ion-ios-time"></i>
                                        <p>
                                            AP/AR
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    @can('account.statement.report')
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('account.statement.report') }}"
                                                    class="nav-link {{ Request::is('reports/ap_ar/account_statement*') ? 'active' : '' }}">
                                                    <i class="ion-cash"></i>
                                                    <p>Account Statements</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('account.balance.report')
                                            <li class="nav-item">
                                                <a href="{{ route('account.balance.report') }}"
                                                    class="nav-link {{ Request::is('reports/ap_ar/account_balances*') ? 'active' : '' }}">
                                                    <i class="ion-cash"></i>
                                                    <p>Account Balances</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('trial.balance.report')
                                            <li class="nav-item">
                                                <a href="{{ route('trial.balance.report') }}"
                                                    class="nav-link {{ Request::is('reports/ap_ar/trial_balance*') ? 'active' : '' }}">
                                                    <i class="ion-cash"></i>
                                                    <p>Trial Balance</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('income.statement.report')
                                            <li class="nav-item">
                                                <a href="{{ route('income.statement.report') }}"
                                                    class="nav-link {{ Request::is('reports/ap_ar/income_statement*') ? 'active' : '' }}">
                                                    <i class="ion-cash"></i>
                                                    <p>Income Statement</p>
                                                </a>
                                            </li>
                                        @endcan
                                        {{-- @can('balance.sheet.report') --}}
                                        <li class="nav-item">
                                            <a href="{{ route('balance.sheet.report') }}"
                                                class="nav-link {{ Request::is('reports/ap_ar/balance_sheet*') ? 'active' : '' }}">
                                                <i class="ion-cash"></i>
                                                <p>Balance Sheet</p>
                                            </a>
                                        </li>
                                        {{-- @endcan --}}
                                        @can('cash.flow.report')
                                            <li class="nav-item">
                                                <a href="{{ route('cash.flow.report') }}"
                                                    class="nav-link {{ Request::is('reports/ap_ar/cash_flow*') ? 'active' : '' }}">
                                                    <i class="ion-cash"></i>
                                                    <p>Cash Flow</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('user.loan.history.report')
                                            <li class="nav-item">
                                                <a href="{{ route('user.loan.history.report') }}"
                                                    class="nav-link {{ Request::is('reports/pa/loan/history*') ? 'active' : '' }}">
                                                    <i class="ion-cash"></i>
                                                    <p>Financial Statistics</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('remittance.report')
                                            <li class="nav-item">
                                                <a href="{{ route('remittance.report') }}"
                                                    class="nav-link {{ Request::is('reports/ap_ar/remittance*') ? 'active' : '' }}">
                                                    <i class="ion-cash"></i>
                                                    <p>User Daily Remittance</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('document.status.report')
                                            <li class="nav-item">
                                                <a href="{{ route('document.status.report') }}"
                                                    class="nav-link {{ Request::is('reports/ap_ar/document_status*') ? 'active' : '' }}">
                                                    <i class="ion-cash"></i>
                                                    <p>Receipt/Payment/Interbank & Journal list</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('customer.total.debt.reports')
                                            <li class="nav-item">
                                                <a href="{{ route('customer.total.debt.reports') }}"
                                                    class="nav-link {{ Request::is('reports/ca/customer/debt*') ? 'active' : '' }}">
                                                    <i class="ion-cash"></i>
                                                    <p>Customer Total Debts</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('account.balance.report')
                                            <li class="nav-item">
                                                <a href="{{ route('general.account.list.reports') }}"
                                                    class="nav-link {{ Request::is('reports/ap_ar/general/account*') ? 'active' : '' }}">
                                                    <i class="ion-cash"></i>
                                                    <p>GL List</p>
                                                </a>
                                            </li>
                                        @endcan

                                    </ul>
                                </li>
                            @endcan
                            @can('submenu.manufacturing.reports')
                                <li class="nav-item has-treeview {{ Request::is('manufacturing/reports*') ? 'menu-open' : '' }}">
                                    <a href="#"
                                        class="nav-link {{ Request::is('manufacturing/reports*') ? 'active' : '' }}">
                                        <i class="ion-stats-bars"></i>
                                        <p>
                                            Manufacturing Reports
                                            <i class="right fa fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @can('manufacturing.reports.history')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.reports.history.index') }}"
                                                    class="nav-link {{ Request::is('manufacturing/reports/history*') ? 'active' : '' }}">
                                                    <i class="fa fa-history"></i>
                                                    <p>Manufacturing History</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.reports.teams')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.reports.teams.index') }}"
                                                    class="nav-link {{ Request::is('manufacturing/reports/teams*') ? 'active' : '' }}">
                                                    <i class="fa fa-users"></i>
                                                    <p>Teams Report</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.reports.team_ledger')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.reports.team_ledger.index') }}"
                                                    class="nav-link {{ Request::is('manufacturing/reports/team-ledger*') ? 'active' : '' }}">
                                                    <i class="fa fa-book"></i>
                                                    <p>Team & Staff Ledger</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.reports.orders')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.reports.orders.index') }}"
                                                    class="nav-link {{ Request::is('manufacturing/reports/orders*') ? 'active' : '' }}">
                                                    <i class="fa fa-clipboard-list"></i>
                                                    <p>Production Orders</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.reports.schedules')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.reports.schedules.index') }}"
                                                    class="nav-link {{ Request::is('manufacturing/reports/schedules*') ? 'active' : '' }}">
                                                    <i class="fa fa-calendar-alt"></i>
                                                    <p>Daily Schedules</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.reports.requisitions')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.reports.requisitions.index') }}"
                                                    class="nav-link {{ Request::is('manufacturing/reports/requisitions*') ? 'active' : '' }}">
                                                    <i class="fa fa-file-alt"></i>
                                                    <p>Requisitions</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.reports.batch_conversion')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.reports.batch_conversion.index') }}"
                                                    class="nav-link {{ Request::is('manufacturing/reports/batch-conversion*') ? 'active' : '' }}">
                                                    <i class="fa fa-exchange-alt"></i>
                                                    <p>Batch Conversion</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('menu.opening-balances')
                    <li class="nav-item has-treeview {{ Request::is('opening-balance/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('opening-balance/*') ? 'active' : '' }}">
                            <i class="ion-android-list"></i>
                            <p>
                                Opening Balances
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('opening-balance.inventory.valuation') }}" class="nav-link ">
                                    <i class="ion-card"></i>
                                    <p>Inventory Valuation</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('opening-balance.account.ledger') }}" class="nav-link ">
                                    <i class="ion-card"></i>
                                    <p>GL Accounts</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('opening-balance.customer.balance') }}" class="nav-link ">
                                    <i class="ion-card"></i>
                                    <p>Customer Balances</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('opening-balance.customer.limit') }}" class="nav-link ">
                                    <i class="ion-card"></i>
                                    <p>Customer Credit Limit</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('opening-balance.supplier.balance') }}" class="nav-link ">
                                    <i class="ion-card"></i>
                                    <p>Supplier Balances</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endcan
                @can('menu.budget')
                    <li class="nav-item has-treeview {{ Request::is('transaction/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('transaction/*') ? 'active' : '' }}">
                            <i class="ion-android-list"></i>
                            <p>
                                Budgets
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('budget.view')
                                <li class="nav-item">
                                    <a href="{{ route('budgets.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Sales Budget</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('ro_budgets.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>ROs Budget</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('budget_expenditures.index') }}" class="nav-link ">
                                        <i class="ion-card"></i>
                                        <p>Budget Expenditure</p>
                                    </a>
                                </li>
                            @endcan
                            <li class="nav-item">
                                <a href="{{ route('area_managers.index') }}" class="nav-link ">
                                    <i class="ion-card"></i>
                                    <p>Area Managers</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('menu.manufacturing')
                    <li class="nav-item has-treeview {{ Request::is('manufacturing/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('manufacturing/*') ? 'active' : '' }}">
                            <i class="ion-ios-cog"></i>
                            <p>
                                Manufacturing
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('submenu.manufacturing.setup')
                                <li class="nav-item has-treeview">
                                    <a href="#" class="nav-link">
                                        <i class="ion-wrench"></i>
                                        <p>Setup <i class="right fa fa-angle-left"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview" style="padding-left: 15px;">
                                        @can('manufacturing.machines.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.machines.index') }}" class="nav-link">
                                                    <i class="fa fa-cogs"></i>
                                                    <p>Machines/Pots</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.staff.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.staff.index') }}" class="nav-link">
                                                    <i class="fa fa-users"></i>
                                                    <p>Manufacturing Staff</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.teams.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.teams.index') }}" class="nav-link">
                                                    <i class="fa fa-user-plus"></i>
                                                    <p>Production Teams</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.boms.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.boms.index') }}" class="nav-link">
                                                    <i class="fa fa-list-alt"></i>
                                                    <p>Bill of Materials</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                            @can('submenu.manufacturing.processing')
                                <li class="nav-item has-treeview">
                                    <a href="#" class="nav-link">
                                        <i class="ion-android-checkbox-outline"></i>
                                        <p>Processing <i class="right fa fa-angle-left"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview" style="padding-left: 15px;">
                                        @can('manufacturing.production_orders.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.production_orders.index') }}" class="nav-link">
                                                    <i class="fa fa-file-text-o"></i>
                                                    <p>Production Orders</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.schedules.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.schedules.index') }}" class="nav-link">
                                                    <i class="fa fa-calendar"></i>
                                                    <p>Daily Schedules</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.work_orders.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.work_orders.index') }}" class="nav-link">
                                                    <i class="fa fa-wrench"></i>
                                                    <p>Work Orders</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.requisitions.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.requisitions.index') }}" class="nav-link">
                                                    <i class="fa fa-clipboard"></i>
                                                    <p>Materials Requisition</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.single_manufacturing.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.single_manufacturing.index') }}" class="nav-link">
                                                    <i class="fa fa-cube"></i>
                                                    <p>Single Product Mfg</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.batch_production.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.batch_production.index') }}" class="nav-link">
                                                    <i class="fa fa-cubes"></i>
                                                    <p>Batch Production</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.batch_conversion.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.batch_conversion.index') }}" class="nav-link">
                                                    <i class="fa fa-exchange"></i>
                                                    <p>Batch Conversion</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.additional_costs.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.additional_costs.index') }}" class="nav-link">
                                                    <i class="fa fa-plus-circle"></i>
                                                    <p>Additional Costs</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.penalties.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.penalties.index') }}" class="nav-link">
                                                    <i class="fa fa-exclamation-triangle"></i>
                                                    <p>Penalties</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.returns.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.returns.index') }}" class="nav-link">
                                                    <i class="fa fa-undo"></i>
                                                    <p>Manufacturing Returns</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manufacturing.reworks.index')
                                            <li class="nav-item">
                                                <a href="{{ route('manufacturing.reworks.index') }}" class="nav-link">
                                                    <i class="fa fa-refresh"></i>
                                                    <p>Reworks</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('menu.backup')
                    <li class="nav-item has-treeview {{ Request::is('transaction/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('transaction/*') ? 'active' : '' }}">
                            <i class="ion-android-list"></i>
                            <p>
                                Backup & Restore
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('backup.index') }}" class="nav-link ">
                                    <i class="ion-card"></i>
                                    <p>Backup & Restore</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<!-- JavaScript for Search Functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('sidebar-search');
        const navItems = document.querySelectorAll('.nav-item');

        searchInput.addEventListener('input', function() {
            const searchTerm = searchInput.value.trim().toLowerCase();

            navItems.forEach(item => {
                // Get the text content of the menu item (top-level or sub-menu)
                const textElement = item.querySelector('p');
                const text = textElement ? textElement.textContent.toLowerCase() : '';

                // Check if the item or any of its children match the search term
                let hasMatch = text.includes(searchTerm);
                const subItems = item.querySelectorAll('.nav-treeview .nav-item');
                let hasSubMatch = false;

                // Check sub-menu items
                subItems.forEach(subItem => {
                    const subTextElement = subItem.querySelector('p');
                    const subText = subTextElement ? subTextElement.textContent
                        .toLowerCase() : '';
                    if (subText.includes(searchTerm)) {
                        hasSubMatch = true;
                    }
                });

                // Show or hide the item
                if (hasMatch || hasSubMatch) {
                    item.style.display = '';
                    // Expand treeview if there's a match in sub-items
                    if (hasSubMatch) {
                        item.classList.add('menu-open');
                        const navLink = item.querySelector('.nav-link');
                        if (navLink) navLink.classList.add('active');
                    }
                } else {
                    item.style.display = 'none';
                }

                // Show or hide sub-items based on search term
                subItems.forEach(subItem => {
                    const subTextElement = subItem.querySelector('p');
                    const subText = subTextElement ? subTextElement.textContent
                        .toLowerCase() : '';
                    subItem.style.display = subText.includes(searchTerm) || hasMatch ?
                        '' : 'none';
                });
            });
        });
    });
</script>
