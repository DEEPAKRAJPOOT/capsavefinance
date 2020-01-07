<ul class="main-menu">
    <li>
        <a href="" class=" {{ ($active=='summary')? 'active': null }} ">Summary</a>
    </li>
    <li>
        <a class=" {{( $active=='bank') ? 'active': null }} "  href="{{ route('lms_get_bank_account', [ 'user_id' => $userInfo->user_id ]) }}">Bank Account</a>
    </li>
    <li>
        <a class=" {{ ($active=='invoice') ? 'active': null }} "  href="{{ route('lms_get_application_invoice', [ 'user_id' => $userInfo->user_id ]) }}">View Invoices</a>
    </li>
    <li>
        <a class=" {{ ($active=='repayement') ? 'active': null }} " href="">Repayment History</a>
    </li>
    <li>
        <a class=" {{($active=='charges') ? 'active': null }} " href="">Charges</a>
    </li>
    <li>
        <a class=" {{ ($active=='soa') ? 'active': null }} "  href="">SOA</a>
    </li>

</ul>