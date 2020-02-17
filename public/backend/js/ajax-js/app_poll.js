/* global messages, message */

try {
var oTable;
        $(document).ready(function () {
            oTable1 = $('#apppollMaster').DataTable({
                "order" : [[0, "asc"]],
                //"sDom": "<'row'<'col-md-2'l><'col-md-7'a><'col-md-2'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                //"sPaginationType": "bootstrap",
                "autoWidth":false,
                "processing": true,
                "serverSide": true,
                "pageLength" : 25,
                searching: false,
                bSort:false,
                // "scrollY": 400,
                // "scrollX": true,
                // scrollCollapse: true,
//                 fixedColumns:   {
//            leftColumns: 1,
//            rightColumns: 1
//        },
            //bSort: true,
                ajax: {
                "url": messages.get_case_pool, // json datasource
                "method": 'POST',
                data: function (d) {
                  //  d.email = $('#customSearchBox').val();
                  //d.status = $('select[name=status]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    //$(".rightMaster-error").html("");
                    //$("#rightMaster").append('<tbody class="countryMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    //$("#rightMaster_processing").css("display", "none");
                }
            },
                columns: [
                    {data: 'app_id'},
                   {data: 'biz_entity_name'},
                    {data: 'name'},
                    {data: 'contact'},
                    // {data: 'email'},
                    // {data: 'mobile_no'},
                    {data: 'assoc_anchor'},
                    // {data: 'user_type'},
                    {data: 'assignee'},
                    {data: 'assigned_by'},
                    // {data: 'shared_detail'},
                    {data: 'status'},
                    {data: 'action'}
                ],
          aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,3,4,5,6,7]}]

            });
            $('#manageUser').on('click', function (e) {
                e.preventDefault();
            oTable1.draw();

        });
            
        });
        
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
