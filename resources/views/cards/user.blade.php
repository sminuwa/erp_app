<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <a class="btn btn-secondary btn-sm" href="{{ route('users.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                        action="{{ route('users.destroy', $record->id) }}" method="post" style="display: inline">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                            <i class="text-danger fa fa-remove"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card-block">
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>Name</th>
                    <td>{{ $record->name ?? null }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $record->email ?? null }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $record->phone ?? null }}</td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td>{{ $record->gender ?? null }}</td>
                </tr>
                <tr>
                    <th>Active</th>
                    <td>{{ $record->active = 1 ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <th>Entry Date Settings</th>
                    <td style="color:brown;font-weight: 800;">{{ Carbon\Carbon::parse($record->date_range_start)->toFormattedDateString() }} to
                        {{ Carbon\Carbon::parse($record->date_range_end)->toFormattedDateString() }}
                        <a href="#" class="dropdown-item set-entry-date text-danger" data-user-id="{{ $record->id }}"
                            data-user-name="{{ $record->name }}" data-date-start="{{ $record->date_range_start }}"
                            data-date-end="{{ $record->date_range_end }}" data-toggle="modal"
                            data-target="#dateRangeModal">
                            <span class="fa fa-calendar-check-o"> Set Entry Date</span>
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>Branch</th>
                    <td>{{ optional($record->branch)->name ?? null }}</td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td>{{ is_null($record->getUserRole) ? '' : $record->getUserRole->role->name ?? null }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-block">
        <form action="{{ route('user.store.role', $record->id) }}" method="POST">
            @csrf
            <input type="hidden" name="model_id" value="{{ $record->id }}" />
            <div class="row">
                <div class="col-sm-4">
                    <label for="role_id">Role</label>
                    <select class="form-control {{ $errors->has('role_id') ? ' is-invalid' : '' }}" name="role_id"
                        id="role_id">
                        <option value="">Select</option>
                        @if (isset($roles))
                            @foreach ($roles as $data)
                                <option value="{{ $data->id }}">
                                    {{ $data->name ?? null }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-sm-4">
                    <input type="radio" name="status" value="1">Assign &nbsp;&nbsp;<input type="radio"
                        name="status" value="0">Revoke
                </div>
                <div class="col-sm-4">
                    <input type="submit" name="submit" value="Save" class="btn btn-primary" />
                </div>
            </div>
        </form>
    </div>
</div>
