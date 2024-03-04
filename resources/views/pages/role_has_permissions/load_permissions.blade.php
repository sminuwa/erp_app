{{-- <table class='table table-bordered' id="record1">
    <thead>
        <tr>
            <td colspan="12" style="text-align: right;">Check All <input type="checkbox" id="checkAll" name="checkAll" />
            </td>
        </tr>
    </thead>
    <tbody>
        <tr>
            @php
                $checked = '';
                $i = 0;
            @endphp
            @foreach ($permissions as $permission)
                <td>{{ $i = $loop->index + 1 }}</td>
                <td>{{ $permission->name }}</td>
                @if (in_array($permission->id, $rolepermssions))
                    <td style="text-align: center;vertical-align: middle;"><input type="checkbox" checked
                            name="permissions[]" class="permissions" value="{{ $permission->id }}" />
                    </td>
                @else
                    <td style="text-align: center;vertical-align: middle;"><input type="checkbox" name="permissions[]"
                            class="permissions" value="{{ $permission->id }}" />
                    </td>
                @endif

                @if ($i % 4 == 0)
        </tr>
        <tr>
            @endif
            @endforeach
        </tr>
    </tbody>
</table> --}}
<script>
    $('#record2,#record3').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "pageLength":200,
        "lengthMenu": [[50, 100, 150, 200, 250, 300,-1], [50, 100, 150, 200, 250, 300,"All"]],
    });
</script>

<table class='table table-bordered' id="record2">
    <thead>
        <tr>
            <th colspan="2"></th>
            <th style="text-align: right;">
                Check All <input type="checkbox" id="checkAll" name="checkAll" />
            </th>
            
        </tr>
    </thead>
    <tbody>

        @foreach ($permissions as $permission)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $permission->name }}</td>
                @if (in_array($permission->id, $rolepermssions))
                    <td style="text-align: center;vertical-align: middle;">
                        <input type="checkbox" checked name="permissions[]" class="permissions"
                            value="{{ $permission->id }}" />
                    </td>
                @else
                    <td style="text-align: center;vertical-align: middle;">
                        <input type="checkbox" name="permissions[]" class="permissions" value="{{ $permission->id }}" />
                    </td>
                @endif
            </tr>
        @endforeach

    </tbody>
</table>
