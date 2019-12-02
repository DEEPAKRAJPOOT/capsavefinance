@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Permission</h3>
            <small>Role Permission</small>
            <ol class="breadcrumb">
                <li><a href="#"><i class="mdi mdi-home"></i> Home</a></li> <li>Manage Role</li>
                <li class="active">Manage Permission</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-sm-12">

                    <div class="head-sec">
                        <div class="pull-left">
                            <h4>Manage Permission to :- {{$name}}</h4>
                        </div>
                        <div class="pull-right">
                            <a href="{{route('get_role')}}" class="btn btn-primary btn-sm mr-3">Back</a>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row grid-margin mt-3">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            
                            <form autocomplete="off" method="POST" action="{{route('save_permission')}}" id="PermissionsForm" name="permissionform" enctype="multipart/form-data">
                                <div class="row pull-right" style="margin: 10px;">
                                    <button type="button" id="checkAll" class="btn btn-primary btn-sm mr-3">Check all</button>
                                    <button type="button" id="uncheckAll" class="btn btn-secondary btn-sm">Uncheck all</button>
                                </div>
                                <div class="clearfix"></div>
                                
                                <p class="error" id="myerr" style="color:red;display:none">Check at least one CheckBox</p>
                                <div>

                                    @foreach($getParentData as $key=> $ParentData)
                                    @php $match = 0 @endphp
                                    @php $checked = '' @endphp
                                    @php $rr = Helpers::checkRole($ParentData['id'], $role_id) @endphp
                                    @if($rr)
                                    @if( $rr->permission_id == $ParentData['id'] && $rr->role_id == $role_id)
                                    @php $match = 1 @endphp
                                    @endif
                                    @endif
                                    <ul>
                                        <li>

                                            @php $checked = ($match==1)?'checked':'' @endphp
                                             <input class="p-chk-{{$ParentData['id']}}" type="checkbox" {{$checked}} name="parent[{{$ParentData['id']}}]" id="permission_id[{{$ParentData['id']}}]" value="{{$ParentData['id']}}">{{$ParentData['display_name']}}
                                             @php $childDatas = Helpers::getByParent($ParentData['id'],'1')->toArray() @endphp

                                            @if($childDatas)
                                            @include('backend.acl.manageChild',['childs' => $childDatas])


                                            @endif
                                        </li>
                                    </ul>
                                    @endforeach
                                </div>
                                
                                {!! Form::hidden('role_id', $role_id) !!}
                                {!! Form::hidden('_token', csrf_token()) !!}
                                <div class="btn-block d-block mt-5" style="clear:both;">
                                    <a href="{{route('get_role')}}" class="btn btn-danger btn-sm" style="clear: both;">Cancel</a>
                                    <button type="submit" class="btn btn-success btn-sm ml-2">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
</div>
</div>
{!!Helpers::makeIframePopup('addRoleFrm','Manage Role', 'modal-lg')!!}
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
<script src="{{ asset('backend/js/ajax-js/permission.js') }}" type="text/javascript"></script>
@endsection