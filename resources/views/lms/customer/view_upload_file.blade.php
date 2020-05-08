@extends('layouts.backend.admin_popup_layout')
@section('content')


<td>{{url($file->doc_name)}}</td>

<embed src="{{ Storage::url($file->doc_name) }}" style="width:600px; height:800px;" frameborder="0">


@endsection