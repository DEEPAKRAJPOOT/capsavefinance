
try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#disbursalBatchRequest').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.backend_ajax_get_disbursal_batch_request, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.batch_id = $('input[name=batch_id]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling

                    $("#disbursalBatchRequest").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#disbursalBatchRequest_processing").css("display", "none");
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
            oTable.draw();
        });                  
    });

    function disbursal_rollback(url, ele) {
        var ele = $(ele);
        var oldHtml = ele.html();
        var tAmt = ele.attr("tAmt");
        var tInv = ele.attr("tInv");
        var tCust = ele.attr("tCust");
        var appId = ele.attr("appId");
        var inv_no = ele.attr("inv_no");
        var fullCustName = ele.attr("fullCustName");
        if (confirm("Are you sure you want to rollback the Online disbursal?\nCustomer name: "+fullCustName+" || App Id: "+appId+" || Total Customer: "+tCust+" || Total Amount: "+tAmt+" || Total Invoices: "+tInv+" || Invoices No: "+inv_no)) {
            $.ajax({
                type: "GET",
                url: url,
                beforeSend: function(res){
                    ele.html('<i class="fa fa-spinner" aria-hidden="true"></i>');
                    ele.prop('disabled', true);
                },
                success: function(res){
                    if(res.status == '1'){
                        ele.remove();
                        oTable.draw();
                        $("#iframeMessage").html('<div class="alert alert-success" role="alert">'+res.message+'</div>');
                    }else{
                        ele.html(oldHtml);
                        $("#iframeMessage").html('<div class="alert alert-danger" role="alert">'+res.message+'</div>');
                    }
                },
                error: function(res){
                    ele.html(oldHtml);
                    $("#iframeMessage").html('<div class="alert alert-danger" role="alert">Please try after some time.</div>');
                }
            });
        }
    }
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}


