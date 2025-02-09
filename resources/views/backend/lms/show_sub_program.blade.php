@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>
                {{ trans('backend.mange_program.manage_sub_program') }} </h3>
            <small>{{ trans('backend.mange_program.program_sub_list') }}</small>
            <ol class="breadcrumb">
                <li style="color:#374767;">  {{ trans('backend.mange_program.home') }} </li>
                <li style="color:#374767;"> <a href='{{ $redirectUrl }}'>  {{ trans('backend.mange_program.manage_program') }} </a></li>
                <li class="active"> {{ trans('backend.mange_program.program_sub_list') }}</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-sm-12">

                    <div class="head-sec">
                        <div class="pull-right" style="margin-bottom: 10px;margin-right: 12px;">
                            @can('add_program')                            
                            <a href="{{route('add_sub_program',['anchor_id'=>$anchor_id ,'program_id'=>$program_id])}}" >
                                <button class="btn  btn-success btn-sm" type="button">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    {{ trans('backend.mange_program.add_sub_program') }}
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
                        <table id="sub_program_list" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>{{ trans('backend.mange_program.sub_program_id') }}</th>
                                    <th>{{ trans('backend.mange_program.anchor_detail') }}</th>
                                    <th>{{ trans('backend.mange_program.sub_program_limit') }}</th>
                                    <th>{{ trans('backend.mange_program.updated_by') }}</th>
                                    <th>{{ trans('backend.mange_program.reason') }}</th>
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
{!!Helpers::makeIframePopup('modifyProgramLimit','Modify Program Limit', 'modal-md')!!}
{!!Helpers::makeIframePopup('showEndProgramReason','View Reason', 'modal-md')!!}
@endsection

@section('jscript')
<script>

    var messages = {
        get_sub_program_list: "{{ URL::route('get_sub_program_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        anchor_id: "{{ isset($anchor_id) ? $anchor_id : null }}",
        program_id : "{{ isset($program_id) ? $program_id : null  }}"

    };
</script>
<script src="{{ asset('backend/assets/js/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/program.js') }}" type="text/javascript"></script>
@endsection
