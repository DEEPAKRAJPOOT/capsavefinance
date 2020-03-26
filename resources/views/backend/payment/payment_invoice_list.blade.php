@extends('layouts.backend.admin_popup_layout')
@section('additional_css')
@section('content')



<div class="col-12">
	<div class="overflow">
		<div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
			<div class="row">
				<div class="col-sm-12">
                   @if (Session::has('error_code') && Session::get('error_code') == 'create_refund')
                   <label class='error'>Unable to initiate refund request.</label><br>
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
                                    <th>Balance</th>
								</tr>
                            </thead>
                            @php 
                                $overdueInterest = 0;
                                $interestRefund = 0;
                                $totalMarginAmount = 0;
                                $nonFactoredAmount = 0;
                                $balanceAmount = 0;
                            @endphp
                                    <tr>
                                        <td>{{date('d-m-Y',strtotime($repayment->trans_date))}}</td>
                                        <td>{{date('d-M-Y',strtotime($repayment->created_at))}}</td>
                                        <td>
                                            @if($repayment->trans_detail->chrg_master_id!='0')
                                                {{$repayment->trans_detail->charge->chrg_name}}
                                            @else
                                                {{$repayment->trans_detail->trans_name}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($repayment->disburse && $repayment->disburse->invoice  {{--  && $repayment->trans_type == config('lms.TRANS_TYPE.INVOICE_KNOCKED_OFF' --}} ))
                                                {{$repayment->disburse->invoice->invoice_no}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($repayment->entry_type=='0')
                                                {{ number_format($repayment->amount,2) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($repayment->entry_type=='1')
                                                {{ number_format($repayment->amount,2) }}
                                            @endif
                                        </td>
                                        <td> {{number_format($repayment->amount,2)}} </td>
                                    </tr>
                                @foreach($repaymentTrails as $repay)
                                    @php 
                                            @if($repay->entry_type=='0')
                                                $balanceAmount += $repay->amount;
                                            @elseif($repay->entry_type=='1')
                                                $balanceAmount -= $repay->amount;
                                            @endif
                                    @endphp
                                    <tr role="row" >
                                        <td> {{date('d-m-Y',strtotime($repay->trans_date))}}</td>
                                        <td>{{date('d-M-Y',strtotime($repay->created_at))}}</td>
                                        <td>
                                            @if($repay->trans_detail->chrg_master_id!='0')
                                                {{$repay->trans_detail->charge->chrg_name}}
                                            @else
                                                {{$repay->trans_detail->trans_name}}
                                            @endif
                                        </td>
                                        <td>
                                            {{-- @if($repay->disburse && $repay->disburse->invoice && $repay->trans_type == config('lms.TRANS_TYPE.INVOICE_KNOCKED_OFF')) --}}
                                                {{$repay->disburse->invoice->invoice_no}}
                                            {{-- @endif --}}
                                        </td>
                                        <td>
                                            @if($repay->entry_type=='0')
                                                {{ number_format($repay->amount,2) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($repay->entry_type=='1')
                                                {{ number_format($repay->amount,2) }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ number_format($balanceAmount,2) }}
                                        </td>
                                    </tr>

                                    @php

                                    if($repay->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                                        $overdueInterest += $repay->amount;
                                    }

                                    if($repay->trans_type == config('lms.TRANS_TYPE.INTEREST_REFUND')){
                                        $interestRefund += $repay->amount;
                                    }
                                    @endphp
                                @endforeach

{{--
                                <!-- blank -->
                                <tr role="row" >
                                    <td colspan="6" style="min-height: 15px"></td>
                                </tr>


                                <tr role="row" >
                                    <td colspan="4">Total Factored</td>
                                    <td>{{ $repayment->amount }}</td>
                                    <td></td>
                                </tr>
                                <tr role="row">
                                    <td style="font-weight:bold" colspan="4"><b>Non Factored</b></td>
                                    <td>{{ number_format($nonFactoredAmount,2) }}</td>
                                    <td></td>
                                </tr>

    
    

                                <!-- blank -->
                                <tr role="row" >
                                    <td colspan="6" style="min-height: 15px"></td>
                                </tr>

                                <tr role="row" >
                                    <td colspan="4">Total amt for Margin</td>
                                    <td>{{ number_format($amountForMargin,2) }}</td>
                                    <td></td>
                                </tr>
                                    
                              

                                @foreach($marginAmountData as $margin)
                                <tr role="row" >
                                    <td colspan="3">% Margin</td>
                                    <td>@if($margin['margin'] >0 ){{ $margin['margin'] }} % @endif</td>
                                    <td>{{ number_format($margin['margin_amount'],2) }}</td>
                                    <td></td>
                                    @php 
                                        $totalMarginAmount += $margin['margin_amount'];
                                    @endphp
                                </tr>
                                @endforeach
                                <tr role="row" >
                                    <td colspan="4">Overdue Interest</td>
                                    <td>{{ number_format($overdueInterest,2) }}</td>
                                    <td></td>
                                </tr>

                                @php  
                                    $totalMarginAmount -= $overdueInterest;
                                @endphp
                                <tr role="row" >
                                    <td colspan="4" style="font-weight:bold"><b>Margin Released</b></td>
                                    <td>@if($totalMarginAmount>0) {{ number_format($totalMarginAmount,2) }} @else 0.00 @endif </td>
                                    <td></td>
                                </tr>

                                <tr role="row" >
                                    <td colspan="6" style="min-height: 15px"></td>
                                </tr>


                                <tr role="row" >
                                    <td colspan="4" style="font-weight:bold"><b>Interest Refund</b></td>
                                    <td>{{ number_format($interestRefund,2) }}</td>
                                    <td></td>
                                </tr>
                                @php 
                                    $totalMarginAmount += $interestRefund;
                                @endphp
                                <tr role="row" >
                                    <td colspan="4" style="font-weight:bold; font-size: 15px"><b>Total Refundable Amount</b></td>
                                    <td>{{ $totalMarginAmount }}</td>
                                    <td></td>
                                </tr>

--}}
							<tbody>

							</tbody>
						</table>
					</div>
					<div id="interestRefundList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
				</div>
            </div>
                   {!!
                    Form::open(
                    array(
                    'method' => 'post',
                    'route' => 'create_payment_refund',
                    'id' => 'frmRequestRefund',
                    )
                    ) 
                    !!}        
                    
                    {!! Form::hidden('trans_id', $transId) !!}
                    {!! Form::hidden('total_refund_amount', $totalMarginAmount) !!}
            <div class="row">
                <div class="form-group col-md-12 text-right">
                    @if($totalMarginAmount > 0)
                    <input type="submit" class="btn btn-success btn-sm" name="add_charge" id="add_charge" value="Submit">
                    @endif
                    <button id="close_btn" type="button" class="btn btn-secondary btn-sm">Cancel</button>   
                </div>
            </div>
                {!!
                Form::close()
                !!} 
		</div>
	</div>
</div>





@endsection

@section('jscript')
<script>
   
    var messages = {
        is_accept: "{{ Session::get('is_accept') }}",    
        error_code : "{{ Session::has('error_code') }}",
    };
    
    $(document).ready(function(){
        var targetModel = 'paymentRefundInvoice';
        var parent =  window.parent;                
        
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
            
    })
    
    
    </script>
@endsection