<ul class="main-menu">
    @can('lease_register')
    <li>
        <a class=" {{ ($active=='lease_register')? 'active': null }} " href="{{ route('lease_register') }}">CFPL InvRegister</a>
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
    @can('report_outstandingreport')
    <li>
        <a class=" {{( $active=='outstandingreport') ? 'active': null }} " href="{{route('report_outstandingreport')}}">Outstanding Report  </a>
    </li>
    @endcan
    @can('outstanding_report_manual')
    <li>
        <a class=" {{( $active=='outstandingreportmanual') ? 'active': null }} " href="{{route('outstanding_report_manual')}}">Invoice Outstanding Report</a>
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
    @can('tds')
    <li>
        <a class=" {{ ($active=='tds')? 'active': null }} " href="{{ route('tds') }}">TDS Report</a>
    </li>
    @endcan
    @can('interest_breakup')
    <li>
        <a class=" {{ ($active=='interest_breakup')? 'active': null }} " href="{{ route('interest_breakup') }}">Interest Breakup</a>
    </li>
    @endcan
    @can('charge_breakup')
    <li>
        <a class=" {{ ($active=='charge_breakup')? 'active': null }} " href="{{ route('charge_breakup') }}">Charge Breakup</a>
    </li>
    @endcan
    @can('tds_breakup')
    <li>
        <a class=" {{ ($active=='tds_breakup')? 'active': null }} " href="{{ route('tds_breakup') }}">TDS Breakup</a>
    </li>
    @endcan
    @can('recon_report')
    <li>
        <a class=" {{( $active=='reconReport') ? 'active': null }} " href="{{route('recon_report')}}">Recon Report</a>
    </li>
    @endcan
</ul>
