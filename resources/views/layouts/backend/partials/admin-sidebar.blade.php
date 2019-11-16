<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <!--main pages start-->
        <li class="nav-item nav-category">
            <span class="nav-link">Main</span>
        </li>

        
    <li class="nav-item active">
            <a class="nav-link"  href="dashboard.php">
                <i class="fa fa fa-home"></i>
                <span class="menu-title">Home</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>

      </li>             
        
    <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu1" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-files-o" aria-hidden="true"></i>
                <span class="menu-title">Manage Leads</span>
               <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu1">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lead.index') }}">My Leads</a>
                    </li>
                                                
                </ul>
            </div>
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
                        <a class="nav-link" href="{{ route('application_list') }}">Manage Application</a>
                        </li>                                   
                </ul>
            </div>
        </li>    

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-files-o" aria-hidden="true"></i>
                <span class="menu-title">Manage Anchor</span>
               <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="manage-anchor.php">Manage Anchor</a>
                    </li>
                                                
                </ul>
            </div>
        </li>   

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-bandcamp" aria-hidden="true"></i>
                <span class="menu-title">Access Management</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="manage-role.php">Manage Roles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="role-permission.php">Manage Permissions</a>
                    </li> 
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Manage Users</a>
                    </li>                                   
                </ul>
            </div>
        </li>      
    </ul>
</nav>