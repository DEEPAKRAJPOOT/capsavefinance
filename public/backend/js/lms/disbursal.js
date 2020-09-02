try {
    var oTable;
    jQuery(document).ready(function ($) {
        //User Listing code
        oTable = $('#disbursalCustomerList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.lms_get_disbursal_customer, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling

                    $("#disbursalCustomerList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#disbursalCustomerList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'customer_id'},
                {data: 'customer_code'},
                {data: 'app_id'},
                {data: 'ben_name'},
                {data: 'bank'},
                {data: 'total_invoice_amt'},
                {data: 'total_disburse_amt'},
                {data: 'total_actual_funded_amt'},
                {data: 'total_invoice'},
                {data: 'status'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0, 2, 3, 4, 5, 6, 7, 8, 9]}]
        });

        //Search
        $('#searchbtn').on('click', function (e) {
            oTable.draw();
        });

        var disbursalList = $('#disbursalList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.lms_get_disbursal_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=batch_no]').val();
                    d.from_date = $('input[name=from_date]').val();
                    d.to_date = $('input[name=to_date]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling

                    $("#disbursalList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#disbursalList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'batch_id'},
                {data: 'type'},
                {data: 'disburse_amount'},
                {data: 'approver'},
                {data: 'value_date'},
                {data: 'created_at'},
                {data: 'download_batch_excel'},
                {data: 'download_bank_resp'},
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });


        $('#searchB').on('click', function (e) {
            disbursalList.draw();
        });

        $('#from_date').datetimepicker({
            format: 'dd/mm/yyyy',
            //  startDate: new Date(),
            autoclose: true,
            minView: 2, });
        $('#to_date').datetimepicker({
            format: 'dd/mm/yyyy',
            //  startDate: new Date(),
            autoclose: true,
            minView: 2, });
        
        
        // $(document).on('click', '#reset', function () {
        //     $(this).parents('.row').find('input.form-control,select.form-control').each(function () {
               
        //         $(this).val('');
        //         disbursalList.draw();
        //     })
        // });


    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}