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
        <div class="clearfix"></div>
    </section>
    <div class="row grid-margin ">
        <div class="col-md-12  mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-fields">
                        <form id="frm-sys-start" method="post" action="{{ route('save_process') }}" enctype= multipart/form-data>
                            @csrf 
                            <div class="active" id="details">
                                <div class="form-sections">

                                    <div class="clearfix"></div>                                    
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">Current System Date</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for=""><span id="current-date">{{ $current_date }}</span></label>                                                        
                                            </div>
                                        </div>                                        
                                        <div class="col-4">
                                            <div class="form-group">
                                                <input type="submit" id="submit" name="btn_process"  class="btn btn-primary ml-2 btn-sm" {{ $enable_sys_start ? '' : 'disabled' }} value="Start System">                                                 
                                            </div>
                                        </div>                                         
                                    </div>
                                    @if ($eod_process_id)
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">System Started at</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for=""><span id="current-date">{{ !empty($sys_start_date) ? \Helpers::convertDateTimeFormat($sys_start_date, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i:s') : '' }}</span></label>                                                        
                                            </div>
                                        </div>                                                                                                                        
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" value="1" id="sys_start_flag" name="flag">
                            <input type="hidden" value="{{ $eod_process_id }}" name="eod_process_id">     
                            <input type="hidden" value="{{ $sys_curr_date }}" name="sys_curr_date">
                        </form>
                        
                        <form id="frm-sys-start" method="post" action="{{ route('save_process') }}" enctype= multipart/form-data>
                            @csrf 
                            <div class="active" id="details">
                                <div class="form-sections">

                                    <div class="clearfix"></div>
                                    <div class="row">
                                    @if($enable_process_start)
                                    

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">Running Hours</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">{{ $running_hours }}</label>                                                        
                                            </div>
                                        </div>
                                                                       
                                    @else
                                    

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">Total Hours</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">{{ $total_hours }}</label>                                                        
                                            </div>
                                        </div>
                                    
                                    @endif
                                        <div class="col-4">
                                            <div class="form-group">
                                                <input type="submit" id="submit" name="btn_process"  class="btn btn-primary ml-2 btn-sm" {{ $enable_process_start ? '' : 'disabled' }} value="Run Eod Process">
                                            </div>
                                        </div>                                    
                                    </div>
                                    <div class="row">
                                    @if(!$enable_process_start)
                                    

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">System Stopped at</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">{{ !empty($sys_end_date) ? \Helpers::convertDateTimeFormat($sys_end_date, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i:s') : '' }}</label>                                                        
                                            </div>
                                        </div>
                                                                       
                                    @endif                                    
                                    
                                </div>
                            </div>
                            <input type="hidden" value="2" name="flag">
                            <input type="hidden" value="{{ $eod_process_id }}" name="eod_process_id">
                            <input type="hidden" value="{{ $sys_curr_date }}" name="sys_curr_date">
                        </form>   
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $status }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div>
                        
                        <p class="mt-2"><strong>Summary</strong></p> 
                        <hr>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Tally Posting Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->tally_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div>
                        
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Interest Accrual Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->int_accrual_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div>
                        
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Repayment Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->repayment_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Disbursal Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->disbursal_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div>                        
                        
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Charge Posting Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->charge_post_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div> 
                        
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Overdue Interest Accrual Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->overdue_int_accrual_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div> 
                        
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Disbursal Block Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->disbursal_block_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div>  

                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Manually Posted Running Transaction Status </label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->running_trans_posting_settled] : '' }}</label>                                                        
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
    update_eod_batch_process_url : "{{ route('update_eod_batch_process') }}",
    eod_status : "{{ $eodData ? $eodData->status : '' }}",
    sys_start_date : "{{ $eodData ? $eodData->sys_start_date : '' }}",
    eod_process_start_date : "{{ $eodData ? $eodData->eod_process_start : '' }}",
};    
function currentDateTime() {
    var today = new Date();
    //var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
    
    var years = today.getFullYear().toString().length == 1 ? '0'+today.getFullYear() : today.getFullYear();
    var months = today.getMonth().toString().length == 1 ? '0'+(today.getMonth()+1) : today.getMonth();
    var days = today.getDate().toString().length == 1 ? '0'+today.getDate() : today.getDate();
    var date = days+'-'+months+'-'+years;
    
    //var ampm = d.getHours() >= 12 ? 'pm' : 'am',
    //var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
    //var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    //days[d.getDay()]+' '+months[d.getMonth()]+' '+d.getDate()+' '+d.getFullYear()+' '+hours+':'+minutes+ampm;
        
    //var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
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
//display_c();
$(document).ready(function(){    
    updateEodStatus();    
})
</script>
@endsection
