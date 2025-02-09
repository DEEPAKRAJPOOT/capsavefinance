@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>
                {{ trans('backend.mange_program.manage_program') }} </h3>
            <small>{{ trans('backend.mange_program.program_list') }}</small>
            <ol class="breadcrumb">
                <li style="color:#374767;">  {{ trans('backend.mange_program.home') }} </li>
                <li style="color:#374767;"> {{ trans('backend.mange_program.manage_program') }}</li>
                <li class="active"> {{ trans('backend.mange_program.program_list') }}</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row" style="margin-bottom: 25px;">
                <div class="col-md-4">
                    <input class="form-control" placeholder="Search by Program Name, Anchor Name" id="search_keyword" name="search_keyword" type="text">
                </div>
                <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
                <div class="col-md-7 text-right">
                  <a href="{{route('manage_program')}}" class="btn btn-success btn-sm float-right" style="margin-left: 10px">View All</a>
                  <span style="margin: 10px !important"></span>
                  <a href="{{route('add_program',['anchor_id'=>$anchor_id])}}" class="btn btn-success btn-sm float-right"><i class="fa fa-plus"></i>{{ trans('backend.mange_program.add_program') }}</a>
                </div>
           </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="program_list" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>{{ trans('backend.mange_program.program_id') }}</th>
                                    <th>{{ trans('backend.mange_program.product_type') }}</th>                                    
                                    <th>{{ trans('backend.mange_program.program_mame') }}</th>
                                    <th>{{ trans('backend.mange_program.anchor_name') }}</th>
                                    <th>{{ trans('backend.mange_program.anchor_limit') }}</th>
                                    <th>Program Type</th>                                                                  
                                    <th>{{ trans('backend.mange_program.status') }}</th>                                    
                                    <th>{{ trans('backend.mange_program.action') }}</th>
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
{!!Helpers::makeIframePopup('editProgram','Edit Anchor Program', 'modal-lg')!!}
{!!Helpers::makeIframePopup('modifyProgramLimit','Modify Program Limit', 'modal-lg')!!}
{!!Helpers::makeIframePopup('showEndProgramReason','View Reason', 'modal-md')!!}
@endsection

@section('jscript')
<script>

    var messages = {
        get_program_list: "{{ URL::route('get_program_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        anchor_id: "{{ isset($anchor_id) ? $anchor_id : null }}"

    };
</script>
<script src="{{ asset('backend/assets/js/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/program.js') }}" type="text/javascript"></script>
@endsection
