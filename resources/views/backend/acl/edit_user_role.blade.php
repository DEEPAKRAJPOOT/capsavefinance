@extends('layouts.backend.admin_popup_layout')

@section('content')

<div class="modal-body text-left">           
    {!!
    Form::open(
    array(
    'route' => 'update_user_role',
    'name' => 'editRoleForm',
    'autocomplete' => 'off', 
    'id' => 'addRoleForm',
    'target' => '_top',
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
                <input type="text" name="f_name" id="f_name" value="{{$userData->f_name}}" class="form-control" tabindex="1" placeholder="First Name" required="">
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label for="txtAnchorName">Last Name
                    <span class="mandatory">*</span>
                </label>
                <input type="text" name="l_name" id="l_name" value="{{$userData->l_name}}" class="form-control" tabindex="2" placeholder="Last Name" required="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Mobile
                    <span class="mandatory">*</span>
                </label>
                <input type="text" name="mobile_no" maxlength="10" id="mobile_no" value="{{$userData->mobile_no}}" class="form-control" tabindex="3" placeholder="Mobile no" required="">
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label for="txtAnchorName">E-mail
                    <span class="mandatory">*</span>
                </label>
                <!-- <input type="email" name="email" id="email" value="{{$userData->email}}" class="form-control" tabindex="4" placeholder="Enter E-mail" required=""> -->
                <span class="form-control" readonly>{{$userData->email}}</span>
               <input type="hidden" name="email"  value="{{$userData->email}}">
            </div>
        </div>
    </div>
    <div class="row">


<!--        <div class="col-md-6">
            <div class="form-group">
                <label for="txtMobile">Password
                    <span class="mandatory">*</span>
                </label>
                <input type="password" class="form-control numbercls" id="password" name="password"   placeholder="Enter Password" required="">
            </div>                      
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="txtMobile">Confirm Password
                    <span class="mandatory">*</span>
                </label>

                <input type="password" class="form-control numbercls" name="conf_password"   placeholder="Enter Confirm Password" required="">
                <div class="failed">

                </div>
            </div>                      
        </div>	-->
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
        <div class="col-6">
            <div class="form-group">
                <label for="txtMobile">Reporting Manager                    
                </label>
                {!!
                Form::select('parent_id',
                [''=>'Select Reporting Manager']+$parentUserData,
                $userData->parent_id,
                array('id' => 'parent_user_id',
                'class'=>'form-control', 'tabindex'=>'6'))
                !!}
            </div>
        </div>
    </div>
    <div class="row">
            @php 
            if($roleData->role_id == 8) {
                $disp = 'block;'; 
            } else {
                $disp = 'none;';
            }
            @endphp
        
        <!--
        <div class="col-md-6 is-apprv-req" style="display:{{ $disp }}">
            <div class="form-group">
                <label for="txtMobile">
                {!! 
                    Form::checkbox('is_appr_required', 1, $userData->is_appr_required,
                    array(
                        'id' => 'is_appr_required',
                        'class'=>'form-control'
                        )
                    ) 
                !!}                    
                is approval required?                    
                </label>
            </div>
        </div>
        -->
        
        <div class="col-6">
            <div class="form-group">
                <label for="txtCreditPeriod"> State
                    <span class="mandatory">*</span>
                </label>                                                
                {!!
                    Form::select('state_id',
                    [''=>'Select State'] + $stateList,
                    $userData->state_id,
                    [
                    'class' => 'form-control',                
                    'id' => 'state_id',
                     'tabindex'=>'7'
                    ])
                !!}                        
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label for="txtCreditPeriod"> City
                    <span class="mandatory">*</span>
                </label>                                                
                {!!
                    Form::select('city_id',
                    [''=>'Select City']+$cityList,
                    $userData->city_id,
                    [
                    'class' => 'form-control',                
                    'id' => 'city_id', 
                    'tabindex'=>'8'
                    ])
                !!}                        
            </div>
        </div>             
    </div>
    
    <div class="row">
    <div class="col-6">
            <div class="form-group">
                <label for="txtMobile">Is Active
                    <span class="mandatory">*</span>
                </label>
                {!!
                Form::select('is_active',
                [''=>'Please select','0'=> 'In Active','1'=>'Active'],
                $userData->is_active,
                array('id' => 'is_active',
                'class'=>'form-control', 'tabindex'=>'9'))
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
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
<script>
var messages = {
    //get_lead: "{{ URL::route('get_lead') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    is_accept: "{{ Session::get('is_accept') }}",
    get_backend_users_url : "{{ route('ajax_get_backend_user_list') }}",
    ajax_get_city_url : "{{ route('ajax_get_city') }}",
};
</script>
<script type="text/javascript">
// Wait for the DOM to be ready
    $(function () {
       
        $("form[name='editRoleForm']").validate({
            rules: {
                f_name: "required",
                l_name: "required",
               mobile_no:{
                required:true,
                minlength:9,
                maxlength:10,
                number: true
                },
                email:{
                    required:true,
                    email:true
                    },
                is_active: "required",
                role_id: "required",
                password:"required",
                conf_password: {
                    equalTo: "#password",
                    required:true
                    
                },
                state_id: "required",
                city_id: "required"
            
            },
            // Specify validation error messages
//            messages: {
//                role: "Please enter role",
//                description: "Please enter role description",
//                is_active: "Please select status"
//            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid
            submitHandler: function (form) {
                form.submit();
            }
        });
        
        $(document).on('change', '#role', function(){   
            var role_id = $(this).val();
            if (role_id == 8) {
                $(".is-apprv-req").show();
            } else {
                $("#is_appr_required").prop("checked", false);
                $(".is-apprv-req").hide(); 
            }            
            $.ajax({
                url  : messages.get_backend_users_url,
                type :'POST',
                data : {role_id : $(this).val(), user_id : $("#user_id").val(), _token : messages.token},
                beforeSend: function() {
                    //$(".isloader").show();
                },
                dataType : 'json',
                success:function(result) {
                    var optionList = result;
                    $("#parent_user_id").empty().append('<option>Select Reporting Manager</option>');
                    $.each(optionList, function (index, data) {
                        $("#parent_user_id").append('<option  value="' + data.user_id + '"  >' + data.f_name + ' ' + data.l_name+  '</option>');
                    }); 
                },
                error:function(error) {
                },
                complete: function() {
                    //$(".isloader").hide();
                },
            })        
        })    
        
        $(document).on('change', '#state_id', function(){
            var state_id = $(this).val();       
            $.ajax({
                url  : messages.ajax_get_city_url,
                type :'POST',
                data : {state_id : state_id, _token : messages.token},
                beforeSend: function() {
                    $(".isloader").show();
                },
                dataType : 'json',
                success:function(result) {
                    var optionList = result;
                    $("#city_id").empty().append('<option>Select City</option>');
                    $.each(optionList, function (index, data) {
                        $("#city_id").append('<option  value="' + data.id + '"  >' + data.name +  '</option>');
                    }); 
                },
                error:function(error) {
                },
                complete: function() {
                    $(".isloader").hide();
                }
            })
        })  
    });

</script>
@endsection