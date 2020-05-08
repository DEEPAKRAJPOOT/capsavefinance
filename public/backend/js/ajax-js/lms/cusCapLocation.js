/* global messages, message */
try {
    var oTable, otable1;
    jQuery(document).ready(function ($) {
        oTable = $('#cusCapLoc_list').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
                "url": message.get_cust_and_cap_loca, // json datasource
                "method": 'POST',
                data: function (d) {
                    d._token = message.token;
                    d.user_id = message.user_id;
                },
                "error": function () {  // error handling
                    $("#cusCapLoc_list").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + message.data_not_found + '</th></tr></tbody>');
                    $("#cusCapLoc_list_processing").css("display", "none");
                }
            },
            columns: [
                { data: 'sr_no' },
               { data: 'user_addr' },
               { data: 'user_state' },
               { data: 'comp_addr' },
               { data: 'comp_state' },
               { data: 'created_at' },
               { data: 'is_active' },
            ],
            aoColumnDefs: [{ 'bSortable': false, 'aTargets': [0, 1] }]
        });
        //Search
        // $('#searchbtn').on('click', function (e) {
        //     oTable.draw();
        // });
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
