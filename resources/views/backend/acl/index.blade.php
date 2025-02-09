@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
     <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Roles</h3>
            <small>Role List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Access Management</li>
                <li class="active">Manage Roles</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
           
            <div class="row">
                <div class="col-sm-12">
<!--                      <div class="head-sec">
                <div class="pull-right" style="margin-bottom: 10px;">
                    <a   href="{{route('get_anchor_lead_list')}}" >
                <button class="btn  btn-success btn-sm" type="button">
                        <span class="btn-label">
                        <i class="fa fa-plus"></i>
                        </span>
                        Manage Anchor Lead
                        </button>
                        
                    </a>
                </div>
            </div>-->
            <div class="head-sec">
                <div class="pull-right" style="margin-bottom: 10px;margin-right: 12px;">
                    <a  data-toggle="modal" data-target="#addRoleFrm" data-url ="{{route('add_role')}}" data-height="300px" data-width="100%" data-placement="top" >
                        <button class="btn  btn-success btn-sm" type="button">
                            <span class="btn-label">
                                <i class="fa fa-plus"></i>
                            </span>
                            Add New Role
                        </button>

                    </a>
                </div>
            </div>
          </div>     
            </div>
<!--            <div class="row">
                <div class="col-md-4">
                    {!!
                    Form::text('by_email',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by First name, Last name and Email',
                    'id'=>'by_name'
                    ])
                    !!}
                </div>
                <div class="col-md-4">

                    {!!
                    Form::select('is_assign',
                    [''=>'Status', '1'=>'Assigned','0'=> 'Pending'],
                    null,
                    array('id' => 'is_active',
                    'class'=>'form-control'))
                    !!}
                     
                </div>
               <button id="searchB" type="button" class="btn  btn-success btn-sm">Search</button>
            
                </div>        -->
                <div class="row">
                <div class="col-sm-12">
                                     <div class="table-responsive">
                                    <table id="RoleList" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                <th>Sr No</th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                 <th>Active</th>
                                                <th>Created On</th>
                                                <th>Action</th>
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
{!!Helpers::makeIframePopup('addRoleFrm','Manage Role', 'modal-md')!!}
@endsection

@section('jscript')
<script>

    var messages = {
        get_role_list: "{{ URL::route('get_role_list') }}",       
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>

<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/role.js') }}" type="text/javascript"></script>
@endsection