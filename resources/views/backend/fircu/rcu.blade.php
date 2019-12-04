@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-subnav')
    <!-- partial -->
    <div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
        <li>
            <a href="{{ route('backend_fi', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">FI Residence</a>
        </li>
        <li>
            <a href="{{ route('backend_rcu', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="active">RCU Document</a>
        </li>
    </ul>


<div class="row grid-margin mt-3">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="rcuList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th>RCU ID</th>
                                        <th>Document Type</th>
                                        <th>Agency Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i= 1;
                                    @endphp
                                    @foreach ($data as $key => $value) 
                                    
                                        <tr role="row" class="odd">
                                            <td class="sorting_1"><input type="checkbox" name="documentIds" value="{{ $value->rcuDoc->id }}">{{ $i }}.</td>
                                            <td>{{ $value->rcuDoc->doc_name }}</td>                                 
                                            <td>abc company</td>                                      
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
                                                        <td width="25%"><b>File Name</b></td>
                                                        <td width="25%"><b>Upload On </b></td>
                                                        <td width="25%">Download</td>
                                                        <td align="center" width="25%">Action</td>
                                                     </tr>
                                                    @foreach ($value->documents as $key1 => $document) 
                                                     <tr>
                                                      <td width="25%">{{ $document->userFile->file_name }}</td>
                                                      <td width="25%">Tue, Nov 12, 2019, 2:56 AM</td>
                                                      <td width="25%"><a href="#"><i class="fa fa-download"></i></a></td>
                                                      <td align="center" width="25%"><a class="mr-2" href="#"><i class="fa fa-eye"></i></a>
                                                      </td>
                                                     </tr>
                                                     @endforeach
                                                  </tbody>
                                               </table>
                                            </td>
                                        </tr>
                                    @php
                                        $i++;
                                    @endphp
                                    @endforeach
                                </tbody>
                            </table>
                            <div id="rcuList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="form-group text-right">
                            <button class="btn btn-success btn-sm " onclick="triggerRCU()">Trigger for FI</button>
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
    <script>
        var messages = {
        };
    </script> 
    <script src="{{ url('backend/js/fi-rcu.js') }}"></script>
@endsection