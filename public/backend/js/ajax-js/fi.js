try {
    var oTable;
    jQuery(document).ready(function ($) {
        //User Listing code
        oTable = $('#fiList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.get_fi_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    //d.search_keyword = $('input[name=search_keyword]').val();
                    //d.is_assign = $('select[name=is_assign]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#fiList").append('<tbody class="fiList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#fiList_processing").css("display", "none");
                }
            },
           columns: [
                {data: 'biz_addr_id'},
                {data: 'address_type'},
                {data: 'name'},
                {data: 'address'},
                {data: 'status'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4,5]}]

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
