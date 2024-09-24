let body = $('body');
$(document).ready(function () {
    ajerks('GET', '/misc/ajax/categories', 'ajax-categories')
    ajerks('GET', '/misc/ajax/customers', 'ajax-customers')
    ajerks('GET', '/misc/ajax/suppliers', 'ajax-suppliers')
    ajerks('GET', '/misc/ajax/products', 'ajax-products')
    ajerks('GET', '/misc/ajax/products/active', 'ajax-active-products')
    ajerks('GET', '/misc/ajax/stores', 'ajax-stores')
    ajerks('GET', '/misc/ajax/branches', 'ajax-branches')
    ajerks('GET', '/misc/ajax/chart-of-accounts', 'ajax-chart-of-accounts')
    ajerks('GET', '/misc/ajax/general-accounts', 'ajax-general-accounts')
    ajerks('GET', '/misc/ajax/companies', 'ajax-companies')
    ajerks('GET', '/misc/ajax/users', 'ajax-users')
    ajerks('GET', '/misc/ajax/relation_officers', 'ajax-relation-officers')
    ajerks('GET', '/misc/ajax/store-products', 'ajax-store-products')
})

let bodi = $('body');

bodi.on('change', '.ajax-branches', function () {
    $('.ajax-stores').attr('branch_id', $(this).val())
    $('.ajax-users').attr('branch_id', $(this).val())
    $('.ajax-customers').attr('branch_id', $(this).val())
    ajerks('GET', '/misc/ajax/stores', 'ajax-stores')
    ajerks('GET', '/misc/ajax/customers', 'ajax-customers')
    ajerks('GET', '/misc/ajax/users', 'ajax-users')
    ajerks('GET', '/misc/ajax/relation_officers', 'ajax-relation-officers')
})

bodi.on('change', '.ajax-categories', function () {
    $('.ajax-products').attr('category_id', $(this).val())
    ajerks('GET', '/misc/ajax/products', 'ajax-products')
})

bodi.on('change', '.ajax-stores', function () {
    $('.ajax-store-products').attr('store_id', $(this).val())
    ajerks('GET', '/misc/ajax/store-products', 'ajax-store-products')
})


bodi.on('change', '.ajax-companies', function () {
    $('.ajax-branches').attr('company_id', $(this).val())
    ajerks('GET', '/misc/ajax/branches', 'ajax-branches')
})

function ajerks(method, url, cssClass) {
    let element = $('.' + cssClass);
    let data;
    if (element.attr('branch_id') && element.attr('name') === 'store_id') {
        let branch_id = element.attr('branch_id')
        data = { branch_id: branch_id }
    }
    if (element.attr('branch_id') && element.attr('name') === 'user_id') {
        let branch_id = element.attr('branch_id')
        data = { branch_id: branch_id }
    }
    if (element.attr('branch_id') && element.attr('name') === 'customer_id') {
        let branch_id = element.attr('branch_id')
        data = { branch_id: branch_id }
    }
    if (element.attr('category_id') && element.attr('name') === 'product_id') {
        let category_id = element.attr('category_id')
        data = { category_id: category_id }
    }
    if (element.attr('store_id') && element.attr('name') === 'product_id') {
        let store_id = element.attr('store_id')
        data = { store_id: store_id }

    }

    $.ajax({
        url: url,
        type: method,
        data,
        success: function (response) {
            element.html(response)
            mySelect2()
            // select default item
            if (element.attr('selected_item')) {
                let selected_item = element.attr('selected_item')
                element.find("[value='" + selected_item + "']").attr('selected', 'selected')
            }

        }
    })

}

function mySelect2() {

    $(".select2-single, .select2-multiple, .select2").select2({
        theme: "bootstrap",
        maximumSelectionSize: 6,
        containerCssClass: ':all:'
    });
}


//cart activities
let type = $('input[name=cart_page_type]').val();
let cart_container = $('.cart-container');

//load cart
$.ajax({
    url: "/ajax/cart/load",
    type: 'GET',
    data: {
        type: type
    }
}).done(function (component) {
    // console.log(component)
    cart_container.html(component)
})



//add cart item
$(document).on('submit', '.addCartItemForm', function (e) {
    e.preventDefault()
    if (type === 'order' || type === 'proforma' || type === 'invoice') {
        if ($('input[name=customer]').val() == '') {
            alert("Please select customer")
            return;
        }
        $('#customer_record').attr('disabled', true);
        $('#customer_val_id').val($('input[name=customer]').val());
        $('#account_type').attr('disabled', true);
    }

    if (type === 'adjustment') {
        if ($('select[name=operation]').val() == '') {
            alert("Please select operation")
            return;
        }
    }

    $.ajax({
        url: $(this).attr('action'),
        type: 'GET',
        data: $(this).serialize() + '&type=' + type,
    }).done(function (component) {
        //reset input only
        if (type !== 'order' && type !== 'proforma' && type !== 'invoice') {
            $('.addCartItemForm')[0].reset();
            $('.select2-single').val(null).trigger('change');
        }
        console.log(component)
        cart_container.html(component)
    })
})

//update to card
$(document).on('keyup', '.quantity, .price', function () {
    let id = $(this).attr('data-value');

    if (type == 'invoice' || type == 'returndebit') {
        $("#valid_qty" + id.substr(1)).html("");
        if (parseFloat($('#quantity' + id.substr(1)).val()) > parseFloat($('#quantity' + id.substr(1)).attr(
            'max-qty'))) {
            $("#valid_qty" + id.substr(1)).html("Typed QTY is more than the available QTY(" + $('#quantity' +
                id.substr(1)).attr('max-qty') + ")");
            $('#quantity' + id.substr(1)).val($('#quantity' + id.substr(1)).attr('max-qty'));
            return false;
        }
    }
    updateCart(id)

})

function updateCart(formId, formData = '') {
    let form = $('#' + formId);

    $.ajax({
        url: form.attr('action'),
        type: 'GET',
        data: form.serialize() + '&type=' + type,
    }).done(function (component) {
        console.log(component)
        formId = formId.substr(1);
        subtotal = $('#price' + formId).val() * $('#quantity' + formId).val();
        $('.subtotal' + formId).text(formatMoney(subtotal));
        $('.totalCart').text(formatMoney(component));
    })
}

//delete card
$(document).on('click', '.deleteCartItem', function (e) {

    e.preventDefault()
    $.ajax({
        url: $(this).attr('url'),
        type: 'GET',
        data: {
            type: type
        }
    }).done(function (component) {
        var numberOfForms = 0;
        $(component).find('form').each(function () {
            numberOfForms++;
        });

        if (numberOfForms == 0) {
            $('#customer_record').attr('disabled', false);
            $('#account_type').attr('disabled', false);
        }
        cart_container.html(component)
    })
})


//change store from card
$(document).on('change', '.store_code', function () {
    let id = $(this).attr('data-value');
    updateCart(id)

})

//change unit measure
$(document).on('change', '.unit_measure', function () {
    let id = $(this).attr('data-value');
    updateCart(id)
})

function formatMoney(n, c, d, t) {
    var c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ?
        d + Math.abs(n - i).toFixed(c).slice(2) : "");
};
