@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Base Rates</h3>
            <small>Base Rate List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Base Rates</li>
                <li class="active">Base Rate List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row" style="margin-bottom: 25px;">
                <div class="col-md-4">
                    <!--<input class="form-control"  placeholder="Search by Company Name" name="search_keyword" type="text">-->
                    {!!
	                Form::text('search_keyword',
					(isset($filter['filter_search_keyword'])) ? $filter['filter_search_keyword'] : null,
	                [
	                'class' => 'form-control',
	                'placeholder' => 'Search by Bank Name',
	                'id'=>'search_keyword'
	                ])
                    !!}
                </div>
                <div class="col-md-1">
                    <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
                </div>
                <div class="col-md-7 text-right">
                    <a data-toggle="modal" class="btn  btn-success btn-sm" data-target="#addBaseRateFrame" data-url ="{{route('add_base_rate')}}" data-height="350px" data-width="100%" data-placement="top" >
                            <i class="fa fa-plus"></i>Add Base Rate
                    </a>
                </div>
           </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="baserateList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Bank Name</th>
                                    <th>Base Rate (%)</th>                   
                                    <th>Min Base Rate (%)</th>
                                    <th>Max Base Rate (%)</th>
                                    <th>Created At</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div id="base-rate-listing-processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
{!!Helpers::makeIframePopup('addBaseRateFrame','Add Base Rate', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editBaseRateFrame','Edit Base Rate Detail', 'modal-lg')!!}
@endsection

@section('jscript')
<script>

var messages = {
    get_base_rate_list: "{{ URL::route('get_ajax_master_base_rate_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
    
    
</script>
<script src="{{ asset('backend/js/ajax-js/baserate.js') }}"></script>
@endsection