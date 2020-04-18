
/* global messages, message */


var sample_data = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    prefetch:messages.get_customer,
    remote:{
        url:messages.get_customer+'?query=%QUERY',
        wildcard:'%QUERY'
    }
});

$('#search_bus ').typeahead(null, {
    name: 'sample_data',
    display: 'customer_id',
    source:sample_data,
    limit: 'Infinity',
    templates:{
        suggestion:Handlebars.compile(' <div> <div class="col-md-12" style="padding-right:5px; padding-left:5px;">@{{biz_entity_name}} <small>( @{{customer_id}} )</small></div> </div>') 
    },
}).bind('typeahead:select', function(ev, suggestion) {
    setClientDetails(suggestion)
}).bind('typeahead:change', function(ev, suggestion) {
    var customer_id = $.trim($("#customer_id").val());
    if(customer_id != suggestion)
    setClientDetails(suggestion)
}).bind('typeahead:cursorchange', function(ev, suggestion) {
    setClientDetails(suggestion)
});

function setClientDetails(data){
    $("#user_id").val(data.user_id);
    $("#customer_id").val(data.customer_id);
    $('#search_bus_error').html('');
}

$(document).on('click', '#move_to_settle', function() {
    let payment_ids = [];

    if ($(".payment_ids").length <= 0) {
        $('#search_bus_error').html('Please Search & Select the Business Name.');
        return false;
    }

    $('.payment_ids').each(function() {
       if($(this).is(":checked")){
         payment_ids.push($(this).val());
       }
    })
    if (!payment_ids.length) {
        $('#search_bus_error').html('Please select at least on payment.');
        return false;
    }else{
        $('#search_bus_error').html('');
    }
    console.log(payment_ids);
})

try {
    var oTable;
    jQuery(document).ready(function ($) {
        oTable = $('#payments_txns').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.get_to_settle_payments,
                "method": 'POST',
                data: function (d) {
                    d.user_id = $("#user_id").val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#payments_txns").append('<tbody class="payments_txns-error"><tr><th colspan="6">No Records Found.</th></tr></tbody>');
                    $("#payments_listing_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'customer_id'},
                    {data: 'user_name'},
                    {data: 'business_name'},
                    {data: 'virtual_account'},
                    {data: 'amount'},
                    {data: 'trans_type'},
                    {data: 'updated_by'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'aTargets': [0,1], 'bSortable': true}]
        });
        //Search
        $(document).on('click','#searchbtn', function (e) {
            if (!$("#user_id").val()) {
                $('#search_bus_error').html('Please Search & Select the Business Name.');
            }else{
                $('#search_bus_error').html('');
                oTable.draw();
            }
        });   
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
