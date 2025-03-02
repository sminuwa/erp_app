 @php
     // Separate checked and unchecked permissions
     $checkedPermissions = [];
     $uncheckedPermissions = [];

     foreach ($permissions as $permission) {
         if (in_array($permission->id, $rolepermssions)) {
             $checkedPermissions[] = $permission;
         } else {
             $uncheckedPermissions[] = $permission;
         }
     }

     // Merge checked first, then unchecked
     $sortedPermissions = array_merge($checkedPermissions, $uncheckedPermissions);
 @endphp
<!-- Search Box -->
<input type="text" id="searchPermissions" class="form-control mb-3" placeholder="Search Permissions...">
 <table class='table table-bordered' id="record1">
     <thead>
         <tr>
             <td colspan="12" style="text-align: right;">Check All <input type="checkbox" id="checkAll" name="checkAll" />
             </td>
         </tr>
     </thead>
     <tbody>
         <tr>
             @php $i = 0; @endphp
             @foreach ($sortedPermissions as $permission)
                 <td>{{ ++$i }}</td>
                 <td>{{ $permission->description }}</td>
                 <td style="text-align: center; vertical-align: middle;">
                     <input type="checkbox" name="permissions[]" class="permissions" value="{{ $permission->id }}"
                         {{ in_array($permission->id, $rolepermssions) ? 'checked' : '' }} />
                 </td>

                 @if ($i % 4 == 0)
         </tr>
         <tr>
             @endif
             @endforeach
         </tr>
     </tbody>
 </table>

 <script>
     $(document).ready(function() {
         

         // Select/Deselect All
         $("#checkAll").on("change", function() {
             $(".permissions").prop("checked", this.checked);
         });
     });
 </script>
