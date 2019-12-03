@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-subnav')
    <!-- partial -->
    <div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
        @can('backend_fi')
        <li>
            <a href="{{ route('backend_fi', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="active">FI Residence</a>
        </li>
        @endcan
        @can('backend_rcu')
        <li>
            <a href="{{ route('backend_rcu', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">RCU Document</a>
        </li>
        @endcan
    </ul>


<div class="row grid-margin mt-3">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="fiList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th>Residence ID</th>
                                        <th>Address Type</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($fiLists as $key=>$fiList)
                                    <?php
                                     $addrType = ['Company (Registered Address)', 'Company (Communication Address)', 'Company (GST Address)', 'Company (Warehouse Address)', 'Company (Factory Address)','Promoter Address'];
                                     ?>
                                    <tr role="row" class="odd">
                                        <td><input type="checkbox" value="{{$fiList->biz_addr_id}}">{{$fiList->biz_addr_id}}</td>
                                        <td>{{$addrType[$fiList->address_type]}}</td>
                                        <td>{{($fiList->biz_owner_id)? $fiList->owner->first_name: $fiList->business->biz_entity_name}}</td>                                      
                                        <td>{{$fiList->addr_1.' '.$fiList->city_name.' '.$fiList->state->name.' '.$fiList->pin_code}}</td>                                      
                                        <td>
                                          <div class="btn-group"><label class="badge badge-warning">Pending&nbsp; &nbsp;</label></div>
                                        </td>
                                        <td>
                                            <div class="btn-group ml-2 mb-1">
                                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                                </button>
                                                <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a class="dropdown-item" href="#" value="0">Pending</a>
                                                    <a class="dropdown-item" href="#" value="1">Inprogress</a>
                                                    <a class="dropdown-item" href="#" value="2">Positive</a>
                                                    <a class="dropdown-item" href="#" value="3">Negative</a>
                                                    <a class="dropdown-item" href="#" value="4">Cancelled</a>
                                                    <a class="dropdown-item" href="#" value="5">Refer to Credit</a>
                                                </div>
                                                <div class="d-flex file-upload-cls">
                                                    <div class="file-browse float-left mr-3 ml-4">
                                                        <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                                        <input type="file" title="Download RCU Report" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                    </div>
                                                    <div class="file-browse float-left ">
                                                        <button class="btn-upload  title=" ffff"="" btn-sm"="" type="button"> <i class="fa fa-upload"></i></button>
                                                        <input type="file" id="file_1" dir="1" title="Upload RCU Report" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                    </div>
                                                </div>
                                            </div>
                                        </td> 
                                        <td align="right"><span class="trigger"></span></td> 
                                    </tr>
                                    <tr class="dpr" style="display: none;">
                                        <td colspan="7" class="p-0">
                                           <table class="overview-table remove-tr-bg" cellpadding="0" cellspacing="0" border="0" width="100%">
                                              <tbody>
                                                 <tr>
                                                    <td width="30%"><b>Agency Name</b></td>
                                                    <td width="25%"><b>User Name</b></td>
                                                    <td width="20%"><b>Updated On</b></td>
                                                    <td width="10%"><b>Download</b></td>
                                                    <td align="center" width="15%" style="border-right: 1px solid #e9ecef;"><b>Status</b></td>
                                                 </tr>
                                                 <tr>
                                                    <td width="30%">Agency Name</td>
                                                    <td width="25%">User Name</td>
                                                    <td width="20%">Updated On</td>
                                                    <td width="10%"><a href="#"><i class="fa fa-download"></i></a></td>
                                                    <td align="center" width="15%" style="border-right: 1px solid #e9ecef;">Pending</td>
                                                 </tr>
                                              </tbody>
                                           </table>
                                        </td>
                                    </tr>
                                @empty
                                <tr><td colspan="7">No data found.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="form-group text-right">
                           <button class="btn btn-success btn-sm " data-toggle="modal" data-target="#myModal">Trigger for FI</button>
                            <!--<a href="#" class="btn btn-success" data-toggle="modal" data-target="#myModal1" style="clear: both;">Report Uploads</a>-->
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('jscript')

@endsection