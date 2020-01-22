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


    @can('lead_list')
        <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu1" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-table" aria-hidden="true"></i>
                    <span class="menu-title">Manage Leads</span>
                   <i class="fa fa-angle-right" aria-hidden="true"></i>
                </a>
                <div class="collapse" id="layoutsSubmenu1">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('lead_list') }}">My Leads</a>
                        </li>
                    </ul>
                </div>
         </li>
    @endcan    

    @canany(['application_pool','application_list'])
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-address-card-o"></i>
                <span class="menu-title">Manage Application</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu">
                <ul class="nav flex-column sub-menu">                    
                @can('application_pool')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('application_pool') }}">Application pool</a>
                        </li> 
                @endcan 
                @can('application_list')       
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('application_list') }}">My Application</a>
                        </li>   
                @endcan 
                                        
                </ul>
            </div>

        </li>  
    @endcan

    @can('applicaiton_list')  
        <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu1" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-address-book-o" aria-hidden="true"></i>
                    <span class="menu-title">Manage FI/RCU</span>
                   <i class="fa fa-angle-right" aria-hidden="true"></i>
                </a>
                <div class="collapse" id="layoutsSubmenu1">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('applicaiton_list') }}">FI/RCU Applications</a>
                        </li>
                    </ul>
                </div>
         </li>
    @endcan  

    @canany(['get_anchor_list','get_anchor_lead_list'])
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu12" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-anchor" aria-hidden="true"></i>
                <span class="menu-title">Manage Anchor</span>
               <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu12">
                <ul class="nav flex-column sub-menu">
                @can('get_anchor_list')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('get_anchor_list') }}">Anchor List</a>

                    </li>
                @endcan
                @can('get_anchor_lead_list')
                        <li class="nav-item">
                        <a class="nav-link" href="{{ route('get_anchor_lead_list') }}">Anchor Uploaded Lead</a>
                    </li>                     
                @endcan      
                </ul>
            </div>
        </li>
    @endcan

    @can('get_co_lenders')
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu12" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-handshake-o" aria-hidden="true"></i>
                <span class="menu-title">Manage Co-lenders</span>
               <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu12">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_co_lenders') }}">Co-lenders List</a>
                 </li>
                </ul>
            </div>
        </li>
    @endcan
    
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu20" aria-expanded="true" aria-controls="collapseExample">
              <i class="fa fa-user-plus" aria-hidden="true"></i>
                <span class="menu-title">Manage Customer</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse show" id="layoutsSubmenu20" style="">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lms_get_customer_list') }}">Manage Sanction Cases</a>
                    </li>
                                                     
                </ul>
            </div>
        </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu123" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                <span class="menu-title">Manage Invoice</span>
               <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu123">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                        <a class="nav-link" href="{{ route('backend_upload_all_invoice') }}">Invoice Upload</a>
                    </li> 
                        <li class="nav-item">
                        <a class="nav-link" href="{{route('backend_get_invoice')}}">Manage Invoice</a>
                    </li>                     
                 
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenuDisbursal" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-bank" aria-hidden="true"></i>
                <span class="menu-title">Manage Disbursal</span>
               <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenuDisbursal">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lms_disbursal_request_list') }}">Disbursal Requests</a>

                    </li> 
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('lms_disbursed_list')}}">Disbursal List</a>
                    </li>                     
                 
                </ul>
            </div>
        </li>
    @canany(['get_agency_list','get_agency_user_list'])
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-user-secret" aria-hidden="true"></i>
                <span class="menu-title">Manage Agency</span>
               <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu">
                <ul class="nav flex-column sub-menu">
                @can('get_agency_list')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('get_agency_list') }}">Agency List</a>
                    </li>
                    @endcan
                    @can('get_agency_user_list')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('get_agency_user_list') }}">Add Agency User</a>
                    </li>                     
                    @endcan      
                </ul>
            </div>
        </li>   
    @endcan      
        
        @php $roleData = \Helpers::getUserRole() @endphp
        
        @if($roleData[0]->is_superadmin == 1)
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-bandcamp" aria-hidden="true"></i>
                <span class="menu-title">Access Management</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('get_role') }}">Manage Roles</a>
                    </li>
<!--                    <li class="nav-item">
                        <a class="nav-link" href="#">Manage Permissions</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('get_role_user') }}">Manage Users</a>
                    </li>                                   
                </ul>
            </div>
        </li>
        @endif
              
        @if($roleData[0]->is_superadmin == 1)
        <li class="nav-item">
           <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu10" aria-expanded="false" aria-controls="collapseExample">
           <i class="fa fa-tasks" aria-hidden="true"></i>
           <span class="menu-title">Masters</span>
           <i class="fa fa-angle-right" aria-hidden="true"></i>
           </a>
           <div class="collapse" id="layoutsSubmenu10">
              <ul class="nav flex-column sub-menu">
                 @can('manage_doa')
                 <li class="nav-item">
                    <a class="nav-link" href="{{ route('manage_doa') }} ">Manage DOA Level</a>
                 </li>
                 @endcan
                 @can('manage_program')
                 <li class="nav-item">
                    <a class="nav-link" href="{{ route('manage_program') }} ">Manage Program</a>
                 </li>
                @endcan
                 <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_charges_list') }}">Manage Charges</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_documents_list') }}">Manage Document</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_industries_list') }}">Manage Industry</a>
                 </li>
                 
          
                 <li class="nav-item">
                    <a class="nav-link" href="#">Manage State</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">Risk Category </a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">Business Segment</a>
                 </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_entity_list') }}">Business Entity</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">Business Constitution</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">Bank Master</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">Industry Master</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">FI agency Master</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">Holiday Master</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">Email Master</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">SMS Master</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">Base Rate Master</a>
                 </li>
              </ul>
           </div>
        </li>
        @endif
    </ul>
</nav>