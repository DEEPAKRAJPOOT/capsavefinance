@extends('layouts.backend.admin_popup_layout')
@section('content')

       <div class="modal-body text-left">
           <form id="agencyForm" name="agencyForm" method="POST" action="{{route('add_agency_reg')}}" target="_top">
              @csrf
              <div class="row">
                 <div class="col-12">
                    <div class="form-group">
                       <label for="txtCreditPeriod">Agency Name
                       <span class="mandatory">*</span>
                       </label>
                       <input type="text" name="comp_name" id="comp_name" value="" class="form-control employee" tabindex="1" placeholder="Agency Name" >
                    </div>
                 </div>
              </div>
               <div class="row">
               <div class="col-6">
                  <div class="form-group">
                     <label for="txtEmail">Email
                     <span class="mandatory">*</span>
                     </label>
                     <input type="email" name="comp_email" id="comp_email" value="" class="form-control email" tabindex="2" placeholder="Email" >
                  </div>
               </div>

               <div class="col-6">
                     <div class="form-group">
                        <label for="txtMobile">Mobile
                        <span class="mandatory">*</span>
                        </label>
                        <input class="form-control numbercls phone" name="comp_phone" id="comp_phone" tabindex="3" type="text" maxlength="10" placeholder="Mobile" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                     </div>
                  </div>
            </div>
            <div class="row">
               <div class="col-6">
                   <div class="form-group">
                      <label for="txtMobile">Address
                      <span class="mandatory">*</span>
                      </label>
                      <input class="form-control comp_addr" name="comp_addr" id="comp_addr" tabindex="4" type="text"  placeholder="Address">
                   </div>
                </div>
                    
                 <div class="col-6">
                    <div class="form-group">
                       <label for="txtEmail">State
                       <span class="mandatory">*</span>
                       </label>                  
                        <select class="form-control state" name="comp_state" id="comp_state" tabindex="5">
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
                          <input class="form-control city" name="comp_city" id="comp_city" tabindex="6" type="text" maxlength="10" placeholder="City">
                       </div>
                    </div>
                   <div class="col-6">
                         <div class="form-group">
                            <label for="txtMobile">Pin Code
                            <span class="mandatory">*</span>
                            </label>
                            <input class="form-control numbercls pin_code" name="comp_zip" id="comp_zip" tabindex="7" type="text" maxlength="6" placeholder="Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                         </div>
                      </div>
                  </div>
                <button type="submit" class="btn  btn-success btn-sm float-right" id="saveAgency">Submit</button>  
           </form>
         </div>
@endsection

@section('jscript')
<script type="text/javascript">
    $(document).ready(function () {
        $('#agencyForm').validate({ // initialize the plugin
            rules: {
                'comp_name' : {
                    required : true,
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
@endsection