@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage UCIC</h3>
            <small>UCIC List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage UCIC</li>
                <li class="active">My UCIC</li>
            </ol>
        </div>
    </section>


    <div class="card">
        <div class="card-body">       
            <div class="row">
                <div class="col-md-6">
                    {!!
                    Form::text('by_code',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by ucic code, pan, Group Code, Group Name, App Id and Entity Name',
                    'id'=>'by_name'
                    ])
                    !!}
                </div>

               <!--
                <div class="col-md-3">

                    {!!
                    Form::select('pan',
                    [''=>'Select Pan'],
                    null,
                    array('id' => 'pan',
                    'class'=>'form-control'))
                    !!}
                </div>
                -->

                <div class="col-md-2">
                    <button id="searchB" type="button" class="btn  btn-success btn-sm">Search</button>
                </div> 
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">

                                
                                    <table id="ucicMaster" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                <th>UCIC Code</th>
                                                <th>App ID</th>
                                                <th>Group Code</th>
                                                <th>Group Name </th>
                                                <th>Entity Name</th>
                                                <th>Email Id</th>
                                                <th>Created At</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    <div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('editLead','Edit Lead Detail', 'modal-md')!!}
{{-- {!!Helpers::makeIframePopup('createLeadForm','Create Lead', 'modal-lg')!!} --}}
@endsection
@section('additional_css')
<style>
    #leadMaster_wrapper  #leadMaster_info{margin: -40px 0px 0px 164px;}
</style>
@endsection
@section('jscript')
<script>

    var messages = {
        get_ucic: "{{ URL::route('get_ucic') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/ucic.js') }}" type="text/javascript"></script>


@php 
$operation_status = session()->get('operation_status', false);
@endphp
@if( $operation_status == config('common.YES'))

<script>
    try {
        var p = window.parent;
        p.jQuery('#editLead').modal('hide');
        window.parent.location.reload();
    } catch (e) {
        if (typeof console !== 'undefined') {
            console.log(e);
        }
    }
</script>

@endif
@endsection




