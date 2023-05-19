@extends('layouts.backend.admin-layout')
@section('content')
@section('additional_css')
<style>
    #groupConfirmationModal .modal-body {
        max-height: 400px;
        overflow-y: auto;
    } 
</style>
@endsection
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Group</h3>
            <small>Add Group</small>
            <ol class="breadcrumb">
                <li style="color:#374767;">Home</li>
                <li style="color:#374767;">Manage Group</li>
                <li class="active">Add Group</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <form id="groupForm" method="POST" action="{{ isset($groupData->group_id) ? route('update_new_group') : route('save_new_group') }}">
                @csrf
                <input type="hidden" name='group_id' id="group_id" value="{{ isset($groupData->group_id) ? $groupData->group_id : ''}}">
                <input type="hidden" name='group_name_confirm' id="group_name_confirm" value="0">
                <input type="hidden" name='is_approve_group' id="is_approve_group" value="0">
                
                <div class="row">
                    <div class="form-group col-6">
                        <label for="tax_value">Group Name <span class="mandatory">*</span></label>
                        <input type="text" class="form-control text-uppercase" id="group_name" name="group_name" placeholder="Group Name" value="{{ old('group_name', $groupData->group_name ?? '')}}" onfocusout="this.value = this.value.trim().toUpperCase()">
                        {!! $errors->first('group_name', '<span class="error">:message</span>') !!}
                    </div>
                    <div class="form-group col-6">
                        <label for="tax_value">Group Code {{--<span class="mandatory">*</span>--}}</label>
                        <input type="text" class="form-control" id="group_code" name="group_code" placeholder="Group Code" value="{{ old('group_code', $groupData->group_code ?? '---')}}" disabled>
                        <input type="hidden" name="group_code"  value="{{ old('group_code', $groupData->group_code ?? null)}}">
                        {!! $errors->first('group_code', '<span class="error">:message</span>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6 INR">
                        <label for="tax_value">Current Group Sanction {{--<span class="mandatory">*</span>--}}</label>
                        <div class="relative">
                            <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
                            <input type="text" class="form-control" id="current_group_sanction" name="current_group_sanction" placeholder="Current Group Sanction" value="{{ $currentActiveGroupSanction }}" disabled>
                            {!! $errors->first('current_group_sanction', '<span class="error">:message</span>') !!}
                        </div>
                    </div>
                    <div class="form-group col-6 INR">
                        <label for="tax_value">Current Group Outstanding {{--<span class="mandatory">*</span>--}}</label>
                        <div class="relative">
                            <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
                            <input type="text" class="form-control" id="current_group_outstanding" name="current_group_outstanding" placeholder="Current Group Outstanding" value="{{ $currentGroupOutstanding }}" disabled>
                            {!! $errors->first('current_group_outstanding', '<span class="error">:message</span>') !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for="tax_value">Group Field 1 {{--<span class="mandatory">*</span>--}}</label>
                        <input type="text" class="form-control" id="group_field_1" name="group_field_1" placeholder="Group Field 1" value="{{ old('group_field_1', $groupData->group_field_1 ?? '')}}" {{ $isGroupApproved ? '' : ''}}>
                        {!! $errors->first('group_field_1', '<span class="error">:message</span>') !!}
                    </div>
                    <div class="form-group col-6">
                        <label for="tax_value">Group Field 2 {{--<span class="mandatory">*</span>--}}</label>
                        <input type="text" class="form-control" id="group_field_2" name="group_field_2" placeholder="Group Field 2" value="{{ old('group_field_2', $groupData->group_field_2 ?? '')}}" {{ $isGroupApproved ? '' : ''}}>
                        {!! $errors->first('group_field_2', '<span class="error">:message</span>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for="tax_value">Group Field 3 {{--<span class="mandatory">*</span>--}}</label>
                        <input type="text" class="form-control" id="group_field_3" name="group_field_3" placeholder="Group Field 3" value="{{ old('group_field_3', $groupData->group_field_3 ?? '')}}" {{ $isGroupApproved ? '' : ''}}>
                        {!! $errors->first('group_field_3', '<span class="error">:message</span>') !!}
                    </div>
                    <div class="form-group col-6">
                        <label for="tax_value">Group Field 4 {{--<span class="mandatory">*</span>--}}</label>
                        <input type="text" class="form-control" id="group_field_4" name="group_field_4" placeholder="Group Field 4" value="{{ old('group_field_4', $groupData->group_field_4 ?? '')}}" {{ $isGroupApproved ? '' : ''}}>
                        {!! $errors->first('group_field_4', '<span class="error">:message</span>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for="tax_value">Group Field 5 {{--<span class="mandatory">*</span>--}}</label>
                        <input type="text" class="form-control" id="group_field_5" name="group_field_5" placeholder="Group Field 5" value="{{ old('group_field_5', $groupData->group_field_5 ?? '')}}" {{ $isGroupApproved ? '' : ''}}>
                        {!! $errors->first('group_field_5', '<span class="error">:message</span>') !!}
                    </div>
                    <div class="form-group col-6">
                        <label for="tax_value">Group Field 6 {{--<span class="mandatory">*</span>--}}</label>
                        <input type="text" class="form-control" id="group_field_6" name="group_field_6" placeholder="Group Field 6" value="{{ old('group_field_6', $groupData->group_field_6 ?? '')}}" {{ $isGroupApproved ? '' : ''}}>
                        {!! $errors->first('group_field_6', '<span class="error">:message</span>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12 mb-0 text-right">
                        @if(isset($groupData->group_id))
                            @can('approve_new_group')
                                @if(!$isGroupApproved)
                                <button type="submit" class="btn btn-success btn-sm" id="approve_group" onclick="approveGroup(this)">Approve</button>
                                @endif
                            @endcan

                            @can('update_new_group')
                            <button type="submit" class="btn btn-success btn-sm" id="add_group" onclick="updateGroup(this)">Update</button>
                            @endcan
                        @else
                            @can('save_new_group')
                                <button type="submit" class="btn btn-success btn-sm" id="add_group" onclick="updateGroup(this)">Submit</button>
                            @endcan    
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="groupConfirmationModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Group Name Confirmation</h4>
      </div>
      <div class="modal-body">
        <h6>Below listed group names already exist in the system:</h6>
        <table class="table white-space table-striped" id="table-content">            
        </table>
        <form id="groupConfirmationForm" method="post">
            <div class="row mt-3">
                <div class="col-12">
                    <label for="chrg_type"><strong>Do you want to continue with new group name ?</strong></label><br/>
                    <label class="checkbox-inline mr-3">
                        <input type="radio" name="group_name_confirmation" id="confirmInput1" value="1"> Yes
                    </label>
                    <label class="checkbox-inline">
                        <input type="radio" name="group_name_confirmation" id="confirmInput2" value="0"> No
                    </label>
                </div>
            </div>
          </form>
       </div>
        <div class="modal-footer">
            {{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
            <button type="button" class="btn btn-success btn-sm" id="groupConfirmationBtn">Submit</button>
        </div>
    </div>
  </div>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    var messages = {
        unique_group_name_url:"{{ route('check_unique_group_name') }}",
        check_group_name_suggestions:"{{ route('check_group_name_suggestions') }}",
        id: "{{ isset($groupData->group_id) ? 'yes' : '' }}",
        group_name: "{{ isset($groupData->group_name) ? $groupData->group_name : '' }}",
        token: "{{ csrf_token() }}",
        save_group:"{{ route('save_new_group') }}",
        approve_group:"{{ route('approve_new_group') }}",
        update_group:"{{ route('update_new_group') }}",
    }
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script type="text/javascript">

    $(document).ready(function() {

        $('#groupForm').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) { 
                e.preventDefault();
                return false;
            }
        });

        $("#groupConfirmationBtn").click(function () {
            $('#groupConfirmationModal').modal('hide');
            var isGroupConfirmation = $("#groupConfirmationModal").find('input[name="group_name_confirmation"]:checked').val();
            
            if (isGroupConfirmation == 0) {
                $("#group_name_confirm").val(0);
                return false;
            }

            if (isGroupConfirmation == 1) {
                $("#group_name_confirm").val(1);
                // setTimeout(function() {
                    $("#groupForm").submit();
                // }, 500);
            }
        });

        $.validator.addMethod("uniqueGroupName",
            function(value, element, params) {
                var result = true;
                var data = {name : value, _token: common_vars.token};
                if (params.id) {
                    data['group_id'] = params.id;
                }
                $.ajax({
                    type:"POST",
                    async: false,
                    url: messages.unique_group_name_url, // script to validate in server side
                    data: data,
                    success: function(data) {
                        result = (data.status == 1) ? false : true;
                    }
                });
                return result;                
            },'Group name already exists'
        );

        $.validator.addMethod("alphaNumNspaceNspecialchars", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9& ]*$/.test(value);
        });

        $('#groupForm').validate({
            rules: {
                'group_name': {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    alphaNumNspaceNspecialchars: true,
                    uniqueGroupName: {
                        id: (messages.id != '') ? $('#group_id').val() : null
                    }
                },
                'current_group_sanction': { number: true },
                'current_group_outstanding': { number: true },
                // 'group_field_1': { alphabetsnspace: true },
                // 'group_field_2': { alphabetsnspace: true },
                // 'group_field_3': { alphabetsnspace: true },
                // 'group_field_4': { alphabetsnspace: true },
                // 'group_field_5': { alphabetsnspace: true },
                // 'group_field_6': { alphabetsnspace: true },
            },
            messages: {
                'group_name': {
                    required: "Please enter group name.",
                    alphaNumNspaceNspecialchars: "Only letters, numbers, space and special chars(&) allowed."
                }
            },
            submitHandler: function (form, event) {
                event.preventDefault();
                var groupName = $.trim($("#group_name").val());
                var data = { group_name : groupName, _token: common_vars.token };
                var isGrpConfirm = $("#group_name_confirm").val();                

                $.ajax({
                    type:"POST",
                    async: false,
                    url: messages.check_group_name_suggestions, // script to validate in server side
                    data: data,
                    success: function(response) {   
                        if(messages.id != '' && groupName == messages.group_name) {
                            isGrpConfirm = 1;
                        }

                        if (response.status == 0 || isGrpConfirm == 1) {
                            var isAprGrp = $("#is_approve_group").val();
                            if (isAprGrp == 1) {
                                event.target.action = messages.approve_group;
                                if(response.status == 0 && !confirm('Are you sure you want to approve this group?')) {
                                    return false;
                                }
                            }else {
                                if (messages.id != '') {
                                    event.target.action = messages.update_group;
                                }else {
                                    event.target.action = messages.save_group;
                                }
                            }
                            form.submit();
                            return false;
                        }
                        $("#group_name_confirm").val(0);
                        $('#groupConfirmationModal').modal('show');
                        $('input[name="group_name_confirmation"]:checked').prop('checked', false);
                        var htmlData = '<tr><th>S.No</th><th>Group Name</th></tr>';           
                        if(response.status == 1 && Array.isArray(response.data) && response.data.length) {
                            response.data.forEach(function (grpName, index) {
                                htmlData += `<tr><td>${++index}</td><td>${grpName}</td></tr>`;
                            });
                        }
                        $("#table-content").html(htmlData);
                    }
                });
            },
        });
    });
    
    function approveGroup(event) {
        $("#is_approve_group").val(1);
    }

    function updateGroup(event) {
        $("#is_approve_group").val(0);
    }
</script>
@endsection
