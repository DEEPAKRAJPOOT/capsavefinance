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
        <link href="{{url('common/js/summernote/dist/summernote.css')}}" rel="stylesheet">
        <link rel="stylesheet" href="http://www.codermen.com/css/bootstrap.min.css">
        <script src="http://www.codermen.com/js/jquery.js"></script>
        @yield('additional_css')
    </head>

    <body class="sidebar-icon-only"> 
        <div class=" row-offcanvas row-offcanvas-right">
            <!-- partial -->
            @if(Session::has('message'))
            <div class="content-wrapper-msg">
            <div class=" alert-success alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
            {{ Session::get('message') }}
            </div>
            </div>
            @endif
            
            @yield('content')
        </div>
          
        <script src="{{url('backend/assets/js/jquery.min.js')}}"></script>
        <script src="{{url('backend/assets/js/popper.min.js')}}"></script>
        <script src="{{url('backend/assets/js/bootstrap.min.js')}}"></script>
        <script src="{{url('backend/assets/js/perfect-scrollbar.jquery.min.js')}}"></script>
        <script src="{{url('backend/assets/js/jsgrid.min.js')}}"></script>
        <script src="{{url('backend/assets/js/hoverable-collapse.js')}}"></script>
        <script src="{{url('backend/assets/js/misc.js')}}"></script>
        <script src="{{url('backend/assets/plugins/datatables/js/datatable.min.js')}}"></script>
        <script src="{{url('common/js/iframePopup.js')}}"></script>
        <script src="{{ asset('common/js/jquery.validate.js') }}"></script>
        <script src="{{url('common/js/summernote/dist/summernote.js')}}"></script>
        <script src="{{url('common/js/datetimepicker/js/bootstrap-datetimepicker.js')}}"></script>
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
    </script>
    @yield('jscript')
    </body>
</html>
