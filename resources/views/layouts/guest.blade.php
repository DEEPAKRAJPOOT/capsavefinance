<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet"> <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{url('frontend/assets/css/style.css')}}">
        <link rel="stylesheet" href="{{ url('frontend/assets/css/perfect-scrollbar.min.css') }}" />
        <link rel="stylesheet" href="{{ url('frontend/assets/css/jsgrid.min.css') }}" />
        <link rel="stylesheet" href="{{ url('frontend/assets/css/jsgrid-theme.min.css') }}" />
        <link rel="stylesheet" href="{{ url('frontend/assets/css/uploadfile.css') }}">
        <link rel="stylesheet" href="{{ url('frontend/assets/css/data-table.css') }}" />
        <link rel="stylesheet" href="{{ url('common/js/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />
        <link rel="stylesheet" href="{{ url('frontend/assets/css/style2.css') }}" />
        <link rel="stylesheet" href="{{ url('frontend/assets/css/custom.css') }}" />
    </head>
    <body>

        <header>
            <div class="container">
                <div class="d-flex">
                    <a href="#"><img src="{{url('frontend/assets/images/logo.svg')}}" alt="logo" width="150px"> </a>
                </div>
        </header>
                <div id="iframeMessage" class="content-wrapper-msg"></div>
                @if(Session::has('message'))
                <div class="content-wrapper-msg">
                <div class=" alert-success alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    {{ Session::get('message') }}
                </div>
                </div>
                @endif

                 @if(Session::has('error'))
                   <div class="content-wrapper-msg">
                    <div class=" alert-danger alert" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        {{ Session::get('error') }}
                    </div>
                   </div>
                @endif

                @if (count($errors) > 0)
                  <div class="content-wrapper-msg">
                <div class="alertMsgBox">
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                  </div>
                @endif        
        @yield('content')
        <div class="isloader" style="display:none;">  
            <img src="{{asset('backend/assets/images/loader.gif')}}">
        </div>
        <style>
            .error{
                color:red !important;
            }
            .isloader{ 
                position: fixed;    
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,.6);
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-content: center;
                z-index: 9;
            }
        </style>
        <script src="{{ url('frontend/assets/js/popper.min.js') }}"></script>
        <script src="{{url('frontend/assets/js/jquery-3.4.1.min.js')}}"></script>
        <script src="{{url('frontend/assets/js/bootstrap.min.js')}}"></script>
        <script src="{{url('frontend/assets/js/jquery.validate.js')}}"></script>
        <script src="{{url('common/js/datetimepicker/js/bootstrap-datetimepicker.js')}}"></script>
        <script>
            $(".sign-UP .btn").click(function () {
                $(".otp-section").fadeIn();
                $("body").addClass("scroll-hiddin");
            })

            $(".section-header button").click(function () {
                $(".otp-section").fadeOut();
                $("body").removeClass("scroll-hiddin");
            })


            $(document).ready(function(){
                datepickerDisFdate();

                $('.number_format').on('input', function(event) {
                   // skip for arrow keys
                   if(event.which >= 37 && event.which <= 40) return;

                   // format number
                   $(this).val(function(index, value) {
                       return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                   });
                });   
            });

            function datepickerDisFdate(){
                $(".datepicker-dis-fdate").datetimepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    minView : 2,
                    endDate: new Date()
                });
            }

            function unsetError(ele){
                $(ele+' +span').remove();
            }

            function setError(ele, msg){
                $(ele).after('<span class="error">'+msg+'</span>');
            }
        </script>
        @yield('scripts')
    </body>
</html>
