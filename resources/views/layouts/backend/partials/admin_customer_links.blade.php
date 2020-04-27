<ul class="main-menu">
    <li>
        <a href="{{ route('lms_get_customer_applications', [ 'user_id' => $userInfo->user_id ]) }}" class=" {{ ($active=='summary')? 'active': null }} ">Summary</a>
    </li>
    @can('lms_get_bank_account')
    <li>
        <a class=" {{( $active=='bank') ? 'active': null }} "  href="{{ route('lms_get_bank_account', [ 'user_id' => $userInfo->user_id ]) }}">Bank Account</a>
    </li>
    @endcan
    @can('addr_get_customer_list')
    <li>
		<a class=" {{( $active=='address') ? 'active': null }} " href="{{route('addr_get_customer_list',[ 'user_id' => $userInfo->user_id ])}}">Address </a>
    </li>
    @endcan
    @can('user_wise_invoice')
    <li>
        <a class=" {{ ($active=='invoice') ? 'active': null }} "  href="{{ route('user_wise_invoice', [ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ]) }}">View Invoices</a>
    </li>
    @endcan
    
   <!--  <li>
        <a class=" {{ ($active=='repayement') ? 'active': null }} " href="#">Repayment History</a>
    </li> -->
    @can('manage_charge')
    <li>
        <a class=" {{($active=='charges') ? 'active': null }} " href="{{route('manage_charge', ['user_id' => request()->get('user_id')])}}">Charges</a>
    </li>
      @endcan
          <li>
<!--
     @can('limit_management')
     <li>
        <a class=" {{($active=='customer') ? 'active': null }} " href="{{route('limit_management', ['user_id' => request()->get('user_id')])}}">Limit Management</a>
    </li>
      @endcan
    <!--
    <li>
        <a class=" {{ ($active=='soa') ? 'active': null }} "  href="#">SOA</a>
    </li>
    -->  
</ul>