@extends('layouts.app')

@section('content')

<section>
    <div class="banner-section">
        <div class="search-box">
            <!--Grid column-->
                       
            <div class="col-md-1 mb-4 add-right">
                <div class="add-img">
                    
                </div>
            </div>
            <!--Grid column-->
        </div>
    </div>
</section>
<!-- SECTION -->
<section>
    <div class="container mt--5 mb-5">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card box-shadow-div">
                    <div class="card-header">
                        <h5 class="subscription"><strong>Confirm Payment </strong></h5>
                    </div>
                    <form action="{{route('confirm_payment')}}"  autocomplete="off" enctype="multipart/form-data" method="POST" class="input-group" name="rightsForm" id="rightsForm">
                        {{ csrf_field() }}
                       
                        <div class="card-body">
                            <div class="row">
                                <?php
                                $cancelurl='';
                                $message='';
                                if(@$payData['curruntRoute']=='add_right'){
                                    $cancelurl=route('add_right');
                                }elseif(@$payData['curruntRoute']=='save_buy_right_popup' ||@$payData['curruntRoute']=='save_report'|| @$payData['curruntRoute']=='accept_term_condition'){
                                    $cancelurl=route('right_details',['user_id' => @$rightData['user_id'], 'right_id' => @$rightData['right_id']]);
                                }
                                    
                                    
                                
                                   
                                ?>
                                <div class="col-md-12">
                                    
                                
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                     <strong> Right's title</strong>
                                      </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                               {{@$rightData['title']}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                            <strong>Type of Right</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                               {{@$rightData['right_type']}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                           <strong> Innovator</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                               {{@$rightData['inventor']}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                            <strong>Co-Innovators</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                               {{@$rightData['assignee']}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                           <strong> Group</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                               {{@$rightData['right_group']}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                            <strong>Right's Creation Date</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                               {{(@$rightData['date'])?Helpers::getDateByFormat(@$rightData['date'],'Y-m-d','d/m/Y') :''}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                            <strong>Right's Expiration Date</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                               {{(@$rightData['expiry_date'])?Helpers::getDateByFormat(@$rightData['expiry_date'],'Y-m-d','d/m/Y') :''}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                           <strong> This Right is for</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                               {{@$rightData['right_for']}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                            <strong>Hyperlink to the Right</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                               {{@$rightData['url']}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                               <strong> Description</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                               {{@$rightData['description']}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                              <strong> Keywords</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                               {{@$rightData['keywords']}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                              <strong>Payment Amount</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                              $ {{@$payData['fee']}}
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                    if(@$payData['curruntRoute']=='save_buy_right_popup'){
                                        $strname='';
                                        $purchase_type='';
                                        if(@$payData['purchase_type']=='1'){
                                            $strname="Years";
                                            $purchase_type='Exclusive';
                                        }else if(@$payData['purchase_type']=='2'){
                                         $strname="Months";
                                         $purchase_type='Non Exclusive';
                                        }
                                    ?>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                              <strong>Purchase Type</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                              {{$purchase_type}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                              <strong>Number of {{$strname}} for licensing</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">:</div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                              {{$payData['buyforyear']}}
                                            </div>
                                        </div>
                                    </div>
                                    <?php }?>
                                    
                                    <div class="col-md-12">
                                        <h6 class="subscription mb-3"><strong>Subscription</strong></h6>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                
                                                @if(@$rightData['subscription']=='1')
                                                <label class="control-label"><i class="fa fa-check-circle"></i> Basic</label>
                                                <!--<p> <i class="fa fa-money" aria-hidden="true"></i> Upload your Rights with a $1 deposit which will be refunded once validated</p>-->
                                                @elseif(@$rightData['subscription']=='2') 
                                                <label class="control-label"><i class="fas fa-check-circle"></i> Pro</label>
                                                <p>Number of Month : {{@$rightData['num_of_month']}}</p>
                                                <p><i class="fa fa-money" aria-hidden="true"></i> Upload your Intellectual Property with $96/month as a subscription fee, with lots of benefit.</p>
                                                @elseif(@$rightData['subscription']=='3')
                                                <label class="control-label"><i class="fas fa-check-circle"></i> Enterprise</label>
                                                @else
                                                <label class="control-label"></label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-md-12  Trading">
                                            <div class="heading"><strong>Payment</strong>&nbsp;&nbsp;<span style="font-size:10px;">(You will get redirected to the PayPal gateway for making the payment, in order to complete this transaction.)</span></div>
                                            <div class="">
                                                <div class="row m-0">
                                                
                                                    <div class="col-md-6   left-us">
                                                        <div class="col01">
                                                            <div class="more-detail mr-auto mb-3" >
                                                                <label style="margin-left:280px;"><strong>Total Payment</strong></label>
                                                                
                                                            </div>
                                                            
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6  right-us">
                                                        <div class="col01">

                                                                <div class="more-detail mr-auto mb-3" >
                                                                    <label>$ {{@$payData['fee']}}</label>


                                                                </div>

                                                            
                                                        </div>

                                                    </div>
                                               

                                                </div>
                                            </div>
                                        </div>



                                    
                                    
                                    <!--<div class="col-md-12  Trading">
                                            <div class="heading"><strong>Trading</strong></div>
                                            <div class="">
                                                <div class="row m-0">
                                                <?php if(@$rightData['is_exclusive_purchase']=='1' && @$rightData['is_non_exclusive_purchase']=='1'){?>
                                                    <div class="col-md-6   left-us">
                                                        <div class="col01">
                                                            <div class="more-detail mr-auto mb-3" >
                                                                <?php if(@$rightData['is_exclusive_purchase']=='1'){?>
                                                                <label><i class="fa fa-check-square"></i> Exclusive Licensing</label>
                                                                <?php }else{?>
                                                                <label><i class="fa fa-square"></i> Exclusive Licensing</label>
                                                                <?php }?>
               
                                                            </div>
                                                            <span id="exp" style="<?php if(@$rightData['is_exclusive_purchase']=='1'){echo 'display:block';}else{ echo 'display:none';}?>"  ><strong>$ {{@$rightData['exclusive_purchase_price']}} /Year</strong> </span>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6  right-us">
                                                        <div class="col01">
                                                            
                                                                <div class="more-detail mr-auto mb-3" >
                                                                    <?php if(@$rightData['is_non_exclusive_purchase']=='1'){?>
                                                                    <label><i class="fa fa-check-square"></i> Non-Exclusive Licensing</label>
                                                                    <?php }else{?>
                                                                       <label><i class="fa fa-square"></i> Non-Exclusive Licensing</label>
                                                                   <?php }?>
                                                                    
                                                                    
                                                                </div>
                                                            
                                                            <span id="non_exp" style="<?php if(@$rightData['is_non_exclusive_purchase']=='1'){echo 'display:block';}else{ echo 'display:none';}?>"><strong>$ {{@$rightData['non_exclusive_purchase_price']}} /Month</strong> </span>
                                                        </div>

                                                    </div>
                                                <?php }else if(@$rightData['is_exclusive_purchase']=='1'){?>
                                                    <div class="col-md-12   left-us">
                                                        <div class="col01">
                                                            <div class="more-detail mr-auto mb-3" >
                                                                <?php if(@$rightData['is_exclusive_purchase']=='1'){?>
                                                                <label><i class="fa fa-check-square"></i> Exclusive Licensing</label>
                                                                <?php }else{?>
                                                                <label><i class="fa fa-square"></i> Exclusive Licensing</label>
                                                                <?php }?>
               
                                                            </div>
                                                            <span id="exp" style="<?php if(@$rightData['is_exclusive_purchase']=='1'){echo 'display:block';}else{ echo 'display:none';}?>"  ><strong>$ {{@$rightData['exclusive_purchase_price']}} /Year</strong> </span>
                                                        </div>

                                                    </div>
                                                    
                                                <?php }elseif(@$rightData['is_non_exclusive_purchase']=='1'){?>
                                                    <div class="col-md-12  right-us">
                                                        <div class="col01">
                                                            
                                                                <div class="more-detail mr-auto mb-3" >
                                                                    <?php if(@$rightData['is_non_exclusive_purchase']=='1'){?>
                                                                    <label><i class="fa fa-check-square"></i> Non-Exclusive Licensing</label>
                                                                    <?php }else{?>
                                                                       <label><i class="fa fa-square"></i> Non-Exclusive Licensing</label>
                                                                   <?php }?>
                                                                    
                                                                    
                                                                </div>
                                                            
                                                            <span id="non_exp" style="<?php if(@$rightData['is_non_exclusive_purchase']=='1'){echo 'display:block';}else{ echo 'display:none';}?>"><strong>$ {{@$rightData['non_exclusive_purchase_price']}} /Month</strong> </span>
                                                        </div>

                                                    </div>
                                                <?php }?>
                                                    
                                                </div>
                                            </div>
                                        </div>-->
                                        
                                    
                                </div>
                                
                                
                                
                                
                                  
                                
                                
                                
                         
                                <div class="col-md-12">
                                   
                                    <div class="mt-4 mb-4">
                                        <div class="form-group row m-0">
                                            <div class="col-md-4 mr-auto"> <a href="{{$cancelurl}}"  class="btn btn-x-md btn-default ">Back</a></div>
                                            <div class="col-md-5 text-right">
<!--                                                <input type="submit" name="btnDraft" id="btnDraft" value="Draft" class="btn btn-x-md btn-info save-right-info">-->
                                                <input type="submit" name="btnPost" id="btnPost" class="btn btn-x-md btn-primary submitRights" value="Payment">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!-- Card -->
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
    <!-- The Modal -->
</section>
<!-- / END SECTION -->
@endsection
@section('pageTitle')
Confirm Payment
@endsection
@section('additional_css')
<style>
    .error{color:red;}
    label#type_id-error {
    position: absolute;
    bottom: 20px;
    font-size: 12px;
}
label#cluster_id-error {
    font-size: 12px;
    position: absolute;
    bottom: 20px;
}
label.error{
    font-size: 12px;}
#exclusive-error {
    font-size: 12px;
    position: absolute;
    top: 20px;
    left: 0;
    width: 160px;
}
#attachments0-error {
    font-size: 12px;
    position: absolute;
    top: 35px;
    left: 5px;
    width: 160px;
}
</style>
<style>
    .user-head h4 {
    color: #3eb9ce;
    text-decoration: underline;
}
    .license {background: #fff;}
   .user-head {
    padding: 10px 15px;
    background: #fff;
    margin: 10px;
}
.user-head td {
    color: #666;
    font-size: 14px;
}
    
    </style>

<link rel="stylesheet" href="{{ asset('frontend/inside/css/bootstrap-tagsinput.css') }}">
@endsection
@section('jscript')
<script>
    var messages = {
        req_right_title: "{{ trans('error_messages.req_right_title') }}",
        req_right_type: "{{ trans('error_messages.req_right_type') }}",
        req_right_type_numeric: "{{ trans('error_messages.req_right_type_numeric') }}",

        req_right_number: "{{ trans('error_messages.req_right_number') }}",
        req_right_inventor: "{{ trans('error_messages.req_right_inventor') }}",
        req_right_assignee: "{{ trans('error_messages.req_right_assignee') }}",

        req_right_cluster: "{{ trans('error_messages.req_right_cluster') }}",
        req_right_cluster_numeric: "{{ trans('error_messages.req_right_cluster_numeric') }}",
        req_right_date: "{{ trans('error_messages.req_right_date') }}",
        req_right_description: "{{ trans('error_messages.req_right_description') }}"
    };

    $("#date").datepicker({
        onSelect: function (date) {
            var min = $(this).datepicker('getDate');
            $('#expiry_date').datepicker('option', {minDate: min});
        }
    })

    $('#OpenImgUpload').click(function () {
        $('.myUploadpopup').trigger('click');
    });

</script>

<script src="{{ asset('frontend/inside/js/bootstrap-tagsinput.js')}}"></script>
@endsection
