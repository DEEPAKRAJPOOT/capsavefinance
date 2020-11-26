@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    @include('backend.nach.common.section')
    <div class="card">
        <div class="card-body">
            <div class="row">

                <div class="col-3 text-right">
                    {!!
                        Form::text('search_keyword',
                        null,
                        [
                        'class' => 'form-control',
                        'placeholder' => 'Search by App Id, Entity Name and Pan',
                        'id'=>'by_name'
                        ])
                    !!}
                </div>
                <div class="col-md-2">

                    {!!
                        Form::select('is_assign',
                        [''=>'Select', '1'=>'PENDING','2'=> 'PDF UPLOADED','3'=>'SENT TO APPROVAL','4'=>'NACH ACTIVED','5'=>'FAILED','6'=>'ClOSED'],
                        null,
                        array('id' => 'is_active',
                        'class'=>'form-control'))
                    !!}
                </div>
                <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
                <div class="col-6 text-right">
                    <div class="btn-group btn-custom-group inline-action-btn">
                       <button data-toggle="modal" data-target="#createNachFrame" data-url ="{{route('backend_create_nach') }}" data-height="300px" data-width="100%" data-placement="top" class="btn btn-success btn-sm">Create NACH</button>
                    </div>
               </div>

            </div>
            <div class="row">
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="backendUserNachList" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                <th>User Type</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Bank Name</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Status</th>
                                                <th>Created Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    <div id="backendUserNachList-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{!!Helpers::makeIframePopup('createNachFrame','Create NACH', 'modal-md')!!}
{!!Helpers::makeIframePopup('sendNextstage','Send Next Stage', 'modal-md')!!}

@endsection

@section('jscript')
<script>

    var messages = {
        backend_ajax_user_nach_list: "{{ URL::route('backend_ajax_user_nach_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/user_nach.js') }}"></script>
@endsection