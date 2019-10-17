@extends('layouts.app')

@section('content')

<section>
    <div class="container">
        <div class="container">
            <div class="alertMsgBox hide"  id="msgBlockSuccess">
            </div>   
        </div> 
        <div class="container">
            <div class="alertMsgBox hide"  id="msgBlockError"></div>   
        </div>
        <div class="row">

            <div id="header" class="col-md-3">
                @include('layouts.user-inner.left-corp-menu')
                <!-- <div class="list-section">
                  <div class="kyc">
                        <h2>KYC</h2>
                        <p class="marT15 marB15">Individual Natural Person (director, shareholder, Ultimate Beneficial Owner)</p>
                        <ul class="menu-left">
                          <li><a  href="#">Company Details</a></li>
                              <li><a href="#">Address Details</a></li>
                              <li><a class="active" href="#">Shareholding Structure</a></li>
                              <li><a href="#">Financial Information</a></li>
                               <li><a href="#">Documents & Declaration</a></li>
                        </ul>
                      
                     </div>
                </div>-->
            </div>
            <div class="col-md-9 dashbord-white">
                <div class="form-section">

                    <?php
                    //echo '<pre>';
                    //// print_r($nextShare);
                    // echo '</pre>';
                    ?>
                    {!!
                    Form::open(
                    array(
                    'name' => 'shareholderForm',
                    'id' => 'shareholderFormAjax',
                    'autocomplete' => 'off','class'=>'needs-validation form'
                    ))
                    !!}

                    @if(is_array($nextShare) && count($nextShare))
                    <?php
                    foreach ($nextShare as $obj) {
                        ?>  
                        <div class="row marB10">
                            <div class="col-md-12">
                                <h3 class="h3-headline">Shareholding Structure - {{$obj['company_name']}}</h3>
                            </div>
                        </div>

                        {{Form::hidden('share_parent_id[]',$obj['corp_shareholding_id'])}}
                        {{Form::hidden('share_level[]',($obj['share_level']+1))}}

                        <div id="childInfo_{{$obj['corp_shareholding_id']}}" class="is_child">



                            <div class="clonedclonedSocialmedias-{{$obj['corp_shareholding_id']}} Shareholding-cls marB40 padB30 border-bottom" id="clonedSocialmedias{{$obj['corp_shareholding_id']}}_0">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">

                                            {{ Form::label('shareType'.$obj['corp_shareholding_id'].'_0', 'Individual/Company', array('class' => ''))}}
                                            {!!
                                            Form::select('shareType'.$obj['corp_shareholding_id'].'_0',
                                            [''=>'Select Individual/company','1'=>'Individual','2'=>'Company'],'',
                                            array('id'=>'shareType'.$obj['corp_shareholding_id'].'_0','class'=>'form-control'))
                                            !!}
                                        
                                             <span class="text-danger" id="errorshareType{{$obj['corp_shareholding_id']}}_0"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">

                                            {{ Form::label('companyName'.$obj['corp_shareholding_id'].'_0', "Individual name/company name", array('class' => ''))}}
                                            {{ Form::text('companyName'.$obj['corp_shareholding_id'].'_0','', ['class' => 'form-control','placeholder'=>'Enter name','id'=>'companyName'.$obj['corp_shareholding_id'].'_0']) }}
                                            <span class="text-danger" id="errorcompanyName{{$obj['corp_shareholding_id']}}_0"></span>
                                        </div>
                                    </div>
                                </div>					

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">

                                            {{ Form::label('passportNo'.$obj['corp_shareholding_id'].'_0', "Passport No./ License No.", array('class' => ''))}}
                                            {{ Form::text('passportNo'.$obj['corp_shareholding_id'].'_0','', ['class' => 'form-control','placeholder'=>'Enter Passport No./ License No.','id'=>'passportNo'.$obj['corp_shareholding_id'].'_0']) }}
                                            <span class="text-danger" id="errorpassportNo{{$obj['corp_shareholding_id']}}_0"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">

                                            {{ Form::label('sharePercentage'.$obj['corp_shareholding_id'].'_0', "Sharingholding Percentage", array('class' => ''))}}
                                            {{ Form::text('sharePercentage'.$obj['corp_shareholding_id'].'_0','', ['class' => 'form-control','placeholder'=>'Enter Percentage','id'=>'sharePercentage'.$obj['corp_shareholding_id'].'_0']) }}
                                            <span class="text-danger" id="errorsharePercentage{{$obj['corp_shareholding_id']}}_0"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group inputborder-left">
                                            {{ Form::label('shareValue'.$obj['corp_shareholding_id'].'_0','Value in USD', array('class' => ''))}}
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control number" name="shareValue{{$obj['corp_shareholding_id']}}_0" placeholder="Enter Value" id="shareValue{{$obj['corp_shareholding_id']}}_0">
                                            </div>
                                            <span class="text-danger" id="errorshareValue{{$obj['corp_shareholding_id']}}_0"></span>
                                            <!-- 				 <a href="#" class="pull-right marT5 text-color shareholderCls">+ Add Shareholder</a> -->
                                        </div>
                                    </div>
                                   
                                </div>	
                            </div>	
                            {{Form::hidden('rows'.$obj['corp_shareholding_id'].'','1')}}
                            <div class="row marT20 marB5">
                                <div class="col-md-12">
                                    <span class="add-socialmedia pull-right marT10 text-color" data="{{$obj['corp_shareholding_id']}}" data-row="1"  style="">+Add Shareholder</span>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    @else 
                    <div class="row marB10">
                        <div class="col-md-12">
                            <h3 class="h3-headline">Shareholding Structure</h3>
                        </div>
                    </div>
                    {{Form::hidden('share_parent_id[]','0')}}
                    {{Form::hidden('share_level[]','0')}}
                    <div id="childInfo_0" class="is_child">
                        <div class="clonedclonedSocialmedias-0 Shareholding-cls marB40 padB30 border-bottom">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">

                                        {{ Form::label('shareType0_0', 'Individual/Company', array('class' => ''))}}
                                        {!!
                                        Form::select('shareType0_0',
                                        [''=>'Select Individual/company','1'=>'Individual','2'=>'Company'],'',
                                        array('class'=>'form-control'))
                                        !!}

                                    <span class="text-danger" id="errorshareType0_0"></span>  
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        {{ Form::label('companyName0_0', "Individual name/company name", array('class' => ''))}}
                                        {{ Form::text('companyName0_0','', ['class' => 'form-control','placeholder'=>'Enter name','id'=>'companyName0_0']) }}
                                        <span class="text-danger" id="errorcompanyName0_0"></span> 
                                    </div>
                                </div>
                            </div>					

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        {{ Form::label('passportNo0_0', "Passport No./ License No.", array('class' => ''))}}
                                        {{ Form::text('passportNo0_0','', ['class' => 'form-control','placeholder'=>'Enter Passport No./ License No.','id'=>'passportNo0_0']) }}
                                        <span class="text-danger" id="errorpassportNo0_0"></span> 
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">

                                        {{ Form::label('sharePercentage0_0', "Shareholding Percentage", array('class' => ''))}}
                                        {{ Form::text('sharePercentage0_0','', ['class' => 'form-control','placeholder'=>'Enter Percentage','id'=>'sharePercentage0_0']) }}
                                        <span class="text-danger" id="errorsharePercentage0_0"></span> 
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group inputborder-left">
                                        {{ Form::label('shareValue0_0','Value in USD', array('class' => ''))}}
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="text" class="form-control number" name="shareValue0_0" id="shareValue0_0" placeholder="Enter Value">
                                            
                                        </div>
                                        <span class="text-danger" id="errorshareValue0_0"></span> 
                                        <!-- <a href="#" class="pull-right marT5 text-color shareholderCls">+ Add Shareholder</a> -->
                                    </div>
                                </div>
                            </div>	
                        </div>	

                        <div class="row marT20 marB5">
                            <div class="col-md-12">
                                <span class="add-socialmedia pull-right marT10 text-color" data="0" style="">+Add Shareholder</span>
                            </div>
                        </div>


                    </div>
                     {{Form::hidden('rows0','1')}}
                    @endif






                    <div class="row marT60 marB20">
                        <div class="col-md-12 text-right">

                            <a href="{{route('company-address-show')}}" class="btn btn-prev">Previous</a>	
                            {{ Form::submit('Save',['class'=>'btn btn-save','name'=>'save']) }}
                            {{ Form::submit('Save & Next',['class'=>'btn btn-save','name'=>'save_next']) }}
                        </div>
                    </div>

                    {{Form::close()}}
                </div>
            </div>

        </div>	
    </div>

</section>
@endsection

@section('pageTitle')
Shareholding Structure
@endsection

@section('jscript')
<script src="{{ asset('backend/theme/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('frontend/outside/js/validation/shareHolding.js')}}"></script>
<script src="{{ asset('frontend/outside/js/validation/shareholderForm.js')}}"></script>
<script>
var messages = {
    social_media_form_limit: "{{ config('common.SOCIAL_MEDIA_LINK') }}",
    document_form_limit: "{{ config('common.DOCUMENT_LIMIT') }}",
    shareholder_save_ajax: "{{URL::route('shareholder_save_ajax')}}",
};

</script>
@endsection

