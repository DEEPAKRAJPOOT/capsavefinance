
/* global messages, message */

try {
    var oTable, otable1;
    jQuery(document).ready(function ($) {
        //Agency Listing code
        oTable = $('#agencyList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_agency_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_name = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#agencyList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'agency_id'},
                    {data: 'agency_name'},
                    {data: 'address'},
                    {data: 'email'},
                    {data: 'phone'},
                    {data: 'created_at'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,3,4,5,6]}]
        });

        //Agency User listing code
        oTable1 = $('#agencyUserList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_agency_user_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_name = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#agencyUserList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'user_id'},
                    {data: 'user_name'},
                    {data: 'agency_name'},
                    {data: 'email'},
                    {data: 'phone'},
                    {data: 'created_at'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,3,4,5,6]}]
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
