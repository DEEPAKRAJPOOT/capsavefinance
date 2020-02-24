
/* global messages, message */

try {
    var oTable, otable1;
    jQuery(document).ready(function ($) {
        //Agency Listing code
        oTable = $('#transTypeList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_trans_type_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_name = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling                   
                    $("#transTypeList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'trans_type'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });

        //Search
        $('#searchbtn').on('click', function (e) {
            oTable.draw();
            oTable1.draw();
        });   
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
