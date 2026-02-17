<script type="text/javascript">
$(function() {
    var rowIndex = {{ isset($model->id) ? $model->materials->count() : 1 }};
    var products = @json($products);
    var stores = @json($stores);

    // Toggle batch fields based on BOM type
    function toggleBatchFields() {
        var bomType = $('#bom_type').val();
        if (bomType === 'batch') {
            $('#batch-fields').show();
        } else {
            $('#batch-fields').hide();
        }
    }

    toggleBatchFields();
    $('#bom_type').on('change', toggleBatchFields);

    // Initialize select2 for existing rows
    $('.select2-single').select2({
        width: '100%'
    });

    // Build product options HTML
    function buildProductOptions(selectedId) {
        var options = '<option value="">Select Product...</option>';
        products.forEach(function(p) {
            var sel = (selectedId && p.id == selectedId) ? 'selected' : '';
            options += '<option value="' + p.id + '" ' + sel + '>' + p.code + ' - ' + p.name + '</option>';
        });
        return options;
    }

    // Build store options HTML
    function buildStoreOptions(selectedId) {
        var options = '<option value="">Select Store...</option>';
        stores.forEach(function(s) {
            var sel = (selectedId && s.id == selectedId) ? 'selected' : '';
            options += '<option value="' + s.id + '" ' + sel + '>' + s.name + '</option>';
        });
        return options;
    }

    // Add new material row
    $('#add-material').on('click', function() {
        var newRow = `
            <tr class="material-row" data-row="${rowIndex}">
                <td>${$('.material-row').length + 1}</td>
                <td>
                    <select class="form-control select2-single material-product" name="materials[${rowIndex}][product_id]" required>
                        ${buildProductOptions()}
                    </select>
                </td>
                <td>
                    <select class="form-control select2-single material-store" name="materials[${rowIndex}][source_store_id]">
                        ${buildStoreOptions()}
                    </select>
                </td>
                <td>
                    <input type="number" step="0.0001" class="form-control material-qty" name="materials[${rowIndex}][quantity]" min="0.0001" required>
                </td>
                <td>
                    <input type="text" class="form-control material-unit-cost" value="0.00" readonly>
                </td>
                <td>
                    <input type="text" class="form-control material-total-cost" value="0.00" readonly>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-material"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;

        $('#materials-body').append(newRow);
        rowIndex++;

        // Initialize select2 for new row
        var lastRow = $('#materials-body tr:last');
        lastRow.find('.select2-single').select2({ width: '100%' });
    });

    // Remove material row - use event delegation
    $(document).on('click', '.remove-material', function() {
        if ($('.material-row').length > 1) {
            var row = $(this).closest('tr');
            // Destroy select2 before removing to prevent memory leaks
            row.find('.select2-single').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
            row.remove();
            renumberRows();
            calculateTotals();
        } else {
            alert('At least one material is required.');
        }
    });

    // Renumber rows
    function renumberRows() {
        $('.material-row').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    // Get product cost when product is selected
    $(document).on('change', '.material-product', function() {
        var row = $(this).closest('tr');
        var productId = $(this).val();

        if (productId) {
            $.ajax({
                url: '{{ route("manufacturing.ajax.product-cost") }}',
                type: 'GET',
                data: { product_id: productId },
                success: function(response) {
                    if (response.status) {
                        row.find('.material-unit-cost').val(parseFloat(response.cost).toFixed(2));
                        calculateRowTotal(row);
                        calculateTotals();
                    }
                }
            });
        } else {
            row.find('.material-unit-cost').val('0.00');
            row.find('.material-total-cost').val('0.00');
            calculateTotals();
        }
    });

    // Calculate row total when quantity changes
    $(document).on('input', '.material-qty', function() {
        var row = $(this).closest('tr');
        calculateRowTotal(row);
        calculateTotals();
    });

    // Calculate single row total
    function calculateRowTotal(row) {
        var qty = parseFloat(row.find('.material-qty').val()) || 0;
        var unitCost = parseFloat(row.find('.material-unit-cost').val()) || 0;
        var totalCost = qty * unitCost;
        row.find('.material-total-cost').val(totalCost.toFixed(2));
    }

    // Calculate all totals
    function calculateTotals() {
        var totalMaterialCost = 0;
        $('.material-total-cost').each(function() {
            totalMaterialCost += parseFloat($(this).val()) || 0;
        });

        var laborCost = parseFloat($('#labor_cost').val()) || 0;
        var powerCost = parseFloat($('#power_cost').val()) || 0;
        var otherCost = parseFloat($('#other_cost').val()) || 0;
        var totalOtherCost = laborCost + powerCost + otherCost;

        var totalCost = totalMaterialCost + totalOtherCost;
        var actualOutput = parseFloat($('#actual_output').val()) || 1;
        var unitCost = actualOutput > 0 ? totalCost / actualOutput : 0;

        $('#total-material-cost').text(totalMaterialCost.toFixed(2));
        $('#total-other-cost').text(totalOtherCost.toFixed(2));
        $('#summary-material-cost').text(totalMaterialCost.toFixed(2));
        $('#summary-other-cost').text(totalOtherCost.toFixed(2));
        $('#summary-total-cost').text(totalCost.toFixed(2));
        $('#summary-unit-cost').text(unitCost.toFixed(2));
    }

    // Recalculate when other costs change
    $(document).on('input', '.other-cost', function() {
        calculateTotals();
    });

    // Recalculate when actual output changes
    $('#actual_output').on('input', function() {
        calculateTotals();
    });

    // Refresh all material costs to current prices
    $('#refresh-costs').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Refreshing...');

        var pending = 0;
        var done = 0;

        $('.material-row').each(function() {
            var row = $(this);
            var productId = row.find('.material-product').val();
            if (productId) {
                pending++;
                $.ajax({
                    url: '{{ route("manufacturing.ajax.product-cost") }}',
                    type: 'GET',
                    data: { product_id: productId },
                    success: function(response) {
                        if (response.status) {
                            row.find('.material-unit-cost').val(parseFloat(response.cost).toFixed(2));
                            calculateRowTotal(row);
                        }
                    },
                    complete: function() {
                        done++;
                        if (done >= pending) {
                            calculateTotals();
                            $btn.prop('disabled', false).html('<i class="fa fa-sync"></i> Refresh Costs');
                        }
                    }
                });
            }
        });

        if (pending === 0) {
            $btn.prop('disabled', false).html('<i class="fa fa-sync"></i> Refresh Costs');
        }
    });

    // Initial calculation
    calculateTotals();
});
</script>
