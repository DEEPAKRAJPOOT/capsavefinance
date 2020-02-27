@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
   {!!
         Form::open(
         array(
         'method' => 'post',
         'route' => 'save_je_config',
         'id' => 'frmJeConfig',
         )
         ) 
   !!}   
   <div class="row align-items-center">
         <div class="col-md-2">
            <div class="form-group">
               <label class="mb-0">Transaction Type</label>
               <span class="mandatory">*</span>
            </div>
         </div>
         <div class="col-md-3">
            <div class="form-group">
               <select name="trans_type" id="trans_type"  class="form-control form-control-sm">
                     <option value="">Select Transaction Type</option>
                     @if(isset($transType) && !empty($transType))
                        @foreach($transType as $key=>$val)
                        <option value="{{$val->trans_config_id}}" {{(old('trans_type') == $val->trans_config_id)? 'selected': ''}}> {{$val->trans_type}} </option>                            
                        @endforeach
                     @endif
               </select>
            </div>

         </div>
   </div>

   <div class="row align-items-center">
         <div class="col-md-2">
            <div class="form-group">
               <label class="mb-0">Variables</label>
               <span class="mandatory">*</span>
            </div>
         </div>

         <div class="col-md-3">
            <div class="form-group">
               <select name="variable[]" id="variable" class="multi-select-demo form-control form-control-sm" multiple="multiple">
               @if(isset($variables) && !empty($variables))
                     @foreach($variables as $key=>$val)
                     <option value="{{$val->id}}" {{(old('variable') == $val->id)? 'selected': ''}}> {{$val->name}} </option>                            
                     @endforeach
               @endif
               </select>
            </div>
         </div>
   </div>

   <div class="row align-items-center">
         <div class="col-md-2">
            <div class="form-group">
               <label class="mb-0">Journal</label>
               <span class="mandatory">*</span>
            </div>
         </div>
         <div class="col-md-3">
            <div class="form-group">
               <select name="journal" id="journal"  class="form-control form-control-sm">
                     <option value="">Select Journal</option>
                     @if(isset($journals) && !empty($journals))
                        @foreach($journals as $key=>$val)
                        <option value="{{$val->id}}" {{(old('journal') == $val->id)? 'selected': ''}}> {{$val->name}} </option>                            
                        @endforeach
                     @endif
               </select>
            </div>

         </div>
   </div>

   <div class="row align-items-center">
         <div class="col-md-2">
         </div>
         <div class="col-md-3">
            <div class="form-group">
               <button class="mb-0">Submit</button>
            </div>
         </div>
   </div>
   {!!  Form::close() !!} 
   <div class="row">
         <div class="col-sm-12">
            <div class="table-responsive">
               <table id="jeConfigList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                     <thead>
                        <tr role="row">
                           <th>Journal Name</th>
                           <th>Journal Type</th>
                           <th>Transaction Type</th>
                           <th>Variables</th>
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