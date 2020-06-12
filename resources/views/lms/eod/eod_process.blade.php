@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')

<div class="content-wrapper">              
    <section class="content-header">
        <div class="header-icon">
            <i class="fa fa-clipboard" aria-hidden="true"></i>
        </div>
        <div class="header-title">
            <h3 class="mt-2">EOD Process</h3>

            <ol class="breadcrumb">
                <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
                <li class="active">EOD Process</li>
            </ol>
        </div>
    </section>
    <div class="row grid-margin ">
        <div class="col-md-12  mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-fields">
                        <div class="active" id="details">
                            <div class="form-sections">

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Current System Date: <span id="current-date"></span></label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="submit" id="submit" name="btn_process"  class="pull-right btn btn-primary ml-2 btn-sm" {{ $enable_process_start ? '' : 'disabled' }} value="Run Eod Process">
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row dataTables_wrapper mt-4">
                            <div class="overflow">
                                <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
                                        <table id="eodProcessList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                            <thead>
                                            <tr role="row">
                                                    <th>Current System Date</th>
                                                    <th>System Started at</th>
                                                    <th>System Stopped at</th>
                                                    <th>Eod Processed By</th>
                                                    <th>Eod Process Starded at</th>
                                                    <th>Eod Process Stopped at</th>
                                                    <th>Total Hours</th>
                                                    <th>Tally Posting Status</th>
                                                    <th>Interest Accrual Status</th>
                                                    <th>Repayment Status</th>
                                                    <th>Disbursal Status</th>
                                                    <th>Charge Posting Status</th>
                                                    <th>Overdue Interest Accrual Status</th>
                                                    <th>Disbursal Block Status</th>
                                                    <th>Manually Posted Running Transaction Status</th>
                                                    <th>Final Status</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div id="eodProcessList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('jscript')
<script>
var messages = {
    _token : "{{ csrf_token() }}",
    update_eod_batch_process_url : "{{ route('update_eod_batch_process',['eod_process_id'=>$eod_process_id]) }}",
    sys_start_date: "{{ $sys_start_date }}"
};    
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
    if (messages.eod_status == 2) {        
    //if (messages.eod_process_start_date == '') {
        var data = {'_token': messages._token};
        $.ajax({
        type: "POST",
            url: messages.update_eod_batch_process_url,
            data: data,
            cache: false,
            async:false,
            beforeSend: function( xhr ) {
                parent.$('.isloader').show();
            },    
            success: function (res) {        
                parent.$('.isloader').hide();
                location.reload();
            },
            error: function (error) {
                console.log(error);
            }
        }); 
    }
}
display_c();
$(document).ready(function(){    
    updateEodStatus();    
})
</script>
@endsection
