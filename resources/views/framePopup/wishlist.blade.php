@extends('layouts.frame')
@section('content')
<div class="container-fluid">
    <div class="row">        
        <div class="authfy-container col-md-12">
            <div class="  authfy-panel-right wishlist-form p-0">
                <div class="authfy-login ">
                    <div class="  active">

                        @if(Session::has('message_div'))
<!--                        <div class="alert alert-success alert-dismissible">                            
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                            {{ Session::get('message_div') }}  
                        </div>-->
                        @endif
                    </div>

                    <div style="margin-top: 12px;" class=" ">
                        <form class="wishForm" autocomplete="off" method="POST" action="{{route('save_wishlist')}}" id="frmWish">
                            {{ csrf_field() }}
                            <div class="col-xs-12 col-sm-12">


                                <label class="control-label">Why you are here?</label>
                                <div class="form-group">
                                    {!!
                                    Form::select('type_id',
                                    [''=>'Why you are here?'] + Helpers::getUserType(),
                                    '',
                                    array('id' => 'type_id',
                                    'class'=>'form-control select2Cls'))
                                    !!} 
                                </div>


                            </div>

                            <div class="col-xs-12 col-sm-12">
                           


                                     
                                        <label class="control-label"> Email </label>
                                        <div class="form-group">
                                            <input type="text" value="" maxlength="50" name="email" id="email" class="form-control" placeholder="Email">
                                        </div>
                                    

                                
                            </div>
                            <div class="col-xs-12 col-sm-12">


                                <label class="control-label"> Phone</label>
                                <div class="form-group">
                                    <input type="text" value="" maxlength="10" name="phone" id="phone" class="form-control numcls" placeholder="Phone">
                                </div>


                            </div>
                            <div class="col-xs-12 col-sm-12">


                                <label class="control-label"> Areas of Interest </label>
                                <div class="form-group">
                                    <input type="text" name="areas_of_interest" id="areas_of_interest" data-role="tagsinput" value="" class="form-control" placeholder="Areas of Interest">
                                </div>


                            </div>
                            <div class="col-xs-12 col-sm-12">
                                <button type="submit" class="btn btn-lg btn-primary btn-block">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- ./row -->
</div>
@endsection
@section('pageTitle')
Inventrust Feebback
@endsection
@section('jscript')
<script>
   
var messages = {
    is_accept: "{{ Session::get('is_accept') }}",
};
     $(document).ready(function(){
        if(messages.is_accept == 1){
     var parent =  window.parent;     
       parent.jQuery("#feedback").modal('hide');  
       window.parent.location.reload();
    }
        
    })
</script>
<style type="text/css">
    .wishlist-form  label {

        font-weight: 600;

    }
    .wishlist-form .btn {
    width: auto;
    display: inline-block;
    vertical-align: top;
    margin: 0 10px 0 0;
        margin-top: 0px;
    padding: 7px 18px;
    font-size: 16px;
    margin-top: 10px;
}
    .mt-4{margin-top: 40px;}
    .wishlist-form .authfy-login{

        height: auto;

    }
    .wishlist-form {
        border-radius: 7px;
        padding: 20px;
    }
    .wishlist-form h3 {
        color: #25a5de;
    }
    .mb-0{margin-bottom: 0px}
    .btn-primary {

        background-color: #48a0bc;
        border-color: #48a0bc;


    }
    .form-control {
        display: block;
        width: 100%;
        height: 38px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 0;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
        box-shadow: none;
        -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
        -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    }
    form#frmWish label.error {
    position: absolute;
    bottom: -25px;
    left: 16px;
    color: #f00;
    font-weight: normal;
    font-size: 13px;
    left: 9px;
}
    body .bootstrap-tagsinput .tag {
        color: #fff;
        background: #898989f2;
        padding: 2px 8px;
        font-size: 12px;
        border-radius: 3px;
        font-weight: 600;
    }

    .alert.alert-success{ 
        position: fixed;
        z-index: 9;
        padding: 21px 28px 20px 11px;top: 10px;
        right: 17px;
        font-size: 15px;
        border-radius: 6px;
        box-shadow: 0 0 16px #bdb8b8;
        background: #fff;
        white-space: nowrap;
        min-width: 270px;
        border-left: solid 5px #76a75e;color: #5a5656;
        font-size: 18px;}
</style>
<link rel="stylesheet" href="{{ asset('frontend/inside/css/bootstrap-tagsinput.css') }}">
<script src="{{ asset('backend/js/validation/wishlist.js') }}"></script>
<script src="{{ asset('frontend/inside/js/bootstrap-tagsinput.js') }}"></script>
<script src="{{ asset('frontend/outside/js/login.js') }}"></script>
@endsection
