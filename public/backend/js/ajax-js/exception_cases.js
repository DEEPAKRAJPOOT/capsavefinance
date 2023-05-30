try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#invoiceListDisbursedQue').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ordering: false,
            ajax: {
                "url": messages.backend_get_ep_list_approve, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.anchor_id = $('select[name=search_anchor]').val();
                    d.supplier_id = $('select[name=search_supplier]').val();
                     d.biz_id = $('input[name=search_biz]').val();
                    d.front = $('input[name=front]').val();
                    d._token = messages.token;
                    d.app_id = messages.appp_id;
                },
                "error": function () {  // error handling
                   
                    $("#invoiceListDisbursedQue").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#appList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'anchor_id', width: '2%' },
                {data: 'invoice_id', width: '10%' },
                {data: 'anchor_name', width: '19%' },
                {data: 'supplier_name', width: '15%' },
                {data: 'invoice_date', width: '10%' },
                {data: 'invoice_amount', width: '13%' },
                {data: 'remark', width: '10%' },
                {data: 'updated_at', width: '10%' },
                {data: 'action', width: '5%' },
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,2]}]
        });

        //Search
         $('#search_biz').on('click', function (e) {
            oTable.draw();
        });                   
    });

    $(document).on('click', '#chkAll', function () {
        // Check or Uncheck All checkboxes
        $("#chkAll").change(function(){
            var checked = $(this).is(':checked');
            if(checked){
            $(".chkstatus").each(function(){
                $(this).prop("checked",true);
            });
            }else{
            $(".chkstatus").each(function(){
                $(this).prop("checked",false);
            });
            }
        });
    
     // Changing state of CheckAll checkbox 
        $(".chkstatus").click(function(){
    
            if($(".chkstatus").length == $(".chkstatus:checked").length) {
                $("#chkAll").prop("checked", true);
            } else {
                $("#chkAll").prop("checked", false);
            }
    
        });
    
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

