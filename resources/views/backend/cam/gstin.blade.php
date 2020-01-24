@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-sidebar')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
   @include('layouts.backend.partials.cam_nav')
   <div class="inner-container">
   <div class="card mt-4">
      <div class="card-body ">
         <div class="data">
            <h2 class="sub-title bg mb-4">GST Sales Ledger Sales GS</h2>
            <div class="pl-4 pr-4 pb-4 pt-2">
               @if($gstdocs->count() > 0)
                     @foreach($gstdocs as $gstdoc)
                  <div class="doc" style="text-align: center;">
                     <small>{{ date("F",strtotime('2019-'.$gstdoc->gst_month.'-01')) . '-'. $gstdoc->gst_year }}</small>
                     <ul>
                        <li><span class="icon"><i class="fa fa-file-excel-o"></i></span></li>
                        <li><a href="{{ Storage::url($gstdoc->file_path) }}" download target="_blank">Download GST Statement</a></li>
                        <li><a href="javascript:void(0)"></a>&nbsp;</li>
                     </ul>
                  </div>
                     @endforeach
                  @endif
                  @foreach($all_gst_details as $gst_data)
                  @if(file_exists(public_path("storage/user/".$appId.'_'.$gst_data['pan_gst_hash'].".pdf")))
                  <div class="doc" style="text-align: center;">
                     <small><strong>{{ $gst_data['pan_gst_hash'] }}</strong></small>
                     <ul>
                        <li><span class="icon"><i class="fa fa-file-pdf-o"></i></span></li>
                        <li><a href="{{ Storage::url('user/'.$appId.'_'.$gst_data['pan_gst_hash'].'.pdf') }}" download target="_blank">Download Karza GST Statement</a></li>
                        <li><a href="javascript:void(0)"></a>&nbsp;</li>
                     </ul>
                  </div>
                  @endif
                  @endforeach
                  <div class="clearfix"></div>
                  <br/>
                  <hr>


              @if($gstResponsShow)

                   @php
               $current_year = $gstResponsShow['current']['financial_year'];
               $previous_year = $gstResponsShow['previous']['financial_year'];
                   @endphp
                  @if($gstResponsShow && $gstResponsShow['current']['turnover_and_customers'])
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
               <thead>
                     <tr>
                        <th colspan="5" >Business Overview</th>
                     </tr>
                     <tr>
                  <th  style="background:#62b59b;">Financial Year  </th>
                        <th style="background:#62b59b;"> Gross Turnover </th>
                        <th style="background:#62b59b;">Net Turnover  </th>
                        <th style="background:#62b59b;">Total Customers  </th>
                        <th style="background:#62b59b;">Total Invoices </th>
                       </tr>  
                  </thead>
                  <tbody>
                 
                       <tr>
                        <td> {{$previous_year}}</td>
                        <td>{!! \Helpers::roundFormatCurreny($gstResponsShow['previous']['turnover_and_customers']['gross_turnover'])!!}   </td>
                        <td>{!! \Helpers::roundFormatCurreny($gstResponsShow['previous']['turnover_and_customers']['net_turnover'])!!}  </td>
                        <td> {{$gstResponsShow['previous']['turnover_and_customers']['ttl_customer']}}</td> 
                         <td> {{ $gstResponsShow['previous']['turnover_and_customers']['ttl_inv']}} </td>                       
                     </tr>              
                      <tr>
                        <td> {{$current_year}}</td>
                        <td>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['turnover_and_customers']['gross_turnover'])!!}   </td>
                        <td>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['turnover_and_customers']['net_turnover'])!!}  </td>
                        <td> {{$gstResponsShow['current']['turnover_and_customers']['ttl_customer']}}</td> 
                         <td> {{ $gstResponsShow['current']['turnover_and_customers']['ttl_inv']}} </td>                       
                     </tr>                   
                  </tbody>
                  <tbody>
                  </tbody>
               </table> 
               <br>
               @endif

                 @if($currenttop3Cus) <!---start code for display top 3 customer-->                
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr>
                        <th colspan="5" >Top 3 Customers ({{$current_year}})</th>
                     </tr>
                     <tr>
                        <th  style="background:#62b59b;">PAN  </th>
                        <th  style="background:#62b59b;"> Name </th>
                        <th  style="background:#62b59b;">Turnover  </th>
                        <th  style="background:#62b59b;">Total Invoices  </th>
                        <th  style="background:#62b59b;">Share </th>
                       </tr>
                  </thead>
                
                  <tbody>
                  
                  @foreach($currenttop3Cus as $custVal)                   
                      <tr>
                        <td> {{ $custVal['pan']}}</td>
                        <td>{{ $custVal['name']}}</td>
                        <td> {!! \Helpers::roundFormatCurreny($custVal['ttl_tax'])!!} </td>
                        <td>{{ $custVal['ttl_rec']}}</td> 
                         <td>{{ number_format($custVal['share']*100,2)}}%</td>                       
                     </tr>
                     @endforeach
                  </tbody>
                  <tbody>
                  </tbody>
               </table>
               <br>
               <!-- <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr align="center">
                        <th colspan="5" >{{$previous_year}}</th>
                     </tr>
                  </thead>
                  <tbody>
                  <tr>
                        <th>PAN  </th>
                        <th> Name </th>
                        <th>Turnover  </th>
                        <th>Total Invoices  </th>
                        <th>Share </th>
                       </tr>
                  @foreach($previoustop3Cus as $custVal)                   
                      <tr>
                        <td>{{ $custVal['pan']}}</td>
                        <td>{{ $custVal['name']}}  </td>
                        <td>{!!\Helpers::roundFormatCurreny($custVal['ttl_tax'])!!}</td>
                        <td>{{ $custVal['ttl_rec']}}  </td> 
                         <td> {{ number_format($custVal['share']*100,2)}}%</td>                       
                     </tr>
                     @endforeach
                  </tbody>
                  <tbody>
                  </tbody>
               </table> -->
               @endif <!---end code for display top 3 customer-->

             <!---start code for display top 3 supplier-->
               @if($currenttop3Sup)
             
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr >
                        <th colspan="5" >Top 3 Suppliers ({{$current_year}})</th>
                     </tr>
                     <tr>
                     <th  style="background:#62b59b;">PAN  </th>
                        <th  style="background:#62b59b;"> Name </th>
                        <th  style="background:#62b59b;">Turnover  </th>
                        <th  style="background:#62b59b;">Total Invoices  </th>
                        <th  style="background:#62b59b;">Share </th>
                       </tr>
                  </thead>
                  <tbody>                  
                  @foreach($currenttop3Sup as $custVal)                   
                      <tr>
                        <td> {{ $custVal['pan']}} </td>
                        <td>{{ $custVal['name']}} </td>
                        <td>{!! \Helpers::roundFormatCurreny($custVal['ttl_tax'])!!} </td>
                        <td> {{ $custVal['ttl_rec']}} </td> 
                         <td>{{ number_format($custVal['share']*100,2)}}%</td>                       
                     </tr>
                     @endforeach
                  </tbody>
                  <tbody>
                  </tbody>
               </table>
               <br>
               <!-- <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr align="center">
                        <th colspan="5" >{{$previous_year}}</th>
                     </tr>
                  </thead>
                  <tbody>
                  <tr>
                        <th>PAN  </th>
                        <th> Name </th>
                        <th>Turnover  </th>
                        <th>Total Invoices  </th>
                        <th>Share </th>
                       </tr>
                  @foreach($previoustop3Sup as $custVal)                   
                      <tr>
                        <td>  {{ $custVal['pan']}}  </td>
                        <td>{{ $custVal['name']}} </td>
                        <td>{!!\Helpers::roundFormatCurreny($custVal['ttl_tax'])!!}</td>
                        <td>{{ $custVal['ttl_rec']}}</td> 
                         <td>{{ number_format($custVal['share']*100,2)}}% </td>                       
                     </tr>
                     @endforeach
                  </tbody>
                  <tbody>
                  </tbody>
               </table> -->
               @endif <!---end code for display top 3 supplier-->

            <!--start filling code  display code-->
               @if($gstResponsShow['current']['filing_status'])             
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr >
                        <th colspan="5" >Filing Status  ({{$current_year}})</th>
                     </tr>
                     <tr>
                     <th  style="background:#62b59b;">Month  </th>
                        <th  style="background:#62b59b;"> GSTR1 </th>
                        <th  style="background:#62b59b;">GSTR3B  </th>
                        <th  style="background:#62b59b;">GSTR4 </th>
                       </tr>
                  </thead>
                  <tbody>                  
                  @foreach($gstResponsShow['current']['filing_status'] as $key =>$fileStatusVal)
                  @php
                  $fileMonth=substr($fileStatusVal['ret_period'],0,2);
                  $fileYear=substr($fileStatusVal['ret_period'],-4); 
                  $fileDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                      <tr>
                        <td> 
                        {{ date("M Y",strtotime($fileDate))}}
                     </td>
                        <td> @if($gstResponsShow['current']['filing_status'][$key]['status'][0]['status'] && $gstResponsShow['current']['filing_status'][$key]['status'][0]['status']=='FIL') Filed @else Not Filed @endif</td>
                        <td> @if($gstResponsShow['current']['filing_status'][$key]['status'][3]['status'] && $gstResponsShow['current']['filing_status'][$key]['status'][3]['status']=='FIL') Filed @else Not Filed @endif</td>
                        <td>  @if($gstResponsShow['current']['filing_status'][$key]['status'][2]['status']=='FIL') Filed @else --  @endif</td> 
                        </tr>                    
                     @endforeach
                  </tbody>
                  <tbody>
                  </tbody>
               </table>
               <br>
               @endif    <!--end filling code  display code-->


