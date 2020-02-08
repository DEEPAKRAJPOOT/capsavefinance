<ul class="main-menu">
    <li>
        <a href="" class=" {{ ($active=='summary')? 'active': null }} ">Summary</a>
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
    <li>
        <a class=" {{ ($active=='invoice') ? 'active': null }} "  href="{{ route('lms_get_application_invoice', [ 'user_id' => $userInfo->user_id ]) }}">View Invoices</a>
    </li>
    <li>
        <a class=" {{ ($active=='repayement') ? 'active': null }} " href="#">Repayment History</a>
    </li>
    <li>
        <a class=" {{($active=='charges') ? 'active': null }} " href="{{route('manage_charge')}}">Charges</a>
    </li>
    <li>
        <a class=" {{ ($active=='soa') ? 'active': null }} "  href="#">SOA</a>
    </li>

</ul>