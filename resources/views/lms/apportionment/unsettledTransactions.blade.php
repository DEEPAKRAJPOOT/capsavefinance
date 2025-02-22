@extends('layouts.backend.admin-layout')
@section('additional_css')
<style>
    .Lh-3{
        line-height:2.5;
    }

    .sticky {
        top: 0;
    }
    
    .sticky-section {        
        background-color: #fff;
        padding: 1.3rem 1.5rem;
        z-index: 999;
        margin-right: 30px;
        margin-top: 55px;
    }
</style>
@endsection

@section('content')
@if($sanctionPageView == true)
    @include('layouts.backend.partials.admin_customer_links',['active'=>'unsettledTrans'])
                
@endif
@if(Session::has('untrans_error'))
        <div class="content-wrapper-msg">
        <div class=" alert-danger alert" role="alert">
        <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ Session::get('untrans_error') }}
        </div>
        </div>
    @endif
<div class="content-wrapper">
    @if(!$sanctionPageView == true)
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manual Apportionment</h3>
            <small>Unsettled Transactions</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Payment</li>
                <li class="active">Manage Repayment</li>
            </ol>
        </div>
    </section>
    @endif
    <div class="card">
        <div class="card-body"> 
          @if($sanctionPageView)
             <div class="table-responsive ps ps--theme_default w-100">
                      @include('lms.customer.limit_details')
                    </div>
             @endif
            <div class="sticky">	
                @if(!$sanctionPageView)      
                    @include('lms.apportionment.common.userDetails')
                    @if($paymentId)
                    @include('lms.apportionment.common.paymentDetails')
                    @endif
                @endif
            </div> 
            <form id="unsettlementFrom" action="" method="post" onsubmit="return apport.validateMarkSettled(this)">
             @csrf	
            <div class="row">
                @if($paySug && $paymentId)
                    @include('lms.apportionment.common.listUnsettledTransactions')
                @elseif(!$paymentId)
                    @include('lms.apportionment.common.listUnsettledTransactions')
                @endif
            </div>
            <div class="row pull-right action-btn mt-2">
                <div class="col-md-12" >
                    @if($paymentId) 
                    <span class="pull-left mr-3 mt-1" id="msg_action"></span>
                        @can('apport_mark_settle_confirmation')
                            @if($paymentApportionment)
                                <input type="button" name="action" value="Mark Settled" class="btn btn-success btn-sm" onclick="javascript:alert('You cannot perform this action as you have not uploaded  the unsettled payment apportionment CSV file.')" id="MarkSettled">
                            @else
                                <input type="submit" name="action" value="Mark Settled" class="btn btn-success btn-sm" id="MarkSettled">
                            @endif
                        @endcan                         
                        @if (!$paymentApportionment)
                            @can('download_apport_unsettled_trans')
                            <a href="{{ URL::route('download_apport_unsettled_trans',[ 'user_id' => $userId , 'payment_id' => $paymentId, 'sanctionPageView' => $sanctionPageView ]) }}" class="btn btn-success btn-sm float-left mr-2 @if($paySug)disabled @endif" id="dwnldUnTransCsv">Download CSV</a>
                            @endcan
                            @else
                            @can('delete_download_csv_apport_unsettled_trans')
                            <a href="javascript:void(0);" class="btn btn-danger btn-sm float-left mr-2 @if($paySug)disabled @endif" id="dltUnTransCsv">Delete CSV</a>
                            @endcan
                        @endif
                        @can('upload_apport_unsettled_trans')
                        <a data-toggle="modal" data-target="#uploadUnsettledTransactionsFrame1" data-height="" data-width="100%" data-placement="top" class="btn btn-success btn-sm float-left mr-2 @if($paySug)disabled @endif" id="uploadUnTransCsv">Upload CSV</a>
                        @endcan
                    @endif
                    @if($sanctionPageView) 
                        @can('apport_trans_waiveoff')
                        @if($paymentApportionment)
                            <input type="button" value="Waived Off" class="btn btn-success btn-sm" onclick="javascript:alert('You cannot perform this action as you have not uploaded  the unsettled payment apportionment CSV file.')">
                        @else
                            <input type="button" value="Waived Off" class="btn btn-success btn-sm" onclick="apport.onWaveOff()">
                        @endif    
                        @endcan
                        @if($userDetails['status_id'] == 41 && in_array($userDetails['wo_status_id'],[config('lms.WRITE_OFF_STATUS.APPROVED'),config('lms.WRITE_OFF_STATUS.TRANSACTION_SETTLED')]))
                            <input type="submit" name="action" value="Write Off" class="btn btn-success btn-sm">
                        @endif
                    @endif
                </div>
            </div>
            </form>
        </div>
    </div>
    <a data-toggle="modal" data-target="#viewDetailFrame" data-url="" data-height="400px" data-width="100%" data-placement="top" class="view_detail_transaction"></a>
