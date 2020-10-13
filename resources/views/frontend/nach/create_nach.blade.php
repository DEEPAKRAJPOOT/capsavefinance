@extends('layouts.popup_layout')
@section('content')
<form style="width: 100%" method="POST" action="{{ Route('front_add_nach_detail') }}" enctype="multipart/form-data" target="_top">
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
