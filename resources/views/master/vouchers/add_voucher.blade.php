@extends('layouts.backend.admin_popup_layout')
@section('content')
 @if(Session::has('error'))
        <div class=" alert-danger alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
            {{ Session::get('error') }}
        </div>
@endif
 <div class="modal-body text-left">
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

</script>
@endsection