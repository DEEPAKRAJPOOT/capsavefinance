<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title></title>
 <link rel="shortcut icon" href="{{url('backend/assets/images/favicon.png')}}" />
    <!--<link rel="stylesheet" href="fonts/font-awesome/font-awesome.min.css" />-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{url('backend/assets/css/perfect-scrollbar.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/jsgrid.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/jsgrid-theme.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/uploadfile.css')}}" >
    <link rel="stylesheet" href="{{url('backend/assets/css/data-table.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/plugins/datatables/css/datatables.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/style.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/custom.css')}}" />
    <link rel="stylesheet" href="{{ url('common/js/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />

        <style>
            @import url("https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,700,900");
            body{margin:0; padding: 0;}
            td, th{padding: 5px;}
            .bld {  font-weight: bold;}
        </style>
    </head>
    <body>
        @php
        $sum = 0 ;
        if($result->invoicePayment)
        {
          foreach($result->invoicePayment as $row)
          {
            $sum+=$row->repaid_amount;
          }
        }
        @endphp
        <table  border="1" align="center" cellspacing="0" cellpadding="0" style="font-size:15px;padding:20px;font-family:Montserrat,Arial,sans-serif;line-height: 24px;width: 100%">
            <tbody>

                <tr>
                    <td align="center" style="">
                        <table width="100%" border="1">
                            <tbody>
                                <tr>
                                    <td colspan="4"><b>Invoice Number : {{($result->invoice_no) ? $result->invoice_no : '' }} </b> </td>
                                </tr>
                                <tr>
                                    <td style="border-right:none;">
                                        <b>   Repay count :  </b>
                                    </td>
                                       <td style="border-left:none;">{{($result->invoicePayment) ? count($result->invoicePayment) : 0}}</td> 
                                     <td style="border-right:none;">  
                                   <b>  Invoice Approved Amount (₹): </b>
                                      </td>
                                   <td style="border-left:none;">    {{($result->invoice_due_date) ? number_format($result->invoice_amount) : '' }}
                                    </td>
                                </tr>
                                <tr>
                                   
                                     <td style="border-right:none;">
                                          <b>    Disburse Amount (₹):  </b> 
                                       </td>
                                    <td style="border-left:none;">   {{($result->disbursal) ? number_format($result->disbursal->principal_amount) : '' }}
                                    </td> 
                                     <td style="border-right:none;">
                                     <b>    Actual Funded Amount (₹):  </b>
                                     </td>
                                   <td style="border-left:none;">     {{($result->disbursal) ? number_format($result->disbursal->disburse_amount) : '' }}
                                    </td>
                                </tr>
                                <tr>
                                     <td style="border-right:none;">
                                     <b>    Funded Date:  </b>
                                       </td>
                                   <td style="border-left:none;">   {{($result->disbursal) ? $result->disbursal->disburse_date : '' }}
                                    </td> 
                                     <td style="border-right:none;">
                                     <b>    Tenor (in days): </b>
                                        </td>
                                   <td style="border-left:none;">   {{($result->disbursal) ? $result->disbursal->tenor_days : '' }}
                                    </td>
                                </tr>
                                <tr>
                                     <td style="border-right:none;">
                                    <b>     Payment Due Date:  </b>
                                      </td>
                                    <td style="border-left:none;">     14-March-2020
                                    </td> 
                                     <td style="border-right:none;">
                                     <b>    Interest Per Annum (%):  </b>
                                       </td>
                                   <td style="border-left:none;">    {{($result->disbursal) ? $result->disbursal->interest_rate : '' }}
                                    </td>
                                </tr>
                                <tr>
                                     <td style="border-right:none;">
                                    <b>     Processing Fee (%):   </b>
                                     </td>
                                    <td style="border-left:none;">     0 %
                                    </td> 
                                     <td style="border-right:none;">
                                    <b>     Discount Type:  </b>
                                      </td>
                                    <td style="border-left:none;">  front end
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-right:none;">
                                    <b>     Grace period (in days):  </b>
                                      </td>
                                    <td style="border-left:none;">     0
                                    </td> 
                                      <td style="border-right:none;">
                                     <b>    Penal Interest Per Annum (%):  </b>
                                      </td>
                                    <td style="border-left:none;">     0 %
                                    </td>
                                </tr>
                                <tr>
                                     <td style="border-right:none;">
                                   <b>      Repayment Amount:  </b>
                                    </td>
                                    <td style="border-left:none;">       ₹  	{{($result->disbursal) ? number_format($result->disbursal->repayment_amount) : '' }}
                                    </td> 
                                     <td style="border-right:none;">
                                     <b>    Total Amount Repaid:  </b>
                                      </td>
                                    <td style="border-left:none;">    ₹0
                                    </td>
                                </tr>
                                <tr>
                                     <td style="border-right:none;">
                                     <b>    Penal days:  </b>
                                     </td>
                                   <td style="border-left:none;">     0
                                    </td> 
                                     <td style="border-right:none;">
                                      <b>   Penalty Amount:  </b>
                                       </td>
                                    <td style="border-left:none;">    ₹0
                                    </td>
                                </tr>
                                <tr>
                                     <td style="border-right:none;">
                                      <b>   Principal Amount:  </b>
                                      </td>
                                    <td style="border-left:none;">     0
                                    </td> 
                                     <td style="border-right:none;">
                                     <b>    Total Amount to Repay:  </b>
                                      </td>
                                      <td style="border-left:none;">    ₹  <span id="totalAmountMsg">{{($result->disbursal->repayment_amount-$sum > 0) ?  number_format($result->disbursal->repayment_amount-$sum) : 0 }}</span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                      <!--  <table width="100%" border="0">
                           <form id="signupForm">
                       <tr>
                           <td colspan="2">
                        <label for="repaid_amount" class="form-control-label">Repayment Date* :</label>
                        <input type="text" onchange="handler()" class="form-control datepicker-dis-redate amountRepay" id="repaid_date" onchange="getPenalty(this)" name="repaid_date" value="" readonly="readonly" style="height:45px;">
                        <span id="repaid_date_msg" class="error"></span>
                       </td>
                       <td colspan="2">
                           <label for="repaid_amount" class="form-control-label">Repaid Amount*(₹):</label>
                       <input type="text" class="form-control numbercls amountRepay" id="repaid_amount" name="repaid_amount" value="" style="height:45px;">
                        <span id="repaid_amount_msg" class="error"></span>
                       </td> 

                       </tr>
                       <tr>
                       <td colspan="2">
                           <label for="repaid_amount" class="form-control-label">Payment Type*</label> </br>
                           @php 
                                               $get = Config::get('payment.type');
                                              @endphp
                                                
                                                <select class="form-control amountRepay" name="payment_type" id="payment_type">
                                                    <option value=""> Select Payment Type </option>
                                                     @foreach($get as $key=>$val)
                                                    <option value="{{$key}}"> {{$val}}</option>
                                                     @endforeach  
                                                </select>
                           <span id="payment_type_msg" class="error"></span>
                       </td>
                       <td colspan="2">
                           <span id="appendInput"></span>
                      </td> 

                       </tr>
                        <tr>
                       <td colspan="2">
                         
                                <label for="repaid_amount" class="form-control-label">Upload Document*:</label>
                                    <input type="file" class="form-control numbercls amountRepay" id="additional" name="additional" value="" style="height:45px;">
                       <span id="additional_msg" class="error"></span>
                       </td>
                        <td colspan="2">
                           
                                <label for="repaid_amount" class="form-control-label">Comment :</label>
                                <input type="text" class="form-control numbercls amountRepay" id="comment" name="comment" value="" style="height:45px;">
                      <span id="comment_msg" class="error"></span>
                        </td> 

                       </tr>
                          <tr>
                       <td  colspan="2">
                            <span id="submit_msg"></span>     
                        </td>
                      <td>
                         
                     <input type="hidden" value="{{($result->disbursal->repayment_amount-$sum > 0) ?  $result->disbursal->repayment_amount-$sum : 0 }}" id='final_amount'>
                     <input type="hidden" value="{{($result->invoice_id) ?  $result->invoice_id : ''}}" id='invoice_id'>
                     <input type="hidden" value="{{($result->app_id) ?  $result->app_id : '' }}" id='app_id'>
                     <input type="hidden" value="{{($result->supplier_id) ?  $result->supplier_id : '' }}" id='supplier_id'>
                     <input type="button" data-invoice-no="24" class="btn btn-sm btn-primary submitBtn"  id="submit" value="Submit">
                     <input type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal"  value="Close">
                        </td> 

                       </tr>  
                       </form>
                        </table>  -->
                    </td>
                </tr>
            </tbody>
        </table>
        
    </td>
