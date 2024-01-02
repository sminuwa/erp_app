<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                <a href="{{ route('chart_of_accounts.show', $record->id) }}"> {{ $record->id }}</a>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <a class="btn btn-secondary" href="{{ route('chart_of_accounts.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                        action="{{ route('chart_of_accounts.destroy', $record->id) }}" method="post"
                        style="display: inline">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-secondary cursor-pointer">
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
                    <th>Prefix</th>
                    <td>{{ $record->prefix }}</td>
                </tr>
                <tr>
                    <th>Class</th>
                    <td>{{ $record->class }}</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{ $record->description }}</td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
