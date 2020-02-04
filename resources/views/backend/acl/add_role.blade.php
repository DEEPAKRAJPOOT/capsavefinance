@extends('layouts.backend.admin_popup_layout')

@section('content')

<div class="modal-body text-left">           
    {!!
    Form::open(
    array(
    'route' => 'save_add_role',
    'name' => 'editRoleForm',
    'autocomplete' => 'off', 
    'id' => 'editRoleForm',
    'target' => '_top',
    'method'=> 'POST'
    )
    )
    !!}
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label for="role">Role Name
                    <span class="mandatory">*</span>
                </label>
                <input type="text" name="role" id="role" value="@if($roleInfo){{$roleInfo->name}}@endif" class="form-control employee" tabindex="1" placeholder="Role Name" >
            </div>
        </div>     
        <div class="col-6">
            <div class="form-group">
                <label for="" class="">Status<span class="error_message_label">*</span></label>	
                <select class="form-control" name="is_active" id="is_active" >
                    <option value="">Select Status</option>
                    <option value="1" @if(($roleInfo) && ($roleInfo->is_active==1))selected @else @endif>Active</option>
                    <option value="0" @if(($roleInfo) && ($roleInfo->is_active==0))selected @else @endif>Inactive</option>
                </select>
            </div>
        </div>
    </div>    
    <div class="row">
    <div class="col-12">
            <div class="form-group">
                <label for="role_description" class="">Description<span class="error_message_label">*</span></label>
                <textarea type="text" class="form-control" name="description" placeholder="Enter Role Description">@if($roleInfo){{$roleInfo->description}}@endif</textarea>
            </div>
        </div>
    </div>

    {!! Form::hidden('role_id',$role_id) !!}
    <button type="submit" class="btn btn-primary float-right btn-sm" id="saveAnch">Submit</button>  
    {!!
    Form::close()
    !!}
</div>




@endsection

@section('jscript')

<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
<script>

var messages = {
    //get_lead: "{{ URL::route('get_lead') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",

};
</script>
<script type="text/javascript">
// Wait for the DOM to be ready
$(function() {
  $("form[name='editRoleForm']").validate({
    rules: {
      role: "required",
      description: "required",
      is_active:"required",
    },
    // Specify validation error messages
    messages: {
      role: "Please enter role",
      description: "Please enter role description",
      is_active:"Please select status"
     },
    // Make sure the form is submitted to the destination defined
    // in the "action" attribute of the form when valid
    submitHandler: function(form) {
      form.submit();
    }
  });
});
    
</script>
@endsection