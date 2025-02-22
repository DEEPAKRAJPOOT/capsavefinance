@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Group</h3>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Group</li>
                <li class="active">Group List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row" style="margin-bottom: 25px;">
                <div class="col-md-4">
                    <input class="form-control" placeholder="Search by Group Name, Group ID" name="search_keyword" type="text">
                </div>
                <div class="col-md-1">
                    <button type="button" id="searchbtngroup" class="btn btn-success btn-sm float-right">Search</button>
                </div>
                @can('add_new_group')
                <div class="col-md-7 text-right">
                    <a class="btn btn-success btn-sm" href="{{ route('add_new_group') }}">
                        <i class="fa fa-plus"></i> Add Group
                    </a>
                </div>
                @endcan
           </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="GroupList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="group-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Group ID</th>
                                    <th>Group Name</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div id="group-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
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
    get_all_group_list: "{{ URL::route('get_all_group_list') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
};
</script>
<script src="{{ asset('backend/js/ajax-js/group.js') }}"></script>
@endsection