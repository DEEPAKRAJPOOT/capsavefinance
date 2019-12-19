@extends('layouts.backend.admin_popup_layout')
@section('content')

       <div class="modal-body text-left">
           <form id="agencyUserForm" name="agencyUserForm" method="POST" action="{{route('save_agency_user_reg')}}" target="_top">
              @csrf
              <div class="row">
                 <div class="col-6">
                    <div class="form-group">
                       <label for="txtCreditPeriod">First Name
                       <span class="mandatory">*</span>
                       </label>
                       <input type="text" name="f_name" id="f_name" value="" class="form-control employee" tabindex="1" placeholder="First Name" >
                    </div>
                 </div>
                 <div class="col-6">
                    <div class="form-group">
                       <label for="txtCreditPeriod">Last Name
                       <span class="mandatory">*</span>
                       </label>
                       <input type="text" name="l_name" id="l_name" value="" class="form-control employee" tabindex="2" placeholder="Last Name" >
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
                        <input class="form-control numbercls phone" name="mobile_no" id="mobile_no" tabindex="4" type="text" maxlength="10" placeholder="Mobile" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                     </div>
                  </div>
            </div>
            <div class="row">
               <div class="col-6">
                   <div class="form-group">
                      <label for="txtMobile">Type
                      </label>
                      <input class="form-control comp_addr" name="comp_addr" id="comp_addr" tabindex="5" type="text" value="Agency" placeholder="Type" disabled>
                   </div>
                </div>
                    
                 <div class="col-6">
                    <div class="form-group">
                       <label for="txtEmail">Agency
                       <span class="mandatory">*</span>
                       </label>                  
                        <select class="form-control state" name="agency_id" id="agency_id" tabindex="6">
                          <option value=""> Select Agency</option>
                          @foreach($agencies as $key => $agency)
                          <option value="{{$agency->agency_id}}"> {{$agency->comp_name}} </option>
                          @endforeach
                        </select>
                    </div>
                 </div>
                </div>
                <button type="submit" class="btn  btn-success btn-sm float-right">Submit</button>  
           </form>
         </div>
@endsection

@section('jscript')
<script type="text/javascript">
    $(document).ready(function () {
        $('#agencyUserForm').validate({ // initialize the plugin
            rules: {
                'f_name' : {
                    required : true,
                },
                'l_name' : {
                    required : true,
                },
                'email' : {
                    required : true,
                    email: true,
                },
                'mobile_no' : {
                    required : true,
                    number: true,
                    minlength:10,
                    maxlength:10
                },
                'agency_id' : {
                    required : true,
                    number: true
                }
            },
            messages: {
                'f_name': {
                    required: "Please enter First Name",
                },
                'l_name': {
                    required: "Please enter Last Name",
                },
                'email': {
                    required: "Please enter Email Id",
                },
                'mobile_no': {
                    required: "Please enter mobile number",
                },
                'agency_id': {
                    required: "Please select agency",
                }
            }
        });
    });
</script>
@endsection