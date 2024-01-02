<table class='table table-bordered'>
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
</table>
