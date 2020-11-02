@extends('layouts.popup_layout')
@section('content')
<form style="width: 100%" method="POST" action="{{ Route('front_add_nach_detail') }}" enctype="multipart/form-data" target="_top" id="submitForm">
    @csrf
    <div class="modal-body text-left">
        <div class="row">
            <div class="col-12">
               <div class="form-group">
                  <label for="bank">Select Bank</label>
                  <select class="form-control" name="bank_account_id">
                     <option selected diabled value=''>Select Bank</option>
                        @foreach($userBankData as $key => $value)
                          <option value="{{ $value->bank_account_id }}">{{ $value->acc_no }} ( {{ $value->acc_name }} )</option>
                        @endforeach
                  </select>
               </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-right btn-sm" id="savedocument" >Submit</button>  
    </div>
</form>
 
@endsection

@section('jscript')

<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript"> 
var messages = {
  token: "{{ csrf_token() }}",
  backend_ajax_nach_user: "{{ URL::route('backend_ajax_nach_user') }}",
  backend_ajax_nach_user_bank: "{{ URL::route('backend_ajax_nach_user_bank') }}",
      
   };
 $(document).ready(function () {
  /////// jquery validate on submit button/////////////////////
  $('#submitForm').validate({ // initialize the plugin
      
    rules: {
      'bank_account_id': {
        required: true,
      }
    },
    messages: {
      'bank_account_id': {
        required: "Please select Bank",
      }
    }
  });

  $('#submitForm').validate();

  $("#savedocument").click(function(){
    if($('#submitForm').valid()){
      $('form#submitForm').submit();
      $("#savedocument").attr("disabled","disabled");
    }  
  });        
});
  </script>
@endsection