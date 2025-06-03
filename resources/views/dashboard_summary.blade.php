@php
    $totalActual = collect($summaries)->sum('actual');
    $totalBudget = collect($summaries)->sum('budget');
    $variance = $totalBudget > 0 ? (($totalActual - $totalBudget) / $totalBudget) * 100 : 0;
    $companyGroups = collect($summaries)->groupBy(function ($item) {
        return $item['type'] === 'Company' ? $item['label'] : null;
    });
@endphp

<div class="mb-3">
    <h5 class="text-muted">
        Showing results for
        <strong>{{ $selected_quarter === 'All' ? 'All Quarters (Jan–Dec)' : 'Quarter ' . $selected_quarter }}</strong><br>
        <span>Total Sales: <strong>&#8358;{{ number_format($totalActual, 2) }}</strong></span><br>
        <span>Total Budget: <strong>&#8358;{{ number_format($totalBudget, 2) }}</strong></span><br>
        <span>Total Variance: <strong>{{ number_format($variance, 2) }}%</strong>
            {{ $variance >= 0 ? 'Above' : 'Below' }} Budget</span>
    </h5>
</div>

@foreach ($companyGroups as $companyLabel => $items)
    @php
        $company = $items->firstWhere('type', 'Company');
        // $branches = collect($summaries)->filter(
        //     fn($x) => $x['type'] === 'Branch' && $companyLabel && str_contains($x['label'], $companyLabel),
        // );
        $branches = collect($summaries)->filter(
            fn($x) => $x['type'] === 'Branch' && ($x['company_label'] ?? '') === $companyLabel,
        );
    @endphp

    @if ($company)
        <div class="col-12">
            <h4 class="text-primary font-weight-bold">{{ $company['label'] }}</h4>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fa fa-building"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ $company['label'] }} (Total)</span>
                        <span class="info-box-number">
                            &#8358;{{ number_format($company['actual'], 2) }}<br>
                            <small class="text-white-50">Budget:
                                &#8358;{{ number_format($company['budget'], 2) }}</small><br>
                            <small class="text-white-50">Total Sales:
                                &#8358;{{ number_format($totalActual, 2) }}</small><br>
                            <small class="text-white-50">Total Budget:
                                &#8358;{{ number_format($totalBudget, 2) }}</small>
                        </span>
                        <div class="progress">
                            <div class="progress-bar"
                                style="width: {{ $company['budget'] > 0 ? min(100, ($company['actual'] / $company['budget']) * 100) : 0 }}%">
                            </div>
                        </div>
                        <span class="progress-description">
                            {{ $company['budget'] > 0 ? number_format((($company['actual'] - $company['budget']) / $company['budget']) * 100, 2) : 0 }}%
                            {{ $company['actual'] >= $company['budget'] ? 'Above' : 'Below' }} Budget
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($branches->count())
        <div class="row nested-summary">
            @foreach ($branches as $summary)
                <div class="col-md-4">
                    <div
                        class="info-box {{ $summary['budget'] > 0 && $summary['actual'] / $summary['budget'] < 0.5 ? 'bg-danger' : 'bg-info' }}">
                        <span class="info-box-icon"><i class="fa fa-code-branch"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">
                                {{ $summary['label'] }}
                                <small class="d-block text-white-50">Branch</small>
                            </span>
                            <span class="info-box-number">
                                &#8358;{{ number_format($summary['actual'], 2) }}<br>
                                <small class="text-white-50">Budget:
                                    &#8358;{{ number_format($summary['budget'], 2) }}</small><br>
                                <small class="text-white-50">Total Sales:
                                    &#8358;{{ number_format($totalActual, 2) }}</small><br>
                                <small class="text-white-50">Total Budget:
                                    &#8358;{{ number_format($totalBudget, 2) }}</small>
                            </span>
                            <div class="progress">
                                <div class="progress-bar"
                                    style="width: {{ $summary['budget'] > 0 ? min(100, ($summary['actual'] / $summary['budget']) * 100) : 0 }}%">
                                </div>
                            </div>
                            <span class="progress-description">
                                {{ $summary['budget'] > 0 ? number_format((($summary['actual'] - $summary['budget']) / $summary['budget']) * 100, 2) : 0 }}%
                                {{ $summary['actual'] >= $summary['budget'] ? 'Above' : 'Below' }} Budget
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endforeach

