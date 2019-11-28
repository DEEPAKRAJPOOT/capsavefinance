<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <!--main pages start-->
        <!-- <li class="nav-item nav-category">
            <span class="nav-link">Main</span>
        </li> -->  
        <li class="nav-item active">
            <a class="nav-link"  href="#">
                <i class="fa fa fa-home"></i>
                <span class="menu-title">Home</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
      </li>             
        

    <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-file-image-o"></i>
                <span class="menu-title">Manage Application</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu">
                <ul class="nav flex-column sub-menu">                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('front_application_list') }}">Manage Application</a>
                    </li>                                   
                </ul>
            </div>
        </li>      
    </ul>
</nav>