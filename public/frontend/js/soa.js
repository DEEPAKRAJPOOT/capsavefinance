try {
    var oTable;
    jQuery(document).ready(function ($) {
        //User Listing code
        
        oTable = $('#lmsSoaList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 50,
            dom: 'lBrtip',
            bSort: false,
            responsive: true,
            searching: false,
            ajax: {
                "url": messages.lms_get_soa_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.from_date = $('input[name="from_date"]').val();
                    d.to_date = $('input[name="to_date"]').val();
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d.customer_id = $('input[name=customer_id]').val();
                    d.trans_entry_type = $('select[name=trans_entry_type]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling

                    $("#lmsSoaList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#lmsSoaList_processing").css("display", "none");
                }
            },
            fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                if(aData.backgroundColor){
                    $(nRow).css('background', aData.backgroundColor);
                }
            },
            columns: [
                {data: 'customer_id'},
                {data: 'trans_date', width:'80px'},
                {data: 'value_date', width:'80px'},
                {data: 'trans_type'},
                {data: 'tran_id'},
                {data: 'batch_no'},
                {data: 'invoice_no'},
                {data: 'capsave_invoice_no'},
                {data: 'narration'},
                {data: 'currency'},
                {data: 'debit'},
                {data: 'credit'},
                {data: 'balance'}
            ],
            buttons: [
                {
                    text:      '<i style="font-size: 16px;color: green;font-weight: normal;" class="fa fa-file-excel-o pr-sm-1"></i><span style="font-size: 14px;color: #3c8964;font-weight: bold;">Download SOA</span>',
                    className: "excel_button",
                    init: function(api, node, config) {
                        $(node).removeClass('dt-button')
                    },
                    action: function ( e, dt, node, config ) {
                        download('excel');
                    }
                },
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4,5,6,7]}]
        });
        //Search
        $('#searchbtn').on('click', function (e) {
            $("#client_details").html('');
            var user_id = $.trim($("#user_id").val());
            var biz_id = $.trim($("#biz_id").val());
            
            oTable.draw();
        });
    });

    function download(action){
        url = '';
        from_date = $('input[name="from_date"]').val().trim();
        to_date = $('input[name="to_date"]').val().trim();
        customer_id = $('input[name=customer_id]').val().trim();
        trans_entry_type = $('select[name=trans_entry_type]').val().trim();

        if(action.trim() == 'excel'){
            url = messages.excel_soa_url;
        }

        if(from_date){
            url += '&from_date='+from_date;
        }

        if(to_date){
            url += '&to_date='+to_date;
        }

        if(trans_entry_type){
            url += '&trans_entry_type='+trans_entry_type;
        }
        window.open(url, '_blank');
    }
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}