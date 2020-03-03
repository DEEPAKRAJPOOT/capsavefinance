try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#payment_advice').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.backend_get_payment_advice, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.type = $('select[name=type]').val();
                    d.date = $('input[name=date]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#payment_advice").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#payment_advice_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'customer_id'},
                {data: 'f_name'},
                {data: 'trans_date'},
                {data: 'created_at'},
                {data: 'amount'},
                {data: 'action'},
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,2]}]
        });

        //Search
        $('.searchbtn').on('click', function (e) {
            oTable.draw();
        });                   
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

