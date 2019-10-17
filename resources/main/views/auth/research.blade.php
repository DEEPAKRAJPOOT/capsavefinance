@extends('layouts.guest')

@section('content')
<div class="image-container set-full-height register-page" style="background-image: url('assets/img/paper-1.jpeg')">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">

                <!--      Wizard container        -->
                <div class="wizard-container">

                    <div class="card wizard-card" data-color="orange" id="wizardProfile">
                        @include('auth.progress')
                        <form action="{{route('research_publication')}}" enctype="multipart/form-data" method="post" id="researchForm">
                            {{ csrf_field() }}
                            <div class="tab-content">
                                <div id="reasearch" class="content_wz">
                                    <h5 class="info-text">Research Publication</h5>
                                    <div class="col-md-12">
                                        <div class="row reasearch">
                                            @if(count($researchArr)==0)
                                            <div class="reasearch-edit clonedResearch add-sec" id="clonedResearch0">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Title<small class = "ismandtory">*</small></label>
                                                        <input type="text" name="title[0]" id="title0" class="form-control clsRequired is_required" placeholder="Title">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Journal/Magazine<small class = "ismandtory">*</small></label>
                                                        <input type="text" name="journal_magazine[0]" id="journal_magazine0" class="form-control clsRequired is_required" placeholder="">
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 small-select form-group">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <label>Month/Year of Publication<small class = "ismandtory">*</small></label>
                                                        </div>
                                                        <div class="text-field-select col-md-6">
                                                             {!!
                                                            Form::select('publication_month[0]',
                                                            [''=>'Select Month'] + Helpers::getMonthDropdown(),
                                                            null,
                                                            array('id' => 'publication_month0',
                                                            'class'=>'form-control select2Cls clsRequired is_required'))
                                                            !!}
                                                        </div>

                                                        <div class="text-field-select col-md-6">                                                             
                                                            {!!
                                                            Form::select('publication_year[0]',
                                                            [''=>'Select Year'] + Helpers::getYearDropdown(),
                                                            null,
                                                            array('id' => 'publication_year0',
                                                            'class'=>'form-control select2Cls clsRequired is_required'))
                                                            !!}
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group ">
                                                        <div class="file-browse">
                                                            <span class="button button-browse">
                                                                <img src="{{asset('frontend/inside/images/icon/attachment-icon.svg')}}">
                                                                <input name="attachment[0]" id="attachment0" type="file" class = "attach_photo is_required">
                                                            </span>
                                                            <label class = "fileslabel" id ="picName0">Attach File ( one attachment at a time )</label>
<!--                                                            <input type="text" name="attachment[0]" id="attachment0" class="file-name" readonly="">-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="deleteResearchbtn remove" style="display: none;"><i class="fa fa-trash-o remove deleteResearch" title="Remove" aria-hidden="true"></i></div>
                                            </div>
                                            @else
                                            @foreach($researchArr as $res => $research)
                                            
                                            <div class="reasearch-edit clonedResearch add-sec" id="clonedResearch{{$res}}">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Title<small class = "ismandtory">*</small></label>
                                                        <input type="text" value = "{{(isset($research->title) && !empty($research->title)) ? $research->title : (old('title') ? old('title') : '')}}" name="title[{{$res}}]" id="title{{$res}}" class="form-control clsRequired is_required" placeholder="Title">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Journal/Magazine<small class = "ismandtory">*</small></label>
                                                        <input type="text" value = "{{(isset($research->journal_magazine) && !empty($research->journal_magazine)) ? $research->journal_magazine : (old('journal_magazine') ? old('journal_magazine') : '')}}" name="journal_magazine[{{$res}}]" id="journal_magazine0" class="form-control clsRequired is_required" placeholder="">
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 small-select form-group">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <label>Month/Year of Publication<small class = "ismandtory">*</small></label>
                                                        </div>
                                                        <div class="text-field-select col-md-6">
                                                             {!!
                                                            Form::select('publication_month['.$res.']',
                                                            [''=>'Select Month'] + Helpers::getMonthDropdown(),
                                                            (isset($research->publication_month) && !empty($research->publication_month)) ? $research->publication_month : (old('publication_month') ? old('publication_month') : ''),
                                                            array('id' => 'publication_month'.$res,
                                                            'class'=>'form-control select2Cls clsRequired is_required'))
                                                            !!}
                                                        </div>

                                                        <div class="text-field-select col-md-6">
                                                            {!!
                                                            Form::select('publication_year['.$res.']',
                                                            [''=>'Select year'] + Helpers::getYearDropdown(),
                                                            (isset($research->publication_year) && !empty($research->publication_year)) ? $research->publication_year : (old('publication_year') ? old('publication_year') : ''),
                                                            array('id' => 'publication_year'.$res,
                                                            'class'=>'form-control select2Cls clsRequired is_required'))
                                                            !!}
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group ">
                                                        <div class="file-browse">
                                                            <span class="button button-browse">
                                                                <img src="{{asset('frontend/inside/images/icon/attachment-icon.svg')}}">
                                                                <input name="attachment[{{$res}}]" id="attachment{{$res}}" type="file" class = "attach_photo is_required">
                                                            </span>
                                                            <label class ="fileslabel" id ="picName{{$res}}">
                                                              Attach File ( one attachment at a time )</label>
                                                               
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="@if($res >=1)display:block @else display:none @endif" class="remove deleteResearch deleteResearchbtn"><i class="fa fa-trash-o" title="Remove" aria-hidden="true"></i></div>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>




                            <div class="wizard-footer">
                            <div class="col-sm-12"  style="padding: 0 8px;">
                                <div class="col-sm-6 text-left">
                                    <span style="@if(count($researchArr) < 5) visibility:visible @else visibility:hidden @endif" class="add-reasearch btn btn-primary mb-2"><span>+</span> Add Research Publication</span>

                                   
                                </div>
                                <div class="col-sm-6 text-right"> <input type='button' onclick="window.location='{{route("skills")}}'" class='btn btn-previous btn-default btn-wd' name='previous' value='Previous' />
                                    <input type='submit' class='btn btn-next btn-fill btn-warning btn-wd submitResearch' name='next' value='Next' />
                                </div>
                                <div class="clearfix"></div>
</div></div>
                        </form>
                    </div>
                </div>
                <!-- wizard container -->
            </div>
        </div>
        <!-- end row -->
    </div>
    <!--  big container -->
</div>
@endsection
@section('pageTitle')
Research Publication
@endsection
@section('addtional_css')
<link href="{{ asset('frontend/outside/css/paper-bootstrap-wizard.css')}}" rel="stylesheet" />
<!-- Fonts and Icons -->
<link href="http://netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">
<link href='https://fonts.googleapis.com/css?family=Muli:400,300' rel='stylesheet' type='text/css'>
<link href="{{ asset('frontend/outside/css/themify-icons.css')}}" rel="stylesheet">
<link href="{{ asset('frontend/outside/css/developer.css')}}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<!--End-->
@endsection
@section('jscript')
<script>
    var messages = {
        research_form_limit:"{{ config('common.RESEARCH_FORM_LIMIT') }}",
          is_scout: "{{ session()->get('is_scout') }}",
    };
</script>
<script src="{{ asset('frontend/outside/js/validation/research.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
@endsection