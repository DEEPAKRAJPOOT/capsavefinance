@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-sidebar')
@include('layouts.backend.partials.admin-subnav')

<?php 
   $class_enable = 'getAnalysis';
?>

<div class="content-wrapper">
   @include('layouts.backend.partials.cam_nav')
   <div class="inner-container">
      <div class="card mt-4">
         <div class="card-body ">
            <div id="pullMsg"></div>
            <div class="data">
               <h2 class="sub-title bg mb-4">Last 6 months Bank statement-</h2>
               <div class="clearfix"></div>
               <div class="pl-4 pr-4 pb-4 pt-2">
                  @if($bankdocs->count() > 0)
                     @foreach($bankdocs as $bankdoc)
                  <div class="doc"  style="text-align: center;" id="bank_doc_{{$bankdoc->app_doc_file_id}}">
                     <small>{{ $bankdoc->doc_name }}</small>
                     <ul>
                        <li><span class="icon"><i class="fa fa-file-pdf-o"></i></span></li>
                        <li><a href="{{ Storage::url($bankdoc->file_path) }}" download target="_blank">Download Bank Statement</a></li>
                        <li>
                             <a href="javascript:void(0)" data-toggle="modal" data-target="#uploadBankDocument" data-url ="{{route('upload_bank_document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'app_doc_file_id' => $bankdoc->app_doc_file_id,'doc_id' => $bankdoc->doc_id]) }}" data-height="300px" data-width="100%" class="hide" id="reprocess_{{$bankdoc->app_doc_file_id}}">Re-Process</a>
                        </li>
                     </ul>
                  </div>
                     @endforeach
                  @endif
                  <div class="clearfix"></div>

                  <div style="text-align: right;">                  
                  @if(!empty($active_json_filename) && file_exists(storage_path("app/public/user/docs/$appId/banking/".$active_xlsx_filename)))
                     <a class="btn btn-success btn-sm" href="{{ Storage::url('user/docs/'.$appId.'/banking/'.$active_xlsx_filename) }}" download>Download</a>
                     @can('upload_xlsx_document')
                     <a class="btn btn-success btn-sm" href="javascript:void(0)"  data-toggle="modal" data-target="#uploadXLSXdoc" data-url ="{{route('upload_xlsx_document', ['app_id' => request()->get('app_id'), 'file_type' => 'banking']) }}" data-height="150px" data-width="100%">Upload XLSX</a>
                     @endcan
                  @endif 
                  @if(!empty($pending_rec) && $pending_rec['status'] == 'fail')
                     @php $class_enable="disabled"; @endphp
                     <a class="btn btn-success btn-sm process_stmt" pending="{{ $pending_rec['biz_perfios_id'] }}" href="javascript:void(0)">Process</a>
                  @endif
                  <a href="javascript:void(0)" class="btn btn-success btn-sm hide" biz_perfios_id="" id="getReport">Get Report</a>
                  @if(request()->get('view_only') && $bankdocs->count() > 0)
                    @can('getAnalysis')
                     <a href="javascript:void(0)" class="btn btn-success btn-sm <?php echo $class_enable ?>">Get Analysis</a>
                     @endcan
                  @endif   
                  </div> 
                  <div class="clearfix"></div>
                  <br/>
                  <hr>
                  <div class="clearfix"></div>
                  <h2 class="sub-title mt-4">Banking Analysis</h2>
                  <div class=" pb-4 pt-2">
                     <table cellspacing="0" cellpadding="0" class="table overview-table">
                        <tbody>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="3" style="font-size:18px;">Bank Summary </td>
                           </tr>
                           <tr>
                              <td style="border-right:0px;">
                                 <p style="margin-bottom: 0.5rem;margin-top: 0.5rem;"><b>Name :</b> &nbsp; {{ !empty($customers_info) ? $customers_info[0]['name'] : '' }}</p>
                                  <p style="margin-bottom: 0.5rem;"><b>Email :</b>  &nbsp; {{ !empty($customers_info) ? strtolower($customers_info[0]['email']) : '' }}</p>
                              </td>
                              <td  style="border-left:0px;">
                                 <p style="margin-bottom: 0.5rem;margin-top: 0.5rem;"><b>Account Number :</b>  &nbsp; {{ !empty($customers_info) ? $customers_info[0]['account_no'] : '' }}</p>
                                 <p style="margin-bottom: 0.5rem;"><b>Mobile :</b>  &nbsp; {{ !empty($customers_info) ? strtolower($customers_info[0]['mobile']) : '' }}</p>
                              </td>
                               <td  style="border-left:0px;">
                                  <p style="margin-bottom: 0.5rem;"><b>Bank Name :</b>  &nbsp; {{ !empty($customers_info) ? $customers_info[0]['bank'] : '' }}</p>
                                 <p style="margin-bottom: 0.5rem;"><b>Pan :</b>  &nbsp; {{ !empty($customers_info) ? strtolower($customers_info[0]['pan']) : '' }}</p>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     <div class="clearfix"></div>
                  </div>
                  <div id="paginate">
                        <?php 
                           echo $xlsx_pagination;
                        ?>
                  </div>
                  <div id="gridView">
                     <?php 
                        echo $xlsx_html;
                     ?>
                  </div>
               </div>
               <div class="clearfix"></div>
               <br/>
               <form method="POST" action="{{route('save_bank_detail')}}">
                  <input type="hidden" name="app_id" value="{{$appId}}">  
                  <input type="hidden" name="biz_id" value="{{$biz_id}}">  
                  <input type="hidden" name="bank_detail_id" value="{{$debtPosition->bank_detail_id ?? ''}}">  
                  @csrf
                  <div class="mt-4">
                     <h2 class="sub-title bg">Debt Position</h2>
                     <div class="pl-4 pr-4 pb-4 pt-2">
                        <div class="form-group row">
                         <label for="debt_on" class="col-sm-2 col-form-label">Date As On</label>
                         <div class="col-sm-4">
                           <input type="text" class="form-control" value="{{$debtPosition->debt_on ?? '' }}" name="debt_on" id="debt_on" placeholder="Select Date">
                         </div>
                        </div>
                        <textarea class="form-control form-control-sm" id="debt_position_comments" name="debt_position_comments" rows="3" spellcheck="false">{{$debtPosition->debt_position_comments ?? '' }}</textarea>
                     </div>
                 </div>
                 <br/>
                 <button type="submit" class="btn btn-success btn-sm float-right mt-2 mb-3"> Save</button>
              </form>
            </div>
         </div>
      </div>
   </div>
