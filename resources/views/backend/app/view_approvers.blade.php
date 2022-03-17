@extends('layouts.backend.admin_popup_layout')

@section('content')
    <div class="row">    
        <div class="col-12 dataTables_wrapper mt-4">
            <div class="overflow">
                <div id="tMsg"></div> 
                <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="approver" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th>Approver Name</th>
                                        <th>Email</th>
                                        <th>Approved Date</th>
                                        <th>Status</th>
                                        <th>Uploaded By</th>
                                        <th width="2%">Action (Upload/Download)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($approvers) != 0)
                                    @foreach($approvers as $approver)
                                        @php
                                            $style = '';
                                        @endphp 
                                        @if ($approver['approval_file_id'] == '' && $approver['stauts'] == 'Approved')
                                            @php
                                                $style = 'display: none;';
                                            @endphp   
                                        @endif
                                    <tr>
                                        <td>{{ $approver['approver'] }} {{--<small> ( {{ $approver['approver_role'] }} )</small>--}}</td>
                                        <td>{{ $approver['approver_email'] }}</td>
                                        <td>{{ $approver['approved_date'] }}</td>
                                        <td>{{ $approver['stauts'] }}</td>
                                        <td>
                                            @if ($approver['file_updated_by'] && $approver['file_updated_at'])
                                                {{ \Helpers::getUserName($approver['file_updated_by']) }}<br/>{{ $approver['file_updated_at'] }}
                                            @else
                                                ---
                                            @endif
                                        </td>
                                        <td>
                                        @can('upload_approval_mail_copy')
                                            @if (!$isFinalUpload)
                                                <a href="javascript:void(0)" onclick="document.querySelector('.approval-doc-files').click();setDataValue('{{ $approver['app_id'] }}#{{ $approver['app_appr_status_id'] }}');" title="Upload Approval Mail Copy" class="btn btn-action-btn btn-sm" style="{{ $style  }}"><i class="fa fa-upload fa-2xl fa-fw" aria-hidden="true"></i></a>
                                                <input type="file" id="approval-doc-file-{{ $approver['app_appr_status_id'] }}" class="approval-doc-files" name="approval-doc-file" style="display: none;" class="form-control">   
                                            @endif
                                        @endcan
                                        @can('download_approval_file_copy')
                                            @if ($approver['approval_file_id'] && $approver['approval_file_id'] != '') 
                                                <a href="{{ route('download_approval_file_copy', ['file_id' => $approver['approval_file_id'] ]) }}" title="Download Approval Mail Copy" class="btn btn-action-btn btn-sm"><i class="fa fa-download fa-2xl fa-fw" aria-hidden="true"></i></a></td>
                                            @endif
                                        @endcan
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4"> No record found </td>
                                    </tr>
                                    @endIf
                                </tbody>
                            </table>
                            <input type="hidden" id="dataValue" value=""/>
                            <div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('jscript')
<script>
try {
    var messages = {
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        upload_approval_mail_copy  : "{{ route('upload_approval_mail_copy') }}",
    };
    jQuery(document).ready(function ($) {
        var el = document.querySelector('.approval-doc-files');
        if(el){
            el.addEventListener('change', readFile, false);
        }
    });
    function readFile() {
        if (window.File && window.FileList && window.FileReader) {
            var files = event.target.files; //FileList object
            var dataValue = $("#dataValue").val();
            formdata = new FormData();
            isValidExt = false;
            for (let i = 0; i < files.length; i++) {
                var file = files[i];
                //alert(file.type);
                if (file.type.match('image')){
                    isValidExt = true;
                    if (formdata) {
                        formdata.append("approval_doc_file", file);
                        formdata.append("_token", messages.token);
                    }
                }
            }
            if(isValidExt){
                dataValues = dataValue.split("#");
                console.log(dataValues);
                formdata.append("app_id", dataValues[0]);
                formdata.append("app_appr_status_id", dataValues[1]);
                formdata.append("uploadApprovalMailCopyViaApproverList", 1);
            }else{
                isValidExt = false; 
            }
            if(isValidExt){
                $.ajax({
                    type: "POST",
                    url: messages.upload_approval_mail_copy,
                    dataType: 'json',
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status === 1)
                        {
                            $('#tMsg').show().html('<span style="color: green;font-weight: 900;margin: 22px 5px 32px;font-size: 13px;">'+res.msg+'</span>');
                                $('html, body').animate({
                                    scrollTop: $('#tMsg').offset().top -100 
                            }, 1000);
                            //hideAlertBox();
                            setTimeout(function(){
                                var p = window;
                                p.location.reload();
                            }, 1000);  
                          if(res.isFinalSubmit === 1){
                               window.parent.$('#searchbtn').trigger('click');
                           }
                        }else if (res.status === 0){
                            $('#tMsg').show().html('<span style="color: red;font-weight: 900;margin: 22px 5px 32px;font-size: 13px;">'+res.msg+'</span>');
                                $('html, body').animate({
                                    scrollTop: $('#tMsg').offset().top -100 
                            }, 1000);
                            //hideAlertBox();
                            setTimeout(function(){
                                var p = window;
                                p.location.reload();
                            }, 1000);

                        }
                        return false;
                    },
                    error: function (error) {
                        alert('error; ' +error);
                    } 
                });
            }else{
                $('#tMsg').show().html('<span style="color: red;font-weight: 900;margin: 22px 5px 32px;font-size: 13px;">Please enter a value with a valid extensions. (Type:- image format)</span>');
                        $('html, body').animate({
                            scrollTop: $('#tMsg').offset().top - 60
                    }, 1000);
            }
            $('.approval-doc-files').val('');
            $("#dataValue").val('');
            hideAlertBox();
        } else {
            console.log('Browser not support');
        }
    }
    function setDataValue(d){
       $("#dataValue").val('').val(d);
    }
    function hideAlertBox(){
        $("#tMsg").delay(4000).fadeOut(200, function() {
            $(this).alert('close');
        });
    }
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
</script>
@endsection  