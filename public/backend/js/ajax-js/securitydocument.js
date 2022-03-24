
/* global messages, message */

try {
    var oTable;
    jQuery(document).ready(function ($) {
        //industries Listing code
        oTable = $('#securityDocList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.get_securitydocument_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#securityDocList").append('<tbody class="securityDocList-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#securityDocList_processing").css("display", "none");
                }
            },
            columns: [
                { data: 'name' },
                { data: 'created_at' },
                { data: 'created_by' },
                { data: 'is_active' }
            ],
            aoColumnDefs: [{ 'bSortable': true, 'aTargets': [0, 1, 2] }]
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