<!--start Monthly Sales code  display code-->

   @if($gstResponsShow['current']['quarterly_summary']  &&  $gstResponsShow['current']['quarterly_summary']['quarter1'])             
              <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr >
                        <th colspan="5" >Monthly Sales   ({{$current_year}})</th>
                     </tr>
                     <tr>
                     <th  style="background:#62b59b;">Month  </th>
                        <th  style="background:#62b59b;"> GSTR1 Invoice Value </th>
                        <th  style="background:#62b59b;">GSTR1  Taxable Value</th>
                        <th  style="background:#62b59b;">GSTR3B Invoice Value </th>
                        <th  style="background:#62b59b;">GSTR3B Taxable Value </th>
                       </tr>
                  </thead>
                  <tbody>
                  @foreach($gstResponsShow['current']['quarterly_summary']['quarter1']['months'] as $key =>$monthSaleVal)
                  @php
                  $fileMonth=substr($monthSaleVal['ret_period'],0,2);
                  $fileYear=substr($monthSaleVal['ret_period'],-4); 
                  $monthSaleValDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                      <tr>
                      <td>  {{ date("M Y",strtotime($monthSaleValDate))}}</td>
                        <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr1']['ttl_val'] ) !!}</td>
                        <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr1']['ttl_tax'] ) !!}</td>
                        <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr3b']['ttl_val'] ) !!}</td>
                        <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr3b']['ttl_tax'] ) !!}</td>
                        </tr>                    
                     @endforeach

                     @php
                  $fileMonth=substr($gstResponsShow['current']['quarterly_summary']['quarter1']['months'] [0]['ret_period'],0,2);
                  $fileYear=substr($monthSaleVal['ret_period'],-4); 
                  $firstmonthSaleValDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                     <tr>
                     <th>  Q1 Total ( {{ date("M Y",strtotime($firstmonthSaleValDate))  }}  -  {{ date("M Y",strtotime($monthSaleValDate))  }} )</td>    <!--  (Apr 2019 - Jun 2019) -->
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter1']['total_gstr1'] ['ttl_val'])  !!}</th>
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter1']['total_gstr1'] ['ttl_tax'])  !!}</th>
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter1']['total_gstr3b'] ['ttl_val'])  !!}</th>
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter1']['total_gstr3b'] ['ttl_tax'] )  !!}  
                         </th>
                        </tr>

                  @foreach($gstResponsShow['current']['quarterly_summary']['quarter2']['months'] as $key =>$monthSaleVal)
                  @php
                  $fileMonth=substr($monthSaleVal['ret_period'],0,2);
                  $fileYear=substr($monthSaleVal['ret_period'],-4); 
                  $monthSaleValDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                  <tr>
                  <td>  {{ date("M Y",strtotime($monthSaleValDate))}}</td>
                  <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr1']['ttl_val'] ) !!}</td>
                  <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr1']['ttl_tax'] ) !!}</td>
                  <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr3b']['ttl_val'] ) !!}</td>
                  <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr3b']['ttl_tax'] ) !!}</td>
                  </tr>                    
                  @endforeach 

                  @php
                  $fileMonth=substr($gstResponsShow['current']['quarterly_summary']['quarter2']['months'] [0]['ret_period'],0,2);
                  $fileYear=substr($monthSaleVal['ret_period'],-4); 
                  $firstmonthSaleValDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                     <tr>
                     <th>  Q1 Total ( {{ date("M Y",strtotime($firstmonthSaleValDate))  }}  -  {{ date("M Y",strtotime($monthSaleValDate))  }} )</td>    <!--  (Apr 2019 - Jun 2019) -->
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter2']['total_gstr1'] ['ttl_val'])  !!}</th>
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter2']['total_gstr1'] ['ttl_tax'])  !!}</th>
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter2']['total_gstr3b'] ['ttl_val'])  !!}</th>
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter2']['total_gstr3b'] ['ttl_tax'] )  !!}  </th>
                        </tr> 
          @foreach($gstResponsShow['current']['quarterly_summary']['quarter3']['months'] as $key =>$monthSaleVal)
                  @php
                  $fileMonth=substr($monthSaleVal['ret_period'],0,2);
                  $fileYear=substr($monthSaleVal['ret_period'],-4); 
                  $monthSaleValDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                  <tr>
                  <td>  {{ date("M Y",strtotime($monthSaleValDate))}}</td>
                  <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr1']['ttl_val'] ) !!}</td>
                  <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr1']['ttl_tax'] ) !!}</td>
                  <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr3b']['ttl_val'] ) !!}</td>
                  <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr3b']['ttl_tax'] ) !!}</td>
                  </tr>                    
                  @endforeach 

                  @php
                  $fileMonth=substr($gstResponsShow['current']['quarterly_summary']['quarter2']['months'] [0]['ret_period'],0,2);
                  $fileYear=substr($monthSaleVal['ret_period'],-4); 
                  $firstmonthSaleValDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                     <tr>
                     <th>  Q1 Total ( {{ date("M Y",strtotime($firstmonthSaleValDate))  }}  -  {{ date("M Y",strtotime($monthSaleValDate))  }} )</td>    <!--  (Apr 2019 - Jun 2019) -->
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter2']['total_gstr1'] ['ttl_val'])  !!}</th>
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter2']['total_gstr1'] ['ttl_tax'])  !!}</th>
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter2']['total_gstr3b'] ['ttl_val'])  !!}</th>
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter2']['total_gstr3b'] ['ttl_tax'] )  !!}  
                         </th>
                        </tr> 

                  </tbody>
                  <tbody>
                  </tbody>
               </table>
               <br>
               @endif    <!--end Monthly Sales code  display code-->


