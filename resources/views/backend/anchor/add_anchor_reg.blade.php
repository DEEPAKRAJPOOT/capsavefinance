@extends('layouts.backend.admin_popup_layout')
@section('content')

       <div class="modal-body text-left">
           <form id="anchorForm" name="anchorForm" method="POST" onkeyup="return checkValidation();" action="{{route('add_anchor_reg')}}" target="_top" enctype="multipart/form-data">
		@csrf
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
                                 <input type="email" name="email" id="email" value="" class="form-control email" tabindex="4" placeholder="Email" >
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
                                    <label for="txtCity">City
                                    <span class="mandatory">*</span>
                                    </label>

                                    <select name="city" id="city" class="form-control" style="width:350px">
                                        <option value="">Select City</option>
                                 </select>
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
                           
                        </div>
                
                   <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtEmail">Assigned Sale Manager
                                 <span class="mandatory">*</span>
                                 </label>
                                  
                                  {!!
                                Form::select('assigned_sale_mgr',
                                [''=>'Please Select']+Helpers::getAllUsersByRoleId(4),
                                '',
                                array('id' => 'assigned_sale_mgr',
                                'class'=>'form-control',
                                'name'=>'assigned_sale_mgr'))
                                !!}
                              </div>
                           </div>   
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtCreditPeriod">Upload CAM <small>(Allowed Formats: JPG,PNG,PDF)</small><span class="error_message_label">*</span></label>
                                 <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="doc_file" name="doc_file">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                 </div>
                             </div>
                           </div>                       
                        </div>  
                     <div class="row">
                        <div class="col-6">
                           <div class="form-group">
                              <label for="anchor_logo">Upload Anchor Logo<small>(Allowed Formats: JPEG,JPG,PNG)</small><span class="error_message_label">*</span></label>
                              <div class="custom-file">
                                 <input type="file" class="custom-file-input" id="anchor_logo" name="anchor_logo">
                                 <label class="custom-file-label" for="customFile">Choose logo file</label>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label for="is_buyer">Logo AlignMent<span class="mandatory">*</span></label>
                              <select class="form-control" name="logo_align" id="logo_align">
                                 <option value="">Please Select</option>
                                 <option value="1">Left</option>
                                 <option value="2">Right</option>
                              </select>
                              {!! $errors->first('logo_align', '<span class="error">:message</span>') !!}
                           </div> 
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-4">
                           <label for="chrg_type"><strong>Upload Invoice Copy Mandatory</strong></label><br />
                           <div class="form-check-inline ">
                              <label class="fnt">
                              <input type="radio" class="form-check-input is_phy_inv_req" checked name="is_phy_inv_req" value="1">Yes
                              </label>
                           </div>
                           <div class="form-check-inline">
                              <label class="fnt">
                              <input type="radio" class="form-check-input is_phy_inv_req" name="is_phy_inv_req" value="0">No
                              </label>
                           </div>
                        </div> 
                     </div>
                
                <button type="submit" class="btn  btn-success btn-sm float-right" id="saveAnch">Submit</button>  
           </form>
         </div>
     



@endsection

@section('jscript')

<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>

    var messages = {
        //get_lead: "{{ URL::route('get_lead') }}",
        check_exist_email: "{{ URL::route('check_exist_email_anchor') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script type="text/javascript">
        $(document).ready(function () {

         
         $(document).on('keyup', '#email', function(){
              var email = $(this).val();
              if (!email.length) {
                  return false;
              }
              $.ajax({
                  url: messages.check_exist_email,
                  type: 'POST',
                  data: {
                      'email' : email,
                      '_token' : messages.token,
                  },
                  success: function(response){
                     var nameclass = response.status ? 'success' : 'error';
                      $('#email-error').removeClass('error success');
                     if($('#email-error').length){
                        $('#email-error').text(response.message).addClass(nameclass);
                     }else{
                         $('#email').after('<label id="email-error" class="'+ nameclass +'" for="email">'+response.message+'</label>');
                     }
                  }
              });
          });


           
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
                                required: true
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
                $('select.city').each(function () {
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
                                minlength: 6
                            })
                });
                $('input.assigned_sale_mgr').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                            })
                });                   
                $('input.is_phy_inv_req').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                            })
                });                   
            });
            //$("#btnAddMore").on('click', addInput);
            $('form#anchorForm').validate(
               {
                  rules: {
                     doc_file: {
                        required: true,
                        extension: "jpg,jpeg,png,pdf",
                     },
                     assigned_sale_mgr: {
                        required: true
                     },
                     city: {
                        required: true
                     },
                     anchor_logo: {
                        required: false,
                        extension: "jpg,jpeg,png",
                        filesize : 1, // here we are working with MB
                     },
                     logo_align: {
                        required: function(element) {
                           if ($("#anchor_logo").val() != '') {
                                 return true;
                           } else {
                                 return false;
                           }
                        }
                     }
                  },
                  messages: {
                     doc_file: {
                        required: "This field is required.",
                        extension:"Invalid file format",
                     },
                     assigned_sale_mgr: {
                        required: 'This field is required.'
                     },
                     city: {
                        required: 'This field is required.'
                     },
                     anchor_logo: {
                        extension: "Invalid logo file format"
                     }
                  }
               }
            );

        });


        function checkValidation(e) {
            let employee = document.getElementById('employee').value;
            let phone = document.getElementById('phone').value; 
            let pincode = document.getElementById('pin_code').value; 
            let pattern = /^[a-zA-Z\s-, ]+$/;

            if(!employee.match(pattern)) {
               document.getElementById('employee').value = "";
               
            } else if(employee.length >= 50) {
               document.getElementById('employee').value = "";
            };

            if(isNaN(phone)) {
               document.getElementById('phone').value = "";
            };

            if(isNaN(pincode)) {
               document.getElementById('pin_code').value = "";
            };
        }
        
</script>
<script type="text/javascript">
   $(".custom-file-input").on("change", function() {
   var fileName = $(this).val().split("\\").pop();
   $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
   });
</script>


<script type="text/javascript">

    $('#state').on('change',function(){
    var stateID = $(this).val();
    if(stateID){
      //  var image_id =  $(this).data('city-error');
        $.ajax({
           type:"POST",
           data: { "approved": "True", 'state_id': stateID, '_token': messages.token},
           url:"{{url('/anchor/get-city-list')}}",
           success:function(data){
            if(data){
                $("#city").empty();
                $.each(data,function(key,value){
                    $("#city").append('<option value="'+value+'">'+value+'</option>');

                  //   $( "select" ).has( "label" ).css( "background-color", "red" );
                    
                     if ( $('#city').next("label").length > 0 ) {
                        $("#city").next().remove();
                     } else {
                     }
                });

            }else{
               $("#city").empty();
            }
           }
        });
    }else{
        $("#city").empty();
    }

   });
</script>

@endsection