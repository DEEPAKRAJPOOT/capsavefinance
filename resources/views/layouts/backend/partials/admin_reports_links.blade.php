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
        <a class=" {{( $active=='duereport') ? 'active': null }} " href="{{route('report_duereport')}}">Invoice Due Report </a>
    </li>
    <li>
        <a class=" {{( $active=='overduereport') ? 'active': null }} " href="{{route('report_overduereport')}}">Invoice Over Due Report  </a>
    </li>
     <li>
        <a class=" {{( $active=='realisationreport') ? 'active': null }} " href="{{route('report_realisationreport')}}"> Realisation Report </a>
    <li>
        <a class=" {{( $active=='consolidatedSoa') ? 'active': null }} " href="{{route('soa_consolidated_view')}}">SOA  </a>
    </li>
</ul>  