</div>
{!!Helpers::makeIframePopup('uploadBankDocument','Re-Upload Document', 'modal-md')!!}
{!!Helpers::makeIframePopup('uploadXLSXdoc','Upload XLSX Document', 'modal-md')!!}
@endsection
@section('jscript')
<script type="text/javascript">
   appId = '{{$appId}}';
   appurl = '{{URL::route("getAnalysis") }}';
   process_url = '{{URL::route("process_banking_statement") }}';
   _token = "{{ csrf_token() }}";
</script>
<script type="text/javascript">
    $("tr").each(function(){
      var deleteTd = true;
      target_tr = $(this);
      target_tr.find('td').each(function() {
        var thishtml = $(this).html();
        if(thishtml !== "") {
           deleteTd = false;
        }
      })
      if (deleteTd) {
        target_tr.remove();
      }
    })
 </script>

<script type="text/javascript">
   $(document).on('click', '.getAnalysis', function() {
      data = {appId, _token};
      $.ajax({
         url  : appurl,
         type :'POST',
         data : data,
         beforeSend: function() {
           $(".isloader").show();
         },
         dataType : 'json',
         success:function(result) {
            let mclass = result['status'] ? 'success' : 'danger';
            var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
            $("#pullMsg").html(html);
            if(result['errors']){
               $errors = result['errors'];
               Object.keys($errors).forEach(function(key) {
                  $('#reprocess_' + key).removeClass('hide');
                   $('#bank_doc_' + key).append('<small class="error" id="append_'+ key +'">'+ $errors[key] +'</small>');
               });
            }
            if (result['status']) {
               window.open(result['value']['file_url'], '_blank');
            }
            
         },
         error:function(error) {
            // body...
         },
         complete: function() {
            $(".isloader").hide();
         },
      })
   })



   $(document).on('click', '#getReport', function(argument) {
      var biz_perfios_id = $(this).attr('biz_perfios_id');
      getReport(biz_perfios_id);
   })

    $(document).on('click', '.process_stmt', function(argument) {
      var biz_perfios_id = $(this).attr('pending');
      getReport(biz_perfios_id);
   })

    function getReport(biz_perfios_id) {
       data = {appId, _token, biz_perfios_id};
      $.ajax({
         url  : process_url,
         type :'POST',
         data : data,
         beforeSend: function() {
           $(".isloader").show();
         },
         dataType : 'json',
         success:function(result) {
            let mclass = result['status'] ? 'success' : 'danger';
            var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
            $("#pullMsg").html(html);
            $(".isloader").hide();
            if (result['status']) {
             window.open(result['value']['file_url'], '_blank');
            }
         },
         error:function(error) {

         },
         complete: function() {
            $(".isloader").hide();
         },
      })
    }

    $(document).on('click','.pagination',function() {
         pageNo = $(this).attr('id');
         getExcel(pageNo);
      })
      function getExcel(page = 1) {
         var fileType = 'banking';
         data = {appId, page, _token, fileType};
         $.ajax({
            url  : '{{URL::route("getExcelSheet") }}',
            type :'POST',
            data : data,
            dataType : 'json',
            success:function(result) {
               $('#gridView').html(result.response.data);
               $('#paginate').html(result.response.paginate);
            },
            error:function(error) {

            },
            complete: function() {

            },
         })
      }
</script>
<script src="https://cdn.ckeditor.com/4.13.1/standard-all/ckeditor.js"></script>
<script>
   $('#debt_on').datetimepicker({
     format: 'dd/mm/yyyy',
     pickTime: false,
     minView: 2, 
     pickerPosition: 'bottom-right', 
   }).on('changeDate', function(e){
       $(this).datetimepicker('hide');
   });
CKEDITOR.replace('debt_position_comments', {
        extraPlugins: 'easyimage',
        height: 220,
        cloudServices_uploadUrl: 'https://33333.cke-cs.com/easyimage/upload/',
        cloudServices_tokenUrl: 'https://33333.cke-cs.com/token/dev/ijrDsqFix838Gh3wGO3F77FSW94BwcLXprJ4APSp3XQ26xsUHTi0jcb1hoBt',
        easyimage_toolbar: [
            'EasyImageFull',
            'EasyImageSide',
            'EasyImageAlignLeft',
            'EasyImageAlignRight',
            'EasyImageAlignCenter',
            'EasyImageAlt',
        ]
  });
</script>
@endsection
