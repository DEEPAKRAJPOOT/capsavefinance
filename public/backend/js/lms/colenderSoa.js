try {
    var oTable;
    jQuery(document).ready(function ($) {
        oTable = $('#lmsSoaList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 50,
            dom: 'lBrtip',
            bSort: false,
            responsive: true,
            searching: false,
            ajax: {
                "url": messages.get_colender_soa_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.from_date = $('input[name="from_date"]').val();
                    d.to_date = $('input[name="to_date"]').val();
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d.user_id = $('input[name=user_id]').val();
                    d.customer_id = $('input[name=customer_id]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling

                    $("#lmsSoaList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#lmsSoaList_processing").css("display", "none");
                }
            },
            fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                var iscolor = 1; 
                if (aData.trans_type.indexOf('TDS') > -1 || aData.trans_type.indexOf('Refunded') > -1 || aData.trans_type.indexOf('Non Factored Amount') > -1)
                { iscolor = null; }
                if(aData.payment_id && iscolor){
                    $(nRow).css('background', '#ffcc0078');
                    $(nRow).css('line-height', '1');
                }
                if(aData.trans_type==' Repayment'){
                    $(nRow).css('background', '#f3c714');
                }
            },
            columns: [
                {data: 'customer_id'},
                {data: 'trans_date', width:'80px'},
                {data: 'value_date', width:'80px'},
                {data: 'trans_type'},
                {data: 'batch_no'},
                {data: 'invoice_no'},
                {data: 'narration'},
                {data: 'currency'},
                {data: 'debit'},
                {data: 'credit'},
                {data: 'balance'}
            ],
            buttons: [
                
                {
                    text: 'PDF',
                    action: function ( e, dt, node, config ) {
                        download('pdf');
                    }
                },
                {
                    text: 'Excel',
                    action: function ( e, dt, node, config ) {
                        download('excel');
                    }
                }
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4,5,6,7]}]
        });

        //Search
        $('#searchbtn').on('click', function (e) {
            $("#client_details").html('');
            var user_id = $.trim($("#user_id").val());
            var biz_id = $.trim($("#biz_id").val());
            
            //showClientDetails({user_id:user_id,biz_id:biz_id,_token: messages.token})
            oTable.draw();
        });

    });

    function download(action){
        url = '';
        from_date = $('input[name="from_date"]').val().trim();
        to_date = $('input[name="to_date"]').val().trim();
        customer_id = $('input[name=customer_id]').val().trim();
        if(action.trim() == 'pdf'){
            url = messages.pdf_soa_url;
        }
        if(action.trim() == 'excel'){
            url = messages.excel_soa_url;
        }
        if(from_date){
            url += '&from_date='+from_date;
        }
        if(to_date){
            url += '&to_date='+to_date;
        }
        window.open(url, '_blank');
    }

    function showClientDetails(data){
        $.ajax({
            type: "POST",
            url: messages.get_soa_client_details,
            data: data,
            dataType: "json",
            success: function (res) {
                var html = `<table class="table " cellpadding="0" cellspacing="0" style="margin-bottom: 22px;border-top-style: none;
                border-left-style: none;
                border-right-style: none;
                border-bottom-style: none;">
                            <tbody>
                                <tr>
                                    <td><b>Client Name</b></td>
                                    <td>`+res.client_name+`</td>
                                    <td><b>Date & Time</b></td>
                                    <td>`+res.datetime+`</td>
                                </tr>
                                <tr>
                                    <td><b>Address</b></td>
                                    <td>`+res.address+`</td>
                                    <td><b>Currency</b></td>
                                    <td>`+res.currency+`</td>
                                </tr>
                                
                                <tr>
                                    <td><b>Limit Amt</b></td>
                                    <td>`+res.limit_amt+`</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>`; 
                        //console.log(html);
                        //$("#client_details").html(html);
            }
        });
    }
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}