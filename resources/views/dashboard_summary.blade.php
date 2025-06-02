{{-- <div class="row">
    @foreach ($summaries as $summary)
        <div class="col-md-4">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ $summary['label'] }}</span>
                    <span class="info-box-number">&#8358;{{ number_format($summary['actual'], 2) }}</span>
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
</div> --}}
{{-- @php
    $groupedSummaries = collect($summaries)
        ->sortByDesc(function ($item) {
            return $item['budget'] > 0 ? $item['actual'] / $item['budget'] : 0;
        })
        ->groupBy('type');
@endphp

<div class="mb-3">
    @php
        $totalActual = collect($summaries)->sum('actual');
        $totalBudget = collect($summaries)->sum('budget');
    @endphp
    <h5 class="text-muted">
        Showing results for
        <strong>{{ $selected_quarter === 'All' ? 'All Quarters (Jan–Dec)' : 'Quarter ' . $selected_quarter }}</strong><br>
        <span>Total Sales: <strong>&#8358;{{ number_format($totalActual, 2) }}</strong></span><br>
        <span>Total Budget: <strong>&#8358;{{ number_format($totalBudget, 2) }}</strong></span><br>
        @php
            $variance = $totalBudget > 0 ? (($totalActual - $totalBudget) / $totalBudget) * 100 : 0;
        @endphp
        <span>Total Variance: <strong>{{ number_format($variance, 2) }}%</strong>
            {{ $variance >= 0 ? 'Above' : 'Below' }} Budget</span>
    </h5>
</div>

@foreach ($groupedSummaries['Company'] ?? [] as $companySummary)
    <div class="col-12 mt-4">
        <h4 class="text-primary font-weight-bold">{{ $companySummary['label'] }} - COMPANY SUMMARY</h4>
        <div class="row">
            <div class="col-md-4">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fa fa-building"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">
                            {{ $companySummary['label'] }}
                            <small class="d-block text-white-50">Company</small>
                        </span>
                        <span class="info-box-number">
                            &#8358;{{ number_format($companySummary['actual'], 2) }}<br>
                            <small class="text-white-50">Budget:
                                &#8358;{{ number_format($companySummary['budget'], 2) }}</small><br>
                            <small class="text-white-50">Total Sales:
                                &#8358;{{ number_format($totalActual, 2) }}</small><br>
                            <small class="text-white-50">Total Budget:
                                &#8358;{{ number_format($totalBudget, 2) }}</small>
                        </span>
                        <div class="progress">
                            <div class="progress-bar"
                                style="width: {{ $companySummary['budget'] > 0 ? min(100, ($companySummary['actual'] / $companySummary['budget']) * 100) : 0 }}%">
                            </div>
                        </div>
                        <span class="progress-description">
                            {{ $companySummary['budget'] > 0 ? number_format((($companySummary['actual'] - $companySummary['budget']) / $companySummary['budget']) * 100, 2) : 0 }}%
                            {{ $companySummary['actual'] >= $companySummary['budget'] ? 'Above' : 'Below' }} Budget
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach ($groupedSummaries['Branch']->filter(fn($item) => str_contains($item['label'], $companySummary['label'])) as $branchSummary)
            <div class="col-md-4">
                <div
                    class="info-box {{ $branchSummary['budget'] > 0 && $branchSummary['actual'] / $branchSummary['budget'] < 0.5 ? 'bg-danger' : 'bg-info' }}">
                    <span class="info-box-icon"><i class="fa fa-code-branch"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">
                            {{ $branchSummary['label'] }}
                            <small class="d-block text-white-50">Branch</small>
                        </span>
                        <span class="info-box-number">
                            &#8358;{{ number_format($branchSummary['actual'], 2) }}<br>
                            <small class="text-white-50">Budget:
                                &#8358;{{ number_format($branchSummary['budget'], 2) }}</small><br>
                            <small class="text-white-50">Total Sales:
                                &#8358;{{ number_format($totalActual, 2) }}</small><br>
                            <small class="text-white-50">Total Budget:
                                &#8358;{{ number_format($totalBudget, 2) }}</small>
                        </span>
                        <div class="progress">
                            <div class="progress-bar"
                                style="width: {{ $branchSummary['budget'] > 0 ? min(100, ($branchSummary['actual'] / $branchSummary['budget']) * 100) : 0 }}%">
                            </div>
                        </div>
                        <span class="progress-description">
                            {{ $branchSummary['budget'] > 0 ? number_format((($branchSummary['actual'] - $branchSummary['budget']) / $branchSummary['budget']) * 100, 2) : 0 }}%
                            {{ $branchSummary['actual'] >= $branchSummary['budget'] ? 'Above' : 'Below' }} Budget
                        </span>
                    </div>
                </div>
            </div>
    </div>
    </div>
@endforeach
</div> --}}
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
