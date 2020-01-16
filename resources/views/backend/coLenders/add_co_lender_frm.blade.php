@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    {!! Form::open(['url'=>route('save_co_lenders'),'id'=>'save_co_lenders']) !!}
    
   
       
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="txtCreditPeriod">Full Name
                        <span class="mandatory">*</span>
                    </label>
                    <input type="text" name="employee" id="employee" value="" class="form-control employee" tabindex="1" placeholder="Full Name" >
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="txtSupplierName">Business Name
                        <span class="mandatory">*</span>
                    </label>
                    <input type="text" name="comp_name" id="comp_name" value="" class="form-control comp_name" tabindex="2" placeholder="Business Name" >
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="txtEmail">Email
                        <span class="mandatory">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="" class="form-control email" tabindex="3" placeholder="Email" >
                </div>
            </div>

            <div class="col-6">
                <div class="form-group">
                    <label for="txtMobile">Mobile
                        <span class="mandatory">*</span>
                    </label>

                    <input class="form-control numbercls phone" name="phone" id="phone" tabindex="4" type="text" maxlength="10" placeholder="Mobile" required="">
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
                    <label for="txtMobile">Address
                        <span class="mandatory">*</span>
                    </label>

                    <input class="form-control comp_addr" name="comp_addr" id="comp_addr" tabindex="5" type="text"  placeholder="Address" required="">
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
                    <label for="txtEmail">State
                        <span class="mandatory">*</span>
                    </label>     
                    
                    
                    
                      <select class="form-control state" name="state" id="state" tabindex="6">
                             <option value=""> Select State</option>
                             @foreach($states as $key => $state)
                             <option value="{{$state->id}}"> {{$state->name}} </option>
                             @endforeach
                         </select>
               </div>
            </div>


        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="txtMobile">City
                        <span class="mandatory">*</span>
                    </label>

                    <input class="form-control city" name="city" id="city" tabindex="7" type="text" maxlength="10" placeholder="City" required="">
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
                    <label for="txtMobile">Pin Code
                        <span class="mandatory">*</span>
                    </label>

                    <input class="form-control numbercls pin_code" name="pin_code" id="pin_code" tabindex="8" type="text" maxlength="6" placeholder="Pin Code" required="">
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
                    <label for="txtMobile">GST
                        <span class="mandatory">*</span>
                    </label>

                    <input class="form-control" name="gst" id="gst"  type="text"  placeholder="GST" required>

                </div>
            </div>
            
            
            <div class="col-6">
                <div class="form-group">
                    <label for="txtMobile">Percentage(%)
                        <span class="mandatory">*</span>
                    </label>

                    <input class="form-control" name="perc" id="perc"  type="text"  placeholder="Percentage(%)" required="">

                </div>
            </div>
            
            

        </div>

        


        <button type="submit" class="btn  btn-success btn-sm float-right" id="saveAnch">Submit</button>  
{!! Form::close()  !!}
</div>




@endsection

@section('jscript')

<script src="{{ asset('common/js/jquery.validate.js') }}"></script>

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
//                                remote: {
//                                url: messages.check_exist_user,
//                                type: 'post',
//                                data: {
//                                'username': $('#email').val()
//                                }
//                            }

                        })
            });
            $('input.phone').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                            number: true,
                            minlength: 10,
                            maxlength: 10
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
            $('input.comp_addr').each(function () {
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
           
            // test if form is valid                
        })
        //$("#btnAddMore").on('click', addInput);
        $('form#save_co_lenders').validate();
    });

</script>
@endsection