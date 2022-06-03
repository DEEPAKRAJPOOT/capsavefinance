/* global messages, message */

try {
    var oTable, otable1;
    jQuery(document).ready(function ($) {
        oTable = $('#LimitList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.get_ajax_limit_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    // d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#LimitList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
            columns: [
                { data: 'limit_id' },
                { data: 'single_limit' },
                { data: 'multiple_limit' },
                { data: 'start_date' },
                { data: 'end_date' },
                { data: 'is_active' }
            ],
            aoColumnDefs: [{ 'bSortable': false, 'aTargets': [0, 1] }]
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