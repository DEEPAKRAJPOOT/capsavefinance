@extends('layouts.backend.admin_popup_layout')
@section('additional_css')
@section('content')
<div class="col-12">
	<div class="overflow">
		<div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
			<div class="row">
				<div class="col-sm-12">                                 
					@include('lms.refund.common.payment_advise')
					<div id="interestRefundList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
				</div>
            </div>
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