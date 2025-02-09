try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#lmsOutstandingLogsList').DataTable({
            destroy: true,
            //deferLoading: false,
            processing: true,
            serverSide: true,
            pageLength: 50,
            bSort: false,
            responsive: true,
            searching: false,
            ajax: {
                "url": messages.lms_get_invoice_outstanding_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.to_date = $('input[name="to_date"]').val();
                    d.user_id = $('input[name=user_id]').val();
                    d.customer_id = $('input[name=customer_id]').val();
                    d.generate_report = $('input[name=generate_report]').val();
                    d._token = messages.token;
                },
                complete: function () {
                    $('#sendMailBtn').prop('disabled', false); 

                },
                "error": function () {  // error handling   
                    $('#sendMailBtn').prop('disabled', false);                 
                    $("#lmsOutstandingLogsList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#lmsOutstandingLogsList_processing").css("display", "none");
                } 
                
            },
            columns: [
                {data: 'customer_id'},
                {data: 'date'},
                {data: 'created_at'},
                {data: 'created_by'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });

        //Search
        $('#sendMailBtn').on('click', function (e) {
            if(confirm('Are you sure you want to generate the report') === true){
                $("#generate_report").val('0');
                $('#sendMailBtn').prop('disabled', true);
                $("#to_date").parent().find('span.error').detach();
                if (!$("#to_date").val()) {
                    $("#to_date").parent().append('<span class="error">Please Select To Date</span>');
                    return false;
            }
            $("#generate_report").val('1')

            }else{
                return false;
            }
            $(".isloader").show(); 
            var user_id = $("#user_id").val();
            var to_date = $("#to_date").val();
            var generate_report = 1;
            var postData = ({'user_id':user_id, 'to_date':to_date, 'generate_report':generate_report, '_token': messages.token});
            jQuery.ajax({
                url: messages.lms_set_invoice_outstanding_date,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    $(".isloader").hide(); 
                    alert(errorThrown);
                },
                success: function (data) {
                    $(".isloader").hide(); 
                    oTable.draw();
                }
            });
        });
    });    
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}