@extends('layouts.backend.admin-layout')

@section('content')


@include('layouts.backend.partials.admin_customer_links',['active'=>'userInvoice'])


<div class="content-wrapper">
	<div class="row grid-margin mt-3">
		<div class="  col-md-12  ">
			
			<div class="card">
                <div class="card-body">

                <div class="head-sec">
                    <div class="pull-right" style="margin-bottom: 10px;margin-right: 12px;">
                        @can('create_user_invoice')
                        <a href="{{ route('create_user_invoice') }}" >
                            <button class="btn  btn-success btn-sm" type="button">
                                <span class="btn-label">
                                    <i class="fa fa-plus"></i>
                                </span>
                                Create Invoice
                            </button>

                        </a>
                        @endcan
                    </div>
                </div>
                
                    <div class="table-responsive ps ps--theme_default w-100">

                        <table class="table border-0">
                            <tbody>
                                <tr>
                                    <td class="text-left border-0" width="30%"> <b>Billing Address</b> </td>
                                    <td class="text-right border-0" width="30%"> <b>Original Of Recipient</b> </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <table class="table  table-td-right">
                               <tbody>
                               
                                <tr>
                                    <td class="text-left" width="30%"><b>Pan No : </b> AABCA5150B</td>
                                    <td class="text-left" width="30%"><b>Invoice No : </b> MH/19-20/001</td>
                                </tr>
                               
                                <tr>
                                    <td class="text-left" width="30%"><b>GSTIN : </b> EDRFT565</td>
                                    <td class="text-left" width="30%"><b>Invoice Date : </b> 01-April-2019</td>
                                </tr>
                               
                                <tr>
                                    <td class="text-left" width="30%" rowspan="1"><b>Address : </b><br>Ador Powertron Limited, Plot No-51</td>
                                    <td class="text-left" width="30%"><b>Refrence No : </b> CAP00A00000512</td>
                                </tr>
                               
                                <tr>
                                    <td class="text-left" width="30%"><b>State Code : </b> 27</td>
                                    <td class="text-left" width="30%"><b>Place of Supply : </b> Maharastra</td>
                                </tr>
                               

                            </tbody>
                        </table>
                    </div>
                </div>	

			</div>
		</div>
	</div>
</div>

@endsection


@section('additional_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
@endsection
@section('jscript')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script>


var messages = {
	data_not_found: "{{ trans('error_messages.data_not_found') }}",
	token: "{{ csrf_token() }}",
	user_id:"{{ $userInfo->user_id }}",
	};
</script>


@endsection