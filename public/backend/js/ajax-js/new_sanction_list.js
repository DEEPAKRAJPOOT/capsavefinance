try {
    var oTable;
    jQuery(document).ready(function ($) {
        //User Listing code
        oTable = $('#new_sanction_list').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.get_new_sanction_letter_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d._token = messages.token;
                    d.app_id = messages.app_id;
                    d.biz_id = messages.biz_id;
                },
                "error": function () {  // error handling

                    $("#new_sanction_list").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#new_sanction_list_processing").css("display", "none");
                }
            },
            columns: [
                { data: 'ref_no' },
                { data: 'date_of_final_submission' },
                { data: 'status' },
                { data: 'created_by' },
                { data: 'created_at' },
                { data: 'action' },

            ],
            aoColumnDefs: [{ 'bSortable': false, 'aTargets': [0, 1, 3, 5] }]
        });

        //Search
        $('#search_biz').on('click', function (e) {
            oTable.draw();
        });
        $(document).on('click', '#regenerateButton', function () {
            if (confirm('Are you sure you want to regenerate sanction letter again?')) {
                let current_id = $(this).attr("data-id");
                if (current_id) {
                    var postData = ({ 'sanction_id': current_id, app_id: messages.app_id, '_token': messages.token });
                    jQuery.ajax({
                        url: messages.ajax_update_regenerate_sanction_letter,
                        method: 'post',
                        dataType: 'json',
                        data: postData,
                        error: function (xhr, status, errorThrown) {
                            alert(errorThrown);
                        },
                        success: function (data) {
                            if (data.status == 1) {
                                oTable.draw();
                                $("#createSanctionLetterA").removeClass("hide");
                            } else {
                                $("#createSanctionLetterA").addClass("hide");
                            }

                        }
                    });
                } else {
                    alert('Data Missing, Please try again!');
                }
            } else {
                return false;
            }
        });
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

