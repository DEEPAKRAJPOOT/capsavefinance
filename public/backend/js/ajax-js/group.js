/* global messages, message */
try {
    var oTable,oTable1;
    jQuery(document).ready(function ($) {
        oTable = $('#GroupList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: false,
            ajax: {
               "url": messages.get_all_group_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#GroupList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#GroupList_processing").css("display", "none");
                }
            },
           columns: [
                {data: 'group_code'},
                {data: 'group_name'},
                {data: 'created_at'},
                {data: 'updated_at'},
                {data: 'is_active'},
                {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4,5]}]
        });
        //Search
        $('#searchbtngroup').on('click', function (e) {
            oTable.draw();
        });

        oTable1 = $('#groupUcicList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: false,
            ajax: {
               "url": messages.get_group_ucic_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    // d.search_keyword = $('input[name=search_keyword]').val();
                    d.group_id = messages.group_id;
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#groupUcicList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#groupUcicList_processing").css("display", "none");
                }
            },
            "drawCallback": function (settings) {
                totalSanctionAmt = settings.json.total_sanction_amt;
                totalOutstandingAmt = settings.json.total_outstanding_amt;
                $(".curr-grp-sanc-amt").text(totalSanctionAmt);
                $(".curr-grp-out-amt").text(totalOutstandingAmt);
            },
            columns: [
                {data: 'ucic_code'},
                {data: 'entity_name'},
                {data: 'product_type'},
                {data: 'sanction'},
                {data: 'outstanding'},
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2]}]
        });
        //Search
        // $('#searchbtngroup').on('click', function (e) {
        //     oTable.draw();
        // });
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}