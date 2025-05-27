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
@php
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

@foreach ($groupedSummaries as $type => $items)
    <div class="col-12">
        <h4 class="text-primary font-weight-bold">{{ strtoupper($type) }} SUMMARY</h4>
    </div>
    <div class="row">
        @foreach ($items as $summary)
            <div class="col-md-4">
                <div
                    class="info-box {{ $summary['type'] === 'Company'
                        ? 'bg-success'
                        : ($summary['budget'] > 0 && $summary['actual'] / $summary['budget'] < 0.5
                            ? 'bg-danger'
                            : 'bg-info') }}">
                    <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">
                            {{ $summary['label'] }}
                            <small class="d-block text-white-50">{{ $summary['type'] ?? 'N/A' }}</small>
                        </span>
                        <span class="info-box-number">
                            &#8358;{{ number_format($summary['actual'], 2) }}<br>
                            <small class="text-white">Budget:
                                &#8358;{{ number_format($summary['budget'], 2) }}</small><br>
                            {{-- <small class="text-white-50">Total Sales:
                                &#8358;{{ number_format($totalActual, 2) }}</small><br>
                            <small class="text-white-50">Total Budget:
                                &#8358;{{ number_format($totalBudget, 2) }}</small> --}}
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
@endforeach
