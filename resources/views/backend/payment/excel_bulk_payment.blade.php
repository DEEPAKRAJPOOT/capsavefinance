<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title></title>
 <link rel="shortcut icon" href="{{url('backend/assets/images/favicon.png')}}" />
    <!--<link rel="stylesheet" href="fonts/font-awesome/font-awesome.min.css" />-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{url('backend/assets/css/perfect-scrollbar.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/jsgrid.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/jsgrid-theme.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/uploadfile.css')}}" >
    <link rel="stylesheet" href="{{url('backend/assets/css/data-table.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/plugins/datatables/css/datatables.min.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/style.css')}}" />
    <link rel="stylesheet" href="{{url('backend/assets/css/custom.css')}}" />
    <link rel="stylesheet" href="{{ url('common/js/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />

        <style>
            @import url("https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,700,900");
            body{margin:0; padding: 0;}
            td, th{padding: 5px;}
            .bld {  font-weight: bold;}
        </style>
    </head>
    <body>
    
        <div class="modal-body">
			<div class="row">
			<div class="col-md-6">
			<div class="form-group">
        <label for="txtCreditPeriod">Customer  <span class="error_message_label">*</span></label>
        <select class="form-control getCustomer" name="customer_id">
                                               <option> Please Select</option>
                                                    @foreach($customer as $row)
                                                    <option value="{{$row->user_id}}">{{$row->user->f_name}}/{{$row->customer_id}}</option>
                                                 @endforeach   
                                                </select>
		</div>
		</div>
		<div class="col-md-6">
		<div class="form-group">
               <label>Upload</label>
			   <input type="file" class="form-control form-control-sm">
			   <a class="float-right" href="xls/payment-upload.xls"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Download Template </a>
			   </div></div>
			   </div>
			   <div class="clearfix"></div>
			     <button type="button" class="btn btn-success btn-sm mt-3 float-right ml-2" data-dismiss="modal">Upload</button>
			      <button type="button" class="btn btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Cancel</button>
              
            </div>

     <script src="{{url('backend/assets/js/jquery-3.4.1.min.js')}}"></script>
    <script src="{{url('backend/assets/js/popper.min.js')}}"></script>
    <script src="{{url('backend/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{url('backend/assets/js/perfect-scrollbar.jquery.min.js')}}"></script>
    <script src="{{url('backend/assets/js/jsgrid.min.js')}}"></script>
    <script src="{{url('backend/assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{url('backend/assets/js/misc.js')}}"></script>
    <script src="{{url('backend/assets/js/jquery.validate.js')}}"></script>
    <script src="{{url('backend/assets/plugins/datatables/js/datatable.min.js')}}"></script>
    <script src="{{url('common/js/datetimepicker/js/bootstrap-datetimepicker.js')}}"></script>
    <script src="{{url('common/js/iframePopup.js')}}"></script> 
 
</body>
</html>