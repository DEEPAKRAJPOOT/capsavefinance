@extends('layouts.backend.admin_popup_layout')
@section('content')

       <div class="modal-body text-left">
           <form id="agencyForm" name="agencyForm" method="POST" action="{{route('add_agency_reg')}}">
              @csrf
              <div class="row">
                 <div class="col-6">
                    <div class="form-group">
                       <label for="txtCreditPeriod">Agency Name
                       <span class="mandatory">*</span>
                       </label>
                       <input type="text" name="comp_name" id="comp_name" value="{{old('comp_name')}}" class="form-control employee" tabindex="1" placeholder="Agency Name" maxlength="50">
                       @error('comp_name')
                          <span class="error">{{ $message }}</span>
                       @enderror
                    </div>
                 </div>
                 <div class="col-6">
                    <div class="form-group">
                       <label for="txtCreditPeriod">Type
                       <span class="mandatory">*</span>
                       </label><br/>
                       <div id="check_block">
                        @foreach($agency_types as $key=>$agency_type)
                          <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;"><input type="checkbox" value="{{$key}}" name="type_id[]" {{(is_array(old('type_id')) && in_array($key,old('type_id')))? 'checked': ''}}> {{$agency_type}}</label>
                        @endforeach
                       </div>
                       @error('type_id')
                          <span class="error">{{ $message }}</span>
                       @enderror
                    </div>
                 </div>
              </div>
               <div class="row">
               <div class="col-6">
                  <div class="form-group">
                     <label for="txtEmail">Email
                     <span class="mandatory">*</span>
                     </label>
                     <input type="email" name="comp_email" id="comp_email" value="{{old('comp_email')}}" class="form-control email" tabindex="2" placeholder="Email" maxlength="50">
                     @error('comp_email')
                        <span class="error">{{ $message }}</span>
                     @enderror
                  </div>
               </div>

               <div class="col-6">
                     <div class="form-group">
                        <label for="txtMobile">Mobile
                        <span class="mandatory">*</span>
                        </label>
                        <input class="form-control numbercls phone" name="comp_phone" id="comp_phone" value="{{old('comp_phone')}}" tabindex="3" type="text" maxlength="10" placeholder="Mobile" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                        @error('comp_phone')
                          <span class="error">{{ $message }}</span>
                       @enderror
                     </div>
                  </div>
            </div>
            <div class="row">
               <div class="col-6">
                   <div class="form-group">
                      <label for="txtMobile">Address
                      <span class="mandatory">*</span>
                      </label>
                      <input class="form-control comp_addr" name="comp_addr" id="comp_addr" value="{{old('comp_addr')}}" tabindex="4" type="text" placeholder="Address" maxlength="100">
                      @error('comp_addr')
                        <span class="error">{{ $message }}</span>
                     @enderror
                   </div>
                </div>
                    
                 <div class="col-6">
                    <div class="form-group">
                       <label for="txtEmail">State
                       <span class="mandatory">*</span>
                       </label>                  
                        <select class="form-control state" name="comp_state" id="comp_state"  tabindex="5">
                          <option value=""> Select State</option>
                          @foreach($states as $key => $state)
                          <option value="{{$state->id}}" {{(old('comp_state') == $state->id)? 'selected': ''}}> {{$state->name}} </option>
                          @endforeach
                        </select>
                        @error('comp_state')
                          <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                 </div>
                </div>

                <div class="row">
                    <div class="col-6">
                       <div class="form-group">
                          <label for="txtMobile">City
                          <span class="mandatory">*</span>
                          </label>
                          <!--<input class="form-control city" name="comp_city" id="comp_city" value="{{old('comp_city')}}" tabindex="6" type="text" placeholder="City" maxlength="50">-->
                          <select name="comp_city" id="comp_city" class="form-control" style="width:350px">
                            <option value="">Select City</option>
                          </select>
                           @error('comp_city')
                              <span class="error">{{ $message }}</span>
                          @enderror

                       </div>
                    </div>
                   <div class="col-6">
                         <div class="form-group">
                            <label for="txtMobile">Pin Code
                            <span class="mandatory">*</span>
                            </label>
                            <input class="form-control numbercls pin_code" name="comp_zip" id="comp_zip" value="{{old('comp_zip')}}" tabindex="7" type="text" maxlength="6" placeholder="Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                            @error('comp_zip')
                              <span class="error">{{ $message }}</span>
                            @enderror
                         </div>
                      </div>
                  </div>
                <input type="hidden" name="is_active" value="1" id='is_active'>
                <button type="submit" class="btn  btn-success btn-sm float-right" id="saveAgency">Submit</button>  
           </form>
         </div>
@endsection

@section('jscript')

<script>

    var messages = {
        //get_lead: "{{ URL::route('get_lead') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#agencyForm1').validate({ // initialize the plugin
            rules: {
                'comp_name' : {
                    required : true,
                },
                'type_id[]': {
                    required: true,
                },
                'comp_email' : {
                    required : true,
                    email: true,
                },
                'comp_phone' : {
                    required : true,
                    number: true,
                    minlength:10,
                    maxlength:10
                },
                'comp_addr' : {
                    required : true,
                },
                'comp_state' : {
                    required : true,
                    number: true
                },
                'comp_city' : {
                    required : true
                },
                'comp_zip' : {
                    required : true,
                    number : true,
                    minlength : 6,
                    maxlength : 6
                }
            },
            messages: {
                'comp_name': {
                    required: "Please enter Agency Name",
                },
                'type_id[]': {
                    required: 'Please select at least one type',
                },
                'comp_email': {
                    required: "Please enter Email Id",
                },
                'comp_phone': {
                    required: "Please enter mobile number",
                },
                'comp_addr': {
                    required: "Please enter agency address",
                },
                'comp_state': {
                    required: "Please select state",
                },
                'comp_city': {
                    required: "Please enter city name",
                },
                'comp_zip': {
                    required: "Please enter pin code",
                }
            }
        });
    });
</script>

<script type="text/javascript">

    $('#comp_state').on('change',function(){
    var stateID = $(this).val();
    if(stateID){
      //  var image_id =  $(this).data('city-error');
        $.ajax({
           type:"POST",
           data: { "approved": "True", 'state_id': stateID, '_token': messages.token},
           url:"{{url('/anchor/get-city-list')}}",
           success:function(data){
            if(data){
                $("#comp_city").empty();
                $.each(data,function(key,value){
                    $("#comp_city").append('<option value="'+value+'">'+value+'</option>');

                  //   $( "select" ).has( "label" ).css( "background-color", "red" );

                     if ( $('#comp_city').next("label").length > 0 ) {
                        $("#comp_city").next().remove();
                     } else {
                     }
                });

            }else{
               $("#comp_city").empty();
            }
           }
        });
    }else{
        $("#comp_city").empty();
    }

   });
</script>


@endsection