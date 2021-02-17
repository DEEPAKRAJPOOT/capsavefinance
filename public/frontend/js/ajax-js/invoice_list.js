try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#invoiceList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.frontend_get_invoice_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.status_id = $('select[name=status_id]').val();
                    d.biz_id = $('input[name=search_biz]').val();
                    d._token = messages.token;
                  
                },
                "error": function () {  // error handling
                   
                    $("#invoiceList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#appList_processing").css("display", "none");
                }
            },
            columns: [
             
                {data: 'invoice_id'},
                {data: 'anchor_name'},
                {data: 'supplier_name'},
                {data: 'invoice_date'},
                {data: 'invoice_amount'},
                {data:'invoice_upload'},
                {data: 'status'}
               
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,2]}]
        });

        //Search
        // $('.searchbtn').on('change', function (e) {
        //     oTable.draw();
        // });   
        //Search
        $('#search_biz').on('click', function (e) {
            oTable.draw();
        });                 
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

