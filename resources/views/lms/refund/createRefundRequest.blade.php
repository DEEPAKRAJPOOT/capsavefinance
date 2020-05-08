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
					@include('lms.refund.common.payment_advise')
					<div id="interestRefundList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
				</div>
            </div>
            {!! Form::open( array( 'method' => 'post', 'route' => 'lms_refund_request_create', 'id' => 'frmRequestRefund') ) !!}        
            {!! Form::hidden('paymentId', $paymentId) !!}
            {!! Form::hidden('total_refund_amount', $refundableAmount) !!}
            <div class="row">
                <div class="form-group col-md-12 text-right">
                    @if($refundableAmount > 0)
                    <input type="submit" class="btn btn-success btn-sm" name="add_charge" id="add_charge" value="Submit">
                    @endif
                    <button id="close_btn" type="button" class="btn btn-secondary btn-sm">Cancel</button>   
                </div>
            </div>
            {!! Form::close() !!} 
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