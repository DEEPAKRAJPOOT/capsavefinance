@extends('layouts.backend.admin_popup_layout')
<div class="row">
    <div class="col-12">
        <table class="table table-bordered overview-table" cellpadding="0" cellspacing="0" border="1">
            <tbody>
                <tr>
                    <td>Query Details</td>
                    <td><div style="max-height: 200px; max-width: 500px; overflow:auto;">{!! $arrData->qms_cmnt !!}</div></td>
                </tr>
                
                @foreach($arrFileData as $arr)
                <tr>
                    <td>{{$arr->file_name}}</td>
                    <td> <a  href="{{ isset($arr->file_path) ? Storage::url($arr->file_path) : '' }}" class="btn-upload btn-sm" type="button" download="{{$arr->file_name}}"><i class="fa fa-download"></i></a></td>
                </tr>
                @endforeach
            </tbody>    
        </table>
    </div>
</div>	

