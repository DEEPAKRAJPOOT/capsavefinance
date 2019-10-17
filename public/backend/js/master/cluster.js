/* global messages, message */

try {

    var oTable;
    jQuery(document).ready(function ($) {
        
        //Cluster master code
        oTable = $('#clusterMaster').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            bSort: true,
            searching: false,
            order: [[1, "desc"]],
            ajax: {
                "url": messages.get_ajax_cluster, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_title = $('input[name=by_title]').val();
                    d.status = $('select[name=status]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $(".clusterMaster-error").html("");
                    $("#clusterMaster").append('<tbody class="clusterMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#clusterMaster_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'checkbox'},
                {data: 'id'},
                {data: 'title'},
                {data: 'is_active'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0, 4]}]

        });

        /*validation for Cluster Master */
        $("#ProClusterMaster").validate({
            rules: {
                title: {
                    required: true,
                },
                status: {
                    required: true,
                },
            },
            messages: {
                title: {
                    required: messages.title
                },
                status: {
                    required: messages.is_active
                },
            }
        });
        //Search
        $('#manageCluster').on('submit', function (e) {
            e.preventDefault();
            oTable.draw();

        });
        
        // add multiple select / deselect 
        $("#selectedAll").click(function () {
            if ($(this).is(':checked')) {
                $(".checkAllBox").prop("checked", true);
            } else
            {
                $(".checkAllBox").removeAttr("checked");
            }
        });
        
                //Multiple delete code here
        $('#delete_selected_cluster').on("click", function () {
            if ($(".del_selected:checked").length < 1) {
                alert('Please select at least 1 row');
                return false;
            }
            if ($('.del_selected:checked').length > 0) {  // at-least one checkbox checked
                var ids = [];
                $('.del_selected').each(function () {
                    if ($(this).is(':checked')) {
                        ids.push($(this).val());
                    }
                });
                $.ajax({
                    type: "POST",
                    url: messages.delete_cluster,
                    async: true,
                    data: {id: ids, _token: messages.token},
                    success: function (result) {
                        oTable.draw();
                    }
                });
            }
        });

    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
