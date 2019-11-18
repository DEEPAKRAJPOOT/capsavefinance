@extends('layouts.backend.admin-layout')

@section('content')


<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css">
<style>
    select[name='leadMaster_length']{
        height: calc(1.8125rem + 2px);
        margin: 0 10px 0 10px;
        width: 100px;
    }
    input[type='search']{
        height: calc(1.8125rem + 2px);
        display: inline;
        position: absolute;
        border: 1px solid rgba(0, 0, 0, 0.15);
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Application</h3>            
            <ol class="breadcrumb">
                <li><a href="https://admin.zuron.in/admin/dashboard"><i class="mdi mdi-home"></i> Home</a></li>
                <li class="active">Manage Application</li>
            </ol>
        </div>
    </section>


    <div class="card">
        <div class="card-body">

            <input type="hidden" name="status" value="">
            <input type="hidden" name="head" value="">

            <div class="row">

                <div class="col-md-4">
                    {!!
                    Form::open(
                    array('name' => 'ProCountryMaster',
                    'autocomplete' => 'off', 
                    'id' => 'searchForm',  

                    )
                    ) 
                    !!}

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
                    Form::select('is_assign',
                    [''=>'Status', '1'=>'Assigned','0'=> 'Not Assigned'],
                    null,
                    array('id' => 'is_active',
                    'class'=>'form-control'))
                    !!}
                </div>
                <button type="submit" class="btn btn-success search">Search</button>
                {!!
                Form::close()
                !!}
                <div class="col-12 dataTables_wrapper">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="appList" class="table white-space table-striped cell-border dataTable no-footer" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">

                                                <th>{{ trans('backend.app_list_head.app_id') }}</th>
                                                <th>{{ trans('backend.app_list_head.name') }}</th>
                                                <th>{{ trans('backend.app_list_head.anchor') }}</th>
                                                <th>{{ trans('backend.app_list_head.user_type') }}</th>
                                                <th>{{ trans('backend.app_list_head.assignee') }}</th>
                                                <th>{{ trans('backend.app_list_head.shared_detail') }}</th>
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

{!!Helpers::makeIframePopup('noteFrame','Add Note')!!}

@endsection

@section('jscript')
<script>

    var messages = {
        get_applications: "{{ URL::route('ajax_app_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/application.js') }}" type="text/javascript"></script>
@endsection