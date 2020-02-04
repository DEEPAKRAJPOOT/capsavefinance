@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')



<div class="content-wrapper">

    <div class="row grid-margin mt-3">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-fields">
                        <div class="active" id="details">
                            <div class="form-sections">
                                <form action="{{route('save_payment')}}" method="post">
                                    <div class="row">
                                    @csrf
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Payment Method <span class="error_message_label">*</span></label>
                                              @php 
                                               $get = Config::get('payment.type');
                                              @endphp
                                                
                                                <select class="form-control amountRepay" name="payment_type" id="payment_type">
                                                    <option value=""> Select Payment Type </option>
                                                     @foreach($get as $key=>$val)
                                                    <option value="{{$key}}"> {{$val}}</option>
                                                     @endforeach  
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                    <label for="txtCreditPeriod">Customer id <span class="error_message_label">*</span> </label>
                                         <select class="form-control getCustomer" name="customer_id">
                                               <option> Please Select</option>
                                                    @foreach($customer as $row)
                                                    <option value="{{$row->user_id}}">{{$row->user->f_name}}/{{$row->customer_id}}</option>
                                                 @endforeach   
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Bank Name <span class="error_message_label">*</span> </label>
                                                
                                                <select class="form-control" name="bank_name">
                                               <option> Select</option>
                                                    @foreach($bank as $row)
                                                    <option value="{{$row->id}}">{{$row->bank_name}}</option>
                                                 @endforeach   
                                                </select>
                                            </div>
                                        </div> 
                                        <div class="col-md-4">
                                            <div class="form-group ">
                                                <label for="txtCreditPeriod">Date of Payment <span class="error_message_label">*</span> </label>

                                                <input type="text" name="date_of_payment" readonly="readonly" class="form-control datepicker-dis-fdate">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group INR">
                                                <label for="txtCreditPeriod">Amount <span class="error_message_label">*</span> </label>
                                                <a href="javascript:void(0);" class="verify-owner-no" style="top:42px;"><i class="fa fa-inr" aria-hidden="true"></i></a>  
                                                <input type="text" id="amount" name="amount" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group ">
                                                <label for="txtCreditPeriod">Payment Refrence No. <span class="error_message_label">*</span> </label>

                                                <input type="text" name="refrence_no" class="form-control">
                                            </div>
                                        </div>


                                        <div class="col-md-8">
                                            <div class="form-group ">
                                                <label for="txtCreditPeriod">Description <span class="error_message_label">*</span> </label>

                                                <textarea name="description" class="form-control" rows="3" cols="3"></textarea>
                                            </div>
                                        </div>
                                         <div class="col-md-4">
                                            <div class="form-group">
                                                <span id="appendInput"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="text-right ">
                                                <input  type="reset" id="pre3" class="btn btn-secondary btn-sm" value="Cancel">
                                                <input type="submit" class="btn btn-primary ml-2 btn-sm"  value="Submit">
                                            </div>
                                        </div>

                                    </div> 
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <h5>Are you sure you want to submit the programe?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('jscript')
<script>
    var messages = {
      
        token: "{{ csrf_token() }}",
    };

    $(document).ready(function () {
        document.getElementById('amount').addEventListener('input', event =>
            event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
    });
;

    $(document).on('change', '#payment_type', function () {
        $('#appendInput').empty();
        var status = $(this).val();
      
            if (status == 1)
            {
                $('#appendInput').append('<label for="repaid_amount" class="form-control-label"><span class="payment_text">Customer Virtual Account No.</span></label><input type="text" class="form-control amountRepay" id="utr_no" name="utr_no" value=""><span id="utr_no_msg" class="error"></span>');

            } else if (status == 2)
            {
                $('#appendInput').append('<label for="repaid_amount" class="form-control-label"><span class="payment_text">Cheque Number</span></label><input type="text" class="form-control amountRepay" id="utr_no" name="utr_no" value=""><span id="utr_no_msg" class="error"></span>');

            } else if (status == 3)
            {
                $('#appendInput').append('<label for="repaid_amount" class="form-control-label"><span class="payment_text">UNR Number</span></label><input type="text" class="form-control amountRepay" id="utr_no" name="utr_no" value=""><span id="utr_no_msg" class="error"></span>');

            }
    });


</script>
@endsection
