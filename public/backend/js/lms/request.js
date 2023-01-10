try {
    var oTable;
    var oTable2;
    var reqIds = [];

    jQuery(document).ready(function ($) {
        //User Listing code
        if($('#requestList').length){
            oTable = $('#requestList').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                searching: false,
                bSort: true,
                ajax: {
                    "url": messages.url, // json datasource
                    "method": 'POST',
                    data: function (d) {
                        d.status = messages.status
                        d.search_keyword = $('input[name=search_keyword]').val();
                        d._token = messages.token;
                    },
                    "error": function () {  // error handling
                        //$("#requestList").append('<tbody class="appList-error"><tr><th colspan="8">' + messages.data_not_found + '</th></tr></tbody>');
                        $("#requestList_processing").css("display", "none");
                    }
                },
                columns: messages.columns,
                aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
            });
        }
        if($('#approvedList').length){
            oTable = $('#approvedList').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                searching: false,
                bSort: true,
                ajax: {
                    "url": messages.url, // json datasource
                    "method": 'POST',
                    data: function (d) {
                        d.status = messages.status
                        d.search_keyword = $('input[name=search_keyword]').val();
                        d._token = messages.token;
                    },
                    "error": function () {  // error handling
                        $("#approvedList").append('<tbody class="appList-error"><tr><th colspan="8">' + messages.data_not_found + '</th></tr></tbody>');
                        $("#approvedList_processing").css("display", "none");
                    }
                },
                columns: [
                    {data: 'ref_code'},
                    {data: 'batch_no'},
                    {data: 'customer_id'},
                    {data: 'biz_entity_name'},                    
                    {data: 'banck_detail'},
                    {data: 'amount'},
                    {data: 'updated_at'},
                    {data: 'action'}
                ],
                aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
            });

            if(messages.status == 8){
                oTable.columns([7]).visible(false);
            }
        }

        $('#searchbtn').on('click', function (e) {
            oTable.draw();
        });

    });

    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable2 = $('#refundBatchRequest').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.backend_ajax_get_refund_batch_request, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.batch_id = $('select[name=batch_id]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling

                    $("#refundBatchRequest").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#refundBatchRequest_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'batch_id'},
                {data: 'total_customer'},
                {data: 'total_disburse_amount'},
                {data: 'created_at'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0, 1, 2]}]
        });

        //Search
        $('#searchbtn').on('click', function (e) {
            oTable2.draw();
        });                  
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}


// $(document).on('change', '.refund-request', function(){
//     var val = $(this).val();
//     if($(this).is(":checked")){
//         reqIds.push(val);
//     }else{
//         reqIds = $.grep(reqIds, function(value) { 
//             return value != val; 
//         });
//     }
// });

