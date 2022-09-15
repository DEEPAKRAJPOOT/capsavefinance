try {
    var oTable;
    jQuery(document).ready(function ($) {
        //User Listing code
        oTable = $('#lmsSoaList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 50,
            deferLoading: 0,
            dom: 'lBrtip',
            bSort: false,
            responsive: true,
            searching: false,
            ajax: {
                "url": messages.lms_get_soa_list, // json datasource
                "method": 'POST',
                data: function (d) {
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
                // {data: 'customer_name'},
                {data: 'trans_date', width:'80px'},
                {data: 'value_date', width:'80px'},
                {data: 'trans_type'},
                {data: 'tran_id'},
                {data: 'batch_no'},
                {data: 'invoice_no'},
                {data: 'capsave_invoice_no'},
                {data: 'narration'},
                {data: 'currency'},
                // {data: 'sub_amount'},
                {data: 'debit'},
                {data: 'credit'},
                {data: 'balance'}
            ],
            buttons: [
                
                {
                    text: 'PDF',
                    action: function ( e, dt, node, config ) {
                        if(messages.datataledraw==1){
                            download('pdf');
                        }else{
                            alert('Please select customer');
                            $("#search_keyword").focus();
                        }
                    }
                },
                {
                    text: 'Excel',
                    action: function ( e, dt, node, config ) {
                        if(messages.datataledraw==1){
                            download('excel');
                        }else{
                            alert('Please select customer');
                            $("#search_keyword").focus();
                        }
                    }
                }
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4,5,6,7]}]
        });

        //Search
        if(messages.datataledraw==1){
            oTable.draw();
        }
    });

    function download(action){
        url = '';

        from_date = $('input[name="from_date"]').val().trim();
        to_date = $('input[name="to_date"]').val().trim();
        trans_entry_type = $('select[name=trans_entry_type]').val().trim();
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

        if(trans_entry_type){
            url += '&trans_entry_type='+trans_entry_type;
        }
        window.open(url, '_blank');
    }

    function validate(){
        var res = true;
        var searchKeyword = $("#search_keyword").val();
        var userId = $("#user_id").val();

        if(searchKeyword){
            if(!userId){
                $('#search_keyword').focus();
                alert('Please select record from list!');
                res = false;
            }
        }else{
            $('#search_keyword').focus();
            alert('Client Id required');
            res = false;
        }

        return res;
    }

} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}