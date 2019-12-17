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


    </head>

    <body class="sidebar-icon-only">
        <div class=" container-scroller">
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

            <div class="container-fluid page-body-wrapper">
                <div class="row row-offcanvas row-offcanvas-right">
                    <!-- partial -->
                    @yield('content')


                </div>
            </div>
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

        <script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();

        $(".trigger").click(function () {
            if ($(this).hasClass("minus")) {
                $(this).removeClass("minus");
            } else {
                $(this).addClass("minus");
            }

            //$(".trigger").removeClass("minus");
            //$(this).addClass("minus");

            $(this).parents("tr").next(".dpr").slideToggle();
        });
    });

    function unsetError(ele) {
        $(ele + ' +span').remove();
    }

    function setError(ele, msg) {
        $(ele).after('<span class="text-danger error">' + msg + '</span>');
    }
        </script>
        @yield('jscript')
    </body>
</html>
