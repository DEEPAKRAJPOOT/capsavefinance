<div class="row">
    <div class="col-md-4 Lh-3"><b>Customer ID:</b> <a href="{{route('lead_detail', ['user_id' => $userDetails['user_id']])}}" id="{{$userDetails['user_id']}}"> {{ $userDetails['customer_id']}}</a></div>
    <div class="col-md-4 Lh-3"><b>Customer Name:</b> {{ $userDetails['customer_name']}}</div>
    <div class="col-md-4 Lh-3"><b>Business Name:</b> {{ $userDetails['biz_entity_name']}}</div>
    <div class="col-md-12 Lh-3"><b>Address:</b> {{ $userDetails['address']}}</div>
</div>