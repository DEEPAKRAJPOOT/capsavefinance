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
                 <h3 class="text-center">Business Overview</h3>
                 <span class="subBnShow">Turnover & Customers</span>
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                 
                  <tbody>
                  <tr>
                        <th>Financial Year  </th>
                        <th> Gross Turnover </th>
                        <th>Net Turnover  </th>
                        <th>Total Customers  </th>
                        <th>Total Invoices </th>
                       </tr>  
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
               @endif





                 @if($currenttop3Cus) <!---start code for display top 3 customer-->
                 <h3 class="text-center">Top 3 Customers</h3>
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr align="center">
                        <th colspan="5" >{{$current_year}}</th>
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
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
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
               </table>
               @endif <!---end code for display top 3 customer-->

             <!---start code for display top 3 supplier-->
               @if($currenttop3Sup)
                 <h3 class="text-center">Top 3 Suppliers</h3>
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr align="center">
                        <th colspan="5" >{{$current_year}}</th>
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
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
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
               </table>
               @endif <!---end code for display top 3 supplier-->
        @endif    <!--end gst  display code-->
            </div>
         </div>
         @if(request()->get('view_only')) 
         <button class="btn btn-success btn-sm pull-right  mt-3"> Save</button>
         @endif
      </div>
   </div>
</div>
</div>
@endsection
@section('jscript')
@endsection
