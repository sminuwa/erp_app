let body = $('body');
$(document).ready(function(){
    ajerks('GET','/misc/ajax/categories','ajax-categories')
    ajerks('GET','/misc/ajax/customers','ajax-customers')
    ajerks('GET','/misc/ajax/suppliers','ajax-suppliers')
    ajerks('GET','/misc/ajax/products','ajax-products')
    ajerks('GET','/misc/ajax/chart-of-accounts','ajax-chart-of-accounts')
    ajerks('GET','/misc/ajax/general-accounts','ajax-general-accounts')
})


function ajerks(method, url, cssClass ){
    $.ajax({
        url: url,
        type: method,
        success:function(response){
            $('.'+cssClass).html(response)
            mySelect2()
            // console.log(response)
        }
    })
}

function mySelect2(){
    $(".select2-single, .select2-multiple, .select2").select2({
        theme: "bootstrap",
        maximumSelectionSize: 6,
        containerCssClass: ':all:'
    });
}