<!--start Sales Last Six Months code  display code-->
             @if($gstResponsShow['last_six_mnth_smry'])             
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr >
                        <th colspan="5" >Sales Last Six Months</th>
                     </tr>
                     <tr>
                     <th  style="background:#62b59b;">  </th>
                        <th  style="background:#62b59b;"> GSTR1 Invoice Value </th>
                        <th  style="background:#62b59b;">GSTR1 Taxable Value</th>
                        <th  style="background:#62b59b;">GSTR3B Invoice Value </th>
                        <th  style="background:#62b59b;">GSTR3B Taxable Value </th>
                       </tr>
                  </thead>
                  <tbody>       
                      <tr>
                        <td>  Total  </td>
                        <td> {!! \Helpers::roundFormatCurreny($gstResponsShow['last_six_mnth_smry']['gstr1'] ['ttl_val'])  !!}</td>
                        <td>{!! \Helpers::roundFormatCurreny($gstResponsShow['last_six_mnth_smry']['gstr1']['ttl_tax'])  !!}</td>
                        <td>{!! \Helpers::roundFormatCurreny($gstResponsShow['last_six_mnth_smry']['gstr3b'] ['ttl_tax'])  !!}</td> 
                        <td>{!! \Helpers::roundFormatCurreny($gstResponsShow['last_six_mnth_smry']['gstr3b'] ['ttl_tax'])  !!}</td> 
                        </tr>    
                  </tbody>
                  <tbody>
                  </tbody>
               </table>
               <br>
               @endif    <!--end Sales Last Six Months code  display code-->




               <!--start Sales Last 15 Months code  display code-->
             @if($gstResponsShow['last_15_mnth_smry'])             
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr >
                        <th colspan="5" >Sales Last 15 Months</th>
                     </tr>
                     <tr>
                     <th  style="background:#62b59b;">  </th>
                        <th  style="background:#62b59b;"> GSTR1 Invoice Value </th>
                        <th  style="background:#62b59b;">GSTR1 Taxable Value</th>
                        <th  style="background:#62b59b;">GSTR3B Invoice Value </th>
                        <th  style="background:#62b59b;">GSTR3B Taxable Value </th>
                       </tr>
                  </thead>
                  <tbody>       
                      <tr>
                        <td>  Total  </td>
                        <td> {!! \Helpers::roundFormatCurreny($gstResponsShow['last_15_mnth_smry']['gstr1'] ['ttl_val'])  !!}</td>
                        <td>{!! \Helpers::roundFormatCurreny($gstResponsShow['last_15_mnth_smry']['gstr1']['ttl_tax'])  !!}</td>
                        <td>{!! \Helpers::roundFormatCurreny($gstResponsShow['last_15_mnth_smry']['gstr3b'] ['ttl_val'])  !!}</td> 
                        <td>{!! \Helpers::roundFormatCurreny($gstResponsShow['last_15_mnth_smry']['gstr3b'] ['ttl_tax'])  !!}</td> 
                        </tr>    
                  </tbody>
                  <tbody>
                  </tbody>
               </table>
               <br>
               @endif    <!--end Sales Last 15 Months code  display code-->




