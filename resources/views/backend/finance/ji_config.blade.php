@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
   <h3>{{ isset($jiConfigData->ji_config_id) ? 'Edit' : 'Add'}} Journal Item</h3>
   {!!
         Form::open(
         array(
         'method' => 'post',
         'route' => 'save_ji_config',
         'id' => 'frmJiConfig',
         )
         ) 
   !!}  
   <input type="hidden" name="je_config_id" value="{{$jeConfigId}}" /> 
   <input type="hidden" name="ji_config_id" value="{{ isset($jiConfigData->ji_config_id) ? $jiConfigData->ji_config_id : ''}}" /> 
   <div class="row align-items-center">
         <div class="col-md-2">
            <div class="form-group">
               <label class="mb-0">Label</label>
               <span class="mandatory">*</span>
            </div>
         </div>
         <div class="col-md-4">
            <div class="form-group">
               <input  class="form-control" type="text" name="label" id="label" placeholder="Enter Description" value="{{ old('label') ? old('label') : isset($jiConfigData->label) ? $jiConfigData->label : ''}}" />
            </div>
         </div>
   </div>

   <div class="row align-items-center">
         <div class="col-md-2">
            <div class="form-group">
               <label class="mb-0">Account</label>
               <span class="mandatory">*</span>
            </div>
         </div>
         <div class="col-md-4">
            <div class="form-group">
               <select name="account" id="account"  class="form-control form-control-sm">
                     <option value="">Select Account Type</option>
                     @if(isset($accounts) && !empty($accounts))
                        @foreach($accounts as $key=>$val)
                        <option value="{{$val->id}}" {{ (old('account') == $val->id)? 'selected': (isset($jiConfigData->account_id) && $jiConfigData->account_id==$val->id) ? 'selected' : ''}}> {{$val->account_name}} - {{$val->account_code}} </option>                            
                        @endforeach
                     @endif
               </select>
            </div>
         </div>
   </div>

   <div class="row align-items-center">
         <div class="col-md-2">
            <div class="form-group">
               <label class="mb-0">Is Partner</label>
               <span class="mandatory">*</span>
            </div>
         </div>

         <div class="col-md-4">
            <div class="form-group">
               <select name="is_partner" id="is_partner" class="form-control form-control-sm">
                  <option value="">Select Partner</option>
                  <option value="1" {{ (old('is_partner') == '1')? 'selected': (isset($jiConfigData->is_partner) && $jiConfigData->is_partner=='1') ? 'selected' : ''}}>Yes</option>
                  <option value="0" {{ (old('is_partner') == '0')? 'selected': (isset($jiConfigData->is_partner) && $jiConfigData->is_partner=='0') ? 'selected' : ''}}>No</option>
               </select>
            </div>
         </div>
   </div>

   <div class="row align-items-center">
         <div class="col-md-2">
            <div class="form-group">
               <label class="mb-0">Entry Type</label>
               <span class="mandatory">*</span>
            </div>
         </div>

         <div class="col-md-4">
            <div class="form-group">
               <select name="value_type" id="value_type" class="form-control form-control-sm">
                  <option value="">Select Entry Type</option>
                  <option value="0" {{ (old('value_type') == '0')? 'selected': (isset($jiConfigData->value_type) && $jiConfigData->value_type=='0') ? 'selected' : ''}}>Debit</option>
                  <option value="1" {{ (old('value_type') == '1')? 'selected': (isset($jiConfigData->value_type) && $jiConfigData->value_type=='1') ? 'selected' : ''}}>Credit</option>                  
               </select>
            </div>
         </div>
   </div>

   <div class="row align-items-center">
         <div class="col-md-2">
            <div class="form-group">
               <label class="mb-0">Variables/Operators</label>
            </div>
         </div>

         <div class="col-md-4">
            <div class="form-group">
               <select name="variable" id="variable" class="form-control form-control-sm">
               <option value="">Select Variable/Operator</option>
                  @foreach(config('common.OPERATORS') as $key=>$val)
                     <option value="{{$val}}"> {{$val}} </option>
                  @endforeach
                  @if(isset($variables) && !empty($variables))
                     @foreach($variables as $key=>$val)
                     <option value="{{$val}}"> {{$val}} </option>                            
                     @endforeach
                  @endif
               </select>
            </div>
         </div>
   </div>

   <div class="row align-items-center">
         <div class="col-md-2">
            <div class="form-group">
               <label class="mb-0">Formula Configuration</label>
               <span class="mandatory">*</span>
            </div>
         </div>
         <div class="col-md-4">
            <div class="form-group">
               <textarea class="form-control" type="text" name="config_value" id="config_value" placeholder="Make formula here" value="{{ old('config_value') ? old('config_value') : isset($jiConfigData->config_value) ? $jiConfigData->config_value : ''}}">{{ old('config_value') ? old('config_value') : isset($jiConfigData->config_value) ? $jiConfigData->config_value : ''}}</textarea>
            </div>
         </div>
   </div>

   <div class="row align-items-center">
         <div class="col-md-2">
         </div>
         <div class="col-md-3">
            <div class="form-group">
               <button class="btn  btn-success btn-sm">Submit</button>
            </div>
         </div>
   </div>
   {!!  Form::close() !!} 
   @if(isset($jiConfigData->ji_config_id) && !empty($jiConfigData->ji_config_id))
   <div class="row">
      <div class="col-sm-12">
         <a href="{{ route('add_ji_config', ['je_config_id' => $jeConfigId]) }}">
            <button class="btn  btn-success btn-sm">Add Journal Item</button>
         </a>
      </div>
   </div>
   @endif
   <div class="row">
         <div class="col-sm-12">
            <div class="table-responsive">
               <table id="jiConfigList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                     <thead>
                        <tr role="row">
                           <th>Account Name</th>
                           <th>Is Partner</th>
                           <th>Label</th>
                           <th>Entry Type</th>
                           <th>Formula</th>  
                           <th>Action</th>                          
                        </tr>
                     </thead>
                     <tbody>
                     </tbody>
               </table>
            </div>
         </div>
   </div>
</div>

@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script>
var messages = {
    get_ajax_jiconfig_list: "{{ URL::route('get_ajax_jiconfig_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };

    $(document).ready(function(){
        $('#frmJiConfig').validate({
            rules: {
               label: {
                    required: true
                },
                account: {
                   required: true
                },
                is_partner: {
                   required: true
                },
                value_type: {
                   required: true
                },
                config_value: {
                   required: true
                }
            }
        });

        $('#variable').change(function(){
            var str = $('#config_value').val();
            var optr = $('#variable').val();
            $('#config_value').val(str + optr);
        });
    });
</script>
<script src="{{ asset('backend/js/ajax-js/finance.js') }}"></script>
@endsection