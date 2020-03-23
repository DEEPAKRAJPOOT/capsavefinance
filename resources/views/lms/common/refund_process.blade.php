@extends('layouts.backend.admin_popup_layout')

@section('content')


<div class="modal-body text-left">          
            <div class="row">                
                <div class="col-12">

                    @if (Session::has('error_code') && Session::get('error_code') == 'no_offer_found')
                    <label class='error'>You cannot move this application to next stage as limit assessment is not done.</label><br>
                    @endif
                    
                    @if ($viewFlag != '1' && $currStatus == config('lms.REQUEST_STATUS.APPROVED'))                    
                    <h5>Are you sure to process the Refund?</h5>
                    @endif                    
                                   
                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
                        <table id="interestRefundList" class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Trans Date</th>
                                    <th>Value Date</th>
                                    <th>Tran Type</th>
                                    <th>Invoice No</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                </tr>
                            </thead>
                            @foreach($refundData['TRANSACTIONS'] as $repay)
                            <tr role="row" >
                                <td>{{ date('d-m-Y',strtotime($repay['TRANS_DATE'])) }}</td>
                                <td>{{ date('d-M-Y',strtotime($repay['VALUE_DATE'])) }}</td>
                                <td>{{ $repay['TRANS_TYPE'] }}</td>
                                <td>{{ $repay['INV_NO'] }}</td>
                                <td>{{ $repay['DEBIT'] }}</td>
                                <td>{{ $repay['CREDIT'] }}</td>
                            </tr>
                            @endforeach
                            <!-- blank -->
                            <tr role="row" >
                                <td colspan="6" style="min-height: 15px"></td>
                            </tr>

                            <tr role="row" >
                                <td colspan="4">Total Factored</td>
                                <td>{{ number_format($refundData['TOTAL_FACTORED']->amount,2) }}</td>
                                <td></td>
                            </tr>
                            <tr role="row">
                                <td style="font-weight:bold" colspan="4"><b>Non Factored</b></td>
                                <td>{{ number_format($refundData['NON_FACTORED']->amount,2) }}</td>
                                <td></td>
                            </tr>

                            <!-- blank -->
                            <tr role="row" >
                                <td colspan="6" style="min-height: 15px"></td>
                            </tr>

                            <tr role="row" >
                                <td colspan="4">Total amt for Margin</td>
                                <td>{{ number_format($refundData['TOTAL_AMT_FOR_MARGIN']->amount,2) }}</td>
                                <td></td>
                            </tr>

                            @foreach($refundData['MARGIN'] as $margin)                            
                            <tr role="row" >
                                <td colspan="3">% Margin</td>
                                <td>@if($margin->variable_value > 0 ){{ number_format($margin->variable_value,2) }} % @endif</td>
                                <td>{{ number_format($margin->amount,2) }}</td>
                                <td></td>
                            </tr>
                            @endforeach
                            
                            <tr role="row" >
                                <td colspan="4">Overdue Interest</td>
                                <td>{{ number_format($refundData['OVERDUE_INTEREST']->amount,2) }}</td>
                                <td></td>
                            </tr>
                            <tr role="row" >
                                <td colspan="4" style="font-weight:bold"><b>Margin Released</b></td>
                                <td>{{ number_format($refundData['MARGIN_RELEASED']->amount,2) }}</td>
                                <td></td>
                            </tr>
                            
                            <tr role="row" >
                                <td colspan="6" style="min-height: 15px"></td>
                            </tr>


                            <tr role="row" >
                                <td colspan="4" style="font-weight:bold"><b>Interest Refund</b></td>
                                <td>{{ number_format($refundData['INTEREST_REFUND']->amount,2) }}</td>
                                <td></td>
                            </tr>

                            <tr role="row" >
                                <td colspan="4" style="font-weight:bold; font-size: 15px"><b>Total Refundable Amount</b></td>
                                <td>{{ number_format($refundData['TOTAL_REFUNDABLE_AMT']->amount,2) }}</td>
                                <td></td>
                            </tr>
                            <tbody>

                            </tbody>
                        </table>
                    </div>                    
               </div>
                               
            </div>
    
            @if ($viewFlag != '1' && $currStatus != config('lms.REQUEST_STATUS.PROCESSED'))
            <div class="row">
                <div class="col-12">
                
                    {!!
                     Form::open(
                     array(
                     'method' => 'post',
                     'route' => 'lms_process_refund',
                     'id' => 'frmProcessRefund',
                     )
                     ) 
                     !!}    

                    @if ($currStatus == config('lms.REQUEST_STATUS.APPROVED')) 
                    {!! Form::hidden('process', 1) !!}
                    @endif



                    @if (count($statusList) > 0 && $currStatus != config('lms.REQUEST_STATUS.APPROVED'))
                    <div class="form-group">
                        {!! Form::label('status', 'Select Status', ['class' => 'control-label'] )  !!}                            
                        {!!  Form::select('status', $statusList, '', ['class' => 'form-control' ]) !!}                            
                    </div>
                    @endif

                    <div class="form-group">
                       <label for="txtCreditPeriod">Comment
                       <span class="mandatory">*</span>
                       </label>
                       <textarea type="text" name="comment" value="" class="form-control" tabindex="1" placeholder="Add Comment" required=""></textarea>
                    </div>

                    {!! Form::hidden('req_id', $reqId) !!}

                    <button type="submit" class="btn btn-success btn-sm btn-move-next-stage">Submit</button> &nbsp;
                    <button id="close_btn" type="button" class="btn btn-secondary btn-sm">Cancel</button>   


                    {!!
                    Form::close()
                    !!}
                
                </div>
            </div>
            @endif        
        </div>


@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script>
   
var messages = {
    is_accept: "{{ Session::get('is_accept') }}",    
    error_code : "{{ Session::has('error_code') }}",    
 };
     $(document).ready(function(){        
        var targetModel = 'lms_view_process_refund';
        var parent =  window.parent;
        
        $('.btn-move-next-stage').click(function() {            
            if ($('#frmSaveReqStatus').valid()) {
                parent.$('.isloader').show();
            }
        });
        
        if (messages.error_code) {
            parent.$('.isloader').hide();
        }
        
        if(messages.is_accept == 1){
           parent.jQuery("#"+targetModel).modal('hide');  
           parent.oTable.draw();
           parent.$('.isloader').hide();           
        }

        $('#close_btn').click(function() {
            //alert('targetModel ' + targetModel);
            parent.$('#'+targetModel).modal('hide');
        });
        
        $('#frmProcessRefund').validate({
            rules: {
                comment: {
                    required: true
                }
            },
            messages: {
            }
        });
            
    })
    
    
    </script>
@endsection
