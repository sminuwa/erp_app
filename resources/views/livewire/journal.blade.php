<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <table id="example1" class="table table-bordered table-striped text-left table-responsive-xl">
        <thead>
        <tr>
            <th>Processed Date</th>
            <th>Reference</th>
            <th>Description</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Date Created</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Processed Date</th>
            <th>Reference</th>
            <th>Description</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Date Created</th>
            <th>Actions</th>
        </tr>
        </tfoot>
        <tbody>
        @foreach ($records as $record)
            <tr class="@if($record->status == 0) bg-warning @endif">
                <td>{{ Carbon\Carbon::parse($record->date)->toFormattedDateString() }}</td>
                <td>{{ $record->reference }}</td>
                <td>{{ $record->description ?? null }}</td>
                <td>{!!  $record->status == 0 ? '<span class="badge badge-danger">pending</span>' : '<span class="badge badge-success">posted</span>' !!}</td>
                <td>{{ optional($record->createdBy)->name }}</td>
                <td>{{ Carbon\Carbon::parse($record->created_at)->toFormattedDateString() }}</td>
                <td align="center">
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a href="{{ route('journal.show', $record->id) }}"
                               class="dropdown-item">
                                <i class="fa fa-eye" aria-hidden="true"></i> Open
                            </a>
                            <a href="{{ route('journal.print', $record->id) }}" target="_blank"
                               class="dropdown-item" >
                                <i class="fa fa-print" aria-hidden="true"></i> Print
                            </a>
                            @if($record->status == 0)
                                @can('journal.post')
                                <a href="{{ route('journal.post',$record->id) }}"
                                   onclick="return confirm('Are you sure you want to post this journal?');"
                                   class="dropdown-item">
                                    <i class="fa fa-check" aria-hidden="true"></i> Post
                                </a>
                                @endcan
                                <a href="{{ route('journal.edit',$record->id) }}"
                                   class="dropdown-item">
                                    <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                </a>
                                    @can('journal.delete')
                                <a href="{{ route('journal.delete',$record->id) }}"
                                   onclick="return confirm('Are you sure you want to delete this journal?');"
                                   class="dropdown-item">
                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                </a>
                                    @endcan
                            @else
                                @can('journal.reverse')
                                <a href="{{ route('journal.reverse',$record->id) }}"
                                   onclick="return confirm('Are you sure you want reverse this transaction?')"
                                   class="dropdown-item">
                                    <i class="fa fa-reply" aria-hidden="true"></i> Reverse
                                </a>
                                @endcan
                            @endif
                        </div>
                    </div>

                </td>
            </tr>

        @endforeach
        </tbody>

    </table>
</div>
