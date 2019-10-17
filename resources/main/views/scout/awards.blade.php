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
                        <form action="{{route('scout_awards_honors')}}" id="awardForm" method="post">
                             <input type = "hidden" value = "{{Auth::user()->id}}" name = "userId">
                            {{ csrf_field() }}
                            <div class="tab-content">
                                <div  id="awards">
                                    <h5 class="info-text">Awards & Honors
                                    </h5>
                                    <div class="col-md-12">
                                        <div class="row awards">
                                             @if(count($awardArr)==0)
                                             
                                             <div class=" reasearch">
                                            <div class="awards-edit clonedAwards add-sec" id="clonedAwards0">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>Title<small>*</small></label>
                                                        <input type="text" name="title[0]" id="title0" class="form-control clsRequired" placeholder="Title" autofocus="autofocus">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group ">
                                                        <label>Brief Description</label>
                                                        <div data-error="hasError" class="textarea-field">
                                                            <textarea id="description0" name="description[0]" class="form-control" placeholder="Brief Description"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                 
                                                   <div class="deleteAwardsbtn remove" style="display: none;"><i class="fa fa-trash-o remove deleteAwards" title="Remove" aria-hidden="true"></i></div>
                                                 
                                                   
                                            </div>
                                             </div>
                                             @else
                                             @foreach($awardArr as $res => $award)
                                                <div class="awards-edit clonedAwards " id="clonedAwards".$res>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>Title<small>*</small></label>
                                                        <input value = "{{(isset($award->title) && !empty($award->title)) ? $award->title : (old('title') ? old('title') : '')}}" type="text" name="title[{{$res}}]" id="title{{$res}}" class="form-control clsRequired" placeholder="Title" autofocus="autofocus">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group ">
                                                        <label>Brief Description</label>
                                                        <div data-error="hasError" class="textarea-field">
                                                            <textarea id="description{{$res}}}" name="description[{{$res}}]" class="form-control" placeholder="Brief Description">{{(isset($award->description) && !empty($award->description)) ? $award->description : (old('description') ? old('description') : '')}}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                   <div class="deleteAwardsbtn" style="@if($res >=1)display:block @else display:none @endif"><i class="fa fa-trash-o remove deleteAwards" title="Remove" aria-hidden="true"></i></div>
                                                </div>
                                                   
                                            </div>
                                             
                                              @endforeach  
                                              @endif
                                        </div>
                                        
                                    </div>
                                    

                                </div>
                            </div>
                            
                             
                            
                            
                            
                            <div class="wizard-footer">
                                 <div class="col-sm-6 text-left">                                   
                                    <span class="add-awards btn btn-primary mb-2"><span>+</span> Add Awards & Honors</span>
                                </div>
                                <div class="col-sm-6 text-right">
                                    
                                    <input type='button' onclick="window.location ='{{route("scout_research_publication",['userId'=>Auth::user()->id])}}'" class='btn btn-previous btn-default btn-wd' name='previous' value='Previous' />
                                    <input type='submit' class='submitAwards btn btn-finish btn-fill btn-warning btn-wd' name='finish' value='Finish' />
                                </div>
                                <div class="clearfix"></div>
                            </div>
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
Awards & Honors
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
        award_form_limit:"{{ config('common.AWARD_FORM_LIMIT') }}",
         is_scout: "{{ session()->get('is_scout') }}",
    };
</script>
<script src="{{ asset('frontend/outside/js/validation/awards.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
@endsection