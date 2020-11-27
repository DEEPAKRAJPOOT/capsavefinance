try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#nachList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: false,
            ajax: {
                "url": messages.get_all_nach, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#nachList").append('<tbody class="nachList-error"><tr><th colspan="15">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#nachList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'users_nach_id'},
                {data: 'nach_date'},
                {data: 'sponsor_bank_code'},
                {data: 'acc_name'},
                {data: 'acc_no'},
                {data: 'ifsc_code'},
                {data: 'branch_name'},
                {data: 'amount'},
                {data: 'phone_no'},
                {data: 'email_id'},
                {data: 'period_from'},
                {data: 'period_to'},
                {data: 'debit_type'},
                {data: 'created_at'},
                {data: 'uploaded_file_id'},
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,2]}]
        });

        //Search
        $('#searchbtn').on('click', function (e) {
            oTable.draw();
        });
        
        $(document).on('click', '#exportExcel', function () {
            var arr = [];
            i = 0;
            th = this;
            $(".chkstatus:checked").each(function () {
                arr[i++] = $(this).val();
            });
            if (arr.length == 0) {
                replaceAlert('Please select atleast one checked', 'error');
                return false;
            }
            if (confirm('Are you sure, you want to export the selected record(s).'))
            {  
                $("#excelExportForm").submit();
            } else {
                return false;
            }
        });
        $(document).on('click', '#chkAll', function () {
            var isChecked = $("#chkAll").is(':checked');
            if (isChecked)
            {
                $('input:checkbox').attr('checked', 'checked');
            } else
            {
                $('input:checkbox').removeAttr('checked');
            }
        });
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

