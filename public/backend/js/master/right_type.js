/* global messages, message */

try {

    var oTable;
    jQuery(document).ready(function ($) {
        
        //Right type master code
        oTable = $('#rightTypeMaster').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            bSort: true,
            searching: false,
            order: [[1, "desc"]],
            ajax: {
                "url": messages.get_ajax_right_type, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_title = $('input[name=by_title]').val();
                    d.status = $('select[name=status]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $(".rightTypeMaster-error").html("");
                    $("#rightTypeMaster").append('<tbody class="countryMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#rightTypeMaster_processing").css("display", "none");
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

        /*validation for right Type Master */
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
        $('#manageRightType').on('submit', function (e) {
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
        $('#delete_selected_right_type').on("click", function () {
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
                    url: messages.delete_right_type,
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
