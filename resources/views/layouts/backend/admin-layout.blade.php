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
    <link rel="stylesheet" href="{{url('backend/assets/css/style.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/custom.css')}}" />
    <link rel="stylesheet" href="{{ url('common/js/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />

    @yield('additional_css') 
    <style>
   
    .dateGrace
    {
        background-color: #ec9595 !important;
    }
    

        .error
        {
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
</head>

<body class="sidebar-icon-only" style="margin: 0px !important">
    <div class=" container-scroller">
        @include('layouts.backend.partials.admin-header')
        <div class="container-fluid page-body-wrapper">
            <div class="row row-offcanvas row-offcanvas-right">
                <!-- partial -->
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
                
                @include('layouts.backend.partials.admin-sidebar')
                @yield('content')
                @include('layouts.backend.partials.admin-footer')
            </div>
        </div>
    </div>
    <div class="isloader" style="display:none;z-index: 99999999;">  
        <img src="{{asset('backend/assets/images/loader.gif')}}">
    </div>
</body>

    <script src="{{url('backend/assets/js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{url('backend/assets/js/popper.min.js')}}"></script>
    <script src="{{url('backend/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{url('backend/assets/js/perfect-scrollbar.jquery.min.js')}}"></script>
    <script src="{{url('backend/assets/js/jsgrid.min.js')}}"></script>
    <script src="{{url('backend/assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{url('backend/assets/js/misc.js')}}"></script>
    <script src="{{url('backend/assets/js/jquery.validate.js')}}"></script>
    <script src="{{url('backend/assets/plugins/datatables/js/datatable.min.js')}}"></script>
    <script src="{{url('backend/assets/plugins/datatables/Buttons/dataTables.buttons.min.js')}}"></script>
    <script src="{{url('backend/assets/plugins/ckeditor/ckeditor.js')}}"></script>
    <script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
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
           /* use for hide future date  */
            datepickerDisFdate();
            /* use for hide past date  */
            datepickerDisPdate();
            $(document).on('input','.number_format', function(event) {
               // skip for arrow keys
               if(event.which >= 37 && event.which <= 40) return;

               // format number
               $(this).val(function(index, value) {
                   return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
               });
            });

            $(document).on('keypress','.float_format', function(event) {
                let num = $(this).val();
                if(event.which == 8 || event.which == 0){
                    return true;
                }
                if(event.which < 46 || event.which > 59) {
                    return false;
                }
               
                if(event.which == 46 && $(this).val().indexOf('.') != -1) {
                    return false;
                }
                if(typeof num.split('.')[1] !== 'undefined' && num.split('.')[1].length > 1){
                    return false;
                }
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

        function setLimit(ele, msg){
            $(ele).parent('div').find('.limit').html(msg);
        }

        function currentDateTime() {
            var today = new Date();        
            var sMonth = padValue(today.getMonth() + 1);
            var sDay = padValue(today.getDate());
            var sYear = today.getFullYear();
            var sHour = today.getHours();
            var sMinute = padValue(today.getMinutes());
            var sAMPM = "AM";

            var iHourCheck = parseInt(sHour);

            if (iHourCheck > 12) {
                sAMPM = "PM";
                sHour = iHourCheck - 12;
            }
            else if (iHourCheck === 0) {
                sHour = "12";
            }

            sHour = padValue(sHour);

            dateTime =  sDay + "-" + sMonth + "-" + sYear + " " + sHour + ":" + sMinute + " " + sAMPM;
            document.getElementById('_current_sys_date').innerHTML = dateTime;
        }
        
        function padValue(value) {
            return (value < 10) ? "0" + value : value;
        }

            setInterval('currentDateTime()',1000);


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
    <script>
    var common_vars={
        unique_charge_url:"{{ route('check_unique_charge') }}",
        token: "{{ csrf_token() }}"
    }
    </script>
    <script src="{{ asset('backend/js/number-format.js') }}"></script>
    <script src="{{ asset('common/js/check_permission.js') }}"></script>
    @yield('jscript')
</html>
