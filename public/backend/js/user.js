/* global messages, message */

try {

    var oTable;
    jQuery(document).ready(function ($) {
        
        //User Listing code
        oTable = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            
            ajax: {
                "url": messages.get_users, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_email = $('input[name=by_email]').val();
                    d.status = $('select[name=is_active]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $(".userMaster-error").html("");
                    $("#userMaster").append('<tbody class="userMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#userMaster_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'checkbox'},
                {data: 'f_name'},
                {data: 'email'},
                {data: 'phone_no'},
                {data: 'created_at'},
                {data: 'status'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0, 6]}]

        });

        //Search
        $('#manageUser').on('submit', function (e) {
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
        $('#delete_selected_user').on("click", function () {
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
                    url: messages.delete_users,
                    async: true,
                    data: {uid: ids, _token: messages.token},
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
