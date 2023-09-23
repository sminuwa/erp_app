let body = $('body');
$(document).ready(function(){
    ajerks('GET','/misc/ajax/categories','ajax-categories')
    ajerks('GET','/misc/ajax/customers','ajax-customers')
    ajerks('GET','/misc/ajax/suppliers','ajax-suppliers')
    ajerks('GET','/misc/ajax/suppliers','ajax-suppliers')
})


function ajerks(method, url, cssClass ){
    $.ajax({
        url: url,
        type: method,
        success:function(response){
            $('.'+cssClass).html(response)
            // console.log(response)
        }
    })
}
