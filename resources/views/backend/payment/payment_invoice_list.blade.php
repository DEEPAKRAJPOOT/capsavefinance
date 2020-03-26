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
                                $balanceAmount = $repayment->amount;
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
                                            if($repay->entry_type=='1')
                                                $balanceAmount += $repay->amount;
                                            elseif($repay->entry_type=='0')
                                                $balanceAmount -= $repay->amount;
                                            
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
                                @endforeach

                                <!-- blank -->
                                <tr role="row" >
                                    <td colspan="8" style="min-height: 15px"></td>
                                </tr>


                                <tr role="row" >
                                    <td colspan="6">Total Factored</td>
                                    <td>{{ number_format($repayment->amount,2) }}</td>
                                </tr>
                                <tr role="row">
                                    <td colspan="6">Non Factored</td>
                                    <td>{{ number_format($nonFactoredAmount,2) }}</td>
                                </tr>
                                <tr role="row" >
                                    <td colspan="6">Overdue Interest</td>
                                    <td>{{ number_format($interestOverdue,2) }}</td>
                                </tr>
                                <tr role="row" >
                                    <td colspan="6"> Interest Refund</td>
                                    <td>{{ number_format($interestRefund,2) }}</td>
                                </tr>
                                <tr role="row" >
                                    <td colspan="6">Margin Released</td>
                                    <td>{{ number_format($marginTotal,2) }}</td>
                                </tr>
                                <tr role="row" >
                                    <td colspan="6" style="font-weight:bold; font-size: 15px"><b>Total Refundable Amount</b></td>
                                    <td>{{  number_format($refundableAmount,2) }}</td>
                                </tr>
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
                    {!! Form::hidden('total_refund_amount', $refundableAmount) !!}
            <div class="row">
                <div class="form-group col-md-12 text-right">
                    @if($refundableAmount > 0)
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