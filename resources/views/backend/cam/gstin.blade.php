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
                  @if(file_exists(public_path("storage/user/".$gst_data['pan_gst_hash'].".pdf")))
                  <div class="doc" style="text-align: center;">
                     <small>GST FROM KARZA</small>
                     <ul>
                        <li><span class="icon"><i class="fa fa-file-excel-o"></i></span></li>
                        <li><a href="{{ Storage::url('user/'.$gst_data['pan_gst_hash'].'.pdf') }}" download target="_blank">Download Karza GST Statement</a></li>
                        <li><a href="javascript:void(0)"></a>&nbsp;</li>
                     </ul>
                  </div>
                  @endif
                  @endforeach
                  <div class="clearfix"></div>
                  <br/>
                  <hr>
               <table id="" class="table  GST-detail overview-table" cellspacing="0" width="100%">
                  <thead>
                     <tr>
                        <th width="30%">Month/Year</th>
                        <th>No Approved Buyer</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                           <div id="head2">
                              October,2019
                           </div>
                        </td>
                        <td colspan="2" style="padding-left: 0rem !important;padding-right: 0rem !important;padding:0;"></td>
                     </tr>
                     <tr>
                        <td>
                           <div id="head2">
                              September,2019
                           </div>
                        </td>
                        <td colspan="2" style="padding-left: 0rem !important;padding-right: 0rem !important;padding:0;"></td>
                     </tr>
                     <tr>
                        <td>
                           <div id="head2">
                              August,2019
                           </div>
                        </td>
                        <td colspan="2" style="padding-left: 0rem !important;padding-right: 0rem !important;padding:0;"></td>
                     </tr>
                     <tr>
                        <td>
                           <div id="head2">
                              July,2019
                           </div>
                        </td>
                        <td colspan="2" style="padding-left: 0rem !important;padding-right: 0rem !important;padding:0;"></td>
                     </tr>
                     <tr>
                        <td>
                           <div id="head2">
                              June,2019
                           </div>
                        </td>
                        <td colspan="2" style="padding-left: 0rem !important;padding-right: 0rem !important;padding:0;"></td>
                     </tr>
                     <tr>
                        <td>
                           <div id="head2">
                              May,2019
                           </div>
                        </td>
                        <td colspan="2" style="padding-left: 0rem !important;padding-right: 0rem !important;padding:0;"></td>
                     </tr>
                     <tr>
                        <td>
                           <div id="head2">
                              April,2019
                           </div>
                        </td>
                        <td colspan="2" style="padding-left: 0rem !important;padding-right: 0rem !important;padding:0;"></td>
                     </tr>
                     <tr>
                        <td>
                           <div id="head2">
                              March,2019
                           </div>
                        </td>
                        <td colspan="2" style="padding-left: 0rem !important;padding-right: 0rem !important;padding:0;"></td>
                     </tr>
                     <tr>
                        <td>
                           <div id="head2">
                              February,2019
                           </div>
                        </td>
                        <td colspan="2" style="padding-left: 0rem !important;padding-right: 0rem !important;padding:0;"></td>
                     </tr>
                     <tr>
                        <td>
                           <div id="head2">
                              January,2019
                           </div>
                        </td>
                        <td colspan="2" style="padding-left: 0rem !important;padding-right: 0rem !important;padding:0;"></td>
                     </tr>
                     <tr>
                        <td>
                           <div id="head2">
                              December,2018
                           </div>
                        </td>
                        <td colspan="2" style="padding-left: 0rem !important;padding-right: 0rem !important;padding:0;"></td>
                     </tr>
                     <tr>
                        <td>
                           <div id="head2">
                              November,2018
                           </div>
                        </td>
                        <td colspan="2" style="padding-left: 0rem !important;padding-right: 0rem !important;padding:0;"></td>
                     </tr>
                  </tbody>
                  <tbody>
                  </tbody>
               </table>
            </div>
         </div>
         <button class="btn btn-success btn-sm pull-right  mt-3"> Save</button>
      </div>
   </div>
</div>
</div>
@endsection
@section('jscript')
@endsection
