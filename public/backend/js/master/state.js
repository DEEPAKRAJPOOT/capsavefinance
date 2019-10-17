/* global messages, message */

try {

    var oTable;
    jQuery(document).ready(function ($) {
        
        //state master code
        oTable = $('#stateMaster').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            bSort: true,
            searching: false,
            order: [[1, "desc"]],
            ajax: {
                "url": messages.get_ajax_states, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_state_name = $('input[name=by_state_name]').val();
                    d.status = $('select[name=is_active]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $(".stateMaster-error").html("");
                    $("#stateMaster").append('<tbody class="countryMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#stateMaster_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'checkbox'},
                {data: 'id'},
                {data: 'country_name'},
                {data: 'name'},
                {data: 'is_active'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0, 5]}]

        });

        /*validation for state Master */
        $("#ProStateMaster").validate({
            rules: {
                country_id: {
                    required: true,
                },
                state_name: {
                    required: true,
                    
                    
                },
                status: {
                    required: true,
                },
            },
            messages: {
                country_id: {
                    required: messages.country_name
                },
                state_name: {
                    required: messages.state_name
                },
                status: {
                    required: messages.is_active
                },
            }
        });
        //Search
        $('#manageState').on('submit', function (e) {
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
        $('#delete_selected_country').on("click", function () {
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
                    url: messages.delete_state,
                    async: true,
                    data: {state_id: ids, _token: messages.token},
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
