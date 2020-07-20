@extends('layouts.backend.admin_popup_layout')

@section('content')

<div class="modal-body text-left">           
    {!!
    Form::open(
    array(
    'route' => 'save_assign_doal_level_role',
    'name' => 'assignDoaLevelRoleForm',
    'autocomplete' => 'off', 
    'id' => 'assignDoaLevelRoleForm',    
    'method'=> 'POST'
    )
    )
    !!}
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label for="txtCreditPeriod">First Name
                    <span class="mandatory">*</span>
                </label>                
                <span class="form-control" readonly>{{$userData->f_name}}</span>               
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label for="txtAnchorName">Last Name
                    <span class="mandatory">*</span>
                </label>                
                <span class="form-control" readonly>{{$userData->l_name}}</span>               
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Mobile
                    <span class="mandatory">*</span>
                </label>                
                <span class="form-control" readonly>{{$userData->mobile_no}}</span>               
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label for="txtAnchorName">E-mail
                    <span class="mandatory">*</span>
                </label>                
                <span class="form-control" readonly>{{$userData->email}}</span>               
            </div>
        </div>
    </div>    
    <div class="row">      
        <div class="col-6">
            <div class="form-group">
                <label for="txtMobile">Role
                    <span class="mandatory">*</span>
                </label>
                {!!
                Form::select('role_id',
                [''=>'Select Role']+$rolesList,
                $roleData->role_id,
                array('id' => 'role',
                'class'=>'form-control', 'tabindex'=>'5'))
                !!}
            </div>
        </div>        
    </div>                

    <button type="submit" class="btn btn-success btn-sm float-right">Submit</button>  


    {!! Form::hidden('_token',csrf_token()) !!}
    {!! Form::hidden('user_id',$userData->user_id, ['id'=>'user_id']) !!}
   

    {!!
    Form::close()
    !!}
</div>
@endsection
@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script>
var messages = {
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    is_accept: "{{ Session::get('is_accept') }}",
};
</script>
<script type="text/javascript">
// Wait for the DOM to be ready
    $(function () {
       
        $("form[name='editRoleForm']").validate({
            rules: {              
                role_id: "required",
            },
            // Specify validation error messages
            //messages: {
            //    role: "Please enter role",
            //},
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid
            submitHandler: function (form) {
                form.submit();
            }
        });
        
    });

</script>
@endsection