$(document).on('click','#pendingBtn', function(){
    var countCheckedCheckboxes = $(".refund-request").filter(':checked').length;
    if(countCheckedCheckboxes <= 0){
        replaceAlert('Please select at least one record!', 'error');
        return false;
    }else{
        $(this).addClass('btn-disabled');
        if (confirm('Are you sure you want to generate the refund request?.')){
            $("#refundReqForm").submit();
        }else{
            $(this).removeClass('btn-disabled');
        }
    }
}) 
$(document).on('click','#approveBtn', function(){
    var countCheckedCheckboxes = $(".refund-request").filter(':checked').length;
    if(countCheckedCheckboxes <= 0){
        replaceAlert('Please select at least one record!', 'error');
        return false;
    }else{
        $(this).addClass('btn-disabled');
        if (confirm('Are you sure you want to approve the refund request?')){
            $("#refundReqForm").submit();
        }else{
            $(this).removeClass('btn-disabled');
        }
    }
}) 
$(document).on('click','#refundQueueBtn', function(){
    var countCheckedCheckboxes = $(".refund-request").filter(':checked').length;
    if(countCheckedCheckboxes <= 0){
        replaceAlert('Please select at least one record!', 'error');
        return false;
    }else{
        $(this).addClass('btn-disabled');
        if (confirm('Are you sure you want to move the request to refund queue?')){
            $("#refundReqForm").submit();
        }else{
            $(this).removeClass('btn-disabled');
        }
    }
}) 
$(document).on('click','#sentToBankBtn', function(){
    var countCheckedCheckboxes = $(".refund-request").filter(':checked').length;
    if(countCheckedCheckboxes <= 0){
        replaceAlert('Please select at least one record!1');
        return false;
    }else{
        $(this).addClass('btn-disabled');
        if (confirm('Are you sure? You want to Sent To Bank it.')){
            $("#refundReqForm").submit();
        }else{
            $(this).removeClass('btn-disabled');
        }
    }
}) 

    $(document).on('click', '.disburseClickBtn', function(){
        var invoiceIds = selectedTransactionIds;
        if (invoiceIds.length == 0) {
            alert('Please select at least one record!');
            return false;
        }
        var dataUrl = $(this).attr('data-url');
        if (invoiceIds.length == 0) {
            replaceAlert('Please select atleast one Ref No', 'error');
            return false;
        }
        var newUrl = dataUrl+'&transaction_ids='+invoiceIds;
        $('#openDisburseInvoice').attr('data-url', newUrl);
        $('#openDisburseInvoice').trigger('click');
    });

   /* $(document).on('click', '.refund-request', function(){
        let current_id = $(this).val();
        if($(this).is(':checked')){
            let parent_inv_ids = $('#transaction_ids').val().trim();
            let allInvIds = parent_inv_ids.split(',');
            if(!parent_inv_ids.length){
                allInvIds = [];
            }
            if(allInvIds.length != 0){
                allInvIds.push(current_id);
                //allInvIds.join();
                $('#transaction_ids').val(allInvIds.join());
            }else{
                $('#transaction_ids').val(current_id);
            }
            
        }else{
            let parent_inv_ids = $('#transaction_ids').val().trim();
            let allInvIds = parent_inv_ids.split(',');
            if(!parent_inv_ids.length){
                allInvIds = [];
            }
            allInvIds = allInvIds.filter(e => e !== current_id);
            $('#transaction_ids').val(allInvIds.join());
        }
    });
    $(document).on('click', '#chkAll_1', function () {
        var isChecked = $("#chkAll").is(':checked');
        if (isChecked)
        {
            let parent_inv_ids = $('#transaction_ids').val().trim();
            let allInvIds = parent_inv_ids.split(',');
            if(!parent_inv_ids.length){
                allInvIds = [];
            }
            $('input:checkbox').attr('checked', 'checked');
            $("input:checkbox[class=refund-request]:checked").each(function(){
                let current_id = $(this).val();
                allInvIds.push(current_id);
                allInvIds.join();
            });
            $('#transaction_ids').val(allInvIds.join());
        } else {
            let parent_inv_ids = $('#transaction_ids').val().trim();
            let allInvIds = parent_inv_ids.split(',');
            if(!parent_inv_ids.length){
                allInvIds = [];
            }
            $("input:checkbox[class=refund-request]:checked").each(function(){
                let current_id = $(this).val();
                allInvIds = allInvIds.filter(e => e !== current_id);
            });
            $('#transaction_ids').val(allInvIds.join());
            $('input:checkbox').removeAttr('checked');
        }
    });*/


     //////////////// for checked & unchecked////////////////
   /*  $(document).on('click', '#chkAll', function () {
        var isChecked = $("#chkAll").is(':checked');
        if (isChecked)
        {
            $('input:checkbox').attr('checked', 'checked');
        } else
        {
            $('input:checkbox').removeAttr('checked');
        }
    });*/

 
var selectedTransactionIds = [];

$(document).on('click', '.refund-request', function(){
    let current_id = $(this).val();
    if($(this).is(':checked')){
        selectedTransactionIds.push(current_id);
    }else{
        selectedTransactionIds = selectedTransactionIds.filter(e => e !== current_id);
    }
    console.log(selectedTransactionIds);
});

$(document).on('click', '#chkAll', function () {
    var isChecked = $("#chkAll").is(':checked');
    $("input:checkbox[class=refund-request]").prop('checked', isChecked);
    if (isChecked)
    {
        $("input:checkbox[class=refund-request]:checked").each(function(){
            let current_id = $(this).val();
            selectedTransactionIds.push(current_id);
        });
    } else {
        $("input:checkbox[class=refund-request]:checked").each(function(){
            let current_id = $(this).val();
            selectedTransactionIds = selectedTransactionIds.filter(e => e !== current_id);
        });
    }
    console.log(selectedTransactionIds);
});
