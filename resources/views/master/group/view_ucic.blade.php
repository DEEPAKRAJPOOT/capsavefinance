@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Group UCIC</h3>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Group UCIC</li>
                <li class="active">UCIC List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            {{--<div class="row" style="margin-bottom: 25px;">
                <div class="col-md-4">
                    <input class="form-control" placeholder="Search by Group Name, Group ID" name="search_keyword" type="text">
                </div>
                <div class="col-md-1">
                    <button type="button" id="searchbtngroup" class="btn btn-success btn-sm float-right">Search</button>
                </div>
           </div>--}}
            
            <div class="table-responsive ps ps--theme_default w-100">
                <table class="table  table-td-right">
                    <tbody>
                        <tr>
                            <td class="text-left" width="30%"><b>Group ID:</b></td>
                            <td>{{ $group->group_code }}</td>
                            <td class="text-left" width="30%"><b>Group Name:</b></td>
                            <td>{{ $group->group_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-left" width="30%"><b>Current Group Sanction:</b></td>
                            <td class="curr-grp-sanc-amt">---</td>
                            <td class="text-left" width="30%"><b>Current Group Outstanding:</b></td>
                            <td class="curr-grp-out-amt">---</td>
                        </tr>
                        <tr>
                            <td class="text-left" width="30%"><b>Group Field 1:</b></td>
                            <td>{{ $group->group_field_1 ?: '---' }}</td>
                            <td class="text-left" width="30%"><b>Group Field 2:</b></td>
                            <td>{{ $group->group_field_2 ?: '---' }}</td>
                        </tr>
                        <tr>
                            <td class="text-left" width="30%"><b>Group Field 3:</b></td>
                            <td>{{ $group->group_field_3 ?: '---' }}</td>
                            <td class="text-left" width="30%"><b>Group Field 4:</b></td>
                            <td>{{ $group->group_field_4 ?: '---' }}</td>
                        </tr>
                        <tr>
                            <td class="text-left" width="30%"><b>Group Field 5:</b></td>
                            <td>{{ $group->group_field_5 ?: '---' }}</td>
                            <td class="text-left" width="30%"><b>Group Field 6:</b></td>
                            <td>{{ $group->group_field_6 ?: '---' }}</td>
                        </tr>
                    </tbody>    
                </table>    
            </div>    
            <div class="row mt-4">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="groupUcicList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="group-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>UCIC Code</th>
                                    <th>Entity Name</th>
                                    <th>Product Type</th>
                                    <th>Sanction Limit</th>
                                    <th>Outstanding Limit</th>
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
    get_group_ucic_list: "{{ URL::route('get_group_ucic_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    group_id: "{{ $groupId }}",
};
</script>
<script src="{{ asset('backend/js/ajax-js/group.js') }}"></script>
@endsection