<!-- partial:partials/_sidebar.html -->
                <nav class="sidebar sidebar-offcanvas" id="sidebar">
                    <ul class="nav">
                        <!--main pages start-->
                        <li class="nav-item nav-category">
                            <span class="nav-link">Main</span>
                        </li>
                        <!--   <li class="nav-item">
                        <a class="nav-link" href="index.php" aria-expanded="false">
                        <i class="mdi mdi-home"></i>
                        <span class="menu-title">Home</span>
                        <i class="mdi mdi-chevron-right"></i>
                        </a>                          
                        </li> -->

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fa fa-file-image-o"></i>
                                <span class="menu-title">Manage leads</span>
                                <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </a>
                            <div class="collapse" id="layoutsSubmenu">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ Route('lead.index') }}">Manage leads</a>
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
                                        <a class="nav-link" href="{{ Route('application_list')}}">Manage Application</a>
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
                                        <a class="nav-link" href="#">Manage Anchor</a>
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
                                        <a class="nav-link" href="#">Manage Roles</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Manage Permissions</a>
                                    </li> 
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Manage Users</a>
                                    </li>                                   
                                </ul>
                            </div>
                        </li>   
                    </ul>
                </nav>