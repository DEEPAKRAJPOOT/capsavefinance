try {

    var oTable;
    jQuery(document).ready(function ($) {
        
        oTable = $('#ucicMaster').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: false,
            ajax: {
               "url": messages.get_ucic, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_code = $('input[name=by_code]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#ucicMaster").append('<tbody class="ucicMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#ucicMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'ucic_code'},
                    {data: 'app_id'},
                    {data: 'group_code'},
                    {data: 'group_name'},
                    {data: 'entity_name'},
                    {data: 'email'},
                    {data: 'created_at'},
                ],

        });

        $('#searchB').on('click', function (e) {
            oTable.draw();

        });
        
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
