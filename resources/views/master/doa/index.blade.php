@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Doa</h3>
            <small>Level List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Doa</li>
                <li class="active">Level List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row" style="margin-bottom: 25px;">
                <div class="col-md-4">
                    <input class="form-control" placeholder="Search by level" id="by_name" name="search_keyword" type="text">
                </div>
                <div class="col-md-1">
                    <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
                </div>
                <div class="col-md-7 text-right">
                    <a data-toggle="modal" class="btn  btn-success btn-sm" data-target="#addDoaLevelFrame" data-url ="{{route('add_doa_level')}}" data-height="400px" data-width="100%" data-placement="top" >
                            <i class="fa fa-plus"></i>Add Level
                    </a>
                </div>
           </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="doaLevelList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Level Code</th>
                                    <th>Level Name</th>
                                    <th>City</th>
                                    <th>Amount</th>
                                    <th>Role(s)</th>
                                    <th>Status</th>
                                    <th>Action</th>                                   
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div id="doaLevelList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
{!!Helpers::makeIframePopup('addDoaLevelFrame','Add Level', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editDoaLevelFrame','Edit Level', 'modal-lg')!!}
{!!Helpers::makeIframePopup('assignRoleLevelFrame','Assign Role', 'modal-lg')!!}
@endsection

@section('jscript')
<script>

var messages = {
    get_doa_levels_list: "{{ URL::route('ajax_doa_levels_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/doa_level.js') }}"></script>
@endsection