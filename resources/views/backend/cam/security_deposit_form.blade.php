@if($route_name != "cam_overview")
@php
    $mdCls = 'md-5';
    if($route_name=="security_deposit"){
        $mdCls = 'md-4';
    }
@endphp
<div class="data mt-4">
    <h2 class="sub-title bg">Pre/Post Disbursement Condition
      </h2>                    
    <div class="col-md-12 mt-4" id="security-doc-block">
        @if(!empty($arrAppSecurityDoc))
             @php
                 $jsonDecode = json_decode($securityDocumentListJson,true);
             @endphp
            @foreach($arrAppSecurityDoc as $key=>$arr)
            @php
                  
                    $key =  $key+1;
                    $disabled1 = '';
                    $disabled2 = '';
                    $disabled4 = ''; 
                    $disableorigDate = 'disabled';
                    $disabled = '';
                    if($arr['is_non_editable'] == 1){
                        
                        if($arr['doc_type'] ==1){
                            $disabled2 = 'disabled';
                        }elseif($arr['doc_type'] ==2){
                            $disabled1 = 'disabled';
                        }
                        
                        foreach ($jsonDecode as $key2 => $sectDoc2){
                            if($sectDoc2['security_doc_id'] == $arr['security_doc_id']){
                                $disabled4 ='';
                            }else{
                                $disabled4 = 'disabled'; 
                            }
                        }
                           
                        $disabled = 'readonly';
                        if($arr['due_date'] == null && $arr['due_date'] == ''){
                            $disableorigDate = '';
                        }
                    }            
              @endphp
            <div class="row p-2 mt-1 toRemoveDiv1 {{($loop->first)? '': 'mt10'}}" style="background-color: #e9e7e7;">
                <input type="hidden" name="app_security_doc_id[]" class="form-control app_security_doc_id" value="{{$arr['app_security_doc_id'] ?? ''}}" id="app_security_doc_id_{{ $key }}"/>
                <div class="col-md-2 mt-1">
                    <label for="txtPassword"><b>Pre/Post Disbursement</b></label>
                    <div class="relative">
                        
                        <select class="form-control doc_type" name="doc_type[]" id="update_doc_type_{{ $key }}" {{$disabled}}>
                            <option value="">Select</option>
                            <option value="1" {{(isset($arr['doc_type']) && $arr['doc_type'] == '1') ? 'selected': '' }} {{ $disabled1 }}>Pre</option>
                            <option value="2" {{(isset($arr['doc_type']) && $arr['doc_type'] == '2') ? 'selected': '' }} {{ $disabled2 }}>Post</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 mt-1">
                    <label for="txtPassword"><b>Type of Document</b></label>
                    <select class="form-control security_doc_id" name="security_doc_id[]" id="update_security_doc_id_{{ $key }}" {{$disabled}}>
                       <option value="">Select</option>
                       @foreach ($jsonDecode as $key1 => $sectDoc)
                           <option value="{{ $sectDoc['security_doc_id'] }}" {{(isset($arr['security_doc_id']) && $sectDoc['security_doc_id'] == $arr['security_doc_id']) ? 'selected': '' }} {{ $disabled4}}>{{ $sectDoc['name'] }}</option>
                       @endforeach
                   </select>
               </div>
               <div class="col-{{ $mdCls }} mt-1">
                       <label for="txtPassword"><b>Description</b></label>
                       <div class="relative">
                           <textarea name="description[]" class="form-control description" placeholder="Description" autocomplete="off" id="update_description_{{ $key }}" {{$disabled}} >{{$arr['description'] ?? ''}} </textarea>
                       </div>
               </div>
               @if($route_name=="security_deposit")    
               <div class="col-md-2 mt-1">
                    <label for="txtPassword"><b>Document Number</b></label>
                    <div class="relative">
                       <input type="text" name="document_number[]" class="form-control document_number" value="{{$arr['document_number'] ?? ''}}" placeholder="Document Number" autocomplete="off" id="update_document_number_{{ $key }}" />
                    </div>
               </div>
               @endif
               <div class="col-md-2 mt-1">
                    <label for="txtPassword"><b>Original Due Date</b></label>
                    <div class="relative">
                            <input type="text" name="due_date[]" maxlength="20" class="form-control sc-doc-date due_date" value ="{{(isset($arr['due_date']) && $arr['due_date']) ? \Carbon\Carbon::createFromFormat('Y-m-d', $arr['due_date'])->format('d/m/Y'): '' }}" placeholder="Original Due Date" autocomplete="off" id="update_due_date_{{ $key }}" readonly="readonly" {{ $disableorigDate }}/>
                    </div>
               </div>
               @if($route_name=="security_deposit")  
               <div class="col-md-2 mt-1">
                   <label for="txtPassword"><b>Completed</b></label>
                   <div class="relative">
                       <select class="form-control completed" name="completed[]" id="update_completed_{{ $key }}" >
                           <option value="">Select</option>
                           <option value="yes" {{(isset($arr['completed']) && $arr['completed'] == 'yes') ? 'selected': '' }}>Yes</option>
                           <option value="no" {{(isset($arr['completed']) && $arr['completed'] == 'no') ? 'selected': '' }}>No</option>
                       </select>
                   </div>
              </div>
              <div class="col-md-2 mt-1">
                       <label for="txtPassword"><b>Exception Received</b></label>
                       <div class="relative">
                           <select class="form-control exception_received" name="exception_received[]" onchange="displayExceptionFields(this.value,{{ $key }});" id="update_exception_received_{{ $key }}" data-previous="{{ $key }}" >
                               <option value="">Select</option>
                               <option value="yes" {{(isset($arr['exception_received']) && $arr['exception_received'] == 'yes') ? 'selected': '' }}>Yes</option>
                               <option value="no" {{(isset($arr['exception_received']) && $arr['exception_received'] == 'no') ? 'selected': '' }}>No</option>
                           </select>
                       </div>
               </div>
             
               <div class="col-md-2 mt-1 exceptionFields_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no' || $arr['exception_received'] == null) ? 'style="display: none;"': '' !!}>
                       <label for="txtPassword"><b>Exception Received From</b></label>
                       <div class="relative">
                       <input type="text" name="exception_received_from[]" class="form-control exception_received_from {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'yes') ? 'required': '' !!}" value="{{$arr['exception_received_from'] ?? ''}}" placeholder="Exception Received From" autocomplete="off" id="update_exception_received_from_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no') ? 'style="visibility: none;height: 0;"': '' !!} />
                       </div>
               </div>
               <div class="col-md-2 mt-1 exceptionFields_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no' || $arr['exception_received'] == null) ? 'style="display: none;"': '' !!}>
                       <label for="txtPassword"><b>Exception Received Date</b></label>
                       <div class="relative">
                       <input type="text" name="exception_received_date[]" class="form-control sc-doc-date-r exception_received_date {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'yes') ? 'required': '' !!}" value ="{{(isset($arr['exception_received_date']) && $arr['exception_received_date']) ? \Carbon\Carbon::createFromFormat('Y-m-d', $arr['exception_received_date'])->format('d/m/Y'): '' }}" placeholder="Exception Received Date" autocomplete="off" id="update_exception_received_date_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no') ? 'style="visibility: hidden;height: 0;"': '' !!} readonly="readonly" />
                       </div>
               </div>
               <div class="col-md-2 mt-1 exceptionFields_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no' || $arr['exception_received'] == null) ? 'style="display: none;"': '' !!}>
                   <label for="txtPassword"><b>Exception Remark</b></label>
                   <div class="relative">
                   <input type="text" name="exception_remark[]" class="form-control exception_remark {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'yes') ? 'required': '' !!}" value="{{$arr['exception_remark'] ?? ''}}" placeholder="Exception Remark" autocomplete="off" id="update_exception_remark_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no') ? 'style="visibility: hidden;height: 0;"': '' !!} />
                   </div>
               </div>
               <div class="col-md-2 mt-1 exceptionFields_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no' || $arr['exception_received'] == null) ? 'style="display: none;"': '' !!}>
                    <label for="txtPassword"><b>Extended Due Date</b></label>
                    <div class="relative">
                    <input type="text" name="extended_due_date[]" class="form-control sc-doc-date-r extended_due_date {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'yes') ? 'required': '' !!}" value ="{{(isset($arr['extended_due_date']) && $arr['extended_due_date']) ? \Carbon\Carbon::createFromFormat('Y-m-d', $arr['extended_due_date'])->format('d/m/Y'): '' }}" placeholder="Extended Due Date" autocomplete="off" id="update_exception_received_date_{{ $key }}" {!!(isset($arr['exception_received']) && $arr['exception_received'] == 'no') ? 'style="visibility: hidden;height: 0;"': '' !!} readonly="readonly" />
                    </div>
                </div>
               <div class="col-md-2 mt-1">
                   <label for="txtPassword"><b>Maturity Date</b></label>
                   <div class="relative">
                   <input type="text" name="maturity_date[]" class="form-control sc-doc-date maturity_date" value="{{ $arr['maturity_date']!=null?\Carbon\Carbon::createFromFormat('Y-m-d', $arr['maturity_date'])->format('d/m/Y'):'' ?? ''}}" placeholder="Maturity Date" autocomplete="off" id="update_maturity_date_{{ $key }}" readonly="readonly" />
                   </div>
               </div>
               <div class="col-md-2 mt-1">
                   <label for="txtPassword"><b>Renewal Reminder Days</b></label>
                   <div class="relative">
                   <input type="text" name="renewal_reminder_days[]" class="form-control digits renewal_reminder_days" value="{{$arr['renewal_reminder_days'] ?? ''}}" placeholder="Renewal Reminder Days" autocomplete="off" id="update_renewal_reminder_days_{{ $key }}" />
                   </div>
               </div>
               <div class="col-md-2 mt-1 INR">
                   <label for="txtPassword"><b>Amount Expected</b></label>
                   <div class="relative">
                   <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
                   <input type="text" name="amount_expected[]" class="form-control number float_format amount_expected" value="{{$arr['amount_expected'] ?? ''}}" placeholder="Amount Expected" autocomplete="off" id="update_amount_expected_{{ $key }}" />
                   </div>
               </div>
               <div class="col-md-2 mt-1 INR">
                   <label for="txtPassword"><b>Document Amount</b></label>
                   <div class="relative">
                   <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
                   <input type="text" name="document_amount[]" class="form-control number float_format document_amount" value="{{$arr['document_amount'] ?? ''}}" placeholder="Document Amount" autocomplete="off" id="update_document_amount_{{ $key }}" />
                   </div>
               </div>
               <div class="col-md-3 mt-1">
                   <label for="txtPassword"><b>Doc Upload</b>
                    @if ($arr['is_upload'] && $arr['is_upload'] == 1)
                    <a href="{{ route('download_storage_file', ['file_id' => $arr['file_id'] ]) }}" title="Download Document"><i class="fa fa-lg fa-download ml-3" aria-hidden="true"></i></a>  
                    @endif
                  </label>
                   <div class="relative">
                    <div class="d-flex">
                       <div class="custom-file upload-btn-cls mb-3">
                           <input type="file" class="custom-file-input getFileName doc_file_sec {!!(isset($arr['is_upload']) && $arr['is_upload'] == 0) ? 'required': '' !!}" name="doc_file_sec[]" id="update_doc_file_{{ $key }}" >
                           <label class="custom-file-label" for="customFile">Choose file</label>
                       </div>
                      
                    </div>
                   </div>
               </div>
                @endif
               <div class="col-md-1 mt-1 center" style="display: flex;flex-direction: column;justify-content: center;align-items: center;padding-top: 0px;">
                @if($loop->first)
            
                 @if($route_name=="security_deposit" && $arr['status'] == 2)
                 <i class="fa fa-2x fa-plus-circle add-security-doc-block ml-2"  style="color: green;"></i>
                 @elseif($route_name=="security_deposit" && $arr['status'] == 4)
                 <i class="fa fa-2x fa-plus-circle add-security-doc-block ml-2"  style="color: green;"></i>
                 @elseif($route_name=="reviewer_summary" && $arr['status'] != 2)
                 <i class="fa fa-2x fa-plus-circle add-security-doc-block ml-2"  style="color: green;"></i>
                 @endif
                 @else
                 @if($route_name=="security_deposit" && $arr['status'] != 4)
                 <i class="fa fa-2x fa-times-circle remove-security-doc-block ml-2" style="color: red;margin-top: 15%;"></i>
                 @elseif($route_name=="reviewer_summary" && $arr['status'] != 2)
                 <i class="fa fa-2x fa-times-circle remove-security-doc-block ml-2" style="color: red;margin-top: 15%;"></i>
                 @endif
                 @endif
               </div>
            </div>
            @endforeach
            @else
            <div class="row p-2 mt-1 toRemoveDiv1" style="background-color: #e9e7e7;">
                <input type="hidden" name="app_security_doc_id[]" class="form-control" value="" placeholder="Group Company" />
                <div class="col-md-2 mt-1">
                    <label for="txtPassword"><b>Pre/Post Disbursement</b></label>
                    <div class="relative">
                        <select class="form-control{{ ($route_name=="security_deposit") ? ' doc_type': '' }}" name="doc_type[]" id="doc_type_1">
                            <option value="">Select</option>
                            <option value="1">Pre</option>
                            <option value="2">Post</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 mt-1">
                     <label for="txtPassword"><b>Type of Document</b></label>
                     <select class="form-control{{ ($route_name=="security_deposit") ? ' security_doc_id': '' }}" name="security_doc_id[]" id="security_doc_id_1">
                        <option value="">Select</option>
                    </select>
                </div>
                <div class="col-{{ $mdCls }} mt-1">
                        <label for="txtPassword"><b>Description</b></label>
                        <div class="relative">
                            <textarea type="text" name="description[]" class="form-control{{ ($route_name=="security_deposit") ? ' description': '' }}" value="" placeholder="Description" autocomplete="off"></textarea>
                        </div>
                </div>      
                @if($route_name=="security_deposit")  
                <div class="col-md-2 mt-1">
                     <label for="txtPassword"><b>Document Number</b></label>
                     <div class="relative">
                        <input type="text" name="document_number[]" class="form-control document_number" value="" placeholder="Document Number" autocomplete="off" alphanumeric="true" checkDocumentNumber="true" data-msg-alphanumeric="Please enter letters and numbers only."/>
                     </div>
                </div>
                @endif
                <div class="col-md-2 mt-1">
                     <label for="txtPassword"><b>Original Due Date</b></label>
                     <div class="relative">
                             <input type="text" name="due_date[]" maxlength="20" class="form-control sc-doc-date" value="" placeholder="Original Due Date" autocomplete="off" readonly="readonly"/>
                     </div>
                </div>
                @if($route_name=="security_deposit")
                <div class="col-md-2 mt-1">
                    <label for="txtPassword"><b>Completed</b></label>
                    <div class="relative">
                        <select class="form-control completed" name="completed[]">
                            <option value="">Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
               </div>
               <div class="col-md-2 mt-1">
                        <label for="txtPassword"><b>Exception Received</b></label>
                        <div class="relative">
                            <select class="form-control exception_received" name="exception_received[]" onchange="displayExceptionFields(this.value,1);" data-previous="1">
                                <option value="">Select</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                </div>
                <div class="col-md-2 mt-1 exceptionFields_1" style="display: none;">
                        <label for="txtPassword"><b>Exception Received From</b></label>
                        <div class="relative">
                        <input type="text" name="exception_received_from[]" class="form-control exception_received_from required" value="" placeholder="Exception Received From" autocomplete="off"/>
                        </div>
                </div>
                <div class="col-md-2 mt-1 exceptionFields_1" style="display: none;">
                        <label for="txtPassword"><b>Exception Received Date</b></label>
                        <div class="relative">
                        <input type="text" name="exception_received_date[]" class="form-control sc-doc-date-r exception_received_date required" value="" placeholder="Exception Received Date" autocomplete="off" readonly="readonly"/>
                        </div>
                </div>
                <div class="col-md-2 mt-1 exceptionFields_1" style="display: none;">
                    <label for="txtPassword"><b>Exception Remark</b></label>
                    <div class="relative">
                    <input type="text" name="exception_remark[]" class="form-control exception_remark required" value="" placeholder="Exception Remark" autocomplete="off"/>
                    </div>
                </div>
                <div class="col-md-2 mt-1 exceptionFields_1" style="display: none;">
                    <label for="txtPassword"><b>Extended Due Date</b></label>
                    <div class="relative">
                    <input type="text" name="extended_due_date[]" class="form-control extended_due_date sc-doc-date required" value="" placeholder="Extended Due Date" autocomplete="off" id="exception_remark_1" readonly="readonly"/>
                    </div>
                </div>
                <div class="col-md-2 mt-1">
                    <label for="txtPassword"><b>Maturity Date</b></label>
                    <div class="relative">
                    <input type="text" name="maturity_date[]" id="maturity_date_1" class="form-control sc-doc-date maturity_date" value="" placeholder="Maturity Date" autocomplete="off" readonly="readonly"/>
                    </div>
                </div>
                <div class="col-md-2 mt-1">
                    <label for="txtPassword"><b>Renewal Reminder Days</b></label>
                    <div class="relative">
                    <input type="text" name="renewal_reminder_days[]" class="form-control digits renewal_reminder_days" id="renewal_reminder_days_1" value="" placeholder="Renewal Reminder Days" autocomplete="off"  min="0" max="365"/>
                    </div>
                </div>
                <div class="col-md-2 mt-1 INR">
                    <label for="txtPassword"><b>Amount Expected</b></label>
                    <div class="relative">
                    <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
                    <input type="text" name="amount_expected[]" class="form-control number float_format amount_expected" value="" placeholder="Amount Expected" autocomplete="off"/>
                    </div>
                </div>
                <div class="col-md-2 mt-1 INR">
                    <label for="txtPassword"><b>Document Amount</b></label>
                    <div class="relative">
                    <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
                    <input type="text" name="document_amount[]" class="form-control number float_format document_amount" value="" placeholder="Document Amount" autocomplete="off"/>
                    </div>
                </div>
                <div class="col-md-3 mt-1">
                    <label for="txtPassword"><b>Doc Upload</b></label>
                    <div class="relative">
                        <div class="d-flex">
                        <div class="custom-file upload-btn-cls mb-3">
                            <input type="file" class="custom-file-input getFileName doc_file_sec required" name="doc_file_sec[]">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-1 mt-1 center" style="display: flex;flex-direction: column;justify-content: center;align-items: center;padding-top: 15px;">
                    <i class="fa fa-2x fa-plus-circle add-security-doc-block ml-2"  style="color: green;margin-top: 15%;"></i>
                </div>
            </div>
        @endif
    </div>
    <input type="hidden" name="{{$route_name}}" value="1">
    <div class="col-md-12 mt-1 mb-2" style="padding: 5px;">
        
    </div>    
</div> 
@endif