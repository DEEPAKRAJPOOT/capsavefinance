try {
    var oTable;
    jQuery(document).ready(function ($) {

        table = $('#eodProcessList').DataTable( {
            processing: true,
            serverSide: true,
            pageLength: 10,
            /*dom: 'lBrtip',
            bSort: false,*/
            responsive: true,
            searching: false,
            ajax: {
                "url": messages.eod_list_url, // json datasource
                "method": 'POST',
                data: function (d) {
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#eodProcessList").append('<tbody class="appList-error"><tr><th colspan="9">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#eodProcessList_processing").css("display", "none");
                }
            },
            columns: [
                {
                    className:      'details-control',
                    orderable:      false,
                    data:           null,
                    defaultContent: '<i class = "glyphicon glyphicon-plus-sign"> </ i>'
                },
                { data: "current_sys_date" },
                { data: "sys_started_at" },
                { data: "sys_stopped_at" },
                { data: "eod_process_mode" },
                { data: "eod_process_started_at" },
                { data: "eod_process_stopped_at" },
                { data: "total_sec" },
                { data: "status" }
            ],
            order: [[0, 'asc']]
        } );

        $('#eodProcessList tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );
         
            if ( row.child.isShown() ) {
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
            }
        });

        function format ( rowData ) {
            var div = $('<div/>')
                .addClass( 'loading' )
                .text( 'Loading...' );
         
            $.ajax( {
                url: messages.eod_process_list_url, // json datasource
                method: 'POST',
                data: {
                    eod_process_id: rowData.eod_process_id,
                    _token: messages.token,
                },
                dataType: 'json',
                success: function ( json ) {
                    div.html( json.html ).removeClass( 'loading' );
                }
            } );
         
            return div;
        }

        //Search
        $('#searchbtn').on('click', function (e) {
            $("#client_details").html('');
            table.draw();
        });

    });


    function currentDateTime() {
        if(messages.status == 0){
            var realStartDate = new Date(messages.real_sys_start_date);
            var sysStartDate = new Date(messages.sys_start_date);
            var diff = sysStartDate - realStartDate;
            var curDate = new Date();
            var today = new Date(curDate.setSeconds(diff/1000));      
        }else{
            var today = new Date(messages.sys_end_date);
        }
        
             
        var sMonth = padValue(today.getMonth() + 1);
        var sDay = padValue(today.getDate());
        var sYear = today.getFullYear();
        var sHour = today.getHours();
        var sMinute = padValue(today.getMinutes());
        var sAMPM = "AM";

        var iHourCheck = parseInt(sHour);

        if (iHourCheck > 12) {
            sAMPM = "PM";
            sHour = iHourCheck - 12;
        }
        else if (iHourCheck === 0) {
            sHour = "12";
        }

        sHour = padValue(sHour);

        dateTime =  sDay + "-" + sMonth + "-" + sYear + " " + sHour + ":" + sMinute + " " + sAMPM;
        document.getElementById('current-date').innerHTML = dateTime;
        display_c();
    }
    
    function padValue(value) {
        return (value < 10) ? "0" + value : value;
    }
    
    function display_c(){
        if(messages.status == 0){
            setTimeout('currentDateTime()',1000);
        }else{
            currentDateTime();
        }
    }
     
    function updateEodStatus() {
        if (messages.enable_process_start) {
            parent.$('.isloader').show();
            $.ajax({
                type: "POST",
                url: messages.update_eod_batch_process_url,
                data: {'_token': messages.token},
                cache: false,
                async:false,
                beforeSend: function( xhr ) {
                },    
                success: function (res) {  
                    alert(res.message);
                    if(res.status == 1){
                        location.reload();
                    }
                    table.draw();
                    parent.$('.isloader').hide();
                },
                error: function (error) {
                    console.log(error);
                    parent.$('.isloader').hide();
                }
            }); 
        }
    }
    display_c();
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}