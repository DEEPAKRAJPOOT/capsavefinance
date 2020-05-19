<ul class="main-menu">
    <li>
        <a class=" {{ ($active=='summary')? 'active': null }} " href="{{ route('report_summary')}}">Summary</a>
    </li>
    <li>
        <a class=" {{ ($active=='customer')? 'active': null }} " href="{{ route('report_customer') }}">Customer</a>
    </li>
    <li>
        <a class=" {{ ($active=='lease_register')? 'active': null }} " href="{{ route('lease_register') }}">Lease Register</a>
    </li>
    <li>
        <a class=" {{( $active=='bank') ? 'active': null }} "  href="{{ route('report_bank') }}">Bank</a>
    </li>
    <li>
        <a class=" {{( $active=='company') ? 'active': null }} " href="{{route('report_company')}}">Company </a>
    </li>
</ul>  