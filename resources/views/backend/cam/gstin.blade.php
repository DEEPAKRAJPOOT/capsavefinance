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
                        <td>{!! \Helpers::formatCurreny($gstResponsShow['previous']['turnover_and_customers']['gross_turnover'])!!}   </td>
                        <td>{!! \Helpers::formatCurreny($gstResponsShow['previous']['turnover_and_customers']['net_turnover'])!!}  </td>
                        <td> {{$gstResponsShow['previous']['turnover_and_customers']['ttl_customer']}}</td> 
                         <td> {{ $gstResponsShow['previous']['turnover_and_customers']['ttl_inv']}} </td>                       
                     </tr>              
                      <tr>
                        <td> {{$current_year}}</td>
                        <td>{!! \Helpers::formatCurreny($gstResponsShow['current']['turnover_and_customers']['gross_turnover'])!!}   </td>
                        <td>{!! \Helpers::formatCurreny($gstResponsShow['current']['turnover_and_customers']['net_turnover'])!!}  </td>
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
                        <td> {!! \Helpers::formatCurreny($custVal['ttl_tax'])!!} </td>
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
                        <td>{!!\Helpers::formatCurreny($custVal['ttl_tax'])!!}</td>
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
                        <td>{!! \Helpers::formatCurreny($custVal['ttl_tax'])!!} </td>
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
                        <td>{!!\Helpers::formatCurreny($custVal['ttl_tax'])!!}</td>
                        <td>{{ $custVal['ttl_rec']}}</td> 
                         <td>{{ number_format($custVal['share']*100,2)}}% </td>                       
                     </tr>
                     @endforeach
                  </tbody>
                  <tbody>
                  </tbody>
               </table> -->
               @endif <!---end code for display top 3 supplier-->
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
