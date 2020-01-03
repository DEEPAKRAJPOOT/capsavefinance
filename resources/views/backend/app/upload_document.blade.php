@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="documentForm" style="width: 100%" method="POST" action="{{ Route('document_save') }}" enctype="multipart/form-data" target="_top">
        <!-- Modal body -->
        @csrf
        <input type="hidden" name="doc_id" id="doc_id" value="">
        <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}">
        <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}">

        <div class="modal-body text-left">

            <div id="is_required_addl_info">
              <div class="row" id="bank_dates">
                <div class="col-6">
                   <div class="form-group">
                      <label for="email">Select Month</label>
                      <select class="form-control" name="bank_month">
                         <option selected diabled value=''>Select Month</option>
                         @for($i=1;$i<=12;$i++)
                              <option value="{{$i}}">{{date('F', strtotime("2019-$i-01"))}}</option>
                         @endfor
                      </select>
                   </div>
                </div>
                <div class="col-6">
                   <div class="form-group">
                      <label for="email">Select Year</label>
                      <select class="form-control" name="bank_year">
                         <option value=''>Select Year</option>
                        @for($i=-3;$i<=0;$i++)
                            <option>{{date('Y')+$i}}</option>
                       @endfor;
                      </select>
                   </div>
                </div>
             </div>
              <div class="form-group">
                  <label for="email">Select Bank Name</label>
                  <select class="form-control" name="file_bank_id">
                      <option disabled value="" selected>Select Bank Name</option>
                      @foreach($bankdata as $bank)
                          <option value="{{$bank['id']}}">{{$bank['bank_name']}}</option>
                       @endforeach
                  </select>
              </div>
              <div class="form-group">
                  <label for="email">Select Financial  Year</label>
                  <select class="form-control" name="finc_year">
                     <option value=''>Select Year</option>
                     @for($i=-3;$i<=0;$i++)
                          <option>{{ (date('Y') + ($i-1)).'-'.(date('Y') + $i) }}</option>
                     @endfor;
                  </select>
               </div>
              <div class="row">
                  <div class="col-6">
                     <div class="form-group">
                        <label for="email">Select GST Month</label>
                        <select class="form-control" name="gst_month">
                           <option selected diabled value=''>Select Month</option>
                           @for($i=1;$i<=12;$i++)
                                <option value="{{$i}}">{{date('F', strtotime("2019-$i-01"))}}</option>
                           @endfor
                        </select>
                     </div>
                  </div>
                  <div class="col-6">
                     <div class="form-group">
                        <label for="email">Select GST Year</label>
                        <select class="form-control" name="gst_year">
                           <option value=''>Select Year</option>
                          @for($i=-3;$i<=0;$i++)
                              <option>{{date('Y')+$i}}</option>
                         @endfor;
                        </select>
                     </div>
                  </div>
               </div>
            </div>

            <div class="custom-file upload-btn-cls mb-3 mt-2">
                <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file[]" multiple="">
                <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
            <div class="row">
                <div class="col-md-12">
                   <div class="form-group">
                      <label for="email">Comment *</label>
                      <textarea type="text" name="comment" value="" class="form-control" tabindex="1" placeholder="Enter comment here ." required=""></textarea>
                   </div>
                </div>
            </div>
            <div class="row" id="is_not_for_gst">
              <div class="col-6">
                 <label>Is Password Protected</label>
                 <div class="form-group">
                    <label for="is_password_y">
                      <input type="radio" name="is_pwd_protected" id="is_password_y" value="1"> Yes
                    </label>
                    <label for="is_password_n">
                      <input type="radio" name="is_pwd_protected" checked id="is_password_n" value="0"> No
                    </label>
                 </div>
              </div>
              <div class="col-6">
                 <label>Is Scanned</label>
                 <div class="form-group">
                    <label for="is_scanned_y">
                      <input type="radio" name="is_scanned" id="is_scanned_y" value="1"> Yes
                    </label>
                    <label for="is_scanned_n">
                      <input type="radio" name="is_scanned" checked id="is_scanned_n" value="0"> No
                    </label>
                 </div>
              </div>
            </div>
            <div class="row" id="password_file_div">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="pwd_txt">Enter File Password</label>
                        <input type="password" placeholder="Enter File Password" class="form-control" name="pwd_txt" id="pwd_txt">
                     </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success float-right btn-sm" id="savedocument" >Submit</button>  
        </div>
    </form>
 
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ url('frontend/js/document.js') }}"></script>
<script>
    
   $(document).ready(function(){
        var docId = parent.$('#uploadDocId').val();
        $('#myModal').modal('show');
        $('#password_file_div').hide();
        $('#is_not_for_gst').show();
        $('input[name=docId]').val(docId);
        $('input[name=doc_id]').val(docId);
        $('select[name=file_bank_id]').parent('div').hide();
        $('select[name=finc_year]').parent('div').hide();
        $('select[name=gst_month]').parent('div').hide();
        $('select[name=gst_year]').parent('div').hide();
        $('textarea[name=comment]').parent('div').hide();
        if (docId != 6 && $('input[name="is_pwd_protected"]').is(':checked') && $('input[name="is_pwd_protected"]:checked').val() == '1') {
            $('#password_file_div').show();
        }
        $('#bank_dates').hide();
        
        if(docId == 4) {
            $('select[name=file_bank_id]').parent('div').show();
            $('#bank_dates').show();
        } else if (docId == 5) {
            $('select[name=finc_year]').parent('div').show();
        } else if (docId == 6) {    
            $('#is_not_for_gst').hide();
            $('select[name=gst_month]').parent('div').show();
            $('select[name=gst_year]').parent('div').show();            
        } else if (docId == 35 || docId == 36) {
            $('#is_not_for_gst').hide();    
            $('textarea[name=comment]').parent('div').show();
        } else {            
            $('#is_not_for_gst').hide();
            $('#is_required_addl_info').hide();       
            $('#bank_dates').hide();       
        }
        
    });
</script>
@endsection