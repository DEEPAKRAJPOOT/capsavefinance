<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <!--main pages start-->
        <!-- <li class="nav-item nav-category">
            <span class="nav-link">Main</span>
        </li> -->
	@can('backend_dashboard')
        <li class="nav-item active">
            <a class="nav-link" href="{{ route('backend_dashboard') }}">
                <i class="fa fa fa-home"></i>
                <span class="menu-title">Dashboard</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
      </li>
	@endcan

    @canany(['get_anchor_list','get_anchor_lead_list'])
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu4" aria-expanded="false"
                aria-controls="collapseExample">
                <i class="fa fa-anchor" aria-hidden="true"></i>
                <span class="menu-title">Manage Anchor</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu4">
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

    @canany(['get_agency_list','get_agency_user_list'])
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu9" aria-expanded="false"
                aria-controls="collapseExample">
                <i class="fa fa-user-secret" aria-hidden="true"></i>
                <span class="menu-title">Manage Agency</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu9">
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

    @canany(['lead_list', 'non_anchor_lead_list'])
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu1" aria-expanded="false"
                aria-controls="collapseExample">
                <i class="fa fa-table" aria-hidden="true"></i>
                <span class="menu-title">Manage Leads</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu1">
                <ul class="nav flex-column sub-menu">
                    @can('lead_list')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lead_list') }}">My Leads</a>
                    </li>
                    @endcan
                    @can('non_anchor_lead_list')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('non_anchor_lead_list') }}">Non Anchor Leads List</a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>
    @endcan    

    @canany(['application_pool','application_list', 'colender_application_list', 'applicaiton_list'])
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu2" aria-expanded="false"
                aria-controls="collapseExample">
                <i class="fa fa-address-card-o"></i>
                <span class="menu-title">Manage Application</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu2">
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
                    @can('applicaiton_list')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('applicaiton_list') }}">FI/RCU Applications</a>
                    </li>
                    @endcan
                    @can('colender_application_list')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('colender_application_list') }}">Co-lender Applications</a>
                    </li>
                    @endcan
                    @can('api_change_year')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('api_change_year') }}">Change Financial Year</a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>
    @endcan

    @can('get_co_lenders')
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu5" aria-expanded="false"
                aria-controls="collapseExample">
                <i class="fa fa-handshake-o" aria-hidden="true"></i>
                <span class="menu-title">Manage Co-lenders</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu5">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('get_co_lenders') }}">Co-lenders List</a>
                    </li>
                </ul>
            </div>
        </li>
    @endcan

    @php $roleData = \Helpers::getUserRole() @endphp
    @if(isset($roleData[0]) && $roleData[0]->role_type == 3)
    @can('anchor_nach_list')
    <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu6" aria-expanded="false"
            aria-controls="collapseExample">
            <i class="fa fa-bank" aria-hidden="true"></i>
            <span class="menu-title">Manage NACH</span>
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
        <div class="collapse" id="layoutsSubmenu6" style="">
            <ul class="nav flex-column sub-menu">
                @can('anchor_nach_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('anchor_nach_list') }}">Register Request</a>
                </li>
                @endcan
            </ul>
        </div>
    </li>
    @endcan
    @endif

    @can('backend_nach_list', 'users_nach_list')
    <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu6" aria-expanded="false"
            aria-controls="collapseExample">
            <i class="fa fa-bank" aria-hidden="true"></i>
            <span class="menu-title">Manage NACH</span>
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
        <div class="collapse" id="layoutsSubmenu6" style="">
            <ul class="nav flex-column sub-menu">
                @can('backend_nach_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('backend_nach_list') }}">Register Request</a>
                </li>
                @endcan
                @can('users_nach_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users_nach_list') }}">Manage NACH List</a>
                </li>
                @endcan
                @can('users_nach_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('nach_repayment_list') }}">NACH Repayment</a>
                </li>
                @endcan
            </ul>
        </div>
    </li>
    @endcan

    @if(config('lms.LMS_STATUS'))
        @can('lms_get_customer_list')
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu6" aria-expanded="false"
                aria-controls="collapseExample">
                <i class="fa fa-user-plus" aria-hidden="true"></i>
                <span class="menu-title">Manage Customer</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu6" style="">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lms_get_customer_list') }}">Manage Sanction Cases</a>
                    </li>

                </ul>
            </div>
        </li>
        @endcan
    @endif

    @if(config('lms.LMS_STATUS'))
        @canany(['backend_upload_all_invoice','backend_get_invoice','backend_get_bank_invoice'])
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu7" aria-expanded="false"
                aria-controls="collapseExample">
                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                <span class="menu-title">Manage Invoice</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu7">
                <ul class="nav flex-column sub-menu">
                    @can('backend_upload_all_invoice')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('backend_upload_all_invoice') }}">Invoice Upload</a>
                    </li>
                    @endcan
                    @can('backend_get_invoice')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('backend_get_invoice')}}">Manage Invoice</a>
                    </li>
            @endcan
                </ul>
            </div>
        </li>
        @endcan
    @endif

    @if(config('lms.LMS_STATUS'))
        @canany(['payment_list','unsettled_payments','settled_payments', 'lms_refund_new'])
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu8" aria-expanded="false"
                aria-controls="collapseExample">
                <i class="fa fa-credit-card" aria-hidden="true"></i>
                <span class="menu-title">Manage Payment</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu8">
                <ul class="nav flex-column sub-menu">
                    @can('payment_list')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('payment_list')}}">Manage Repayment</a>
                    </li>
                    @endcan
                    @can('unsettled_payments')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('unsettled_payments')}}">Unsettled Payments</a>
                    </li>
                    @endcan
                    @can('settled_payments')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('settled_payments')}}">Settled Payment</a>
                    </li>
                    @endcan
                    @can('lms_refund_new')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lms_refund_new') }}">Manage Refund</a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>
        @endcan
    @endif

    @if(config('lms.LMS_STATUS'))
        @canany(['get_tally_batches','get_fin_transactions'])
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenuFinance" aria-expanded="false"
                aria-controls="collapseExample">
                <i class="fa fa-money"></i>
                <span class="menu-title">Manage Finance</span>
                <i class="fa fa-money" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenuFinance">
            <ul class="nav flex-column sub-menu">
            @can('get_tally_batches')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_tally_batches') }}">Accounting Entries</a>
                </li>
            @endcan
            {{-- @can('get_fin_transactions')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_fin_transactions') }}">Transactions</a>
                </li>
            @endcan --}}
            <!--
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('create_je_config') }}">JE Config</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_fin_trans_list') }}">Transaction Type List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_fin_variable') }}">Variables List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_fin_journal') }}">Journal List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_fin_account') }}">Accounts List</a>
                </li>
            -->
            </ul>
            </div>
        </li>
        @endcan
    @endif

    @if(config('lms.LMS_STATUS'))
	   @can('lease_register')
        <li class="nav-item">
            <a class="nav-link"  href="{{ route('lease_register') }}">
                <i class="fa fa-files-o"></i>
                <span class="menu-title">Reports</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>

        </li>
        @endcan
    @endif

    @if(config('lms.LMS_STATUS'))
        @can('eod_process')
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu1" aria-expanded="false"
                aria-controls="collapseExample">
                <i class="fa fa-table" aria-hidden="true"></i>
                <span class="menu-title">Manage EOD Process</span>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu1">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('eod_process') }}">EOD Process</a>
                    </li>
                </ul>
            </div>
        </li>
        @endcan
    @endif

    @canany(['manage_doa', 'manage_program', 'get_charges_list', 
        'get_documents_list', 'get_industries_list', 'get_vouchers_list',
        'get_segment_list', 'get_entity_list', 'get_constitutions_list',
        'get_gst_list', 'get_equipment_list', 'get_baserate_list', 'get_companies_list',
        'get_bank_list','get_borrower_limit', 'get_master_group_list','list_ucic'
        ])
    <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu11" aria-expanded="false"
            aria-controls="collapseExample">
            <i class="fa fa-tasks" aria-hidden="true"></i>
            <span class="menu-title">Manage Master</span>
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
        <div class="collapse" id="layoutsSubmenu11">
            <ul class="nav flex-column sub-menu">
                @can('get_companies_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_companies_list') }}">Manage Companies</a>
                </li>
                @endcan
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
                @can('get_charges_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_charges_list') }}">Manage Charges</a>
                </li>
                @endcan
                @can('get_documents_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_documents_list') }}">Manage Document</a>
                </li>
                @endcan
                @can('get_industries_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_industries_list') }}">Manage Industry</a>
                </li>
                @endcan
                @can('get_vouchers_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_vouchers_list') }}">Manage Voucher</a>
                </li>
                @endcan
                @can('get_segment_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_segment_list') }}">Business Segment</a>
                </li>
                @endcan
                @can('get_entity_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_entity_list') }}">Business Entity</a>
                </li>
                @endcan
                @can('get_constitutions_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_constitutions_list') }}">Business Constitution</a>
                </li>
                @endcan
                @can('get_gst_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_gst_list') }}">Manage GST</a>
                </li>
                @endcan

                @can('get_equipment_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_equipment_list') }}">Manage Equipment</a>
                </li>
                @endcan
                
                @can('get_baserate_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_baserate_list') }}">Manage Base Rate</a>
                </li>
                @endcan
                @can('get_charges_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_tds_list') }}">Manage TDS</a>
                </li>
                @endcan
                @can('get_borrower_limit')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_borrower_limit') }}">Manage Borrower Limit</a>
                </li>
                @endcan
                @can('get_bank_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_bank_list') }}">Manage Bank</a>
                </li>
                @endcan
                @can('list_location_type')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('list_location_type') }}">Manage Location Type</a>
                </li>
                @endcan
                @can('list_security_document')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('list_security_document') }}">Manage Security Document Type</a>
                </li>
                @endcan
                @can('get_master_group_list')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_master_group_list') }}">Manage New Group</a>
                </li>
                @endcan
                @can('list_ucic')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('list_ucic') }}">Manage UCIC</a>
                </li>
                @endcan
            </ul>
        </div>
    </li>
    @endcan

    {{-- @canany(['lms_disbursal_request_list','lms_disbursed_list'])
         <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu8" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-bank" aria-hidden="true"></i>
                <span class="menu-title">Manage Disbursal</span>
               <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu8">
                <ul class="nav flex-column sub-menu">
                    @can('lms_disbursal_request_list')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lms_disbursal_request_list') }}">Disbursal Requests</a>
                    </li>
                    @endcan
                    @can('lms_disbursed_list')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('lms_disbursed_list')}}">Disbursal List</a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>
    @endcan --}}

    {{-- @canany(['lms_refund_list'])
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu8" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-undo" aria-hidden="true"></i>
                <span class="menu-title">Manage Refund</span>
               <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu8">
                <ul class="nav flex-column sub-menu">
                    @can('lms_refund_list')
                    <li class="nav-item">s
                        <a class="nav-link" href="{{ route('lms_refund_list') }}">Manage Refund</a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>
    @endcan --}}

    {{-- <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu8" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-file-text" aria-hidden="true"></i>
                <span class="menu-title">LMS Report</span>
               <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <div class="collapse" id="layoutsSubmenu8">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('soa_customer_view') }}">Supplier SOA</a>
                    </li>
                </ul>
            </div>
    </li> --}}

    @php $roleData = \Helpers::getUserRole() @endphp

    @if(isset($roleData[0]) && $roleData[0]->is_superadmin == 1)
    <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu10" aria-expanded="false"
            aria-controls="collapseExample">
            <i class="fa fa-bandcamp" aria-hidden="true"></i>
            <span class="menu-title">Access Management</span>
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
        <div class="collapse" id="layoutsSubmenu10">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_role') }}">Manage Roles</a>
                </li>
                    <!--<li class="nav-item">
                        <a class="nav-link" href="#">Manage Permissions</a>
                    </li> -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('get_role_user') }}">Manage Users</a>
                </li>
            </ul>
        </div>
    </li>
    
    @endif
   @canany(['assign_lead','assign_cases'])
    <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#layoutsSubmenu12" aria-expanded="false"
            aria-controls="collapseExample">
            <i class="fa fa-exchange" aria-hidden="true"></i>
            <span class="menu-title">Transfer Leads/Cases</span>
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
        <div class="collapse" id="layoutsSubmenu12">
            <ul class="nav flex-column sub-menu">
              @can('assign_lead')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('assign_lead') }}">Assign Leads</a>
                </li>
              @endcan
              @can('assign_cases')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('assign_cases') }}">Assign Cases</a>
                </li>
             @endcan
            </ul>
        </div>
    </li>
    @endcan
    </ul>
</nav>
