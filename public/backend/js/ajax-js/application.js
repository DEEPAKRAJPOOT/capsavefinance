/* global messages, message */

try {
var oTable;
        $(document).ready(function () {
            oTable1 = $('#appList').DataTable({
                "order" : [[0, "asc"]],
                "sDom": "<'row'<'col-md-2'l><'col-md-7'a><'col-md-2'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                //"sPaginationType": "bootstrap",
                "processing": true,
                "searching": false,
                ajax: {
                "url": messages.get_lead, // json datasource
                "method": 'POST',
                data: function (d) {
                  d.by_email = $('input[name=by_email]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#leadMaster").append('<tbody class="leadMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
                columns: [
                 // {data: 'checkbox'},
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'assigned'},
                    
                    //{data: 'mobile_no'},
                    //{data: 'biz_name'},
                    {data: 'created_at'},
                    //{data: 'status'},
                    {data: 'action'}
                ],
                 aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4]}]
            });
           //Search
        $('#manageUser').on('submit', function (e) {
            e.preventDefault();
            oTable1.draw();

        });
            
        });
        
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
