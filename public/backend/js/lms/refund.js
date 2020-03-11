try {
    var oTable;
    var oTable1;
    var oTable2;
    var user_ids = [];
    
    jQuery(document).ready(function ($) {
        
        $(document).on('change', '.user_ids', function() {
            let id = $(this).val();
            if($(this).is(':checked')){
                user_ids.push(id);
            }else{
                user_ids = $.grep(user_ids, function(value) {
                    return value != id;
                });
            }
        });

        //User Listing code
        if($('#refundCustomerList').length){
            oTable = $('#refundCustomerList').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                searching: false,
                bSort: true,
                ajax: {
                    "url": messages.lms_get_refund_customer, // json datasource
                    "method": 'POST',
                    data: function (d) {
                        d.from_date = $('input[name="from_date"]').val();
                        d.to_date = $('input[name="to_date"]').val();
                        d.search_keyword = $('input[name=search_keyword]').val();
                        d._token = messages.token;
                    },
                    "error": function () {  // error handling

                        $("#refundCustomerList").append('<tbody class="appList-error"><tr><th colspan="8">' + messages.data_not_found + '</th></tr></tbody>');
                        $("#refundCustomerList_processing").css("display", "none");
                    }
                },
                columns: [
                    {data: 'user_id'},
                    {data: 'customer_code'},
                    {data: 'ben_name'},
                    {data: 'ben_bank_name'},
                    {data: 'ben_ifsc'},
                    {data: 'ben_account_no'},
                    {data: 'surplus_amount'},
                    {data: 'status'}
                ],
                aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
            });
        }

        //Search
        $('#searchbtn').on('click', function (e) {
            oTable.draw();
            $("#fromDate").val($('input[name="from_date"]').val());
            $("#toDate").val($('input[name="to_date"]').val());
            user_ids = [];

        });
        if($('#interestRefundList').length){

            oTable1 = $('#interestRefundList').DataTable({
                processing: true,
                serverSide: true,
                pageLength: '*',
                searching: false,
                bSort: true,
                ajax: {
                    "url": messages.lms_get_refund_adjust, // json datasource
                    "method": 'POST',
                    data: function (d) {
                        d.from_date = parent.$('#fromDate').val();
                        d.to_date = parent.$('#toDate').val(); 
                        d.user_ids = parent.user_ids;
                        d.action = messages.action;
                        d.trans_type = messages.interest_refund;
                        d._token = messages.token;
                    },
                    "error": function () {  // error handling

                        $("#interestRefundList").append('<tbody class="appList-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                        $("#interestRefundList_processing").css("display", "none");
                    }
                },
                columns: [
                    {data: 'trans_id'},
                    {data: 'customer_id'},
                    {data: 'trans_date'},
                    {data: 'invoice_no'},
                    {data: 'amount'},
                    {data: 'balance_amount'},
                    {data: 'action'}
                ],
                aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
            });
        }

        if($('#nonFactoredRefundList').length){
            oTable2 = $('#nonFactoredRefundList').DataTable({
                processing: true,
                serverSide: true,
                pageLength: '*',
                searching: false,
                bSort: true,
                ajax: {
                    "url": messages.lms_get_refund_adjust, // json datasource
                    "method": 'POST',
                    data: function (d) {
                        d.from_date = parent.$('#fromDate').val();
                        d.to_date = parent.$('#toDate').val(); 
                        d.user_ids = parent.user_ids;
                        d.action = messages.action;
                        d.trans_type = messages.non_factored;
                        d._token = messages.token;
                    },
                    "error": function () {  // error handling

                        $("#nonFactoredRefundList").append('<tbody class="appList-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                        $("#nonFactoredRefundList_processing").css("display", "none");
                    }
                },
                columns: [
                    {data: 'trans_id'},
                    {data: 'customer_id'},
                    {data: 'trans_date'},
                    {data: 'amount'},
                    {data: 'balance_amount'},
                    {data: 'action'}
                ],
                aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
            });
        }

        if($('#marginList').length){
            oTable2 = $('#marginList').DataTable({
                processing: true,
                serverSide: true,
                pageLength: '*',
                searching: false,
                bSort: true,
                ajax: {
                    "url": messages.lms_get_refund_adjust, // json datasource
                    "method": 'POST',
                    data: function (d) {
                        d.from_date = parent.$('#fromDate').val();
                        d.to_date = parent.$('#toDate').val(); 
                        d.user_ids = parent.user_ids;
                        d.action = messages.action;
                        d.trans_type = messages.margin;
                        d._token = messages.token;
                    },
                    "error": function () {  // error handling

                        $("#marginList").append('<tbody class="appList-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                        $("#marginList_processing").css("display", "none");
                    }
                },
                columns: [
                    {data: 'trans_id'},
                    {data: 'customer_id'},
                    {data: 'trans_date'},
                    {data: 'amount'},
                    {data: 'balance_amount'},
                    {data: 'action'}
                ],
                aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
            });
        }

    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}