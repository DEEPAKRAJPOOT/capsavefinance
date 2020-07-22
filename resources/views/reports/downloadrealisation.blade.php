<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{url('backend/assets/css/data-table.css')}}" />
        <style>
            @page {
                margin: 2cm 1.44cm 2.25cm 1.44cm;
            }
            /** Define the header rules **/
            header {
                position: fixed;
                top:-0.5cm;
            }
            footer {
                position: fixed;
                bottom:0.5cm;
            }
            *{
                font-size:13px !important;
                font-family: 'source-sans-pro-regular', sans-serif !important;
            }
            table{
                width: 100% !important;
                /*border:1px solid #ccc;*/
            }
            table th{
                background-color:#808080 !important;
                -webkit-print-color-adjust: exact;
                border-right:#c5c5c5 solid 1px;
                padding:5px 10px;
                color:#ffffff;
            }
            table th.bg-second{
                background-color: #9c9b9b !important;
                -webkit-print-color-adjust: exact;
                border-top: #e2e2e2 solid 1px;
            }
            table th:last-child{ 
                border-right:none;
            }
            td,th{
                font-size: <?php echo count($pdfArr[0]) > 8 ? '8px' : '10px'; ?> !important;
                padding:5px;
                text-align: center;
            }
            table td:last-child{
                border-right:none;
            }
            table td.blank{ 
                background-color:#cccccc !important;
                -webkit-print-color-adjust: exact;
            }
            p{
                margin:0px;
            }
            .data {
                border: 1px solid #e9ecef; 
                margin-bottom: 25px;
            }
            .sub-title.bg {
                background: #efefef;
                padding: 10px 15px;
                width: 100%;
                float: left;
                margin: 0px;
                box-sizing: border-box;
            }
            .pl-4.pr-4.pb-4.pt-2{
                padding:15px;
                clear: both;
            }
            .pagenum:before {
                content: counter(page);
            }
            /*div.breakNow { page-break-inside:avoid; page-break-after:always; }*/
        </style>

    </head>
    <body>
        <!-- Define header and footer blocks before your content -->
        <header>
            @php
            $date = \Carbon\Carbon::now();   
            @endphp
            <span align="right" style="float: left;"><b>{{ $date->isoFormat('MMMM D, Y')}}</b></span>
        </header>
        <footer>
            <hr>
            <span class="pagenum"></span><b> |</b> CFPL
        </footer>
        <main>
            <div  align="center" style="margin-top:40px;">
                <p><b><b>CAPSAVE FINANCE PRIVATE LIMITED</b></b></p>
                <br>
            </div>
        
          
            <div class="breakNow">
                
                   <table border="0" style="width: 100%;clear: both; margin-top: 10px;" align="center" cellspacing="0" cellpadding="1">
                    <tr>
                         <td width="40%">
                            @php if($fromdate && $todate) { @endphp
                            <span style="font-size: small;"><strong>Invoice Realisation Between</strong></span>
                            &nbsp;
                            {{($fromdate)? $fromdate : '' }} &nbsp; To &nbsp; {{($todate)? $todate : ''}}
                            @php  } @endphp
                        </td>
                          <td width="25%">
                            <span style="font-size: small;"><strong>Invoice Realisation Report</strong></span>
                           </td>
                            <td width="10%">
                            <span style="font-size: small;"><strong></strong></span>
                           </td>
                             <td width="25%">
                            <span style="font-size: small;"><strong>&nbsp;&nbsp;Run Date: &nbsp;{{ $date->isoFormat('MMMM D, Y')}}</strong></span>
                           </td>
                    </tr>
                </table>
              
                <table border="1px" style="width: 100%;clear: both; margin-top: 10px;" align="center" cellspacing="0" cellpadding="1">
                    <tr>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Customer Id</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Debtor Name</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Debtor Invoice Acc. No.</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Invoice Date</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Invoice Due Amount </strong></span>
                        </td>
                         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Invoice Due Amount Date</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Grace Period</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Realisation on Date</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Realisation Amount</strong></span>
                        </td>
                       
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>OD/OP Days </strong></span>
                        </td>
                       <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Cheque </strong></span>
                        </td>
                          <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Business Name</strong></span>
                        </td>
                    </tr>
                    @php
                    $invBal = 0;
                    $invApprBal = 0;
                    $subBal = 0;
                    @endphp
                   @foreach($userInfo as $invoice) 
                   @php
                   $invBal += $invoice->invoice->invoice_amount;
                   $invApprBal += $invoice->invoice->invoice_approve_amount;
                   @endphp
                    <tr>
                        <td>
                            <span style="font-size: small;">{{$invoice->customer_id}}</span>
                        </td>
                        <td>
                            <span style="font-size: small;">{{$invoice->invoice->anchor->comp_name}}</span>
                        </td>
                         <td>
                            <span style="font-size: small;">
                                @php
                                if(isset($invoice->Invoice->anchor->anchorAccount)) 
                                {
                                   echo $invoice->Invoice->anchor->anchorAccount->acc_no;
                                 }
                                 @endphp
                            </span>
                        </td>
                        <td>
                            <span style="font-size: small;">{{\Carbon\Carbon::parse($invoice->invoice->invoice_date)->format('d/m/Y')}}</span>
                        </td>
                         <td>
                            <span style="font-size: small;">{{number_format($invoice->invoice->invoice_approve_amount)}}</span>
                        </td>
                         <td>
                            <span style="font-size: small;">{{\Carbon\Carbon::parse($invoice->payment_due_date)->format('d/m/Y')}}</span>
                        </td>
                        <td>
                              <span style="font-size: small;">{{$invoice->grace_period}}</span>
                       
                        </td>
                        <td>
                            <span style="font-size: small;">
                        @php        
                                $payment  = '';                   
                       foreach($invoice->transaction as $row)
                      {
                           if( $row->payment->date_of_payment)
                           {
                             $payment.= \Carbon\Carbon::parse($row->payment->date_of_payment)->format('d/m/Y').",";
                           }
                           
                      }
                    echo  substr($payment,0,-1); 
                      @endphp          
                            </span>
                        </td>
                         <td>
                            <span style="font-size: small;">{{number_format($invoice->invoice->invoice_approve_amount)}}</span>
                        </td>
                         <td>
                            <span style="font-size: small;">{{$invoice->InterestAccrual->count()}}</span>
                        </td>
                        <td>
                            <span style="font-size: small;">
                        @php
                        $chk  = '';                   
                       foreach($invoice->transaction as $row)
                       {
                           if( $row->payment->utr_no)
                           {
                             $chk.= $row->payment->utr_no.",";
                           }
                            if( $row->payment->unr_no)
                           {
                             $chk.= $row->payment->unr_no.",";
                           }
                            if( $row->payment->cheque_no)
                           {
                             $chk.= $row->payment->cheque_no.",";
                           }
                      }
                     echo substr($chk,0,-1);
                    @endphp        
                            </span>
                        </td>
                           <td>
                            <span style="font-size: small;">{{$invoice->invoice->business->biz_entity_name}}</span>
                        </td>
                    </tr>
                    @endforeach
                       <tr>
                           <td colspan="4">
                            <span style="font-size: small;"><strong>Grand Total</strong></span>
                        </td>
                          <td>
                            <span style="font-size: small;"><strong>{{number_format($invApprBal)}}</strong></span>
                        </td>
                         <td> &nbsp;</td>
                        <td> &nbsp;</td>
                       <td> &nbsp;</td>
                          <td>
                            <span style="font-size: small;"><strong>{{number_format($invApprBal)}}</strong></span>
                        </td>
                           <td> &nbsp;</td>
                        <td> &nbsp;</td>
                       <td> &nbsp;</td>
                    </tr>
                </table>
            </div>
            
        </main>
    </body>
</html>



