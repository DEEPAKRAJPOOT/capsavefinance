@extends('layouts.backend.admin_popup_layout')
<div class="row">
    <div class="col-12">
        <table class="table table-bordered overview-table" cellpadding="0" cellspacing="0" border="1">
            <tbody>
                <tr style="background: #f2f2f2;font-weight: 600;">
                    <td>File Name</td>
                    <td>Action</td>
                </tr>
                
                @foreach($arrFileData as $arr)
                <tr>
                    <td>{{$arr->file_name}}</td>
                    <td> <a  href="{{ isset($arr->file_path) ? Storage::url($arr->file_path) : '' }}" class="add-btn-cls btn btn-success btn-sm " style="padding: 0.25rem 0.5rem;" title="Download File" type="button" download="{{$arr->file_name}}"><i class="fa fa-download"></i></a>

                    </td>
                </tr>
                @endforeach
            </tbody>    
        </table>
    </div>
</div>	

