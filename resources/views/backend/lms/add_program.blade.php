@extends('layouts.backend.admin-layout')
@section('content')
<div class="content-wrapper">
   <section class="content-header">
      <div class="header-icon">
         <i class="fa fa-clipboard"></i>
      </div>
      <div class="header-title">
         <h3>
            {{ trans('backend.mange_program.add_program') }} 
         </h3>
         <ol class="breadcrumb">
            <li style="color:#374767;">  {{ trans('backend.mange_program.home') }} </li>
            <li style="color:#374767;"> {{ trans('backend.mange_program.manage_program') }}</li>
            <li class="active"> {{ trans('backend.mange_program.add_program') }}</li>
         </ol>
      </div>
   </section>
   <div class="row grid-margin mt-3">
      <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
         <div class="card">
            <div class="card-body">
               <div class="form-fields">
                  <div class="active" id="details">
                      
                    {{ Form::open(array('url' => route('save_program') ,'id'=>'addProgram')) }}
                      {!! Form::hidden('anchor_id', isset($anchor_id) ? $anchor_id : null)   !!}
                      
                     <div class="form-sections">
                        <div class="col-md-8 col-md-offset-2">
                           <div class="form-head">
                              <h3 class="pull-left"><small>
                                 {{ trans('backend.add_program.add_program') }}
                                 </small>
                              </h3>
                              <div class="pull-right">
                                 <button type="button" id="" class="btn btn-secondry btn-sm"><img src="images/draft.png" alt="draft"> 
                                 {{ trans('backend.add_program.save_as_draft') }}
                                 </button>
                              </div>
                           </div>
                           <div class="clearfix"></div>
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="txtCreditPeriod">
                                    {{ trans('backend.add_program.anchor_name') }} 
                                    <span class="error_message_label">*</span></label>
                                    {!! Form::select('anchor_user_id', $anchorList,'',['class'=>'form-control'])!!}
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="txtCreditPeriod">
                                    {{ trans('backend.add_program.program_detail') }}   
                                    <span class="error_message_label">*</span></label>
                                    <div class="block-div clearfix mb-3 mt-3">
                                       <div class="form-check-inline float-left">
                                          <label class="form-check-label fnt">
                                          {!! Form::radio('prgm_type','1','', ['class'=>'form-check-input'])   !!}
                                          <strong>
                                          {{ trans('backend.add_program.vendor_finance') }}   
                                          </strong>
                                          </label>
                                       </div>
                                       <div class="form-check-inline float-left">
                                          <label class="form-check-label fnt">
                                          {!! Form::radio('prgm_type','2','', ['class'=>'form-check-input'])!!}<strong>
                                          {{ trans('backend.add_program.channel_finance') }}    
                                          </strong>
                                          </label>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="txtCreditPeriod">  
                                    {{ trans('backend.add_program.program_name') }}      
                                    <span class="error_message_label">*</span>
                                    </label>
                                    {!! Form::text('prgm_name','', ['class'=>'form-control','placeholder'=>"Enter Programe Name"])!!}                                                 
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="txtCreditPeriod">  {{ trans('backend.add_program.industry') }}
                                    <span class="error_message_label">*</span></label>
                                    {!! Form::select('industry_id', 
                                    [''=>trans('backend.please_select')] + $industryList,
                                    '',
                                    ['class'=>'form-control industry_change'])
                                    !!}   
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="txtCreditPeriod">
                                    {{ trans('backend.add_program.sub_industry') }} 
                                    <span class="error_message_label">*</span> </label>
                                    {!! Form::select('sub_industry_id', 
                                    [''=>trans('backend.please_select')],
                                    '',
                                    ['class'=>'form-control sub_industry'])
                                    !!}  
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="txtCreditPeriod">   
                                    {{ trans('backend.add_program.anchor_limit') }} 
                                    <span class="error_message_label">*</span> </label>
                                    {!! Form::text('anchor_limit','', ['class'=>'form-control','placeholder'=>trans('backend.add_program.enter_anchor_limit')])!!}  
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="txtCreditPeriod">
                                    {{ trans('backend.add_program.fldf_applicable') }} 
                                    <span class="error_message_label">*</span>
                                    </label>
                                    <div class="">
                                       <div class="form-check-inline">
                                          <label class="form-check-label fnt">
                                          {!! Form::radio('is_fldg_applicable',1,'', ['class'=>'form-check-input'])!!}  
                                         Yes
                                          </label>
                                       </div>
                                       <div class="form-check-inline ">
                                          <label class="form-check-label fnt">
                                          {!! Form::radio('is_fldg_applicable',0,'', ['class'=>'form-check-input'])!!}  No
                                          </label>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="ima"></div>
                        <div class="col-md-8 col-md-offset-2">
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="text-right mt-3">
                                   
                                     {!! Form::button('Cancel', ['class'=>'btn btn-secondary btn-sm' ,'id'=>'pre4']) !!}
                                    
                                     {!! Form::submit('Save and Next', ['class'=>'btn btn-primary submit' ,'id'=>'pre3']) !!}
                                   
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                      
                      
                      {{ Form::close() }}
                      
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('jscript')
<script>
   var messages = {
       get_sub_industry: "{{ URL::route('get_sub_industry') }}",
       data_not_found: "{{ trans('error_messages.data_not_found') }}",
       token: "{{ csrf_token() }}",
       please_select: "{{ trans('backend.please_select') }}"
   
   };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/program.js') }}" type="text/javascript"></script>
@endsection