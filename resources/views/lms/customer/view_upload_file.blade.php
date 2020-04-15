@extends('layouts.backend.admin_popup_layout')
@section('content')


<td>{{url($path.$response->doc_name)}}</td>


<iframe src="{{url($path.$response->doc_name)}};" frameborder="0"></iframe>


@endsection