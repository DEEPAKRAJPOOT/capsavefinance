@extends('layouts.confirm-layout')

@section('content')

 


<table class="table  overview-table">
@if($res != null)
    <thead class="thead-primary">
        <tr>
            <th class="text-left" width="10%">Driving License Detail</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>														
                <div id="accordion" class="accordion d-table col-sm-12">
                    <div class="card card-color mb-0">
                        <div class="card-header" data-toggle="collapse" href="#collapse1">
                            <a class="card-title ">
                                <b>Personal Detail</b>
                            </a>
                        </div>
                        <div id="collapse1" class="card-body collapse p-0 show" data-parent="#accordion">
                            <table class="table table-hover overview-table mb-3">		
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{isset($res->name) ? $res->name : 'N/A' }}</td>													
                                    </tr>
                                     <tr>
                                        <th>Photo</th>
                                        <td><img src="data:image/png;base64, {{$res->img}}" width="130" height="150"></td>													
                                    </tr>
                                    <tr>
                                        <th>Father/Husband Name</th>
                                        <td>{{isset($res->name) ? '' : 'N/A' }}</td>													
                                    </tr>
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td>{{isset($res->dob) ? $res->dob : 'N/A' }}</td>													
                                    </tr>
                                    <tr>
                                        <th>Issued On</th>
                                        <td>{{isset($res->issue_date) ? $res->issue_date : '...' }}</td>													
                                    </tr> 
                                    <tr>
                                        <th>Address</th>
                                        <td>{{isset($res->address) ? $res->address : '...' }}</td>													
                                    </tr>
                                    <tr>
                                        <th>Blood Group</th>
                                        <td>{{isset($res->blood_group) ? $res->blood_group : '...' }}</td>													
                                    </tr>                                      
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="card card-color mb-0">
                        <div class="card-header" data-toggle="collapse" href="#collapse2">
                            <a class="card-title">
                                <b>License Validity</b>
                            </a>
                        </div>
                        <div id="collapse2" class="card-body collapse p-0 show" data-parent="#accordion">
                            <table class="table table-hover overview-table">			
                                <tbody>
                                    <tr>
                                        <th>Transport</th>
                                        <td>--</td>													
                                    </tr>
                                    
                                    <tr>
                                        <th>Non-Transport</th>
                                        <td>--</td>				
                                    </tr>   
                                     
                                </tbody>
                            </table>
                        </div>
                    </div>																  
                    <div class="card card-color mb-0">
                        <div class="card-header" data-toggle="collapse" href="#collapse3">
                            <a class="card-title">
                                <b>Class Of Vehicles</b>
                            </a>
                        </div>
                        <div id="collapse3" class="card-body collapse p-0 show" data-parent="#accordion">
                            <table class="table  overview-table" cellspacing="0" cellpadding="0" border="1">
                                <tbody>
                                    <tr>
                                        
                                        
                                        <td width="20%"><b>Sr. No</b></td>
                                        <td width="20%"><b>COV</b></td>
                                        <td width="20%"><b>	Issued Date</b></td>
                                    </tr>
                                    @php ($i = 0)
                                     
                                    @foreach($res->cov_details as $row)
                                    <tr>
                                        <td width="20%">{{$i}}</td>
                                        <td width="20%">{{$row->cov}}</td>
                                        <td width="20%">{{$row->issue_date}}</td>
                                    </tr>
                                    @php ($i++)
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </td>														
        </tr>													
    </tbody>

    @else 
    <thead class="thead-primary">
        <tr>
            <th class="text-left" colspan="4" width="10%">Driving License Not Verified</th>
        </tr>
    </thead>
    @endif
</table>


 
@endsection

@section('jscript')

<script>
    var messages = {
        is_accept: "{{ Session::get('is_accept') }}",
    };
    $(document).ready(function () {

        if (messages.is_accept == 1) {
            var parent = window.parent;
            parent.jQuery("#pickLead").modal('hide');
            //window.parent.jQuery('#my-loading').css('display','block');

            parent.oTable1.draw();
            //window.parent.location.href = messages.paypal_gatway;
        }

    })
</script>
@endsection