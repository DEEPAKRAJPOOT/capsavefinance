@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="addressForm" name="addressForm" method="POST" action="{{ route('save_Gst') }}" target="_top">
        @csrf

        <input type="hidden" class="form-control" id="id" name="id" maxlength="5" value="{{$gst_data->tax_id}}">
        <div class="row">
            <div class="form-group col-6">
                <label for="tax_name">TAX Type</label>
                <select class="form-control tax_name" name="tax_name" id="tax_name">
                    <option disabled value="" selected>Select TAX</option>
                    <option value="GST" id="gst" {{$gst_data->tax_name == "GST" ? 'selected' : ''}}>GST</option>
                    <option value="IGST" id="igst" {{$gst_data->tax_name == "IGST" ? 'selected' : ''}}>IGST</option>

                </select>
            </div>
            <div class="form-group col-6">
                <label for="tax_value">Tax %</label>
                <input type="text" class="form-control" id="tax_value" name="tax_value" value="{{$gst_data->tax_value}}" placeholder="Range 1 to 100">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6" id="cgst2">
                <label for="cgst">CGST %</label>
                <input type="text" class="form-control" id="cgst" name="cgst" value="{{$gst_data->cgst}}" placeholder="Range 1 to 100" >
            </div>
            <div class="form-group col-6" id="sgst2">
                <label for="sgst">SGST %</label>
                <input type="text" class="form-control" id="sgst" name="sgst" value="{{$gst_data->sgst}}" placeholder="Range 1 to 100" >
            </div>
        </div>

        <div class="row">
            <div class="form-group col-6" id="igst_val">
                <label for="igst_">IGST %</label>
                <input type="text" class="form-control" id="igst_" name="igst" value="{{$gst_data->igst}}" placeholder="Range 1 to 100" >
            </div>
            <div class="form-group col-md-6">
                <label for="address_type">Status</label><br />
                <select class="form-control" name="is_active" id="is_active">
                    <option disabled selected>Select</option>
                    <option value="1" {{$gst_data->is_active == 1 ? 'selected' : ''}}>Active</option>
                    <option value="0" {{$gst_data->is_active == 0 ? 'selected' : ''}}>In-Active</option>

                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 mb-0">
                <input type="submit" class="btn btn-success btn-sm" name="add_gst" id="add_address" value="Submit" />
            </div>
        </div>
    </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function() {

         
        $('#addressForm').validate({ // initialize the plugin
            rules: {
                'tax_name': {
                    required: true,
                },
                'tax_value': {
                    required: true,
                    max:100
                },
                'cgst': {
                    required: true,
                },
                'sgst': {
                    required: true,
                },
                'is_active': {
                    required: true,
                },
            },
            messages: {
                'tax_name': {
                    required: "Please select TAX type",
                },
                'tax_value': {
                    required: "Please enter range",
                },
                'cgst': {
                    required: "Please enter CGST",
                },
                'sgst': {
                    required: "Please enter SGST",
                },
                'is_active': {
                    required: "Please select TAX Status",
                },
            }
        });
    });
</script>

<script>
    let taxval = document.getElementById('tax_value').value;
    let selectTag = document.getElementById("tax_name");
    let taxValue = document.getElementById('tax_value');
    document.getElementById('cgst').readOnly = true;
    document.getElementById('sgst').readOnly = true;
    document.getElementById('igst_').readOnly = true;
    document.getElementById('igst_val').style.display = 'none';
    document.getElementById('cgst').value = taxval
    document.getElementById('sgst').value = taxval
    document.getElementById('igst_').value = taxval
    
    
    selectTag.addEventListener('change', mainFun)
    taxValue.addEventListener('change', selectFun());

    // call main function
    function mainFun() {
        selectFun()
    }

    // function for display or remove input
    function selectFun() {
        taxValue.addEventListener('keyup', valueFun);
        let val = document.getElementById('tax_value').value
        if (document.getElementById('tax_name').value === 'IGST') {
            document.getElementById('cgst2').style.display = 'none';
            document.getElementById('sgst2').style.display = 'none';
            document.getElementById('cgst').value = 0;
            document.getElementById('sgst').value = 0;
            document.getElementById('igst_val').style.display = 'block';

            
            document.getElementById('igst_').value = val || 0;
        } else {
            document.getElementById('cgst2').style.display = 'block';
            document.getElementById('sgst2').style.display = 'block';
            document.getElementById('igst_val').style.display = 'none';

            document.getElementById('cgst').value = val / 2 || 0;
            document.getElementById('sgst').value = val / 2 || 0;
            document.getElementById('igst_').value = 0;

        }

    }

    // function for divide value
    function valueFun() {
        let val = document.getElementById('tax_value').value;
        console.log(val)
        if(document.getElementById('tax_name').value === 'IGST') {
            document.getElementById('cgst').value = 0;
            document.getElementById('sgst').value = 0;
            document.getElementById('igst_').value = val || 0;
        }
        else {
            document.getElementById('cgst').value = val / 2 || 0;
            document.getElementById('sgst').value = val / 2 || 0;
            document.getElementById('igst_').value = 0;
        }
    }

    document.getElementById('tax_value').addEventListener('input', event =>{
            let values = document.getElementById('tax_value').value;
            let s = values.toString();
            if(isNaN(document.getElementById('tax_value').value || event.keyCode(190))) {
                document.getElementById('tax_value').value = ""

            }
            if(s.length >= 6) {
                document.getElementById('tax_value').value = ""
            }

        });

</script>

@endsection