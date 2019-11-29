@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-subnav')
    <!-- partial -->
    <div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
        <li>
            <a href="{{ route('backend_fi', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="active">FI Residence</a>
        </li>
        <li>
            <a href="{{ route('backend_rcu', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">RCU Document</a>
        </li>
    </ul>


<div class="row grid-margin mt-3">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="fiList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th>FI Residence ID</th>
                                        <th>Address Type</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <div id="fiList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- {!!Helpers::makeIframePopup('addAnchorFrm','Add Anchor', 'modal-md')!!} -->
<!-- {!!Helpers::makeIframePopup('editAnchorFrm','Edit Anchor Detail', 'modal-md')!!} -->
@endsection

@section('jscript')
<script>

    var messages = {
        get_fi_list: "{{ URL::route('get_fi_list',['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}",
        data_not_found: "{{ trans('error_messages.no_data_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>

<script src="{{ asset('backend/js/ajax-js/fi.js') }}"></script>
@endsection