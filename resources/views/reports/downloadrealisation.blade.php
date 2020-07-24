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
                font-size: 10px;
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
            #filterTable td,th{
                text-align: left;
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
            @if(!empty($filter))
            <table class="table  table-td-right" id="filterTable">
                <tbody>
                    @if(!empty($filter['userInfo']))
                    <tr>
                        <td><strong>Business Name</strong></td>
                        <td> {{$filter['userInfo']->biz->biz_entity_name}}    </td> 
                        <td><strong>Full Name</strong></td>
                        <td>{{$filter['userInfo']->f_name}} {{$filter['userInfo']->m_name}} {{$filter['userInfo']->l_name}}</td> 

                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td>{{$filter['userInfo']->email}}    </td> 
                        <td><strong>Mobile</strong></td>
                        <td>{{$filter['userInfo']->mobile_no}} </td> 
                    </tr>
                    @endif
                    @if($filter['from_date'] && $filter['to_date'])
                    <tr>
                        <td><strong>From Date</strong></td>
                        <td>{{$filter['from_date']}}</td> 
                        <td><strong>To Date</strong></td>
                        <td>{{$filter['to_date']}}</td> 
                    </tr>
                    @endif
                </tbody>
            </table>
            @endif
          
            <div class="breakNow">
                <table border="1px" style="width: 100%;clear: both; margin-top: 10px;" align="center" cellspacing="0" cellpadding="1">
                    <tr>
                      <th>Customer Id</th>
                      <th>Debtor Name</th>
                      <th>Debtor Invoice Acc. No.</th>
                      <th>Invoice Date</th>
                      <th>Invoice Due Amount </th>
                      <th>Invoice Due Amount Date</th>
                      <th>Grace Period</th>
                      <th>Realisation on Date</th>
                      <th>Realisation Amount</th>
                      <th>OD/OP Days </th>
                      <th>Cheque </th>
                      <th>Business Name</th>
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
                            <span>{{$invoice->customer_id}}</span>
                        </td>
                        <td>
                            <span>{{$invoice->invoice->anchor->comp_name}}</span>
                        </td>
                         <td>
                            <span>
                                @php
                                if(isset($invoice->Invoice->anchor->anchorAccount)) 
                                {
                                   echo $invoice->Invoice->anchor->anchorAccount->acc_no;
                                 }
                                 @endphp
                            </span>
                        </td>
                        <td>
                            <span>{{\Carbon\Carbon::parse($invoice->invoice->invoice_date)->format('d/m/Y')}}</span>
                        </td>
                         <td>
                            <span>{{number_format($invoice->invoice->invoice_approve_amount)}}</span>
                        </td>
                         <td>
                            <span>{{\Carbon\Carbon::parse($invoice->payment_due_date)->format('d/m/Y')}}</span>
                        </td>
                        <td>
                              <span>{{$invoice->grace_period}}</span>
                       
                        </td>
                        <td>
                            <span>
                        @php        
                                $payment  = '';                   
                       foreach($invoice->transaction as $row)
                      {
                           if( $row->payment->date_of_payment)
                           {
                             $payment.= \Carbon\Carbon::parse($row->payment->date_of_payment)->format('d/m/Y').", ";
                           }
                           
                      }
                    echo  substr($payment,0,-1); 
                      @endphp          
                            </span>
                        </td>
                         <td>
                            <span>{{number_format($invoice->invoice->invoice_approve_amount)}}</span>
                        </td>
                         <td>
                            <span>{{$invoice->InterestAccrual->count()}}</span>
                        </td>
                        <td>
                            <span>
                        @php
                        $chk  = '';                   
                       foreach($invoice->transaction as $row)
                       {
                           if( $row->payment->utr_no)
                           {
                             $chk.= $row->payment->utr_no.", ";
                           }
                            if( $row->payment->unr_no)
                           {
                             $chk.= $row->payment->unr_no.", ";
                           }
                            if( $row->payment->cheque_no)
                           {
                             $chk.= $row->payment->cheque_no.", ";
                           }
                      }
                     echo substr($chk,0,-1);
                    @endphp        
                            </span>
                        </td>
                           <td>
                            <span>{{$invoice->invoice->business->biz_entity_name}}</span>
                        </td>
                    </tr>
                    @endforeach
                       <tr>
                           <td colspan="4">
                            <span><strong>Grand Total</strong></span>
                        </td>
                          <td>
                            <span><strong>{{number_format($invApprBal)}}</strong></span>
                        </td>
                         <td> &nbsp;</td>
                        <td> &nbsp;</td>
                       <td> &nbsp;</td>
                          <td>
                            <span><strong>{{number_format($invApprBal)}}</strong></span>
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



