@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    @include('backend.nach.common.section')
    <div class="card">
        <div class="card-body">
            <div class="row">

                <div class="col-md-12 text-right">
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
                                                <th>Name</th>
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