<!--start Monthly Purchases code  display code-->

@if($gstResponsShow['current']['quarterly_summary']  &&  $gstResponsShow['current']['quarterly_summary']['quarter1'])             
              <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr >
                        <th colspan="5" >Monthly Purchases   ({{$current_year}})</th>
                     </tr>
                     <tr>
                     <th  style="background:#62b59b;">Month  </th>
                        <th  style="background:#62b59b;"> GSTR2A Invoice Value </th>
                        <th  style="background:#62b59b;">GSTR2A  Taxable Value</th>
                        <th  style="background:#62b59b;">GSTR4A Invoice Value </th>
                        <th  style="background:#62b59b;">GSTR4A Taxable Value </th>
                       </tr>
                  </thead>
                  <tbody>
                  @foreach($gstResponsShow['current']['quarterly_summary']['quarter1']['months'] as $key =>$monthSaleVal)
                  @php
                  $fileMonth=substr($monthSaleVal['ret_period'],0,2);
                  $fileYear=substr($monthSaleVal['ret_period'],-4); 
                  $monthSaleValDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                      <tr>
                      <td>  {{ date("M Y",strtotime($monthSaleValDate))}}</td>
                        <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr2a']['ttl_val'] ) !!}</td>
                        <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr2a']['ttl_tax'] ) !!}</td>
                        <td></td>
                        <td></td>
                        </tr>                    
                     @endforeach

                     @php
                  $fileMonth=substr($gstResponsShow['current']['quarterly_summary']['quarter1']['months'] [0]['ret_period'],0,2);
                  $fileYear=substr($monthSaleVal['ret_period'],-4); 
                  $firstmonthSaleValDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                     <tr>
                     <th>  Q1 Total ( {{ date("M Y",strtotime($firstmonthSaleValDate))  }}  -  {{ date("M Y",strtotime($monthSaleValDate))  }} )</td>    <!--  (Apr 2019 - Jun 2019) -->
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter1']['total_gstr2a'] ['ttl_val'])  !!}</th>
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter1']['total_gstr2a'] ['ttl_tax'])  !!}</th>
                        <th></th>
                        <th> 
                         </th>
                        </tr>

                  @foreach($gstResponsShow['current']['quarterly_summary']['quarter2']['months'] as $key =>$monthSaleVal)
                  @php
                  $fileMonth=substr($monthSaleVal['ret_period'],0,2);
                  $fileYear=substr($monthSaleVal['ret_period'],-4); 
                  $monthSaleValDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                  <tr>
                  <td>  {{ date("M Y",strtotime($monthSaleValDate))}}</td>
                  <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr2a']['ttl_val'] ) !!}</td>
                  <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr2a']['ttl_tax'] ) !!}</td>
                  <td></td>
                  <td></td>
                  </tr>                    
                  @endforeach 

                  @php
                  $fileMonth=substr($gstResponsShow['current']['quarterly_summary']['quarter2']['months'] [0]['ret_period'],0,2);
                  $fileYear=substr($monthSaleVal['ret_period'],-4); 
                  $firstmonthSaleValDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                     <tr>
                     <th>  Q1 Total ( {{ date("M Y",strtotime($firstmonthSaleValDate))  }}  -  {{ date("M Y",strtotime($monthSaleValDate))  }} )</td>    <!--  (Apr 2019 - Jun 2019) -->
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter2']['total_gstr2a'] ['ttl_val'])  !!}</th>
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter2']['total_gstr2a'] ['ttl_tax'])  !!}</th>
                        <th></th>
                        <th></th>
                        </tr> 
          @foreach($gstResponsShow['current']['quarterly_summary']['quarter3']['months'] as $key =>$monthSaleVal)
                  @php
                  $fileMonth=substr($monthSaleVal['ret_period'],0,2);
                  $fileYear=substr($monthSaleVal['ret_period'],-4); 
                  $monthSaleValDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                  <tr>
                  <td>  {{ date("M Y",strtotime($monthSaleValDate))}}</td>
                  <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr2a']['ttl_val'] ) !!}</td>
                  <td>{!! \Helpers::roundFormatCurreny($monthSaleVal['gstr2a']['ttl_tax'] ) !!}</td>
                  <td></td>
                  <td></td>
                  </tr>                    
                  @endforeach 

                  @php
                  $fileMonth=substr($gstResponsShow['current']['quarterly_summary']['quarter2']['months'] [0]['ret_period'],0,2);
                  $fileYear=substr($monthSaleVal['ret_period'],-4); 
                  $firstmonthSaleValDate=$fileYear."-".$fileMonth."-".'01';
                  @endphp
                     <tr>
                     <th>  Q1 Total ( {{ date("M Y",strtotime($firstmonthSaleValDate))  }}  -  {{ date("M Y",strtotime($monthSaleValDate))  }} )</td>    <!--  (Apr 2019 - Jun 2019) -->
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter2']['total_gstr2a'] ['ttl_val'])  !!}</th>
                        <th>{!! \Helpers::roundFormatCurreny($gstResponsShow['current']['quarterly_summary']['quarter2']['total_gstr2a'] ['ttl_tax'])  !!}</th>
                        <th></th>
                        <th></th>
                        </tr> 

                  </tbody>
                  <tbody>
                  </tbody>
               </table>
               <br>
               @endif    <!--end Monthly Purchases code  display code-->



