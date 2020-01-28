@extends('layouts.backend.admin-layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Companies</h3>
            <small>Companies List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Companies</li>
                <li class="active">Companies List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <?php $currentRoute = Route::currentRouteName(); ?>
        <div class="card-body">
            <form method="GET" action="{{ route($currentRoute) }}" accept-charset="UTF-8" name="companyMaster" autocomplete="off" id="manageCompany">
                <div class="row" style="margin-bottom: 25px;">
                    <div class="col-md-4">
                        <input class="form-control" placeholder="Search by Company Name" name="search_keyword" type="text">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
                    </div>
                    <div class="col-md-7 text-right">
                        <a data-toggle="modal" class="btn  btn-success btn-sm" data-target="#addCompaniesFrame" data-url ="{{route('add_companies')}}" data-height="350px" data-width="100%" data-placement="top" >
                            <i class="fa fa-plus"></i> Add Company
                        </a>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive" id="fi_list">
                        <table id="CompaniesList1" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>S. No.</th>
                                    <th>Company Name</th>
                                    <th>Company Address</th>
                                    <th>GST No.</th>
                                    <th>PAN No.</th>
                                    <th>CIN No.</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $i = 1; ?>
                                @foreach($cmpData as $company)


                                <?php
                                $status = ['0' => 'Inactive', '1' => 'Active'];
                                ?>
                                <tr role="row" class="odd">
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $company['cmp_name'] }}</td>
                                    <td>{{ $company['cmp_add'] }}</td>
                                    <td>{{ $company['gst_no'] }}</td>                                      
                                    <td>{{ $company['pan_no'] }}</td>   
                                    <td>{{ $company['cin_no'] }}</td>
                                    <td>
                                        <div class="btn-group"><label class="badge badge-{{ ($status[$company['is_active']] == "Active") ? 'success' : 'danger' }} current-status">{{ $status[$company['is_active']] }}&nbsp; &nbsp;</label></div> 

                                    </td>
                                    <td>
                                        <div>
                                            
                                        @can('add_bank_account')
                                        <a  data-toggle="modal" 
                                           data-height="400px" 
                                           data-width="100%" 
                                           data-target="#add_bank_account"
                                           id="register" 
                                           data-url="{{ route('add_company_bank_account',['company_id' => $company['company_id']]) }}" >
                                            <button class="btn  btn-success btn-sm float-left mb-2" type="button">
                                                <i class="fa fa-plus"></i> Add Account
                                            </button>
                                        </a>
                                        @endcan
                                        <span style="margin-left:5px" >
                                        <a  
                                            data-toggle="modal" 
                                            title="Edit Company Detail"
                                            data-height="350px" 
                                            data-width="100%"
                                            data-target="#addCompaniesFrame" 
                                            data-url="{{ route('edit_companies',['id' => $company['company_id']]) }}"
                                            data-placement="top"
                                            <button class="btn btn-action-btn btn-sm" type="button">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </a>
                                        </span>
                                        </div>
                                    </td>
                                    <td align="right" ><span class="trigger"></span></td> 
                                </tr>

                                <tr class="dpr" style="display: none;">
                                    <td colspan="8" class="p-0">
                                        <table class="overview-table remove-tr-bg" cellpadding="0" cellspacing="0" border="0" width="100%">
                                            <tbody>
                                                <tr>
                                                    <td width="20%"><b>Account Holder Name</b></td>
                                                    <td width="20%"><b>Bank Name</b></td>
                                                    <td width="12%"><b>Account No.</b></td>
                                                    <td width="12%"><b>IFSC Code</b></td>
                                                    <td width="12%"><b>Branch Name</b></td>
                                                    <td width="12%"><b>Status</b></td>
                                                    <td width="12%"><b>Action</b></td>
                                                </tr>


                                                <?php
                                                $bank_list = \Helpers::getBankAccListByCompId($company['company_id']);
                                                ?>

                                                @foreach($bank_list as $bank)
                                                <?php
                                                $status = ['0' => 'Inactive', '1' => 'Active'];
                                                ?>
                                                <tr>
                                                    <td width="20%">{{ $bank['acc_name'] }}</td>
                                                    <td width="20%">{{ $bank['bank_name'] }}</td>
                                                    <td width="12%">{{ $bank['acc_no'] }}</td>
                                                    <td width="12%">{{ $bank['ifsc_code'] }}</td>
                                                    <td width="12%">{{ $bank['branch_name'] }}</td>
                                                    <td width="12%"><div class="btn-group"><label class="badge badge-{{ ($status[$bank->is_active] == "Active") ? 'success' : 'danger' }} current-status">{{ $status[$bank['is_active']] }}&nbsp; &nbsp;</label></div></td>
                                                    <td width="12%">
                                                        <a  
                                                            data-toggle="modal" 
                                                            title="Edit Bank Account" 
                                                            data-height="400px" 
                                                            data-width="100%" 
                                                            data-target="#add_bank_account"
                                                            id="register" 
                                                            data-url="{{ route('add_company_bank_account',['company_id' => $company['company_id'], 'bank_account_id' => $bank['bank_account_id']]) }}"
                                                            <button class="btn btn-action-btn btn-sm" type="button">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                        </a>
                                                        <span style="margin-left:10px">{!! ($bank['is_default'] == 1) ? "<input type='checkbox' checked disabled='disabled'> Default" : '' !!}</span>
                                                    </td>
                                                </tr>

                                                @endforeach

                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                @endforeach



                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
{!!Helpers::makeIframePopup('addCompaniesFrame','Add Company', 'modal-lg')!!}
{!!Helpers::makeIframePopup('add_bank_account','Add Bank', 'modal-lg')!!}
@endsection
