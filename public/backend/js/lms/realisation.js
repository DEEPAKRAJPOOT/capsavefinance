try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#lmsSoaList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 50,
            bSort: false,
            responsive: true,
            searching: false,
            ajax: {
                "url": messages.lms_get_invoice_realisation_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.from_date = $('input[name="from_date"]').val();
                    d.to_date = $('input[name="to_date"]').val();
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#lmsSoaList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#lmsSoaList_processing").css("display", "none");
                }
            },
            "drawCallback": function( settings ) {
                excelUrl = settings.json.excelUrl;
                $('#dwnldEXCEL').attr('href', excelUrl)
                pdfUrl = settings.json.pdfUrl;
                $('#dwnldPDF').attr('href', pdfUrl)
            },
            columns: [
                {data: 'customer_id'},
                {data: 'debtor_name'},
                {data: 'debtor_acc_no'},
                {data: 'invoice_date'},
                {data: 'invoice_due_amount'},
                {data: 'invoice_due_amount_date'},
                {data: 'grace_period'},
                {data: 'relisation_date'},
                {data: 'relisation_amount'},
                {data: 'od'},
                {data: 'cheque'},
                {data: 'business'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4,5,6,7]}]
        });

        //Search
        $('#searchbtn').on('click', function (e) {
           oTable.draw();
        });

    });

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