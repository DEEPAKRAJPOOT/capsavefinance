@extends('layouts.backend.admin_popup_layout')
@section('content')

       <div class="modal-body text-left">
           <form id="agencyUserForm" name="agencyUserForm" method="POST" action="{{route('save_agency_user_reg')}}">
              @csrf
              <div class="row">
                 <div class="col-6">
                    <div class="form-group">
                       <label for="txtCreditPeriod">First Name
                       <span class="mandatory">*</span>
                       </label>
                       <input type="text" name="f_name" id="f_name" value="{{old('f_name')}}" class="form-control employee" tabindex="1" placeholder="First Name" >
                       @error('f_name')
                          <span class="error">{{ $message }}</span>
                       @enderror
                    </div>
                 </div>
                 <div class="col-6">
                    <div class="form-group">
                       <label for="txtCreditPeriod">Last Name
                       <span class="mandatory">*</span>
                       </label>
                       <input type="text" name="l_name" id="l_name" value="{{old('l_name')}}" class="form-control employee" tabindex="2" placeholder="Last Name" >
                       @error('l_name')
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
                     <input type="email" name="email" id="email" value="{{old('email')}}" class="form-control email" tabindex="3" placeholder="Email" >
                     @error('email')
                          <span class="error">{{ $message }}</span>
                       @enderror
                  </div>
               </div>

               <div class="col-6">
                     <div class="form-group">
                        <label for="txtMobile">Mobile
                        <span class="mandatory">*</span>
                        </label>
                        <input class="form-control numbercls phone" name="mobile_no" id="mobile_no" value="{{old('mobile_no')}}" tabindex="4" type="text" maxlength="10" placeholder="Mobile" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                        @error('mobile_no')
                          <span class="error">{{ $message }}</span>
                        @enderror
                     </div>
                  </div>
            </div>
            <div class="row">
               <div class="col-6">
                  <div class="form-group">
                     <label for="txtEmail">Agency
                     <span class="mandatory">*</span>
                     </label>                  
                      <select class="form-control state" name="agency_id" id="agency_id" tabindex="6">
                        <option value=""> Select Agency</option>
                        @foreach($agencies as $key => $agency)
                        <option value="{{$agency->agency_id}}" {{(old('agency_id') == $agency->agency_id)? 'selected': ''}}> {{$agency->comp_name}} </option>
                        @endforeach
                      </select>
                      @error('agency_id')
                        <span class="error">{{ $message }}</span>
                     @enderror
                  </div>
               </div>
               <div class="col-6">
                  <div class="form-group">
                    <label for="txtMobile">Status
                    </label>
                    <select class="form-control" name="is_active" tabindex="7">
                      <option value="1">Active</option>
                      <option value="0">In-active</option>
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

        $("#agencyUserForm button[type=submit]").click(function(){
            if($('#agencyUserForm').valid()){
                $('#agencyUserForm').submit();
                $("#agencyUserForm button[type=submit]").attr("disabled","disabled");
            }  
        });
    });
</script>
@endsection