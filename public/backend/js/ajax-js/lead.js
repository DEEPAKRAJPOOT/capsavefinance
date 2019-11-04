/* global messages, message */

try {
var oTable;
        $(document).ready(function () {
            oTable = $('#leadMaster').DataTable({
                "order" : [[1, "desc"]],
                "sDom": "<'row'<'col-md-2'l><'col-md-7'a><'col-md-2'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                //"sPaginationType": "bootstrap",
                "processing": true,
                "serverSide": true,
                "ajax": messages.get_lead,
                "fnDrawCallback": function (oSettings) {
                    $(".iframe").colorbox({
                        iframe: true,
                        width: "80%",
                        height: "80%",
                        onClosed: function () {
                            window.location.reload();
                        }
                    });
                },
                columns: [
                    {data: 'checkbox'},
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'mobile_no'},
                    {data: 'biz_name'},
                    {data: 'created_by'},
                    {data: 'is_active'},
                    {data: 'action'}
                ]
            });
        });
        
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
