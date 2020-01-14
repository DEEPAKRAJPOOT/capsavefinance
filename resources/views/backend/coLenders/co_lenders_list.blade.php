@extends('layouts.backend.admin-layout') 

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Co Lenders</h3>
            <small>Co Lenders List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Co Lenders</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
           
            <div class="row">
                <div class="col-sm-12">
                  
                    <div class="head-sec">
                        <div class="pull-right" style="margin-bottom: 10px;margin-right: 12px;">
                        @can('add_co_lender')    
                        <a  data-toggle="modal" data-target="#addcolenders"
                            data-url ="{{route('add_co_lender')}}" 
                            data-height="400px" data-width="100%" data-placement="top" >
                                <button class="btn  btn-success btn-sm" type="button">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Add Co-lender
                                </button>
                            </a>
                        @endcan
                        </div>
                    </div>
                </div>     
            </div>

                <div class="row">
                <div class="col-sm-12">
                                     <div class="table-responsive">
                                    <table id="anchleadList" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                <th>Sr.No.</th>
                                                <th>Name</th>
                                                <th>Business Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
<!--                                                <th>Anchor</th>-->
                                                <th>Created At</th>
                                                 <th>Status</th>
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
{!!Helpers::makeIframePopup('addcolenders','Add Co-lender', 'modal-md')!!}
@endsection

@section('additional_css')

<link rel="stylesheet" href="{{url('backend/assets/css/custom.css')}}" /> @endsection @section('jscript')
<script>
    var messages = {
        get_sub_industry: "{{ URL::route('get_sub_industry') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        please_select: "{{ trans('backend.please_select') }}",

    };
</script>
<script src="{{ asset('backend/assets/js/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/common.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend/js/lms/program.js') }}" type="text/javascript"></script>
@endsection