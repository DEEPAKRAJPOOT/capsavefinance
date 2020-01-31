
/* global messages, message */

try {
    var oTable;
    jQuery(document).ready(function ($) {
        //Documents Listing code
        oTable = $('#documentsList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_documents_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.doc_type_id = $('select[name=doc_type_id]').val();
                    d.product_type = $('select[name=product_type]').val();
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#documentsList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'doc_type_id'},
                    {data: 'doc_name'},
                    {data: 'product_type'},
                    {data: 'is_rcu'},
                    {data: 'created_at'},
                    {data: 'created_by'},
                    {data: 'is_active'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4]}]
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
