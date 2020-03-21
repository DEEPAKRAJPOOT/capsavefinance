/* global messages, message */
try {
    jQuery(document).ready(function ($) {

        oTables = $('#customerList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                url: messages.get_customer,
                method: 'POST',
                data: function (d) {
                    d.by_email = $('input[name=by_email]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d._token = messages.token;
                },
                error: function () { // error handling

                    $("#customerList").append('<tbody class="leadMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#customerList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'app_id'},
                {data: 'customer_id'},
                {data: 'virtual_acc_id'},
                {data: 'customer_name'},
                {data: 'customer_email'},
                {data: 'limit'},
                {data: 'consume_limit'},
                {data: 'available_limit'},
                {data: 'anchor'},
                {data: 'program_type'},
                {data: 'status'}
            ],
            aoColumnDefs: [{
                    'bSortable': false,
                    'aTargets': []
                }]

        });
        
        $('#searchB').on('click', function (e) {
            oTables.draw();

        });


    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}