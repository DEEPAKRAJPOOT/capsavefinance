@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>My Application</h3>            
            <ol class="breadcrumb">
                <li><a href="{{route('front_dashboard')}}"><i class="mdi mdi-home"></i> Home</a></li>
                <li class="active">My Application</li>
            </ol>
        </div>
    </section>


    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    {!!
                    Form::text('search_keyword',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by App Id, Name',
                    'id'=>'by_name'
                    ])
                    !!}
                </div>
                <div class="col-md-4">
                    {!!
                    Form::select('is_status',
                    [''=>'Status', '1'=>'Complete','0'=> 'Incomplete'],
                    null,
                    array('id' => 'is_status',
                    'class'=>'form-control'))
                    !!}
                </div>
                <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>

                <div class="col-md-3 text-right">
                    <div class="btn-group btn-custom-group inline-action-btn">
                       <a href="{{route('business_information_open')}}" class="btn btn-pickup btn-sm">Create Application</a>
                    </div>
               </div>




            </div>
            <div class="row">
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="appList" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">

                                                <th>{{ trans('backend.app_list_head.app_id') }}</th>
                                                <th>{{ trans('backend.app_list_head.name') }}</th>
                                                <th>{{ trans('backend.app_list_head.anchor') }}</th>
                                                <th>{{ trans('backend.app_list_head.user_type') }}</th>
                                                <th>{{ trans('backend.app_list_head.assignee') }}</th>
                                                <th>{{ trans('backend.app_list_head.status') }}</th>
                                                <th>{{ trans('backend.app_list_head.action') }}</th>
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

{!!Helpers::makeIframePopup('assignCaseFrame','Assign Case', 'modal-md')!!}
{!!Helpers::makeIframePopup('sendNextstage','Send Next Stage', 'modal-md')!!}

@endsection

@section('jscript')
<script>

    var messages = {
        get_user_applications: "{{ URL::route('ajax_user_app_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('frontend/js/ajax-js/user_application.js') }}"></script>
@endsection