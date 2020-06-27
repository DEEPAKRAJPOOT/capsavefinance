@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'summary'])
<div class="content-wrapper">
    <div class="row ">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive ps ps--theme_default w-100">
                      @include('lms.customer.limit_details')
                    </div>
                </div>	
                <div class="card-body">
                    <table id="invoice_history" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <td class="sub-title-bg" colspan="5">Associate Anchor</td>
                            </tr>
                            <tr role="row">
                                <th>Anchor Id</th>
                                <th>Anchor Name </th>
                                <th>Program </th>
                                <th>Program Limit</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if($anchors->count() >0)
                            @foreach ($anchors AS $anchor)
                            @if(!empty($anchor->anchor) && ( ($userRole && $userRole->id == 11 && \Auth::user()->anchor_id == $anchor->anchor->anchor_id) || (!$userRole || ($userRole && $userRole->id != 11)) ))                            
                            <tr role="row" class="odd">
                                <td class="sorting_1">{{ $anchor->anchor->anchor_id }}</td>
                                <td>{{ $anchor->anchor->comp_name }}</td>
                                <td>{{ $anchor->program->prgm_name }}</td>
                                <td><i class="fa fa-inr"></i> {{ $anchor->program->anchor_limit }}</td>

                            </tr>
                            @endif
                            @endforeach
                            @else
                            <tr>
                                <td  colspan = "5"> No Anchor Found:</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">   

                            <div class="table-responsive ps ps--theme_default w-100">
                                <table class="table text-center overview-table table-hover">
                                    <thead class="thead-primary">
                                        <tr>
                                            <td class="sub-title-bg" colspan="4">Applications</td>
                                        </tr>
                                        <tr>
                                            <th class="text-left">Application Id.</th>
                                            <th class="text-left">Biz Entity Name</th>
                                            <th class="text-left">Status</th>
                                            <th class="text-left">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if($application->count() >0)
                                        @foreach ($application AS $app)
                                        <tr>
                                            <td class="text-left">{{$app->app_id}}</td>
                                            <td class="text-left">{{$app->business->biz_entity_name}}</td>
                                            <td class="text-left">
                                                @if($app->status == 1)
                                                <button type="button" class="btn btn-success btn-sm">Complete</button>
                                                @elseif($app->status == 2)
                                                <button type="button" class="btn btn-success btn-sm">Sanctioned</button>                                                                                              
                                                @else
                                                <button type="button" class="btn btn-info btn-sm">Not Complete</button>
                                                @endif 
                                            </td>
                                            <td class="text-left">
                                                <div class="d-flex inline-action-btn">
                                                    <a title="View Application Details" href="{{ route('company_details', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id ]) }}" class="btn btn-action-btn btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>

                                                </div>	           
                                            </td>  
                                        </tr>	
                                        @endforeach
                                        @else
                                        <tr>
                                            <td  colspan = "4"> No Application Found:</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection