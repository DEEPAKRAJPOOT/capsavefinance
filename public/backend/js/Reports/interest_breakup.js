try {
    var oTable;
    jQuery(document).ready(function ($) {
        oTable = $('#interest_breakup_report').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 50,
            bSort: false,
            responsive: true,
            searching: false,
            ajax: {
                "url": messages.get_all_interest_breakups, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.from_date = $('input[name="from_date"]').val();
                    d.to_date = $('input[name="to_date"]').val();
                    d._token = messages.token;
                },
                "error": function () {
                    $("#interest_breakup_report").append('<tbody class="error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#interest_breakup_report_processing").css("display", "none");
                }
            },
            "drawCallback": function (settings) {
                excelUrl = settings.json.excelUrl;
                $('#dwnldEXCEL').attr('href', excelUrl)
                pdfUrl = settings.json.pdfUrl;
                $('#dwnldPDF').attr('href', pdfUrl)
            },
            columns: [
                { data: 'loan' },
                { data: 'cust_id'},
                { data: 'client_name' },
                { data: 'disbursed_amt' },
                { data: 'from_date' },
                { data: 'to_date' },
                { data: 'days' },
                { data: 'int_rate' },
                { data: 'int_amt' },
                { data: 'collection_date' },
                { data: 'tds_rate' },
                { data: 'tds_amt' },
                { data: 'net_int' },
                { data: 'tally_batch' }
            ],
            aoColumnDefs: [{ 'bSortable': false, 'aTargets': [1, 2, 3] }]
        });

        //Search
        $('#searchbtn').on('click', function (e) {
            oTable.draw();
        });

    });

    $('#from_date').datetimepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        minView: 2,
    });
    $('#to_date').datetimepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        minView: 2,
    });

    function download(action) {
        url = '';
        from_date = $('input[name="from_date"]').val().trim();
        to_date = $('input[name="to_date"]').val().trim();
        if (action.trim() == 'pdf') {
            url = messages.pdf_soa_url;
        }

        if (action.trim() == 'excel') {
            url = messages.excel_soa_url;
        }

        if (from_date) {
            url += '&from_date=' + from_date;
        }

        if (to_date) {
            url += '&to_date=' + to_date;
        }

        window.open(url, '_blank');
    }

} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}