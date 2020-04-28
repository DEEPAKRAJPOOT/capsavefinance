@extends('layouts.backend.admin-layout')
@section('content')
<div class="content-wrapper">
   <section class="content-header">
      <div class="header-icon">
         <i class="fa  fa-list"></i>
      </div>
      <div class="header-title">
         <h3>Create User Invoice</h3>
         <small>Create User Invoice</small>
         <ol class="breadcrumb">
            <li style="color:#374767;"> Home </li>
            <li style="color:#374767;">View User Invoice</li>
            <li class="active">Create User Invoice</li>
         </ol>
      </div>
   </section>
   <div class="row grid-margin mt-3">
      <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
         <div class="card">
            <div class="card-body">
                <form action="{{route('save_user_invoice',  ['user_id' => $user_id])}}" method="post" id="invoice_form">
                @csrf
                  <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="float-left">Invoice Tag</label>
                        <select class="form-control form-control-sm" id="invoice_type" name="invoice_type">
                            <option value="" disabled selected>Select Invoice Type</option>
                            <option value="I">Interest</option>
                            <option value="C">Charges</option>
                        </select>
                    </div>
                  </div>
                  <div class=" form-fields mb-4">
                      <div class="row">
                          <div class="col-md-6 d-flex">
                              <div class="col-md-12 data p-0">
                                  <div class="">
                                      <h2 class="sub-title bg">Billing Address  </h2>
                                      <div class="pl-4 pr-4 pb-4 pt-2">
                                          <div class="row">
                                              <div class="col-md-12">
                                                  <div class="form-group">
                                                      <label class="m-0">PAN Number: <span>{{$billingDetails['pan_no']}}</span></label>
                                                  </div>
                                              </div>
                                              <div class="col-md-12">
                                                  <div class="form-group">
                                                      <label class="m-0">GSTIN:<span>{{$billingDetails['gstin_no']}}</span></label>
                                                  </div>
                                              </div>
                                              <div class="col-md-12">
                                                  <div class="form-group m-0">
                                                      <label class="m-0">Address:<span>{{$billingDetails['address']}}</span></label>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-6 d-flex">
                              <div class="col-md-12 data p-0">
                                  <div class="">
                                      <h2 class="sub-title bg">Original Of Recipient  </h2>
                                      <div class="pl-4 pr-4 pb-4 pt-2">
                                          <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="txtEmail">Invoice No
                                                    </label>
                                                    <div>
                                                        <ul class="mh-line">
                                                            <li>{{$origin_of_recipient['state_code']}}/ </li>
                                                            <li><input type="text" id="invoice_user_code" class="form-control" tabindex="3" placeholder="" maxlength="3" /></li>
                                                            <li>/{{$origin_of_recipient['financial_year']}}/{{$origin_of_recipient['rand_4_no']}}</li>
                                                        </ul>
                                                    </div> 
                                                </div>
                                            </div>
                                            <input type="hidden" name="state_code" value="{{$origin_of_recipient['state_code']}}">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="txtEmail">Invoice Date
                                                    </label>
                                                    <input type="text" name="invoice_date" id="invoice_date" class="form-control" placeholder="dd/mm/yyyy" readonly maxlength="10" />
                                                </div>
                                            </div>
                                            <input type="hidden" name="reference_no" value="{{$origin_of_recipient['reference_no']}}">
                                            <input type="hidden" name="invoice_no" id="invoice_no" value="{{$origin_of_recipient['state_code'] . '/' . $origin_of_recipient['financial_year'] . '/' . $origin_of_recipient['rand_4_no']}}">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="m-0">Reference No: <span>#{{$origin_of_recipient['reference_no']}}</span></label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group m-0">
                                                    <label class="m-0">Place of Supply: <span>{{$billingDetails['state_name']}}</span>
                                                      <input type="hidden" name="place_of_supply" value="{{$origin_of_recipient['state_name']}}"></label>
                                                </div>
                                            </div>
                                        </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                          <div class="pdf-responsive">
                              <table border="0" cellspacing="0" cellpadding="0" id="table">
                                  <thead>
                                       <tr>
                                        <td rowspan="2" bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong><input type="checkbox" id="checkall"></strong></span>
                                        </td>
                                        <td rowspan="2" bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>Sr No</strong></span>
                                        </td>
                                        <td rowspan="2" bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>Description</strong></span>
                                        </td>
                                        <td rowspan="2" bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>SAC</strong></span>
                                        </td>
                                        <td rowspan="2" bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>Base Amount (Rs)</strong></span>
                                        </td>
                                        <td colspan="2" bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>SGST/UTGST</strong></span>
                                        </td>
                                        <td colspan="2" bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>CGST</strong></span>
                                        </td>
                                        <td colspan="2" bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>IGST</strong></span>
                                        </td>
                                        <td rowspan="2" bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>Total Rental</strong></span>
                                        </td>
                                     </tr>
                                     <tr>
                                        <td bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>Rate (%)</strong></span>
                                        </td>
                                        <td bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>Amount (Rs)</strong></span>
                                        </td>
                                        <td bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>Rate (%)</strong></span>
                                        </td>
                                        <td bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>Amount (Rs)</strong></span>
                                        </td>
                                        <td bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>Rate (%)</strong></span>
                                        </td>
                                        <td bgcolor="#f2f2f2">
                                           <span style="font-size: small;"><strong>Amount (Rs)</strong></span>
                                        </td>
                                     </tr>
                                  </thead>
                                  <tbody id="table_tbody">
                                    <tr><td colspan="12">No records found</td></tr>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
                  <div class="form-group mb-0 mt-1 d-flex justify-content-between">
                      <button type="button" class="btn btn-default" id="preview_invoice">Preview</button>
                      <button type="submit" class="btn btn-primary" id="save_invoice">Save</button>
                  </div>
                </form>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body" id="response"></div>
      </div>
   </div>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
   var message = {
       token: "{{ csrf_token() }}",
       user_id: "{{ $user_id }}",
       state_name: "{{ $origin_of_recipient['state_name'] }}",
       get_app_gstin_url: "{{route('get_app_gstin')}}",
       invoice_state_code : "{{$origin_of_recipient['state_code']}}/",
       invoice_fin : "/{{$origin_of_recipient['financial_year'] . '/' . $origin_of_recipient['rand_4_no']}}",
   }
   $(document).ready(function(){
       $("#invoice_date").datetimepicker({
           setDate : new Date(),
           format: 'dd/mm/yyyy',
           autoclose: true,
           minView : 2,
       });
   });
  $(document).on('keyup', '#invoice_user_code', function(e) {
    var invoice_user_code = $(this).val();
    fullInvoiceNo = message.invoice_state_code + invoice_user_code + message.invoice_fin;
    $('#invoice_no').val(fullInvoiceNo);
  })
  $(document).on('click', '#preview_invoice', function(e) {
    e.preventDefault();
    if (validate_form() != true) {
      return false;
    }
    let myForm = $('#invoice_form')[0];
    let formData = new FormData(myForm);
    formData.append('_token', message.token);
    formData.append('state_name', message.state_name);
    $.ajax({
      type:'POST',
      url : "{{route('preview_user_invoice', ['user_id'=> $user_id])}}",
      data: formData,
      cache : false,
      contentType : false,
      processData : false,
      dataType    : 'json',
      success: function (res) {
        if (res.status == 1) {
          $('#response').html(atob(res.view));
          $('#exampleModalCenter').modal();
        }else{
          alert(res.message);
        }
      }
    })
  })
  $(document).on('click', '#save_invoice', function(e) {
    return validate_form();
  })
  function validate_form() {
    $('#invoice_type_error').remove();
    $('#invoice_user_code_error').remove();
    $('#invoice_date_error').remove();
    let invoice_type = $('#invoice_type').val();
    if (!invoice_type) {
      $('#invoice_type').after('<span id="invoice_type_error" class="error">Please select invoice type</span>');
      $('#invoice_type').focus();
      return false;
    }
    let invoice_user_code = $('#invoice_user_code').val();
    if (!invoice_user_code) {
      $('#invoice_user_code').css({'border':'1px solid #ff1111'});
      $('#invoice_user_code').focus();
      return false;
    }
    let invoice_date = $('#invoice_date').val();
    if (!invoice_date) {
      $('#invoice_date').after('<span id="invoice_date_error" class="error">Please select invoice Date</span>');
      $('#invoice_date').focus();
      return false;
    }
    if($('.trans_check:checked').length == 0){
      alert('Please select a transaction to preview of invoice.');
      return false;
    }
    return true;
  }

  $(document).on('change', '#invoice_type', function(argument) {
    $('#invoice_type_error').remove();
    let invoice_type = $(this).val();
    if (!invoice_type) {
      $('#invoice_type').after('<span id="invoice_type_error" class="error">Please select invoice type</span>');
      $('#invoice_type').focus();
      return false;
    }
    let data = {'invoice_type' : invoice_type};
    data['_token'] =  message.token;
    $.ajax({
      type:'POST',
      url : "{{route('get_invoice_transaction', ['user_id'=> $user_id])}}",
      data: data,
      cache : false,
      dataType    : 'json',
      success: function (res) {
        $('#checkall').prop('checked', false);
        if (res.status == 1) {
          $('#table_tbody').html(atob(res.view));
        }else{
          $('#table_tbody').html('<tr><td style="border: 1px solid #ddd;padding: 5px;" colspan="12">No records found</td></tr>');
          alert(res.message);
        }
      }
    })
  })

  $(document).on('click', '#checkall', function(argument) {
    if ($(this).is(':checked')) {
        $('.trans_check[type="checkbox"]').prop('checked', true);
    }else{
       $('.trans_check[type="checkbox"]').prop('checked', false);
    }
  })
</script>
@endsection