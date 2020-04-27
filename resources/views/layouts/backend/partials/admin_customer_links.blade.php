<ul class="main-menu">
    <li>
        <a class=" {{ ($active=='summary')? 'active': null }} " href="{{ route('lms_get_customer_applications', [ 'user_id' =>  request()->get('user_id')]) }}">Summary</a>
    </li>
    @can('lms_get_bank_account')
    <li>
        <a class=" {{( $active=='bank') ? 'active': null }} "  href="{{ route('lms_get_bank_account', [ 'user_id' =>  request()->get('user_id') ]) }}">Bank Account</a>
    </li>
    @endcan
    @can('addr_get_customer_list')
    <li>
        <a class=" {{( $active=='address') ? 'active': null }} " href="{{route('addr_get_customer_list',[ 'user_id' =>  request()->get('user_id') ])}}">Address </a>
    </li>
    @endcan
    @can('user_wise_invoice')
    <li>
        <a class=" {{ ($active=='invoice') ? 'active': null }} "  href="{{ route('user_wise_invoice', [ 'user_id' =>  request()->get('user_id'), 'app_id' => $userInfo->app->app_id ?? null, 'flag' => 1 ]) }}">View Invoices</a>
    </li>
    @endcan

    @can('manage_charge')
    <li>
        <a class=" {{($active=='charges') ? 'active': null }} " href="{{route('manage_charge', ['user_id' => request()->get('user_id')])}}">Charges</a>
    </li>

    @endcan
    @can('view_user_invoice')
    <li>
        <a class=" {{($active=='userInvoice') ? 'active': null }} " href="{{route('view_user_invoice', [ 'user_id' =>  request()->get('user_id') ] )}}">Create Invoice</a>
    </li>
    @endcan
    @can('apport_unsettled_view')
    <li>
        <a class=" {{ ($active=='unsettledTrans')? 'active': null }} " href="{{route('apport_unsettled_view', ['user_id' => request()->get('user_id'), 'sanctionPageView' => true])}}">Unsettled Tran.</a>
    </li>
    @endcan
    @can('apport_settled_view')
    <li>
        <a class=" {{ ($active=='settledTrans')? 'active': null }} " href="{{route('apport_settled_view', ['user_id' => request()->get('user_id'), 'sanctionPageView' => true])}}">Settled Tran.</a>
    <li>
      @endcan
         
<!--
     @can('limit_management')
     <li>
        <a class=" {{($active=='customer') ? 'active': null }} " href="{{route('limit_management', ['user_id' => request()->get('user_id')])}}">Limit Management</a>
>>>>>>> f_2104_gajendra
    </li>
    @endcan
    @can('apport_refund_view')
    <li>
        <a class=" {{ ($active=='refundTrans')? 'active': null }} " href="{{route('apport_refund_view', ['user_id' => request()->get('user_id'), 'sanctionPageView' => true])}}">Refund Tran.</a>
    </li>
    @endcan
    @can('lms_get_transaction')
    <li>
        <a class=" {{ ($active=='custSoa')? 'active': null }} " href="{{route('lms_get_transaction', ['user_id' => request()->get('user_id'), 'sanctionPageView' => true])}}">Cust. SOA</a>
    </li>
    @endcan
    @can('limit_management')
    <li>
        <a class=" {{($active=='customer') ? 'active': null }} " href="{{route('limit_management', ['user_id' => request()->get('user_id')])}}">Limit Management</a>
    </li>
    @endcan -->
</ul>  