<!--start Purchases Last Six Months code  display code-->
@if($gstResponsShow['last_six_mnth_smry'])             
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr >
                        <th colspan="5" >Purchases Last Six Months</th>
                     </tr>
                     <tr>
                     <th  style="background:#62b59b;">  </th>
                        <th  style="background:#62b59b;"> GSTR2A Invoice Value </th>
                        <th  style="background:#62b59b;">GSTR2A Taxable Value</th>
                        <th  style="background:#62b59b;">GSTR4A Invoice Value </th>
                        <th  style="background:#62b59b;">GSTR4A Taxable Value </th>
                       </tr>
                  </thead>
                  <tbody>       
                      <tr>
                        <td>  Total  </td>
                        <td> {!! \Helpers::roundFormatCurreny($gstResponsShow['last_six_mnth_smry']['gstr2a'] ['ttl_val'])  !!}</td>
                        <td>{!! \Helpers::roundFormatCurreny($gstResponsShow['last_six_mnth_smry']['gstr2a']['ttl_tax'])  !!}</td>
                        <td></td> 
                        <td></td> 
                        </tr>    
                  </tbody>
                  <tbody>
                  </tbody>
               </table>
               <br>
               @endif    <!--end Purchases Last Six Months code  display code-->




               <!--start Purchases Last 15 Months code  display code-->
             @if($gstResponsShow['last_15_mnth_smry'])             
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr >
                        <th colspan="5" >Purchases Last 15 Months</th>
                     </tr>
                     <tr>
                     <th  style="background:#62b59b;">  </th>
                        <th  style="background:#62b59b;"> GSTR2A Invoice Value </th>
                        <th  style="background:#62b59b;">GSTR2A Taxable Value</th>
                        <th  style="background:#62b59b;">GSTR4A Invoice Value </th>
                        <th  style="background:#62b59b;">GSTR4A Taxable Value </th>
                       </tr>
                  </thead>
                  <tbody>       
                      <tr>
                        <td>  Total  </td>
                        <td> {!! \Helpers::roundFormatCurreny($gstResponsShow['last_15_mnth_smry']['gstr2a'] ['ttl_val'])  !!}</td>
                        <td>{!! \Helpers::roundFormatCurreny($gstResponsShow['last_15_mnth_smry']['gstr2a']['ttl_tax'])  !!}</td>
                        <td></td> 
                        <td></td> 
                        </tr>    
                  </tbody>
                  <tbody>
                  </tbody>
               </table>
               <br>
               @endif    <!--end Purchases Last 15 Months code  display code-->




        @endif    <!--end gst  display code-->
            </div>
         </div>
         @if(request()->get('view_only')) 
         <!-- <button class="btn btn-success btn-sm pull-right  mt-3"> Save</button> -->
         @endif
      </div>
   </div>
</div>
</div>
@endsection
@section('jscript')
@endsection
