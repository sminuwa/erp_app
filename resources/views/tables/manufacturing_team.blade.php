<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Team Name</th>
            <th>Branch</th>
            <th>Supervisors</th>
            <th>Members</th>
            <th>Status</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->name }}</td>
                <td>{{ $record->branch->name ?? '-' }}</td>
                <td>
                    @if($record->supervisors->count() > 0)
                        {{ $record->supervisors->map(function($s) { return $s->user ? ($s->user->surname . ' ' . $s->user->firstname) : 'N/A'; })->implode(', ') }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($record->members->count() > 0)
                        {{ $record->members->count() }} member(s)
                    @else
                        -
                    @endif
                </td>
                <td>{{ $record->status == 1 ? 'Active' : 'Inactive' }}</td>
                <td>
                    @can('manufacturing.teams.show')
                        <a class="btn btn-info btn-sm" href="{{ route('manufacturing.teams.show', $record->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>
                    @endcan
                    @can('manufacturing.teams.edit')
                        <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.teams.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('manufacturing.teams.delete')
                        <form onsubmit="return confirm('Are you sure you want to delete this team?')"
                            action="{{ route('manufacturing.teams.destroy', $record->id) }}" method="post" style="display: inline">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                <i class="text-danger fa fa-remove"></i>
                            </button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
