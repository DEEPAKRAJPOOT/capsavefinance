/* global messages, message */

try {
var oTable;
        $(document).ready(function () {
            oTable1 = $('#leadMaster').DataTable({
                "order" : [[0, "asc"]],
                "sDom": "<'row'<'col-md-2'l><'col-md-7'a><'col-md-2'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                //"sPaginationType": "bootstrap",
                "processing": true,
                "serverSide": true,
                "searchable":false,
                ajax: {
                "url": messages.get_lead, // json datasource
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
                 // {data: 'checkbox'},
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'mobile_no'},
                    {data: 'biz_name'},
                    {data: 'created_at'},
                    {data: 'status'},
                    {data: 'action'}
                ]
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
