/* global messages, message */

try {

    var oTable;
    jQuery(document).ready(function ($) {
        
        //Country master code
        oTable = $('#countryMaster').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            order: [[0, "desc"]],
            ajax: {
                "url": messages.get_ajax_countries, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_name = $('input[name=by_name]').val();
                    d.status = $('select[name=is_active]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $(".countryMaster-error").html("");
                    $("#countryMaster").append('<tbody class="countryMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#countryMaster_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'checkbox'},
                {data: 'id'},
                {data: 'country_name'},
                {data: 'country_code'},
                {data: 'is_active'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0, 5]}]

        });

        /*validation for Country Master */
        $("#ProCountryMaster").validate({
            rules: {
                country_name: {
                    required: true,
                },
                country_code: {
                    required: true,
                    maxlength:3,
                    
                },
                status: {
                    required: true,
                },
            },
            messages: {
                country_name: {
                    required: messages.country_name
                },
                country_code: {
                    required: messages.country_code
                },
                status: {
                    required: messages.is_active
                },
            }
        });
        //Search
        $('#manageCountry').on('submit', function (e) {
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
                    url: messages.delete_countries,
                    async: true,
                    data: {cid: ids, _token: messages.token},
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
