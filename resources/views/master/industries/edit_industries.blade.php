@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="industriesForm" name="industriesForm" method="POST" action="{{route('save_industries')}}" target="_top">
              @csrf

      <div class="row">
        <div class="form-group col-md-12">
          <label for="chrg_name">Industry Name</label>
          <input type="text" class="form-control" id="name" name="name" value="{{$industries_data->name}}" placeholder="Enter Industry Name" maxlength="50">
          <input type="hidden" class="form-control" name="id" id="id" maxlength="5" value="{{$industries_data->id}}">
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-12">
          <label for="cibil_indus_code">Industry Code</label>
          <input type="text" class="form-control" id="cibil_indus_code" name="cibil_indus_code" value="{{$industries_data->cibil_indus_code}}" placeholder="Enter Industry Code" maxlength="50">
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-6">
             <label for="chrg_type">Status</label><br />
             <select class="form-control" name="is_active" id="is_active">
                  <option value="" selected>Select</option>
                   <option {{$industries_data->is_active == 1 ? 'selected' : ''}} value="1">Active</option>
                  <option {{$industries_data->is_active == 2 ? 'selected' : ''}} value="2">In-Active</option>
              </select>
        </div>
      </div>
      <div class="row">
         <div class="form-group col-md-12 text-right">
             <input type="submit" class="btn btn-success btn-sm" name="add_charge" id="add_charge" value="Submit"/>
        </div>
      </div>
   </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">

    var messages={
        unique_industry_url:"{{ route('check_unique_industry_url') }}",
        unique_industry_code:"{{ route('check_unique_industry_code') }}",
        token: "{{ csrf_token() }}"
    }

    $(document).ready(function () {

        $.validator.addMethod("uniqueIndustry",
            function(value, element, params) {
                var result = true;
                var data = {name: value, _token: messages.token};
                if (params.industry_id) {
                    data['industry_id'] = params.industry_id;
                }
                console.log(params)
                $.ajax({
                    type:"POST",
                    async: false,
                    url: messages.unique_industry_url, // script to validate in server side
                    data: data,
                    success: function(data) {                        
                        result = (data.status == 1) ? false : true;
                    }
                });                
                return result;                
            },'Industry is already exists'
        );

        $.validator.addMethod("uniqueIndustryCode",
            function(value, element, params) {
                var result = true;
                var data = {cibil_indus_code: value, _token: messages.token};
                if (params.industry_id) {
                    data['industry_id'] = params.industry_id;
                }
                console.log(params)
                $.ajax({
                    type:"POST",
                    async: false,
                    url: messages.unique_industry_code, // script to validate in server side
                    data: data,
                    success: function(data) {                        
                        result = (data.status == 1) ? false : true;
                    }
                });                
                return result;                
            },'Industry Code is already exists'
        );

        $('#industriesForm').validate({ // initialize the plugin
            rules: {
                'name' : {
                    required : true,
                    uniqueIndustry: {
                        industry_id: $("#id").val()
                    }
                },
                'cibil_indus_code' : {
                    required : true,
                    uniqueIndustryCode: {
                        industry_id: $("#id").val()
                    }
                },
                'is_active' : {
                    required : true,
                },
            },
            messages: {
                'name': {
                    required: "Please enter Industry Name",
                },
                'cibil_indus_code': {
                    required: "Please enter Industry Code",
                },
                'is_active': {
                    required: "Please Select Status of Industry",
                },
            }
        });
    });
</script>
@endsection