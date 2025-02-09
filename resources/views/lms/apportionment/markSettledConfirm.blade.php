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

<div class="content-wrapper">

    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manual Apportionment</h3>
            <small>Unsettled Transactions Confirmation</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Payment</li>
                <li style="color:#374767;">Unsettled Repayment</li>
                <li class="active">Unsettled Transactions</li>
            </ol>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <div class="sticky">	
                @include('lms.apportionment.common.userDetails')
                @include('lms.apportionment.common.paymentDetails')
            </div>
            <div class="row" id="unsettlementConfirmRow">
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
                                        <table class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                            <thead>
                                                <tr role="row">                                                   
                                                    {{-- <th>Trans Date</th> --}}
                                                    <th>Trans Date</th>       
                                                    <th>Payment Due Date</th>       
                                                    <th>Invoice No</th>       
                                                    <th>Trans Type</th>		
                                                    <th>Total Repay Amt</th>
                                                    <th>Outstanding Amt</th>
                                                    <th>Payment</th>
                                                    <th>Validated</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($transactions as $trans)
                                                <tr>
                                                    {{-- <td>{{ Carbon\Carbon::now()->format('d-m-Y') }}</td> --}}
                                                    <td>{{ Carbon\Carbon::parse($trans['value_date'])->format('d-m-Y') }}</td>
                                                    <td>{{ Carbon\Carbon::parse($trans['payment_due_date'])->format('d-m-Y') }}</td>
                                                    <td>{{ $trans['invoice_no'] }}</td>
                                                    <td>{{ $trans['trans_name'] }}</td>
                                                    <td>₹ {{ number_format($trans['total_repay_amt'],2) }}</td>
                                                    <td>₹ {{ number_format($trans['outstanding_amt'],2) }}</td>
                                                    <td>₹ {{ number_format($trans['pay'],2) }}</td>
                                                    <td>
                                                        @if($trans['is_valid'])
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                        @else
                                                            <i class="fa fa-times" aria-hidden="true" style="color:red"></i>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('apport_mark_settle_save',[ 'user_id' => $userId , 'payment_id' => $paymentId,'sanctionPageView'=>$sanctionPageView]) }}" method="post" >
             @csrf	
                <div class="row mt-2">
                    <div class="col-md-10">
                    <label>
                        <input type="checkbox" name="confirm" required>
                        <span>The system will treat all rows that are marked (<i class="fa fa-check" aria-hidden="true"></i>) in the Validated column and if the value of the unapplied amount is greater than zero, it will be converted to Non-factored amount. Click on the Confirm button for further processing. </span>
                    </label>
                    </div>
                    <div class="col-md-2" >
                        <input type="submit" value="Confirm" class="btn btn-success btn-sm pull-right" onclick="this.disabled='disabled';this.form.submit();">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('jscript')
<script>

    var messages = {
        url: "{{ URL::route('apport_unsettled_list') }}",
        user_id: "{{$userId}}",
        payment_id: "{{$paymentId}}",
        payment_amt: "{{$payment['payment_amt']}}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };

    $(document).ready(function() {
        var stickyTop = $('.sticky').offset().top - 70;
        $(window).scroll(function() {
            var windowTop = $(window).scrollTop();
            if (stickyTop < windowTop && $("#unsettlementConfirmRow").height() + 
            $("#unsettlementConfirmRow").offset().top - $(".sticky").height() > windowTop) {
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
@endsection