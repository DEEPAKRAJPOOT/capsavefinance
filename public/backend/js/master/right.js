/* global messages, message */

try {

    var oTable;
    jQuery(document).ready(function ($) {
        
        //Source master code
        oTable = $('#rightMaster').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            pageLength: 10,
            bSort: true,
            deferRender: true,
            scrollY: 600,
            scrollCollapse: true,
            scroller: {
                rowHeight: 40,
                boundaryScale: 0.5,
                displayBuffer: 3
            },
            searching: false,
            order: [[1, "desc"]],
            ajax: {
                "url": messages.get_ajax_right, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_title = $('input[name=by_title]').val();
                    d.status = $('select[name=status]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $(".rightMaster-error").html("");
                    $("#rightMaster").append('<tbody class="countryMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#rightMaster_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'checkbox'},
                {data: 'user_name'},
                {data: 'title'},
                {data: 'number'},
                {data: 'phase'},
                {data: 'is_featured'},
                {data: 'action'}
             
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4,5,6]}]

        });
       
       
       /*validation for edit Right */
        $("#ProRightMaster").validate({
            rules: {
                title: {
                    required: true,
                },
                type_id: {
                    required: true,
                },
                number: {
                    required: true,
                },
                inventor: {
                    required: true,
                },
                assignee: {
                    required: true,
                },
                cluster_id: {
                    required: true,
                },
                right_for_id: {
                    required: true,
                },
                url: {
                    required: true,
                },
                description: {
                    required: true,
                },
                keywords: {
                    required: true,
                },
                is_trading: {
                    required: true,
                },
                trading_type: {
                    required: true,
                },
                phase: {
                    required: true,
                },
                is_featured: {
                    required: true,
                }
            },
            
        });
       
       
       

        /*validation for Source Master */
        $("#ProRightMaster").validate({
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
        $('#manageSource').on('submit', function (e) {
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
        $('#delete_selected_source').on("click", function () {
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
                    url: messages.delete_right,
                    async: true,
                    data: {id: ids, _token: messages.token},
                    success: function (result) {
                        oTable.draw();
                        $('.mycls').removeClass('hide');
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
