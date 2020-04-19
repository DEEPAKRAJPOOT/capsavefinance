@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')

<div class="content-wrapper">              
    <section class="content-header">
        <div class="header-icon">
            <i class="fa fa-clipboard" aria-hidden="true"></i>
        </div>
        <div class="header-title">
            <h3 class="mt-2">EOD Process</h3>

            <ol class="breadcrumb">
                <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
                <li class="active">EOD Process</li>
            </ol>
        </div>
        <div class="clearfix"></div>
    </section>
    <div class="row grid-margin ">
        <div class="col-md-12  mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-fields">
                        <form id="frm-sys-start" method="post" action="{{ route('save_process') }}" enctype= multipart/form-data>
                            @csrf 
                            <div class="active" id="details">
                                <div class="form-sections">

                                    <div class="clearfix"></div>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">Current System Date</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">{{ $current_date }}</label>                                                        
                                            </div>
                                        </div>                                        
                                        <div class="col-4">
                                            <div class="form-group">
                                                <input type="hidden" value="1" id="sys_start_flag" name="flag">
                                                <input type="submit" id="submit"   class="btn btn-primary ml-2 btn-sm" value="Start System">                                                 
                                            </div>
                                        </div>                                         
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <form id="frm-sys-start" method="post" action="{{ route('save_process') }}" enctype= multipart/form-data>
                            @csrf 
                            <div class="active" id="details">
                                <div class="form-sections">

                                    <div class="clearfix"></div>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">Running Hours</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="">{{ $running_hours }}</label>                                                        
                                            </div>
                                        </div>                                        
                                        <div class="col-4">
                                            <div class="form-group">
                                                <input type="hidden" value="2" name="flag">
                                                <input type="submit" id="submit"   class="btn btn-primary ml-2 btn-sm" value="Run Eod Process">                                                 
                                            </div>
                                        </div>                                         
                                    </div>
                                </div>
                            </div>
                        </form>   
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $status }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div>
                        
                        <p class="mt-2"><strong>Summary</strong></p> 
                        <hr>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Tally Posting Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->tally_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div>
                        
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Interest Accrual Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->int_accrual_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div>
                        
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Repayment Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->repayment_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Disbursal Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->disbursal_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div>                        
                        
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Charge Posting Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->charge_post_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div> 
                        
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Overdue Interest Accrual Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->overdue_int_accrual_status] : '' }}</label>                                                        
                                </div>
                            </div>                                                     
                        </div> 
                        
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Disbursal Block Status</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">{{ $statusLog ? $statusArr[$statusLog->disbursal_block_status] : '' }}</label>                                                        
                                </div>
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
