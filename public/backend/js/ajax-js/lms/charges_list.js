

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
            ajax: {
                 "url": messages.get_lms_charges_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.type = $('input[name=type]').val();
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
                  
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,2]}]
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







                   
                    
                    
  