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
                                    <div class="col-12">
                                        <div class="form-group">
                                            @if($status == 4)
                                                <input type="button" name="btn_process"  class="pull-right btn btn-primary ml-2 btn-sm"  value="Start System" onclick=startSystem()>
                                            @else
                                                <input type="button" name="btn_process"  class="pull-right btn btn-primary ml-2 btn-sm" {{ $enable_process_start ? '' : 'disabled' }} value="{{ ($status == 3) ? 'Re-':'' }} Run Eod Process" {{ $enable_process_start ? 'onclick=updateEodStatus()' : '' }}>
                                            @endif
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-12 dataTables_wrapper mt-4">
                            <div class="overflow">
                                <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="row">
                                        <div class="col-sm-12">

                                            <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
                                                <table id="eodProcessList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                                    <thead>
                                                    <tr role="row">
                                                            <th></th>
                                                            <th>Current System Date</th>
                                                            <th>System Started at</th>
                                                            <th>System Stopped at</th>
                                                            <th>Eod Processed Mode</th>
                                                            <th>Eod Process Starded at</th>
                                                            <th>Eod Process Stopped at</th>
                                                            <th>System Active Duration</th>
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
    </div>
</div>
@endsection
@section('jscript')
<script>
var messages = {
    token : "{{ csrf_token() }}",
    sys_start_date: "{{ $sys_start_date }}",
    sys_end_date:"{{ $sys_end_date ?? $sys_start_date }}",
    eod_list_url: "{{ route('get_eod_list') }}",
    eod_process_list_url: "{{ route('get_eod_process_list') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    enable_process_start:"{{ $enable_process_start }}",
    real_sys_start_date: "{{ $created_at }}",
    status:"{{ $status }}",
    start_system_url : "{{ route('start_eod_system',['eod_process_id'=>$eod_process_id]) }}",
    update_eod_batch_process_url : "{{ route('update_eod_batch_process',['eod_process_id'=>$eod_process_id]) }}",
};    
</script>
<script src="{{ asset('backend/js/lms/eod.js') }}" type="text/javascript"></script>
@endsection
