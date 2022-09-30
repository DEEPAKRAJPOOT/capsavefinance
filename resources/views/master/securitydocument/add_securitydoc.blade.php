@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="securitydocumentForm" name="securitydocumentForm" method="POST" action="{{route('add_security_document')}}"
        target="_top">
        @csrf

        <div class="row">
            <div class="form-group col-md-12">
                <label for="name">Security Document Type</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Security Document Type"
                    maxlength="50">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_type">Status</label><br />
                <select class="form-control" name="is_active" id="is_active">
                    <option value="" selected>Select</option>
                    <option value="1">Active</option>
                    <option value="2">In-Active</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 text-right">
                <input type="submit" class="btn btn-success btn-sm" name="add_security_document" id="add_security_document"
                    value="Submit" />
            </div>
        </div>
    </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">

    var messages = {
        check_unique_securitydocument_name: "{{ route('check_unique_securitydocument_name') }}",
        token: "{{ csrf_token() }}"
    }

    $(document).ready(function () {
        $.validator.addMethod("uniqueSecurityDocumentName",
            function (value, element, params) {
                var result = true;
                var data = { name: value, _token: messages.token };
                if (params.security_doc_id) {
                    data['security_doc_id'] = params.security_doc_id;
                }
                $.ajax({
                    type: "POST",
                    async: false,
                    url: messages.check_unique_securitydocument_name, // script to validate in server side
                    data: data,
                    success: function (data) {
                        result = (data.status == 1) ? false : true;
                    }
                });
                return result;
            }, 'Security Document Name already exists'
        );

        $('#securitydocumentForm').validate({ // initialize the plugin
            rules: {
                'name': {
                    required: true,
                    uniqueSecurityDocumentName: true
                },
                'is_active': {
                    required: true,
                },
            },
            messages: {
                'name': {
                    required: "Please Enter Security Document Name",
                },
                'is_active': {
                    required: "Please Select Status",
                },
            }
        });
    });
</script>
@endsection