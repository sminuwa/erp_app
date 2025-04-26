{{-- <div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.total.debt.report.print', [$from_date, $to_date, $customer_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Total Receipt and Invoices
            As at
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th style="width: 50%" colspan="3">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="3">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th colspan="4"></th>
            <th colspan="2">BALANCE</th>
        </tr>
        <tr>
            <th>ACCOUNT NO</th>
            <th>CUSTOMER NAME</th>
            <th>TOTAL AMOUNT</th>
            <th>TOTAL PAID</th>
            <th>DR.</th>
            <th>CR.</th>
        </tr>
    </thead>
    @php
        $total_sold = 0;
        $total_pay = 0;
        $total_due = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->code }}</td>
            <td>{{ $sale->customer }}</td>
            <td style="text-align: right">{{ number_format($sale->total, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($sale->pay, 2, '.', ',') }}</td>
            <td style="text-align: right">
                @if ($sale->due > 0)
                    {{ number_format(abs($sale->due), 2, '.', ',') }}
                @endif
            </td>
            <td style="text-align: right">
                @if ($sale->due < 0)
                    {{ number_format(abs($sale->due), 2, '.', ',') }}
                @endif
            </td>
        </tr>
        @php
            $total_sold += $sale->total;
            $total_pay += $sale->pay;
            $total_due += $sale->due;

        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th></th>
            <th style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                {{ number_format($total_pay, 2, '.', ',') }}</th>
            <th style="text-align: right">
                {{ number_format($total_sold, 2, '.', ',') }}</th>
            
            <th style="text-align: right">
                {{ $total_due > 0 ? number_format(abs($total_due), 2, '.', ',') : '' }}
            </th>
            <th style="text-align: right">
                {{ $total_due < 0 ? number_format(abs($total_due), 2, '.', ',') : '' }}
            </th>
        </tr>
    </tfoot>
</table> --}}
<!-- load_customer_total_debt_report.blade.php -->
<!-- load_customer_total_debt_report.blade.php -->
<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.total.debt.report.print', [$from_date, $to_date, $customer_id]) }}" 
           target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>

<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Total Receipt and Invoices
            As at
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th style="width: 50%" colspan="3">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}</th>
            <th style="width: 50%;text-align:right" colspan="3">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th colspan="4"></th>
            <th colspan="2">BALANCE</th>
        </tr>
        <tr>
            <th>ACCOUNT NO</th>
            <th>CUSTOMER NAME</th>
            <th>TOTAL AMOUNT</th>
            <th>TOTAL PAID</th>
            <th>DR.</th>
            <th>CR.</th>
        </tr>
    </thead>
    
    @php
        $debtors_total_sold = 0;
        $debtors_total_pay = 0;
        $debtors_total_due = 0;
    @endphp
    
    <!-- DEBTORS SECTION -->
    <tr>
        <td colspan="6" style="text-align: center; font-weight: bold;">DEBTORS</td>
    </tr>
    <tr>
        <th>ACCOUNT NO</th>
        <th>CUSTOMER NAME</th>
        <th>TOTAL AMOUNT</th>
        <th>TOTAL PAID</th>
        <th>DR.</th>
        <th>CR.</th>
    </tr>
    
    @foreach ($debtors as $debtor)
        @if ($debtor->due != 0)
        <tr>
            <td>{{ $debtor->code }}</td>
            <td>{{ $debtor->customer }}</td>
            <td style="text-align: right">{{ number_format($debtor->total, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($debtor->pay, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($debtor->due, 2, '.', ',') }}</td>
            <td style="text-align: right"></td>
        </tr>
        @endif
        @php
            $debtors_total_sold += $debtor->total;
            $debtors_total_pay += $debtor->pay;
            $debtors_total_due += $debtor->due;
        @endphp
    @endforeach
    
    <tr>
        <td></td>
        <td style="text-align: right; font-weight: bold;">TOTAL</td>
        <td style="text-align: right; font-weight: bold;">{{ number_format($debtors_total_sold, 2, '.', ',') }}</td>
        <td style="text-align: right; font-weight: bold;">{{ number_format($debtors_total_pay, 2, '.', ',') }}</td>
        <td style="text-align: right; font-weight: bold;">{{ number_format($debtors_total_due, 2, '.', ',') }}</td>
        <td></td>
    </tr>
    
    <!-- CREDITORS SECTION -->
    <tr>
        <td colspan="6" style="text-align: center; font-weight: bold;">CREDITORS</td>
    </tr>
    <tr>
        <th>ACCOUNT NO</th>
        <th>CUSTOMER NAME</th>
        <th>TOTAL AMOUNT</th>
        <th>TOTAL PAID</th>
        <th>DR.</th>
        <th>CR.</th>
    </tr>
    
    @php
        $creditors_total_sold = 0;
        $creditors_total_pay = 0;
        $creditors_total_due = 0;
    @endphp
    
    @foreach ($creditors as $creditor)
        @if ($creditor->due != 0)
        <tr>
            <td>{{ $creditor->code }}</td>
            <td>{{ $creditor->customer }}</td>
            <td style="text-align: right">{{ number_format($creditor->total, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($creditor->pay, 2, '.', ',') }}</td>
            <td style="text-align: right"></td>
            <td style="text-align: right">{{ number_format($creditor->due, 2, '.', ',') }}</td>
        </tr>
        @endif
        @php
            $creditors_total_sold += $creditor->total;
            $creditors_total_pay += $creditor->pay;
            $creditors_total_due += $creditor->due;
        @endphp
    @endforeach
    
    <tr>
        <td></td>
        <td style="text-align: right; font-weight: bold;">TOTAL</td>
        <td style="text-align: right"></td>
        <td style="text-align: right"></td>
        <td style="text-align: right"></td>
        <td style="text-align: right; font-weight: bold;">{{ number_format($creditors_total_due, 2, '.', ',') }}</td>
    </tr>
    
    <!-- BALANCE ROW -->
    <tr>
        <td colspan="2" style="text-align: right; font-weight: bold;">BALANCE</td>
        <td colspan="2"></td>
        <td colspan="2" style="text-align: right; font-weight: bold;">{{ number_format(abs($creditors_total_due - $debtors_total_due), 2, '.', ',') }}</td>
    </tr>
</table>