</div>
{!!Helpers::makeIframePopup('viewDetailFrame','Transaction Detail', 'modal-md')!!}
<div class="modal fade" id="uploadUnsettledTransactionsFrame1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upload CSV</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      @include('lms.apportionment.uploadApportUnsettledTrans')
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
        paySug: "{{ $paySug }}",
        url: "{{ URL::route('apport_unsettled_list',['paySug' => $paySug]) }}",
        confirm_writeoff: "{{ URL::route('apport_mark_writeOff_confirmation',[ 'user_id' => $userId , 'payment_id' => $paymentId, 'sanctionPageView' => $sanctionPageView, 'paySug' => $paySug ]) }}",
        confirm_settle: "{{ URL::route('apport_mark_settle_confirmation',[ 'user_id' => $userId , 'payment_id' => $paymentId, 'sanctionPageView' => $sanctionPageView, 'paySug' => $paySug ]) }}",
        trans_waiveoff_url: "{{ URL::route('apport_trans_waiveoff',['sanctionPageView' => $sanctionPageView, 'paySug' => $paySug]) }}",
        user_id: "{{$userId}}",
        payment_id: "{{$paymentId}}",
        payment_amt: "{{ $payment_amt }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        old_data: {!! json_encode($oldData) !!},
        token: "{{ csrf_token() }}",
        deleteCsvApport: "{{ URL::route('delete_download_csv_apport_unsettled_trans',[ 'user_id' => $userId , 'payment_id' => $paymentId, 'payment_appor_id' => ($paymentApportionment->payment_aporti_id)??0, 'sanctionPageView' => $sanctionPageView]) }}",
        downloadCsvApport : "{{ URL::route('download_apport_unsettled_trans',[ 'user_id' => $userId , 'payment_id' => $paymentId, 'sanctionPageView' => $sanctionPageView ]) }}",
        sanctionPageView: "{{  $sanctionPageView }}",
        payment_appor_id: "{{ ($paymentApportionment->payment_aporti_id)??0 }}",
        apporUnsettleRedirect: "{{ URL::route('apport_unsettled_view',[ 'user_id' => $userId , 'payment_id' => $paymentId, 'sanctionPageView' => $sanctionPageView, 'redirect' => true ]) }}",
    };

    jQuery(document).ready(function ($) {
        setTimeout(() => {
            $('.table-responsive').scrollLeft($('.table-responsive').scrollLeft() + 20);
        }, 1000);
    }); 
    
    $(document).ready(function() {
        var stickyTop = $('.sticky').offset().top - 70;
        $(window).scroll(function() {
            var windowTop = $(window).scrollTop();
            if (stickyTop < windowTop && $("#unsettlementFrom").height() + 
            $("#unsettlementFrom").offset().top - $(".sticky").height() > windowTop) {
                $('.sticky').css('position', 'fixed');
                $('.sticky').addClass('sticky-section');
            } else {
                $('.sticky').css('position', 'relative');
                $('.sticky').removeClass('sticky-section');
            }
        });
    });
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/apportionment.js') }}?id={{ time() }}"></script>
<script type="text/javascript">
$(document).ready(function () {
    //xls|xlsx|
    //and xlsx 
    var validator = $('#uploadUnTransForm').validate({ // initialize the plugin
    rules: {
        upload_unsettled_trans: {
        required: true,
        extension: "csv"
      }
    },
    messages: {
    upload_unsettled_trans: {
    required: "Please select file",
    extension:"Please select only csv format",
    }
    }
    });

$("#uploadUnTransForm").submit(function(){
    if($(this).valid()){
        $("#saveUnsettled").attr("disabled","disabled");
    }
});
});
$('#upload_unsettled_trans').click(function(){
    $('#upload_unsettled_trans').change(function(e) {
    var fileName = e.target.files[0].name;
       $('.val_print').html(fileName);
    });
})
$('#uploadUnTransCsv').click(function(){
    $( "#uploadUnTransForm" ).get(0).reset();
    var validator = $( "#uploadUnTransForm" ).validate();
     validator.resetForm();
     $('.val_print').html('').html('Choose file');
})

</script>
@endsection