{{-- @php
    $totalActualSales = collect($summaries)->sum('actual_sales');
    $totalSalesBudget = collect($summaries)->sum('sales_budget');
    $totalActualExpenses = collect($summaries)->sum('actual_expenses');
    $totalExpenseBudget = collect($summaries)->sum('expense_budget');
    $salesVariance = $totalSalesBudget > 0 ? (($totalActualSales - $totalSalesBudget) / $totalSalesBudget) * 100 : 0;
    $expenseVariance =
        $totalExpenseBudget > 0 ? (($totalActualExpenses - $totalExpenseBudget) / $totalExpenseBudget) * 100 : 0;
    $companyGroups = collect($summaries)
        ->filter(fn($x) => isset($x['label']) && $x['type'] === 'Company')
        ->groupBy('label');
@endphp

<div class="mb-3">
    <h5 class="text-muted">
        Showing results for
        <strong>{{ $selected_quarter === 'All' ? 'All Quarters (Jan–Dec)' : 'Quarter ' . $selected_quarter }}</strong><br>
        <span>Total Sales: <strong>₦{{ number_format($totalActualSales, 2) }}</strong></span><br>
        <span>Total Sales Budget: <strong>₦{{ number_format($totalSalesBudget, 2) }}</strong></span><br>
        <span>Total Sales Variance: <strong>{{ number_format($salesVariance, 2) }}%</strong>
            {{ $salesVariance >= 0 ? 'Above' : 'Below' }} Budget</span><br>
        <span>Total Expenses: <strong>₦{{ number_format($totalActualExpenses, 2) }}</strong></span><br>
        <span>Total Expense Budget: <strong>₦{{ number_format($totalExpenseBudget, 2) }}</strong></span><br>
        <span>Total Expense Variance: <strong>{{ number_format($expenseVariance, 2) }}%</strong>
            {{ $expenseVariance >= 0 ? 'Above' : 'Below' }} Budget</span>
    </h5>
</div>

@foreach ($companyGroups as $companyLabel => $items)
    @php
        $company = $items->firstWhere('type', 'Company');
        $branches = collect($summaries)->filter(
            fn($x) => $x['type'] === 'Branch' && ($x['company_label'] ?? '') === $companyLabel,
        );
    @endphp

    @if ($company)
        <div class="col-12">
            <h4 class="text-primary font-weight-bold">{{ $company['label'] }}</h4>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fa fa-building"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ $company['label'] }} (Total)</span>
                        <span class="info-box-number">
                            ₦{{ number_format($company['actual_sales'] ?? 0, 2) }}<br>
                            <small class="text-white-50">Sales Budget:
                                ₦{{ number_format($company['sales_budget'] ?? 0, 2) }}</small><br>
                            <small class="text-white-50">Expenses:
                                ₦{{ number_format($company['actual_expenses'] ?? 0, 2) }}</small><br>
                            <small class="text-white-50">Expense Budget:
                                ₦{{ number_format($company['expense_budget'] ?? 0, 2) }}</small>
                        </span>
                        <div class="progress">
                            <div class="progress-bar"
                                style="width: {{ $company['sales_budget'] > 0 ? min(100, ($company['actual_sales'] / $company['sales_budget']) * 100) : 0 }}%">
                            </div>
                        </div>
                        <span class="progress-description">
                            @isset($company['sales_budget'])
                                {{ $company['sales_budget'] > 0 ? number_format((($company['actual_sales'] - $company['sales_budget']) / $company['sales_budget']) * 100, 2) : 0 }}%
                                {{ $company['actual_sales'] >= $company['sales_budget'] ? 'Above' : 'Below' }} Sales Budget
                            @endisset
                        </span>
                        <span class="progress-description">
                            @isset($company['expense_budget'])
                                {{ $company['expense_budget'] > 0 ? number_format((($company['actual_expenses'] - $company['expense_budget']) / $company['expense_budget']) * 100, 2) : 0 }}%
                                {{ $company['actual_expenses'] >= $company['expense_budget'] ? 'Above' : 'Below' }} Expense
                                Budget
                            @endisset
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($branches->count())
        <div class="row nested-summary">
            @foreach ($branches as $summary)
                <div class="col-md-4">
                    <div
                        class="info-box {{ $summary['sales_budget'] > 0 && $summary['actual_sales'] / $summary['sales_budget'] < 0.5 ? 'bg-danger' : 'bg-info' }}">
                        <span class="info-box-icon"><i class="fa fa-code-branch"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $summary['label'] }}<small
                                    class="d-block text-white-50">Branch</small></span>
                            <span class="info-box-number">
                                ₦{{ number_format($summary['actual_sales'], 2) }}<br>
                                <small class="text-white-50">Sales Budget:
                                    ₦{{ number_format($summary['sales_budget'], 2) }}</small><br>
                                <small class="text-white-50">Expenses:
                                    ₦{{ number_format($summary['actual_expenses'], 2) }}</small><br>
                                <small class="text-white-50">Expense Budget:
                                    ₦{{ number_format($summary['expense_budget'], 2) }}</small>
                            </span>
                            <div class="progress">
                                <div class="progress-bar"
                                    style="width: {{ $summary['sales_budget'] > 0 ? min(100, ($summary['actual_sales'] / $summary['sales_budget']) * 100) : 0 }}%">
                                </div>
                            </div>
                            <span class="progress-description">
                                {{ $summary['sales_budget'] > 0 ? number_format((($summary['actual_sales'] - $summary['sales_budget']) / $summary['sales_budget']) * 100, 2) : 0 }}%
                                {{ $summary['actual_sales'] >= $summary['sales_budget'] ? 'Above' : 'Below' }} Sales
                                Budget
                            </span>
                            <span class="progress-description">
                                {{ $summary['expense_budget'] > 0 ? number_format((($summary['actual_expenses'] - $summary['expense_budget']) / $summary['expense_budget']) * 100, 2) : 0 }}%
                                {{ $summary['actual_expenses'] >= $summary['expense_budget'] ? 'Above' : 'Below' }}
                                Expense Budget
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endforeach

<!-- Chart for Sales vs Expenses vs Budgets -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Branch-Level Performance Comparison</h3>
    </div>
    <div class="card-body">
        <canvas id="branchPerformanceChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('branchPerformanceChart').getContext('2d');
    const data = @json(collect($summaries)->filter(fn($s) => $s['type'] === 'Branch')->values());
    const labels = data.map(item => item.label);
    const sales = data.map(item => item.actual_sales);
    const salesBudgets = data.map(item => item.sales_budget);
    const expenses = data.map(item => item.actual_expenses);
    const expenseBudgets = data.map(item => item.expense_budget);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                    label: 'Sales',
                    data: sales,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                },
                {
                    label: 'Sales Budget',
                    data: salesBudgets,
                    backgroundColor: 'rgba(255, 206, 86, 0.7)'
                },
                {
                    label: 'Expenses',
                    data: expenses,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                },
                {
                    label: 'Expense Budget',
                    data: expenseBudgets,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Sales and Expense Performance vs Budgets'
                }
            }
        }
    });
</script> --}}
