@extends('layouts.backend.admin_popup_layout')
@section('content')
 <div class="modal-body text-left">
      @if(Session::has('error'))
        <div class=" alert-danger alert" style="font-size: 12px;padding: 0.4rem 0.3rem" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
            {{ Session::get('error') }}
        </div>
      @endif
     <form id="voucherForm" name="voucherForm" method="POST" action="{{route('save_voucher')}}">
     @csrf
     <div class="row">
        <div class="form-group col-md-6">
          <label for="chrg_name">Transaction Type</label>
          <select class="form-control" id="trans_type_id" name="trans_type_id">
            <option value="">Select Type</option>
            @foreach($transType as $txn)
            <option value="{{$txn->id}}">{{$txn->trans_name}}</option>
            @endforeach
         </select>
        </div>
    </div>
    <div class="row">
         <div class="form-group col-md-6 chargeTypeCal">
          <label for="chrg_name">Voucher Name</label>
          <input type="text" class="form-control" id="voucher_name" name="voucher_name" placeholder="Enter Voucher Name">
        </div>  
    </div>
    <div class="row">
        <div class="form-group col-sm-6 text-right">
            <button type="submit" class="btn btn-primary btn-sm" name="submit" value="submit" id="submit">Submit</button>
        </div>
    </div>
   </form>

</div>
@endsection
@section('jscript')
<script type="text/javascript">
$(document).ready(function () {
    var messages={
        unique_voucher_url:"{{ route('check_unique_voucher_url') }}",
        token: "{{ csrf_token() }}"
    }

    $.validator.addMethod("uniqueVoucher",
        function(value, element) {
            var result = true;
            var data = {voucher_name : value, _token: messages.token};
            $.ajax({
                type:"POST",
                async: false,
                url: messages.unique_voucher_url, // script to validate in server side
                data: data,
                success: function(data) {                        
                    result = (data.status == 1) ? false : true;
                }
            });                
            return result;                
        },'Voucher name is already exists'
    );    
  // validation
  $('#voucherForm').validate({ // initialize the plugin
      rules: {
          'trans_type_id': {
              required: true,
          },
          'voucher_name': {
              required: true,
              uniqueVoucher: true
          }
      },
      messages: {
          'trans_type_id': {
              required: "Please select transaction type",
          },
          'voucher_name': {
              required: "Please enter voucher Name",
          }
      }
  });
  
});
</script>
@endsection