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
                        d._token = messages.token;
                    },
                    "error": function () {  // error handling
                        //$("#requestList").append('<tbody class="appList-error"><tr><th colspan="8">' + messages.data_not_found + '</th></tr></tbody>');
                        $("#requestList_processing").css("display", "none");
                    }
                },
                columns: [
                    {data: 'id'},
                    {data: 'ref_code'},
                    {data: 'customer_id'},
                    {data: 'biz_entity_name'},                    
                    {data: 'amount'},
                    {data: 'created_at'},
                    // {data: 'assignee'},
                    // {data: 'assignedBy'}
                ],
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
        }
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
        return alert('Please select at least one record!');
    }else{
        $(this).addClass('btn-disabled');
        if (confirm('Are you sure? You want to Submit it.')){
            $("#refundReqForm").submit();
        }else{
            $(this).removeClass('btn-disabled');
        }
    }
}) 
$(document).on('click','#approveBtn', function(){
    var countCheckedCheckboxes = $(".refund-request").filter(':checked').length;
    if(countCheckedCheckboxes <= 0){
        return alert('Please select at least one record!');
    }else{
        $(this).addClass('btn-disabled');
        if (confirm('Are you sure? You want to approve it.')){
            $("#refundReqForm").submit();
        }else{
            $(this).removeClass('btn-disabled');
        }
    }
}) 
$(document).on('click','#refundQueueBtn', function(){
    var countCheckedCheckboxes = $(".refund-request").filter(':checked').length;
    if(countCheckedCheckboxes <= 0){
        return alert('Please select at least one record!');
    }else{
        $(this).addClass('btn-disabled');
        if (confirm('Are you sure? You want to Disbursed it.')){
            $("#refundReqForm").submit();
        }else{
            $(this).removeClass('btn-disabled');
        }
    }
}) 
$(document).on('click','#sentToBankBtn', function(){
    var countCheckedCheckboxes = $(".refund-request").filter(':checked').length;
    if(countCheckedCheckboxes <= 0){
        return alert('Please select at least one record!');
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
        var invoiceIds = $('#transaction_ids').val().trim();
        if (invoiceIds.length == 0) {
            alert('Please select at least one record!');
            return false;
        }
        var dataUrl = $(this).attr('data-url');
        var newUrl = dataUrl+'&transaction_ids='+invoiceIds;
        $('#openDisburseInvoice').attr('data-url', newUrl);
        $('#openDisburseInvoice').trigger('click');
    });

    $(document).on('click', '.refund-request', function(){
        let current_id = $(this).val();
        if($(this).is(':checked')){
            let parent_inv_ids = $('#transaction_ids').val().trim();
            let allInvIds = parent_inv_ids.split(',');
            if(!parent_inv_ids.length){
                allInvIds = [];
            }
            if(allInvIds.length != 0){
                allInvIds.push(current_id);
                allInvIds.join();
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
    $(document).on('click', '#chkAll', function () {
        var isChecked = $("#chkAll").is(':checked');
        if (isChecked)
        {
            let parent_inv_ids = $('#transaction_ids').val().trim();
            let allInvIds = parent_inv_ids.split(',');
            if(!parent_inv_ids.length){
                allInvIds = [];
            }
            $('input:checkbox').attr('checked', 'checked');
            $("input:checkbox[name=checkinvoiceid]:checked").each(function(){
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
            $("input:checkbox[name=checkinvoiceid]:checked").each(function(){
                let current_id = $(this).val();
                allInvIds = allInvIds.filter(e => e !== current_id);
            });
            $('#transaction_ids').val(allInvIds.join());
            $('input:checkbox').removeAttr('checked');
        }
    });