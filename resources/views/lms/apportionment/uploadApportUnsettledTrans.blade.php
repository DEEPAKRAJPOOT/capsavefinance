
<form id="uploadUnTransForm" name="uploadUnTransForm" method="POST" action="{{route('upload_apport_unsettled_trans',[ 'user_id' => $userId , 'payment_id' => $paymentId, 'sanctionPageView' => $sanctionPageView,'type'=>'UploadForm'])}}" enctype="multipart/form-data">
@csrf              
<div class="custom-file">
    <label for="email">Upload Document</label>
    <input type="file" class="custom-file-input" id="upload_unsettled_trans" name="upload_unsettled_trans">
    <label class="custom-file-label val_print" for="upload_unsettled_trans">Choose file</label>
    {!! $errors->first('upload_unsettled_trans', '<span class="error">:message</span>') !!}
</div>
    <br> <br>
    <button type="submit" class="btn btn-success btn-sm float-right" id="saveUnsettled">Submit</button>  
</form>
