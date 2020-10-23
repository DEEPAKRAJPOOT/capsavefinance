try {
    var oTable;
    var oTable2;
    var reqIds = [];

    jQuery(document).ready(function ($) {
        //User Listing code
        if($('#nachRepaymentList').length){
            oTable = $('#nachRepaymentList').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                searching: false,
                bSort: true,
                ajax: {
                    "url": messages.url, // json datasource
                    "method": 'POST',
                    data: function (d) {
                        d.status = messages.status
                        d.search_keyword = $('input[name=search_keyword]').val();
                        d._token = messages.token;
                    },
                    "error": function () {  // error handling
                        //$("#nachRepaymentList").append('<tbody class="appList-error"><tr><th colspan="8">' + messages.data_not_found + '</th></tr></tbody>');
                        $("#nachRepaymentList_processing").css("display", "none");
                    }
                },
                columns: messages.columns,
                aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
            });
        }
        
        if($('#nachRepayTransResList').length){
            oTable = $('#nachRepayTransResList').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                searching: false,
                bSort: true,
                ajax: {
                    "url": messages.url, // json datasource
                    "method": 'POST',
                    data: function (d) {
                        d.status = messages.status
                        d.search_keyword = $('input[name=search_keyword]').val();
                        d._token = messages.token;
                    },
                    "error": function () {  // error handling
                        $("#nachRepayTransResList_processing").css("display", "none");
                    }
                },
                columns: messages.columns,
                aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
            });
        }

        $('#searchbtn').on('click', function (e) {
            oTable.draw();
        });

    });

} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}


$(document).on('click','#nachExpBtn', function(){
    var countCheckedCheckboxes = $(".nach-request").filter(':checked').length;
    console.log(countCheckedCheckboxes);
    if(countCheckedCheckboxes <= 0){
    	console.log("1");
        replaceAlert('Please select at least one record!', 'error');
        return false;
    }else{
    	console.log("2");
        $(this).addClass('btn-disabled');
        if (confirm('Are you sure you want to download NACH Request?')){
        	console.log("3");
            $("#nachReqForm").submit();
        }else{
            $(this).removeClass('btn-disabled');
        }
    }
}) 