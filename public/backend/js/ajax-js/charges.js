
/* global messages, message */

try {
    var oTable, otable1;
    jQuery(document).ready(function ($) {
        //Agency Listing code
        oTable = $('#chargesList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_charges_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#chargesList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    // {data: 'chrg_name'},
                    {data: 'chrg_type'},
                    {data: 'chrg_calculation_amt'},
                    {data: 'is_gst_applicable'},
                    {data: 'created_at'},
                    {data: 'chrg_applicable_id'},
                    {data: 'chrg_desc'},
                    {data: 'is_active'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,3,4,5,6]}]
        });
        //Search
        $('#searchbtn').on('click', function (e) {
            oTable.draw();
        });   
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