</tr>

</tbody>
</table>

     <script src="{{url('backend/assets/js/jquery-3.4.1.min.js')}}"></script>
    <script src="{{url('backend/assets/js/popper.min.js')}}"></script>
    <script src="{{url('backend/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{url('backend/assets/js/perfect-scrollbar.jquery.min.js')}}"></script>
    <script src="{{url('backend/assets/js/jsgrid.min.js')}}"></script>
    <script src="{{url('backend/assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{url('backend/assets/js/misc.js')}}"></script>
    <script src="{{url('backend/assets/js/jquery.validate.js')}}"></script>
    <script src="{{url('backend/assets/plugins/datatables/js/datatable.min.js')}}"></script>
    <script src="{{url('common/js/datetimepicker/js/bootstrap-datetimepicker.js')}}"></script>
    <script src="{{url('common/js/iframePopup.js')}}"></script> 
   <script>
          var messages = {
        backend_activity_invoice_list: "{{ URL::route('backend_activity_invoice_list') }}",
        save_repayment: "{{ URL::route('save_repayment') }}",
        token: "{{ csrf_token() }}",
    };
    
     $(document).ready(function(){ 
        document.getElementById('repaid_amount').addEventListener('input', event =>
         event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
     });
     
        function handler(){
         var final_amount = $("#final_amount").val();
        $("#repaid_amount").val(final_amount);
     };    
       
     $(document).on('change','#payment_type',function(){
          $('#appendInput').empty();
          var status = $(this).val();
         if(status==1)
         {
                   $('#appendInput').append('<label for="repaid_amount" class="form-control-label"><span class="payment_text">UTR Number</span></label><input type="text" class="form-control amountRepay" id="utr_no" name="utr_no" value=""><span id="utr_no_msg" class="error"></span>');
    
         }
         else if(status==2)
         {
                 $('#appendInput').append('<label for="repaid_amount" class="form-control-label"><span class="payment_text">Cheque Number</span></label><input type="text" class="form-control amountRepay" id="utr_no" name="utr_no" value=""><span id="utr_no_msg" class="error"></span>');
      
         }
          else if(status==3)
         {
                 $('#appendInput').append('<label for="repaid_amount" class="form-control-label"><span class="payment_text">UNR Number</span></label><input type="text" class="form-control amountRepay" id="utr_no" name="utr_no" value=""><span id="utr_no_msg" class="error"></span>');
      
         }
     }); 
        datepickerDisRepaymentdate();
      function datepickerDisRepaymentdate(){
            $(".datepicker-dis-redate").datetimepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                minView : 2,
                startDate: '{{($result->disbursal) ? $result->disbursal->disburse_date : '' }}'
            });
        }
        $(document).on('keyup change','.amountRepay',function(){
            if($(this).val()!='')
            {
               var id =  $(this).attr('id');
               $("#"+id+"_msg").hide();    
        }
        });
        
        /////////////////////// form submit////////////////////////
        $('#submit').on('click', function () {
        if($("#repaid_date").val()=='')
        {
             $("#repaid_date_msg" ).show();
            $("#repaid_date_msg" ).text('Please Select repaid date');
            return false;
        }
          if($("#repaid_amount").val()=='')
        {
             $("#repaid_amount_msg" ).show();
             $("#repaid_amount_msg" ).text("Please enter repaid amount");
              return false; 
            
        }
       /*  if($("#repaid_amount").val()!='')
        {
             if(parseFloat($("#repaid_amount").val()) > parseFloat($("#final_amount").val()))
            {
                $("#repaid_amount_msg" ).show();
                $("#repaid_amount_msg" ).text("Repaid amount should be same or less than total amount to Repay"); 
                return false; 
            }
            
        }  */
        if($("#payment_type").val()=='')
        {
             $("#payment_type_msg" ).show();
             $("#payment_type_msg" ).text("Please Select payment type");
             return false;
        }
         if($("#utr_no").val()=='')
        {
             $("#utr_no_msg" ).show();
            if($("#payment_type").val()==1)
            {
              $("#utr_no_msg" ).text("Please enter UTR number");
            }
            else  if($("#payment_type").val()==2)
            {
               $("#utr_no_msg" ).text("Please enter cheque Number");  
            }
            else  if($("#payment_type").val()==3)
            {
               $("#utr_no_msg" ).text("Please enter UNR number");
            }
              return false;
        } 
        if($("#additional").val()=='')
        {
             $("#additional_msg" ).show();
             $("#additional_msg" ).text("Please upload document");
             return false;
        }
      /*  if($("#comment").val()=='')
        {
             $("#comment_msg" ).show();
             $("#comment_msg" ).text("Please enter comment");
             return false;
        }  */
         
        var file  = $("#additional")[0].files[0];
        var datafile = new FormData();
        var repaid_date  = $("#repaid_date").val();
        var repaid_amount  = parseFloat($("#repaid_amount").val().replace(/,/g, ''));
        var final_amount  = $("#final_amount").val();
        var payment_type  = $("#payment_type").val();
        var invoice_id  = $("#invoice_id").val();
        var app_id  = $("#app_id").val();
        var supplier_id  = $("#supplier_id").val();
        var comment  =  $("#comment").val();
        var utr_no  =  $("#utr_no").val();
        datafile.append('_token', messages.token );
        datafile.append('doc_file', file);
        datafile.append('repaid_date', repaid_date);
        datafile.append('repaid_amount', repaid_amount);
        datafile.append('payment_type', payment_type);
        datafile.append('final_amount', final_amount);
        datafile.append('invoice_id', invoice_id);
        datafile.append('user_id', supplier_id);
        datafile.append('app_id', app_id);
        datafile.append('utr_no', utr_no);
        datafile.append('comment', comment);
        
        $('.isloader').show();    
         $.ajax({
            headers: {'X-CSRF-TOKEN':  messages.token  },
            url : messages.save_repayment,
            type: "POST",
            data: datafile,
            processData: false,
            contentType: false,
            cache: false, // To unable request pages to be cached
            enctype: 'multipart/form-data',

            success: function(r)
            {
                 $('.isloader').hide();
                 if(r.status==1)
                 {
                     $("#repaid_amount").val(r.amount);
                      $("#final_amount").val(r.amount);
                     $("#totalAmountMsg").text(r.amount);  
                     $('.amountRepay').val('');
                     $("#submit_msg").html('<b class="success">Repayment successfully done</b>');
                 }
                 else
                 {
                      $("#submit_msg").html('<b class="error">Something went wrong, Please try again</b>');
                 }
            }
          });
        })
      </script>   
</body>
</html>