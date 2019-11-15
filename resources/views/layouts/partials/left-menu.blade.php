<div class="container-fluid page-body-wrapper">
    <div class="  row-offcanvas row-offcanvas-right">	

        <nav class="sidebar sidebar-offcanvas" id="sidebar" style="min-height: 713px;">
            <ul class="nav">
                <!--main pages start-->
                <li class="nav-item nav-category">
                    <span class="nav-link">&nbsp;</span>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#">
                        <i class="fa fa-home"></i>
                        <span class="menu-title">Home</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="{{Route('lead_leadspool')}}" >
                        <i class="fa fa-home"></i>
                        <span class="menu-title">Manage Leads Pools </span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{Route('lead_leadspool')}}" >
                        <i class="fa fa-home"></i>
                        <span class="menu-title">Manage Leads Pools </span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <a class="nav-link" href="{{Route('lead.index')}}" >
                        <i class="fa fa-home"></i>
                        <span class="menu-title">Manage Leads</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <div class="collapse" id="layoutsSubmenu1">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{Route('lead.index')}}">Manage Suppliers</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{Route('supplier.index')}}" >
                        <i class="fa fa-home"></i>
                        <span class="menu-title">Manage Suppliers</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <div class="collapse" id="layoutsSubmenu1">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{Route('supplier.index')}}">Manage Suppliers</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link"  href="{{Route('buyer.index')}}">
                        <i class="fa fa-home"></i>
                        <span class="menu-title">Manage Buyers</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <div class="collapse" id="layoutsSubmenu2">
                        <ul class="nav flex-column sub-menu">

                            <li class="nav-item">
                                <a class="nav-link" href="{{Route('buyer.index')}}">Manage Buyers</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{Route('logistics.index')}}" >
                        <i class="fa fa-home"></i>
                        <span class="menu-title">Manage Logistics</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <div class="collapse" id="layoutsSubmenu3">
                        <ul class="nav flex-column sub-menu">

                            <li class="nav-item">
                                <a class="nav-link" href="{{Route('logistics.index')}}">Manage Logistics</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link"  href="{{Route('lender.index')}}" >
                        <i class="fa fa-home"></i>
                        <span class="menu-title">Manage Lenders</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <div class="collapse" id="layoutsSubmenu4">
                        <ul class="nav flex-column sub-menu">

                            <li class="nav-item">
                                <a class="nav-link" href="{{Route('lender.index')}}">Manage Lenders</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link"  href="#" >
                        <i class="fa fa-home"></i>
                        <span class="menu-title">Manage Masters</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <div class="collapse" id="layoutsSubmenu5">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="#">Manage Documents</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Manage Entity</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Manage Industry</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Manage Offer Mapping</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Manage GST</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#" aria-expanded="false" aria-controls="collapseExample">
                        <i class="fa fa-home"></i>
                        <span class="menu-title pr-2">Access Management</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <div class="collapse" id="layoutsSubmenu6">
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
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#" aria-expanded="false" aria-controls="collapseExample">
                        <i class="fa fa-home"></i>
                        <span class="menu-title">Manage Offers</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <div class="collapse" id="layoutsSubmenu7">
                        <ul class="nav flex-column sub-menu">

                            <li class="nav-item">
                                <a class="nav-link" href="#">Manage Offers</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
