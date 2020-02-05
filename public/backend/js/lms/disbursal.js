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
                    d.is_status = $('select[name=is_status]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling

                    $("#disbursalCustomerList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#disbursalCustomerList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'customer_code'},
                {data: 'ben_name'},
                {data: 'ben_bank_name'},
                {data: 'ben_ifsc'},
                {data: 'ben_account_no'},
                {data: 'total_invoice_amt'},
                {data: 'total_fund_amt'},
                {data: 'total_disburse_amt'},
                {data: 'total_invoice'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0, 2, 3, 4, 6, 7, 8]}]
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
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d.is_status = $('select[name=is_status]').val();
                    d.from_date = $('input[name=from_date]').val();
                    d.to_date = $('input[name=to_date]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling

                    $("#disbursalCustomerList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#disbursalCustomerList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'disburse_date'},
                {data: 'invoice_no'},
                {data: 'inv_due_date'},
                {data: 'invoice_approve_amount'},
                {data: 'principal_amount'},
                {data: 'status_name'},
                {data: 'disburse_amount'},
                {data: 'collection_date'},
                {data: 'collection_amount'},
                {data: 'accured_interest'},
                {data: 'surplus_amount'}
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
        
        
        $(document).on('click', '#reset', function () {
            $(this).parents('.row').find('input.form-control,select.form-control').each(function () {
               
                $(this).val('');
                disbursalList.draw();
            })
        });


    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}