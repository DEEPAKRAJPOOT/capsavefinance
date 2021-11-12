try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#lmsOverdueLogsList').DataTable({
            destroy: true,
            deferLoading: false,
            processing: true,
            serverSide: true,
            pageLength: 50,
            bSort: false,
            responsive: true,
            searching: false,
            ajax: {
                "url": messages.lms_get_invoice_over_due_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.to_date = $('input[name="to_date"]').val();
                    d.user_id = $('input[name=user_id]').val();
                    d.customer_id = $('input[name=customer_id]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling                   
                    $("#lmsOverdueLogsList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#lmsOverdueLogsList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'customer_id'},
                {data: 'date'},
                {data: 'created_at'},
                {data: 'created_by'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });

        //Search
        $('#sendMailBtn').on('click', function (e) {
            if (!$("#to_date").val()) {
                $("#to_date").parent().append('<span class="error">Please Select To Date</span>');
                return false;
            }
            oTable.draw();
        });
        oTable.draw();
    });    
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}