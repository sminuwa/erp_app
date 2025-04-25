<!-- Dependencies for Buttons (Print, PDF, Excel, etc.) -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    function loadDataTable2() {
        // Check if DataTable is already initialized and destroy it first
        if ($.fn.DataTable.isDataTable('.display')) {
            $('.display').DataTable().destroy();
        }

        // Initialize a fresh instance of DataTable
        $('.display').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 100,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "dom": 'Bfrtip',
            "buttons": [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    }

    $(document).ready(function() {
        loadDataTable2();
    });
</script>