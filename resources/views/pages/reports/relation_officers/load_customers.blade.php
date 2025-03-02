{{-- <div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mt-1">
                <h5 style="text-align: center;">Relation Officer & Customer Report
                    From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                    To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                </h5>
            </div>
        </div>

        <table class="display table table-bordered table-striped">
            <thead>
                <tr>
                    <th>RO Code</th>
                    <th>RO Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Branch</th>
                    <th>Total Customers</th>
                    <th>New Customers ({{ \Carbon\Carbon::parse($from_date)->format('d M') }} - {{ \Carbon\Carbon::parse($to_date)->format('d M') }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($relationOfficers as $ro)
                    <tr>
                        <td>{{ $ro->ro_code }}</td>
                        <td>{{ $ro->ro_surname }}, {{ $ro->ro_name }}</td>
                        <td>{{ $ro->ro_phone }}</td>
                        <td>{{ $ro->ro_email }}</td>
                        <td>{{ $ro->branch_name }}</td>
                        <td style="text-align: center;">{{ $ro->total_customers }}</td>
                        <td style="text-align: center; color: {{ $ro->new_customers > 0 ? 'green' : 'red' }}">
                            {{ $ro->new_customers }}
                        </td>
                    </tr>

                    @if (isset($customers[$ro->ro_id]))
                        <tr>
                            <td colspan="7">
                                <strong>Customers Managed by {{ $ro->ro_surname }}, {{ $ro->ro_name }}</strong>
                                <table class="table table-bordered mt-2">
                                    <thead>
                                        <tr>
                                            <th>Customer Code</th>
                                            <th>Customer Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Registered Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customers[$ro->ro_id] as $customer)
                                            <tr>
                                                <td>{{ $customer->code }}</td>
                                                <td>{{ $customer->name }}</td>
                                                <td>{{ $customer->phone }}</td>
                                                <td>{{ $customer->email }}</td>
                                                <td>{{ \Carbon\Carbon::parse($customer->created_at)->format('d M Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div> --}}
{{-- <div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mt-1">
                <h5 style="text-align: center;">Relation Officer & Customer Report
                    From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                    To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                </h5>
            </div>
        </div>

        <div class="table-responsive">
            <table class="display table table-bordered table-striped" id="ro-report">
                <thead>
                    <tr>
                        <th></th> <!-- Expandable Row -->
                        <th>RO Code</th>
                        <th>RO Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Branch</th>
                        <th>Total Customers</th>
                        <th>New Customers ({{ \Carbon\Carbon::parse($from_date)->format('d M') }} -
                            {{ \Carbon\Carbon::parse($to_date)->format('d M') }})</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($relationOfficers as $ro)
                        <tr class="clickable" data-toggle="collapse" data-target="#customers-{{ $ro->ro_id }}"
                            data-ro-id="{{ $ro->ro_id }}">
                            <td class="text-center"><i class="fas fa-plus-circle toggle-icon"></i></td>
                            <td>{{ $ro->ro_code }}</td>
                            <td>{{ $ro->ro_surname }}, {{ $ro->ro_name }}</td>
                            <td>{{ $ro->ro_phone }}</td>
                            <td>{{ $ro->ro_email }}</td>
                            <td>{{ $ro->branch_name }}</td>
                            <td style="text-align: center;">{{ $ro->total_customers }}</td>
                            <td style="text-align: center; color: {{ $ro->new_customers > 0 ? 'green' : 'red' }}">
                                {{ $ro->new_customers }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <!-- Expandable Rows (Excluded from DataTables) -->
                <tbody class="non-datatable-rows">
                    @foreach ($relationOfficers as $ro)
                        <tr id="customers-{{ $ro->ro_id }}" class="collapse">
                            <td colspan="8">
                                @if (isset($customers[$ro->ro_id]) && count($customers[$ro->ro_id]) > 0)
                                    <div class="table-responsive mt-3">
                                        <h6><strong>Customers Managed by {{ $ro->ro_surname }},
                                                {{ $ro->ro_name }}</strong></h6>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Customer Code</th>
                                                    <th>Customer Name</th>
                                                    <th>Phone</th>
                                                    <th>Email</th>
                                                    <th>Registered Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($customers[$ro->ro_id] as $customer)
                                                    <tr>
                                                        <td>{{ $customer->code }}</td>
                                                        <td>{{ $customer->name }}</td>
                                                        <td>{{ $customer->phone ?? 'Nil' }}</td>
                                                        <td>{{ $customer->email ?? 'Nil' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($customer->created_at)->format('d M Y') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-center">No customers assigned to this RO.</p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> --}}
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mt-1">
                <h5 style="text-align: center;">Relation Officer & Customer Report
                    From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                    To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                </h5>
            </div>
        </div>

        <div class="table-responsive">
            <table class="display table table-bordered table-striped" id="ro-report">
                <thead>
                    <tr>
                        <th></th> <!-- Expandable Row -->
                        <th>RO Code</th>
                        <th>RO Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Branch</th>
                        <th>Total Customers</th>
                        <th>New Customers ({{ \Carbon\Carbon::parse($from_date)->format('d M') }} - {{ \Carbon\Carbon::parse($to_date)->format('d M') }})</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($relationOfficers as $ro)
                        <tr class="clickable" data-toggle="collapse" data-target="#customers-{{ $ro->ro_id }}">
                            <td class="text-center"><i class="fas fa-plus-circle toggle-icon"></i></td>
                            <td>{{ $ro->ro_code }}</td>
                            <td>{{ $ro->ro_surname }}, {{ $ro->ro_name }}</td>
                            <td>{{ $ro->ro_phone }}</td>
                            <td>{{ $ro->ro_email }}</td>
                            <td>{{ $ro->branch_name }}</td>
                            <td style="text-align: center;">{{ $ro->total_customers }}</td>
                            <td style="text-align: center; color: {{ $ro->new_customers > 0 ? 'green' : 'red' }}">
                                {{ $ro->new_customers }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <!-- Expandable Rows (Excluded from DataTables) -->
                <tbody class="non-datatable-rows">
                    @foreach ($relationOfficers as $ro)
                        <tr id="customers-{{ $ro->ro_id }}" class="collapse">
                            <td colspan="8">
                                @if (isset($customers[$ro->ro_id]) && count($customers[$ro->ro_id]) > 0)
                                    <div class="table-responsive mt-3">
                                        <h6><strong>Customers Managed by {{ $ro->ro_surname }}, {{ $ro->ro_name }}</strong></h6>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Customer Code</th>
                                                    <th>Customer Name</th>
                                                    <th>Phone</th>
                                                    <th>Email</th>
                                                    <th>Registered Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($customers[$ro->ro_id] as $customer)
                                                    <tr>
                                                        <td>{{ $customer->code }}</td>
                                                        <td>{{ $customer->name }}</td>
                                                        <td>{{ $customer->phone ?? 'Nil' }}</td>
                                                        <td>{{ $customer->email ?? 'Nil' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($customer->created_at)->format('d M Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-center">No customers assigned to this RO.</p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('js')
    <script type="text/javascript">
        $(document).ready(function () {
            // Initialize DataTable with buttons extension
            let table = $('#ro-report').DataTable({
                "paging": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "columnDefs": [
                    { "orderable": false, "targets": 0 } // Disable sorting on expand column
                ],
                "dom": 'Bfrtip', // Add buttons to the DOM
                "buttons": [
                    {
                        extend: 'excel',
                        text: 'Export to Excel',
                        title: 'Relation Officer & Customer Report',
                        exportOptions: {
                            modifier: {
                                page: 'all' // Export all pages
                            },
                            columns: ':visible', // Export visible columns
                            format: {
                                body: function (data, row, column, node) {
                                    // Customize the export format for nested tables
                                    if ($(node).hasClass('non-datatable-rows')) {
                                        return ''; // Exclude expandable rows from export
                                    }
                                    return data;
                                }
                            }
                        },
                        customize: function (xlsx) {
                            // Add customer data to the export
                            let sheet = xlsx.xl.worksheets['sheet1.xml'];
                            let customerData = [];

                            // Loop through each Relation Officer
                            @foreach ($relationOfficers as $ro)
                                @if (isset($customers[$ro->ro_id]) && count($customers[$ro->ro_id]) > 0)
                                    customerData.push(['Customers Managed by {{ $ro->ro_surname }}, {{ $ro->ro_name }}']);
                                    customerData.push(['Customer Code', 'Customer Name', 'Phone', 'Email', 'Registered Date']);
                                    @foreach ($customers[$ro->ro_id] as $customer)
                                        customerData.push([
                                            '{{ $customer->code }}',
                                            '{{ $customer->name }}',
                                            '{{ $customer->phone ?? 'Nil' }}',
                                            '{{ $customer->email ?? 'Nil' }}',
                                            '{{ \Carbon\Carbon::parse($customer->created_at)->format('d M Y') }}'
                                        ]);
                                    @endforeach
                                    customerData.push([]); // Add an empty row between ROs
                                @endif
                            @endforeach

                            // Append customer data to the sheet
                            $('row c', sheet).each(function () {
                                let row = $(this).parent();
                                let rowIndex = row.attr('r');
                                if (rowIndex > 1) { // Skip the header row
                                    $(this).remove(); // Remove existing data
                                }
                            });

                            // Add customer data to the sheet
                            for (let i = 0; i < customerData.length; i++) {
                                let row = $('<row></row>').attr('r', i + 2); // Start from row 2
                                for (let j = 0; j < customerData[i].length; j++) {
                                    let cell = $('<c></c>')
                                        .attr('t', 'inlineStr')
                                        .attr('s', '1')
                                        .append($('<is></is>').append($('<t></t>').text(customerData[i][j])));
                                    row.append(cell);
                                }
                                $(sheet).find('sheetData').append(row);
                            }
                        }
                    }
                ]
            });
        });
    </script>
@endpush