<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Supplier</th>
            <th>Reference </th>
            <th>Purchase Date </th>
            <th>Amount (&#8358;) </th>
            <th>Status </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr class="@if ($record->status == 0) bg-warning @endif">
                <td> {{ optional($record->supplier)->code }} - {{ optional($record->supplier)->name }} </td>
                <td> {{ $record->reference }} </td>
                <td> {{ $record->purchase_date->toDayDateTimeString() }} </td>
                <td style="text-align: right"> {{ number_format($record->totalProductCost()->total, 2) }} </td>
                <td> {{ $record->status == 1 ? 'Completed' : 'Pending' }} </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ route('purchases.show', $record->id) }}">
                                <span class="fa fa-eye">View</span>
                            </a>
                            <form action="{{ route('purchase.approve', $record->id) }}" method="post"
                                onsubmit="return confirm('Are you sure you want to post this order?')">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-check" aria-hidden="true"></i> Post
                                </button>
                            </form>
                            @can('edit.item.purchase')
                                <a class="dropdown-item" href="{{ route('purchases.edit', $record->id) }}">
                                    <span class="fa fa-pencil"> Edit</span>
                                </a>
                            @endcan
                            <a class="dropdown-item" href="{{ route('purchase.print', $record->id) }}"
                                title="Print GRN" target="_BLANK">
                                <span class="fa fa-print"></span> Print
                            </a>
                            @can('delete.item.purchase')
                                <form onsubmit="return confirm('Are you sure you want to cancel?')"
                                    action="{{ route('purchases.destroy', $record->id) }}" method="post"
                                    style="display: inline">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="dropdown-item">
                                        <i class="text-danger fa fa-remove"> Delete</i>
                                    </button>
                                </form>
                            @endcan
                            @if ($record->waybill_no != null)
                                <a class="dropdown-item"
                                    href="{{ route('purchase.waybill.print', $record->id) }}" title="Print Invoice"
                                    target="_BLANK">
                                    <span class="ion-printer">Waybill</span>
                                </a>
                            @endif
                            <a href="javascript:void(0)" data-toggle="modal"
                                data-target="#customermodal{{ $loop->index + 1 }}"
                                class="dropdown-item float-md-right"><i class="fa fa-address-book"></i> WayBill Fill</a>
                        </div>
                    </div>
                </td>
            </tr>
            <!--  modal create customer -->
            <div class="modal fade" id="customermodal{{ $loop->index + 1 }}" style="display: none;" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Generate Purchase Waybill with Invoice
                                ({{ $record->invoice }})
                                by {{ $record->supplier->name }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{ route('purchase.generate.waybill', $record->id) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Way Bill No: </label>
                                            <input type="text" class="form-control" name="waybill_no"
                                                value="{{ old('waybill_no', $record->invoice) }}"
                                                placeholder="WayBill No">
                                        </div>
                                        <div class="form-group">
                                            <label>Driver's Name:</label>
                                            <input type="text" class="form-control" name="driver_name"
                                                value="{{ old('driver_name', $record->driver_name) }}"
                                                placeholder="Driver name">
                                        </div>
                                        <div class="form-group">
                                            <label for="transporter_phone">Phone No: </label>
                                            <input type="text" class="form-control" name="transporter_phone"
                                                value="{{ old('transporter_phone', $record->transporter_phone) }}"
                                                placeholder="Phone Number">
                                        </div>

                                        <div class="form-group">
                                            <label>Warehouse</label>
                                            <input type="text" class="form-control" name="warehouse"
                                                value="{{ old('warehouse', $record->warehouse) }}"
                                                placeholder="Warehouse">
                                        </div>
                                        <div class="form-group">
                                            <label>Vehicle Reg No.:</label>
                                            <input type="text" class="form-control" name="vehicle_reg_no"
                                                value="{{ old('vehicle_reg_no', $record->vehicle_reg_no) }}"
                                                placeholder="Vehicle Registration No">
                                        </div>
                                        <div class="form-group">
                                            <label>Transporter:</label>
                                            <select class="form-control select2-single" name="transporter"
                                                id="transporter" required>
                                                <option value="">Select...</option>
                                                @foreach ($suppliers as $data)
                                                    <option value="{{ $data->id }}"
                                                        {{ $record->transporter == $data->id ? 'selected' : '' }}>
                                                        {{ $data->code }}-{{ $data->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="modal" value="modal" />
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-dark" data-dismiss="modal"><i
                                            class="fa fa-times"></i>
                                        Close
                                    </button>
                                    <button type="submit" class="btn btn-info px-3"><i class="icon-trash"></i>
                                        Generate
                                    </button>
                                </div>
                                @method('post')
                            </form>
                        </div>
                    </div>
                </div>
            </div><!-- End modal delete -->
        @endforeach
    </tbody>
</table>
