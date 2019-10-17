
@if(Auth::check())
<div class="wizard-header text-center">
    <h3 class="wizard-title">Please submit these details to become a <b>validator</b> of rights.</h3>
    <p class="category">Click here to know about the benefits of joining as a <a href="https://inventrust.com/faq" target="_blank">validator</a>.</p>
</div>
@else
<div class="wizard-header text-center">
    <h3 class="wizard-title">Sign up for free!</h3>
    <p class="category">This information will let us know more about you. <a href="{{route('login_open')}}" class="underline">Already have an account?</a></p>
</div>
@endif


<div class="wizard-navigation">
<!--     <div class="progress-with-circle">
            <div class="progress-bar" style="width: 10%;"></div>
        </div>-->
    @php $routeName=\Request::route()->getName();    
    @endphp
    <ul id="progressbar">
        <li class="@if($routeName=='user_register_open') active @else @endif">
            <div class="top-circle-bg">
                <div class="count-top @if($routeName!=='user_register_open') checked @else @endif"><i class="ti-user"></i></div>
                <div class="count-bottom"><i class="ti-user"></i> </div>
            </div>
            <div class="count-heading">Step 1</div>
        </li>
        
    </ul>

</div>
<script src="{{ asset('frontend/outside/js/jquery-2.2.4.min.js') }}"></script>
<script>
 jQuery(document).ready(function () {
   if("{{$routeName}}" == 'education_details'){
       $('#progressbar').append('<style>ul#progressbar:before{width:30%;}</style>');
   }
   if("{{$routeName}}" == 'skills'){
       $('#progressbar').append('<style>ul#progressbar:before{width:50%;}</style>');
   }
   if("{{$routeName}}" == 'research_publication'){
       $('#progressbar').append('<style>ul#progressbar:before{width:70%;}</style>');
   }
   if("{{$routeName}}" == 'awards_honors'){
       $('#progressbar').append('<style>ul#progressbar:before{width:90%;}</style>');
   }
 });
</script>