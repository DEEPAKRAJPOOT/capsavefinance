try {
    var oTable;
    jQuery(document).ready(function ($) {
        oTable = $('#customer_report').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 50,
            bSort: false,
            responsive: true,
            searching: false,
            ajax: {
                "url": messages.get_all_customers, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.from_date = $('input[name="from_date"]').val();
                    d.to_date = $('input[name="to_date"]').val();
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {
                    $("#customer_report").append('<tbody class="error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#customer_report_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'sr_no'},
                {data: 'customer_name', width:'80px'},
                {data: 'email', width:'80px'},
                {data: 'mobile'},
                {data: 'biz_name'},
                {data: 'registered_on'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2]}]
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