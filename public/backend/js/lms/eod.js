try {
    var oTable;
    jQuery(document).ready(function ($) {

        table = $('#eodProcessList').DataTable( {
            processing: true,
            serverSide: true,
            pageLength: 50,
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
                { data: "total_min" },
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
                    div
                        .html( json.html )
                        .removeClass( 'loading' );
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
        /* var sysStartDate = new Date(messages.sys_start_date);
         var curDate = new Date();
     
         var diff = curDate - sysStartDate;
     
         var today = new Date(sysStartDate.setSeconds(diff/1000));*/
     
         var today = new Date();
         var years = today.getFullYear().toString().length == 1 ? '0'+today.getFullYear() : today.getFullYear();
         var months = today.getMonth().toString().length == 1 ? '0'+(today.getMonth()+1) : today.getMonth();
         var days = today.getDate().toString().length == 1 ? '0'+today.getDate() : today.getDate();
         var date = days+'-'+months+'-'+years;
         
         var hours = today.getHours().toString().length == 1 ? '0'+today.getHours() : today.getHours();
         var minutes = today.getMinutes().toString().length == 1 ? '0'+today.getMinutes() : today.getMinutes();
         var seconds = today.getSeconds().toString().length == 1 ? '0'+today.getSeconds() : today.getSeconds();    
         var time = hours + ":" + minutes + ":" + seconds;    
         
         var dateTime = date+' '+time;
         
         //console.log('dateTime', dateTime);
         document.getElementById('current-date').innerHTML = dateTime;
         display_c();
    }
    
    function display_c(){
        var refresh=1000; // Refresh rate in milli seconds
        setTimeout('currentDateTime()',refresh);
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
                    table.draw();
                },
                complete:function(data){
                    location.reload();
                },
                error: function (error) {
                    console.log(error);
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