try {
    var oTable, otable1;
    jQuery(document).ready(function ($) {
        oTable = $('#transTypeList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: true,
            bSort: true,
            ajax: {
               "url": messages.get_trans_type_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_name = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling                   
                    $("#transTypeList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'trans_type'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });

        
        oTableJournal = $('#journalList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: true,
            bSort: true,
            ajax: {
               "url": messages.get_ajax_journal_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_name = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling                   
                    $("#journalList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'name'},
                    {data: 'journal_type'},
                    {data: 'is_active'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });

        oTableAccount = $('#accountList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: true,
            bSort: true,
            ajax: {
               "url": messages.get_ajax_account_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_name = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling                   
                    $("#accountList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'account_code'},
                    {data: 'account_name'},
                    {data: 'is_active'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });

        oTableVariable = $('#variableList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: true,
            bSort: true,
            ajax: {
               "url": messages.get_ajax_variable_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_name = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling                   
                    $("#variableList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'name'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });


        oTableJeConfigList = $('#jeConfigList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: true,
            bSort: true,
            ajax: {
               "url": messages.get_ajax_jeconfig_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_name = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling                   
                    $("#jeConfigList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'journal_name'},
                    {data: 'journal_type'},
                    {data: 'trans_type'},
                    {data: 'variable_name'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });


        oTableJiConfigList = $('#jiConfigList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: true,
            bSort: true,
            ajax: {
                "url": messages.get_ajax_jiconfig_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_name = $('input[name=search_keyword]').val();
                    d.je_config_id = $('input[name=je_config_id]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling                   
                    $("#jiConfigList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'account_name'},
                    {data: 'is_partner'},
                    {data: 'label'},
                    {data: 'value_type'},
                    {data: 'config_value'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });

        oTableTransactions = $('#transactions').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_ajax_transactions, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_name = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling                   
                    $("#transactions").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'biz_id'},
                    {data: 'fullname'},
                    {data: 'amount'},
                    {data: 'amount_type'},
                    {data: 'reference'},
                    {data: 'journals_name'},
                    {data: 'mode_of_pay'},
                    {data: 'narration'},
                    {data: 'created_by'},
                    {data: 'date'},
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });

        // //Search
        // $('#searchbtn').on('click', function (e) {
        //     oTable.draw();
        //     oTable1.draw();
        // });   
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
