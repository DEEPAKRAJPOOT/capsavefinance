
/* global messages, message */

try {
    var oTable;
    jQuery(document).ready(function ($) {        
        //Doa Level Listing
        oTable = $('#doaLevelList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.get_doa_levels_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#doaLevelList").append('<tbody class="doaLevelList-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#doaLevelList_processing").css("display", "none");
                }
            },
           columns: [
                {data: 'level_code'},
                {data: 'level_name'},
                {data: 'product_type'},
                {data: 'city'},
                {data: 'amount'},
                {data: 'role'},
                {data: 'is_active'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,3,4,5]}]
        });
        //Search
        $('#searchbtn').on('click', function (e) {
            oTable.draw();
        });
  
        $(document).on('click', '.doa_status', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            if(confirm('Are you sure? You want to change status.'))  
            {
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: messages.token
                    },
                    success: function (data) {
                        if (data.success) {
                            oTable.draw();
                        }
                    },
                    error: function () {
            
                    }
                });
            }
            else
            {
                return false;
            }
        });
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
