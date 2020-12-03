/* global messages, message */
try {
    var oTable, otable1;
    jQuery(document).ready(function ($) {
        oTable = $('#invoices_list').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_user_invoice_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.invoice_no = $('#invoice_no').val();
                    d._token = messages.token;
                    d.user_id = messages.user_id;
                },
                "error": function () {  // error handling
                    $("#invoices_list").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#invoices_list_processing").css("display", "none");
                }
            },
           columns: [
                {data: 'reference_no'},
                {data: 'invoice_type'},
                {data: 'biz_gst_no'},
                {data: 'invoice_no'},
                {data: 'invoice_date'},
                {data: 'due_date'},
                {data: 'place_of_supply'},
                {data: 'action'},
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1]}]
        });
        //Search
        $('#searchbtn').on('click', function (e) {
            oTable.draw();
        });
        
     



            // popup default
            $(document).on('click', '.make_default', function () {
                var currentValue = ($(this).prop('checked')) ? 1 : 0;
                var acc_id = $(this).data('rel');
                $.confirm({
                    title: 'Confirm!',
                    content: 'Are you sure to Make Default?',
                    buttons: {
                        Yes: {
                            action: function () {
                                jQuery.ajax({
                                    url: messages.set_default_address,
                                    data: {biz_addr_id: acc_id, _token: messages.token , value: currentValue },
                                    'type': 'POST',
                                    beforeSend: function () {
                                       $('.isloader').show();
                                   },
                                    success: function (data) {
                                        $('.isloader').hide();
                                        oTable.draw();
                                    }
                                });
                            }

                        },
                        No: {
                            action: function () {
                            }
                        },
                    },

                });
            });
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
