<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Capsave</title>
    <link rel="shortcut icon" href="{{url('backend/assets/images/favicon.png')}}" />
    <!--<link rel="stylesheet" href="fonts/font-awesome/font-awesome.min.css" />-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{url('backend/assets/css/perfect-scrollbar.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/jsgrid.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/jsgrid-theme.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/uploadfile.css')}}" >
    <link rel="stylesheet" href="{{url('backend/assets/css/data-table.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/plugins/datatables/css/datatables.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/style.css')}}?v="{{Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'd-m-Y h:i A')}}"" />
    <link rel="stylesheet" href="{{url('backend/assets/css/custom.css')}}?v="{{Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'd-m-Y h:i A')}}"" />
    <link rel="stylesheet" href="{{ url('common/js/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />
    @if(Auth::user()->anchor_id == config('common.LENEVO_ANCHOR_ID'))
    <link rel="stylesheet" href="{{url('frontend/assets/css/lenevo/custom.css')}}">
    @endif
    @yield('additional_css')
</head>

<body class="sidebar-icon-only">
    <div class=" container-scroller">
        @include('layouts.front_header')
        <div class="container-fluid page-body-wrapper">
            <div class="row row-offcanvas row-offcanvas-right">
                <!-- partial -->
                <div id="iframeMessage" class="content-wrapper-msg"></div>
                @include('layouts.partials.front_sidebar')
                <!-- partial -->
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
                <div class=" alert-danger alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    {{ Session::get('error') }}
                </div>
                </div>
                @endif
                @yield('content')
                
                @include('layouts.front_footer')
            </div>
        </div>
    </div>

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
    <script src="{{url('backend/assets/js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{url('backend/assets/js/popper.min.js')}}"></script>
    <script src="{{url('backend/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{url('frontend/assets/js/jquery.validate.js')}}"></script>
    <script src="{{url('backend/assets/js/perfect-scrollbar.jquery.min.js')}}"></script>
    <script src="{{url('backend/assets/js/jsgrid.min.js')}}"></script>
    <script src="{{url('backend/assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{url('backend/assets/js/misc.js')}}"></script>
    <script src="{{url('backend/js/promoter.js')}}"></script>
    <script src="{{url('backend/assets/plugins/datatables/js/datatable.min.js')}}"></script>
    <script src="{{url('backend/assets/plugins/datatables/Buttons/dataTables.buttons.min.js')}}"></script>
    <script src="{{url('common/js/datetimepicker/js/bootstrap-datetimepicker.js')}}"></script>
    <script src="{{url('common/js/iframePopup.js')}}"></script>
   
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();

            $(".trigger").click(function(){
                if($(this).hasClass("minus")){
                    $(this).removeClass("minus"); 
                }
                else{
                    $(this).addClass("minus");   
                }

            //$(".trigger").removeClass("minus");
            //$(this).addClass("minus");

            $(this).parents("tr").next(".dpr").slideToggle();
            });

            datepickerDisFdate();
             datepickerDisPdate();

            $('.number_format').on('input', function(event) {
               // skip for arrow keys
               if(event.which >= 37 && event.which <= 40) return;

               // format number
               $(this).val(function(index, value) {
                   return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
               });
            });
        });

        function dateConversion(date) {
            var datearray = date.split("/");
            var newdate = datearray[1] + '/' + datearray[0] + '/' + datearray[2];

            return newdate;
        }        

        function datepickerDisFdate(){
            $(".datepicker-dis-fdate").datetimepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                minView : 2,
                endDate: new Date()
            });
        }
     function datepickerDisPdate(){
            $(".datepicker-dis-pdate").datetimepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                minView : 2,
               startDate: new Date()
            });
        }
        function unsetError(ele){
            $(ele+' +span').remove();
        }

        function setError(ele, msg){
            $(ele).after('<span class="error">'+msg+'</span>');
        }

        function replaceAlert(msg, type){
            let alert_class;
            switch(type){
                case 'success':
                    alert_class = 'alert-success';
                    break;
                case 'error':
                    alert_class = 'alert-danger';
                    break;
                default:
                    alert_class = 'alert-primary';
                    break;
            }

            let alert_msg = '<div class="content-wrapper-msg" id="custom-alert">\
                                <div class="'+alert_class+' alert" role="alert">\
                                    <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span>\
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">\
                                        <span aria-hidden="true">×</span>\
                                    </button>'
                                    +msg+
                                '</div>\
                            </div>';

            $('#custom-alert').remove();
            $(alert_msg).insertAfter('#iframeMessage');
        }
    </script>
    @yield('jscript')
</body>
</html>
