
/* global messages, message */

try {
    var oTable, otable1;
    jQuery(document).ready(function ($) {
        //industries Listing code
        oTable = $('#baserateList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_base_rate_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#industriesList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'bank_id'},
                    {data: 'base_rate'},
                    {data: 'created_at'},
                    {data: 'created_by'},
                    {data: 'is_active'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': true, 'aTargets': [0,1,2]}]
        });
        //Search
        $('#searchbtn').on('click', function (e) {
            e.preventDefault();
            oTable.draw();
        });   
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
