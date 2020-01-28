@extends('layouts.backend.admin_popup_layout')

@section('content')
<div class="modal-body text-left">
    <form id="editAgencyForm" name="editAgencyForm" method="POST" action="{{route('update_agency_reg')}}">
    @csrf
    <input type="hidden" name="agency_id" value="{{request()->get('agency_id')}}">
        <div class="row">
         <div class="col-6">
            <div class="form-group">
               <label for="txtCreditPeriod">Agency Name
               <span class="mandatory">*</span>
               </label>
               <input type="text" name="comp_name" id="comp_name" value="{{old('comp_name', $agencyData->comp_name)}}" class="form-control employee" tabindex="1" placeholder="Agency Name" maxlength="50">
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
                  <label class="checkbox-inline" style="vertical-align: middle; margin-right: 30px; margin-top: 8px;">
                    @if(is_array(old('type_id')))
                      @if(in_array($key, old('type_id')))
                        <input type="checkbox" value="{{$key}}" name="type_id[]" checked>{{$agency_type}}
                        @else
                        <input type="checkbox" value="{{$key}}" name="type_id[]">{{$agency_type}}
                      @endif
                    @else
                      @if(in_array($key, $type_ids))
                        <input type="checkbox" value="{{$key}}" name="type_id[]" checked>{{$agency_type}}
                      @else
                        <input type="checkbox" value="{{$key}}" name="type_id[]">{{$agency_type}}
                      @endif
                    @endif
                  </label>
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
             <input type="email" name="comp_email" id="comp_email" value="{{old('comp_email', $agencyData->comp_email)}}" class="form-control email" tabindex="2" placeholder="Email" maxlength="50">
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
                <input class="form-control numbercls phone" name="comp_phone" id="comp_phone" value="{{old('comp_phone', $agencyData->comp_phone)}}" tabindex="3" type="text" maxlength="10" placeholder="Mobile" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
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
                    <input class="form-control comp_addr" name="comp_addr" id="comp_addr" value="{{old('comp_addr', $agencyData->comp_addr)}}" tabindex="4" type="text"  placeholder="Address" maxlength="100">
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
                      <select class="form-control state" name="comp_state" id="comp_state" tabindex="5">
                        <option value=""> Select State</option>
                        @foreach($states as $key => $state)
                        <option value="{{$state->id}}" {{(old('comp_state', $agencyData->comp_state) == $state->id)? 'selected': ''}}> {{$state->name}} </option>
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
                        <input class="form-control city" name="comp_city" id="comp_city" value="{{old('comp_city', $agencyData->comp_city)}}" tabindex="6" type="text" placeholder="City" maxlength="50">
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
                        <input class="form-control numbercls pin_code" name="comp_zip" id="comp_zip" value="{{old('comp_zip', $agencyData->comp_zip)}}" tabindex="7" type="text" maxlength="6" placeholder="Pin Code" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                        @error('comp_zip')
                            <span class="error">{{ $message }}</span>
                        @enderror
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
        $('#editAgencyForm').validate({ // initialize the plugin
            rules: {
                'type_id[]': {
                    required: true,
                },
            },
            messages: {
                'type_id[]': {
                    required: 'Please select at least one type',
                },
            },
            errorPlacement: function(error, element) {
                error.appendTo('#check_block');
            }


        });
    });
</script>
@endsection