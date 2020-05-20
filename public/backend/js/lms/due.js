try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#lmsSoaList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.lms_get_due_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.from_date = $('input[name="from_date"]').val();
                    d.to_date = $('input[name="to_date"]').val();
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#lmsSoaList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#lmsSoaList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'batch_no'},
                {data: 'batch_date'},
                {data: 'bills_no'},
                {data: 'bill_date'},
                {data: 'due_date'},
                {data: 'invoice_amount'},
                {data: 'invoice_appr_amount'},
                {data: 'balance'}
            ],
            
        });

        $('#searchbtn').on('click', function (e) {
            oTable.draw();
        });                   
    });
    
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

