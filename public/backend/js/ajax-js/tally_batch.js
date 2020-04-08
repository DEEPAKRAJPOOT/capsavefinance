try {
    var oTable, otable1;
    jQuery(document).ready(function ($) {
        oTableBatches = $('#batches').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_ajax_batches, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_name = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling                   
                    $("#batches").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    
                    {data: 'batch_no'},
                    {data: 'records_in_batch'},
                    {data: 'created_at'},
                    {data: 'action'},
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });  
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
