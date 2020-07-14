<ul class="main-menu">
    @can('lease_register')
    <li>
        <a class=" {{ ($active=='lease_register')? 'active': null }} " href="{{ route('lease_register') }}">Lease Register</a>
    </li>
    @endcan
    @can('report_duereport')
    <li>
        <a class=" {{( $active=='duereport') ? 'active': null }} " href="{{route('report_duereport')}}">Invoice Due Report </a>
    </li>
    @endcan
    @can('report_overduereport')
    <li>
        <a class=" {{( $active=='overduereport') ? 'active': null }} " href="{{route('report_overduereport')}}">Invoice Over Due Report  </a>
    </li>
    @endcan
    @can('report_realisationreport')
     <li>
        <a class=" {{( $active=='realisationreport') ? 'active': null }} " href="{{route('report_realisationreport')}}"> Realisation Report </a>
    </li>
    @endcan
    @can('soa_consolidated_view')
    <li>
        <a class=" {{( $active=='consolidatedSoa') ? 'active': null }} " href="{{route('soa_consolidated_view')}}">SOA  </a>
    </li>
    @endcan
    @can('cibil_report')
    <li>
        <a class=" {{( $active=='cibil_report') ? 'active': null }} " href="{{route('cibil_report')}}">Cibil UserData </a>
    </li>
    @endcan
</ul>
