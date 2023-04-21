 <?php
    $route_name = \Request::route()->getName();
  ?>
 <ul class="sub-menu-main pl-0 m-0">
     @can('cam_overview')
        <li>
            <a href="{{route('cam_overview', ['user_id' => request()->get('user_id') , 'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{ $route_name == 'cam_overview' ? 'active' : ''}}">Overview</a>
        </li>
      @endcan
    
    @can('reviewer_summary')
        <li>
            <a href="{{route('reviewer_summary', ['user_id' => request()->get('user_id') , 'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{ $route_name == 'reviewer_summary' ? 'active' : ''}}">Reviewer Summary</a>
        </li>
    @endcan

    @can('cam_report')
        <li>
            <a href="{{route('cam_report', ['user_id' => request()->get('user_id') , 'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{ $route_name == 'cam_report' ? 'active' : ''}}">CAM Report</a>
        </li>
        @endcan

      @can('anchor_view')
        <li>
            <a href="{{route('anchor_view', ['user_id' => request()->get('user_id') , 'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{$route_name == 'anchor_view' ? 'active' : '' }}">Anchor</a>
        </li>
        @endcan

      @can('cam_promoter')
        <li>
            <a href="{{route('cam_promoter', ['user_id' => request()->get('user_id') , 'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{$route_name == 'cam_promoter' ? 'active' : '' }}">Management</a>
        </li>
      @endcan

      @can('cam_cibil')
        <li>
            <a href="{{route('cam_cibil', ['user_id' => request()->get('user_id') , 'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{$route_name == 'cam_cibil' ? 'active' : '' }}">Credit History &amp; Hygine Check</a>
        </li>
      @endcan

       @can('cam_bank')
        <li>
            <a href="{{route('cam_bank', ['user_id' => request()->get('user_id') , 'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{$route_name == 'cam_bank' ? 'active' : '' }}">Banking</a>
        </li>
      @endcan

      @can('cam_finance')
        <li>
            <a href="{{ route('cam_finance', ['user_id' => request()->get('user_id') , 'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{$route_name == 'cam_finance' ? 'active' : '' }}">Financial</a>
        </li>
      @endcan

      @can('cam_gstin')
        <li>
            <a href="{{ route('cam_gstin', ['user_id' => request()->get('user_id') , 'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{$route_name == 'cam_gstin' ? 'active' : '' }}">GST/Ledger Detail</a>
        </li>
      @endcan

      @can('limit_assessment')
        <li>
            <a href="{{ route('limit_assessment', ['user_id' => request()->get('user_id') , 'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{$route_name == 'limit_assessment' ? 'active' : '' }}">Limit Assessment</a>
        </li>
      @endcan
      @can('security_deposit')
      <li>
        <a href="{{route('security_deposit', ['user_id' => request()->get('user_id') , 'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{ $route_name == 'security_deposit' ? 'active' : ''}}">Pre/Post Disbursement</a>
    </li>
    @endcan
       <!--  <li>
            <a href="#" class="{{$route_name == 'cam_gst' ? 'active' : '' }}">Limit Management</a>
        </li> -->
  </ul>