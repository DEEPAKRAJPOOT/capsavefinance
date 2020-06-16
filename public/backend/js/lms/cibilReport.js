/* global messages, message */
try {
    jQuery(document).ready(function ($) {

        oTables = $('#cibilReports').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 50,
            searching: false,
            bSort: true,
            ajax: {
                url: messages.get_cibil_report_lms,
                method: 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d.from_date = $('input[name=from_date]').val();
                    d.to_date = $('input[name=to_date]').val();
                    d._token = messages.token;
                },
                error: function () { // error handling

                    $("#cibilReports").append('<tbody class="leadMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#cibilReports_processing").css("display", "none");
                }
            },
            "drawCallback": function( settings ) {
                excelUrl = settings.json.excelUrl;
                $('#dwnldEXCEL').attr('href', excelUrl)
                pdfUrl = settings.json.pdfUrl;
                $('#dwnldPDF').attr('href', pdfUrl)
            },
            columns: [
                {data: 'username'},
                {data: 'biz_name'},
                {data: 'pull_date'},
                {data: 'pull_status'},
                {data: 'pull_by'}
            ],
            aoColumnDefs: [{
                    'bSortable': false,
                    'aTargets': [0, 3, 4]
                }]

        });
        
        $('#searchbtn').on('click', function (e) {
            oTables.draw();
        });

        $('#from_date').datetimepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            minView: 2, });
        $('#to_date').datetimepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            minView: 2, });

    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}