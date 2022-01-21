

try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#chargesList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            'order': [],
            ajax: {
                 "url": messages.get_lms_charges_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.type = $('select[name=type]').val();
                    d.user_id = $('input[name=user_id]').val();
                    d.from_date = $('input[name=from_date]').val();
                    d.to_date = $('input[name=to_date]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#chargesList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#appList_processing").css("display", "none");
                }
            },
             columns: [
                {data: 'chrg_id'},
                {data: 'chrg_type'},
                {data: 'chrg_calculation_type'},
                {data: 'chrg_calculation_amt'},
                {data: 'is_gst_applicable'},
                {data: 'charge_percent'},
                {data: 'chrg_applicable_id'},
                {data: 'effective_date'},
                {data: 'applicability'}, 
                {data: 'chrg_desc'},
                {data: 'created_at'},
                {data: 'status'},                
            ],
            'columnDefs': [{
                "targets": [0,1,3],
                "orderable": false
            }]
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

function toggleAllCheckboxes()
{
    var isChecked = $("#selectAllCharges").is(':checked');
    if (isChecked) {
        $('input:checkbox.single_charge_select').prop('checked', true);
    } else {
        $('input:checkbox.single_charge_select').prop('checked', false);
    }
}

function selectSingleCharge(event)
{
    count = $(".single_charge_select:checked").length;
    if ($(".single_charge_select").length == $(".single_charge_select:checked").length) {
        $("#selectAllCharges").prop("checked", true);
    } else {
        $("#selectAllCharges").prop("checked", false);
    }
}

//////////////////////////// for bulk request for charge delete////////////////////
$(document).on('click', '#bulkReqForChrgDelete', function () {       
    var arr = [];
    i = 0;
    $(".single_charge_select:checked").each(function () {
        arr[i++] = $(this).val();
    });
 
    if (arr.length == 0) {
        replaceAlert('Please select atleast one checked', 'error');
        return false;
    }

    if (confirm('Are you sure, You want to request for deletion.'))
    {  
        $(".isloader").show(); 
        var userId = $("input[name='user_id']").val();
        var postData = ({'chrg_id': arr, 'user_id': userId, '_token': messages.token});
        jQuery.ajax({
            url: messages.lms_req_for_chrg_deletion,
            method: 'post',
            dataType: 'json',
            data: postData,
            error: function (xhr, status, errorThrown) {
                $(".isloader").hide();
                if (typeof xhr.responseJSON != 'undefined' && typeof xhr.responseJSON.errors != 'undefined' && typeof xhr.responseJSON.errors.chrg_id != 'undefined') {
                    alert(xhr.responseJSON.errors.chrg_id[0]);
                } else {
                    alert(errorThrown);
                }
            },
            success: function (data) {
                $(".isloader").hide();
                if (data.status == 1) {
                    $("#selectAllCharges").prop("checked", false);
                    replaceAlert(data.msg, 'success');
                    oTable.draw();
                } else {
                    replaceAlert(data.msg, 'error');
                }
           }
        });
    } else {
        return false;
    }
});

//////////////////////////// for bulk approval for charge deletion////////////////////
$(document).on('click', '#bulkApprovalForChrgDeletion', function () {       
    var arr = [];
    i = 0;
    $(".single_charge_select:checked").each(function () {
        arr[i++] = $(this).val();
    });
 
    if (arr.length == 0) {
        replaceAlert('Please select atleast one checked', 'error');
        return false;
    }

    if (confirm('Are you sure, You want to approve for deletion.'))
    {  
        $(".isloader").show(); 
        var postData = ({'chrg_id': arr, '_token': messages.token});
        jQuery.ajax({
            url: messages.lms_approve_chrg_deletion,
            method: 'post',
            dataType: 'json',
            data: postData,
            error: function (xhr, status, errorThrown) {
                $(".isloader").hide();
                if (typeof xhr.responseJSON != 'undefined' && typeof xhr.responseJSON.errors != 'undefined' && typeof xhr.responseJSON.errors.chrg_id != 'undefined') {
                    alert(xhr.responseJSON.errors.chrg_id[0]);
                } else {
                    alert(errorThrown);
                }
            },
            success: function (data) {
                $(".isloader").hide();
                if (data.status == 1) {
                    $("#selectAllCharges").prop("checked", false);
                    replaceAlert(data.msg, 'success');
                    oTable.draw();
                } else {
                    replaceAlert(data.msg, 'error');
                }
           }
        });
    } else {
        return false;
    }
});