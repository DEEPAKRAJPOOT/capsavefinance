@extends('layouts.backend.admin-layout')

@section('content')


<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Create User Invoice</h3>
            <small>Create User Invoice</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">View User Invoice</li>
                <li class="active">Create User Invoice</li>
            </ol>
        </div>
    </section>

    <div class="row grid-margin mt-3">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-fields">
                        <div class="active" id="details">

                        

                        <form id="addressForm" name="addressForm" method="POST" action="#" target="_top">
                            <div class="table-responsive ps ps--theme_default w-100">

                                <table class="table border-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-left border-0" width="30%"> <b>Billing Address</b> </td>
                                            <td class="text-right border-0" width="30%"> <b>Original Of Recipient</b> </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>

                                <table class="table border-0">
                                    <tbody>
                                        <tr>
                                            <!-- USER -->
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="gstin">GSTIN</label>
                                                        <select class="form-control" name="gstin" id="gstin">
                                                            <option disabled value="" selected>Select GSTIN</option>
                                                            <option value="">GST</option>
                                                            <option value="">IGST</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="state_id">State Name</label>
                                                        <select class="form-control" name="state_id" id="state_id">
                                                            <option disabled value="" selected>Select State</option>
                                                            @foreach($state_list as $stateName=>$stateList)
                                                            <option value="{{$stateList}}">{{$stateName}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="pan_no">Enter PAN Number</label>
                                                        <input type="text" class="form-control" id="pan_no" name="pan_no" placeholder="Enter PAN No">
                                                    </div>
                                                </div>

                                            </td>

                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="invoice_no">Invoice No</label>
                                                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" placeholder="Invoice Number">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="address">Enter Address</label>
                                                        <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address">
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="invoice_date">Invoice Date</label>
                                                        <input type="text" class="form-control" id="invoice_date" name="invoice_date" placeholder="Invoice Date">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="state_code">Enter State Code</label>
                                                        <input type="text" class="form-control" id="state_code" name="state_code" placeholder="Enter State Code">
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="refrence_no">Refrence No</label>
                                                        <input type="text" class="form-control" id="refrence_no" name="refrence_no" placeholder="Refrence Number">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-left border-0" width="30%"></td>
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="place_of_supply">Place Of Supply</label>
                                                        <input type="text" class="form-control" id="place_of_supply" name="place_of_supply" placeholder="Place Of Supply">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                        </form>



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>




@endsection