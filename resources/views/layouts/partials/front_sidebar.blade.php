<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <!--main pages start-->
        <!-- <li class="nav-item nav-category">
            <span class="nav-link">Main</span>
        </li> --> 
        @if(Auth::user()->anchor_id != config('common.LENEVO_ANCHOR_ID'))
        <li class="nav-item active">
            <a class="nav-link"  href="{{ route('front_dashboard') }}">
                <i class="fa fa fa-home"></i>
                <span class="menu-title">Dashboard</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
        </li>             
        @endif

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
        @if(Auth::user()->anchor_id != config('common.LENEVO_ANCHOR_ID'))
         <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu1" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-file-image-o"></i>
                <span class="menu-title">Manage Invoice</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu1">
                <ul class="nav flex-column sub-menu">                    
                    <li class="nav-item">
                    <a class="nav-link" href="{{ route('front_upload_all_invoice') }}">Upload Invoice</a> 
                    </li> 
                  <li class="nav-item">
                        <a class="nav-link" href="{{ route('get_invoice') }}">Manage Invoice</a>
                    </li>   
                    
                </ul>
            </div>
        </li>
        @endif

        {{--@if(Auth::user()->anchor_id != config('common.LENEVO_ANCHOR_ID'))
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu1" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-bank"></i>
                <span class="menu-title">Manage NACH</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu1">
                <ul class="nav flex-column sub-menu">                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('front_nach_list') }}">Register Request</a> 
                    </li>                    
                </ul>
            </div>
        </li>
        @endif--}}
        @if(Auth::user()->anchor_id != config('common.LENEVO_ANCHOR_ID'))
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu1" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-bank"></i>
                <span class="menu-title">Manage SOA</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu1">
                <ul class="nav flex-column sub-menu">                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('front_consolidated_list') }}">SOA</a> 
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('front_soa_list') }}">Broad SOA</a> 
                    </li>
                </ul>
            </div>
        </li>      
        @endif
    </ul>
</nav>