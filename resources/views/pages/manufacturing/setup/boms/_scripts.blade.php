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

    // Add new material row
    $('#add-material').on('click', function() {
        var productOptions = '<option value="">Select Product...</option>';
        products.forEach(function(p) {
            productOptions += '<option value="' + p.id + '">' + p.code + ' - ' + p.name + '</option>';
        });

        var storeOptions = '<option value="">Select Store...</option>';
        stores.forEach(function(s) {
            storeOptions += '<option value="' + s.id + '">' + s.name + '</option>';
        });

        var newRow = `
            <tr class="material-row" data-row="${rowIndex}">
                <td>${rowIndex + 1}</td>
                <td>
                    <select class="form-control select2-single material-product" name="materials[${rowIndex}][product_id]" required>
                        ${productOptions}
                    </select>
                </td>
                <td>
                    <select class="form-control select2-single material-store" name="materials[${rowIndex}][source_store_id]">
                        ${storeOptions}
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

        // Re-initialize select2 for new row
        $('#materials-body tr:last .select2-single').select2({
            width: '100%'
        });

        renumberRows();
    });

    // Remove material row
    $(document).on('click', '.remove-material', function() {
        if ($('.material-row').length > 1) {
            $(this).closest('tr').remove();
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
    $('.other-cost').on('input', function() {
        calculateTotals();
    });

    // Recalculate when actual output changes
    $('#actual_output').on('input', function() {
        calculateTotals();
    });

    // Initial calculation
    calculateTotals();
});
</script>
