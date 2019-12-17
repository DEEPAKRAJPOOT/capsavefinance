@extends('layouts.backend.admin_popup_layout')

@section('content')

       <div class="modal-body text-left">           
                {!!
                Form::open(
                array(
                'route' => 'update_anchor_reg',
                'name' => 'editAchorForm',
                'autocomplete' => 'off', 
                'id' => 'editAchorForm',
                'target' => '_top',
                'method'=> 'POST'
                )
                )
                !!}
                        <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtCreditPeriod">Full Name
                                 <span class="mandatory">*</span>
                                 </label>
                                  <input type="text" name="employee" id="employee" value="@if($anchorUserData){{$anchorUserData->f_name}}@else{}@endif" class="form-control employee" tabindex="1" placeholder="Full Name" >
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtSupplierName">Business Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="comp_name" id="comp_name" value="@if($anchorData){{$anchorData->comp_name}}@else{}@endif" class="form-control comp_name" tabindex="3" placeholder="Business Name" >
                              </div>
                           </div>
                        </div>
                           <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtEmail">Email
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="email" name="email" id="email" value="@if($anchorData){{$anchorData->comp_email}}@else{}@endif" class="form-control email" tabindex="4" placeholder="Email" readonly="">
                              </div>
                           </div>

                           <div class="col-6">
                                 <div class="form-group">
                                    <label for="txtMobile">Mobile
                                    <span class="mandatory">*</span>
                                    </label>

                                 <input class="form-control numbercls phone" name="phone" id="phone" value="@if($anchorData){{$anchorData->comp_phone}}@else{}@endif" tabindex="6" type="text" maxlength="10" placeholder="Mobile" required="">
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                        </div>
                
                
                <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtEmail">State
                                 <span class="mandatory">*</span>
                                 </label>
<!--                                  <select class="form-control state" name="state" id="state">
                                      <option value="">please select</option>
                                      <option value="1" @if($anchorData->comp_state==1)selected @else @endif >state1</option>
                                      <option value="2" @if($anchorData->comp_state==2)selected @else @endif>state2</option>
                                      <option value="3" @if($anchorData->comp_state==3)selected @else @endif>state3</option>
                                  </select>-->
                                  
                                    <select class="form-control state" name="state" id="state" tabindex="6">
                                    <option value=""> Select State</option>
                                    @foreach($states as $key => $state)
                                    <option value="{{$state->id}}" @if($anchorData->comp_state==$state->id)selected @endif > {{$state->name}} </option>
                                    @endforeach
                                    </select> 
                                  
                              </div>
                           </div>

                           <div class="col-6">
                                 <div class="form-group">
                                    <label for="txtMobile">City
                                    <span class="mandatory">*</span>
                                    </label>

                                    <input class="form-control city" name="city" id="city" value="@if($anchorData){{$anchorData->comp_city}}@else{}@endif" tabindex="6" type="text" maxlength="10" placeholder="City" required="">
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                        </div>
                <div class="row">
                           <div class="col-6">
                                 <div class="form-group">
                                    <label for="txtMobile">Pin Code
                                    <span class="mandatory">*</span>
                                    </label>

                                    <input class="form-control numbercls pin_code" name="pin_code" id="pin_code" value="@if($anchorData){{$anchorData->comp_zip}}@else{}@endif" tabindex="6" type="text" maxlength="6" placeholder="Pin Code" required="">
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                        <div class="col-6">
                                 <div class="form-group">
                                    <label for="txtMobile">Address
                                    <span class="mandatory">*</span>
                                    </label>

                                    <input class="form-control comp_addr" name="comp_addr" id="comp_addr" value="@if($anchorData){{$anchorData->comp_addr}}@else{}@endif" tabindex="6" type="text"  placeholder="Address" required="">
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                        </div>
                
                       <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtEmail">Assigned Sale Manager
                                 <span class="mandatory">*</span>
                                 </label>
                                   {!!
                                Form::select('assigned_sale_mgr',
                                [''=>'Please Select']+Helpers::getAllUsersByRoleId(4)->toArray(),
                                $anchorData->sales_user_id,
                                array('id' => 'assigned_sale_mgr',
                                'class'=>'form-control'))
                                !!}
                              </div>
                           </div>                           
                        </div>  
                
                
                {!! Form::hidden('anchor_id', $anchor_id) !!}
                <button type="submit" class="btn  btn-success btn-sm float-right" id="saveAnch">Submit</button>  
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
        $(document).ready(function () {
            $('#saveAnch').on('click', function (event) {
                $('input.employee').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('input.comp_name').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('input.email').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                                 email: true,
                                 //accept:"[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}"
                            })
                });
                $('input.phone').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                                number: true,
                                minlength:10,
                                maxlength:10
                            })
                });
                $('select.state').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('input.city').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('input.pin_code').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                                number: true,
                            })
                });
                $('input.assigned_sale_mgr').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                            })
                }); 
                // test if form is valid                
            })
            //$("#btnAddMore").on('click', addInput);
            $('form#anchorForm').validate();
        });
$(document).ready(function(){
  $("#email").click(function(){
    $("#email").attr("readonly","readonly");  
  });  
})
</script>
@endsection