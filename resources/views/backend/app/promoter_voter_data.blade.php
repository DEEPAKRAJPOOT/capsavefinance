@extends('layouts.confirm-layout')

@section('content')


<table class="table  table-striped table-hover overview-table">
    @if($res != null)
					<thead class="thead-primary">
						<tr>
							<th class="text-left" colspan="2" width="10%">Voter ID (EPIC) Detail</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>EPIC No	</th>
							<td>{{isset($res->epic_no) ? $res->epic_no : '' }}</td>
						</tr>
						
						<tr>
							<th>Full Name</th>
							<td>{{isset($res->name) ? $res->name : '' }}</td>
						</tr> 
						<tr>
							<th>Full Name (Vernacular Language)</th>
							<td>{{isset($res->name_v1) ? $res->name_v1 : '' }}</td>
						</tr>
						<tr>
							<th>Name of Relative</th>
							<td>{{isset($res->rln_name) ? $res->rln_name : '' }}</td>
						</tr>
						<tr>
							<th>Name of Relative (Vernacular Language)</th>
							<td>{{isset($res->rln_name_v1) ? $res->rln_name_v1 : '' }}</td>
						</tr>
						<tr>
							<th>Relationship</th>
							<td>{{isset($res->rln_type) ? $res->rln_type : '' }}</td>
						</tr>
						<tr>
							<th>Gender</th>
							<td>{{isset($res->gender) ? $res->gender : '' }}</td>
						</tr> 
						<tr>
							<th>Age</th>
							<td>{{isset($res->age) ? $res->age : '' }}</td>
						</tr>
						<tr>
							<th>Date of Birth</th>
							<td>{{isset($res->dob) ? $res->dob : '' }}</td>
						</tr>
						<tr>
							<th>House No</th>
							<td>{{isset($res->house_no) ? $res->house_no : '' }}</td>
						</tr>
						<tr>
							<th>Part (Location) Name</th>
							<td>{{isset($res->ps_name) ? $res->ps_name : '' }}</td>
						</tr>
						<tr>
							<th>Parliamentary Constituency Name</th>
							<td>{{isset($res->pc_name) ? $res->pc_name : '' }}</td>
						</tr> 
						<tr>
							<th>Assembly Constituency</th>
							<td>{{isset($res->ac_name) ? $res->ac_name : '' }}</td>
						</tr>
						<tr>
							<th>District</th>
							<td>{{isset($res->district) ? $res->district : '' }}</td>
						</tr>
						<tr>
							<th>State</th>
							<td>{{isset($res->state) ? $res->state : '' }}</td>
						</tr>
						<tr>
							<th>State Code</th>
							<td>{{isset($res->st_code) ? $res->st_code : '' }}</td>
						</tr> 
						<tr>
							<th>Part (Location) no</th>
							<td>{{isset($res->part_name) ? $res->part_name : '' }}</td>
						</tr>
						<tr>
							<th>Lat Long for the polling booth</th>
							<td>{{isset($res->ps_lat_long) ? $res->ps_lat_long : '' }}</td>
						</tr>
						<tr>
							<th>Polling Booth Address</th>
							<td>{{isset($res->status) ? $res->status : '' }}</td>
						</tr>
						<tr>
							<th>Section of the constituency part</th>
							<td>....</td>
						</tr>
						<tr>
							<th>Serial number</th>
							<td>{{isset($res->section_no) ? $res->section_no : '' }}</td>
						</tr>
						<tr>
							<th>Unique ID</th>
							<td>{{isset($res->id) ? $res->id : '' }}</td>
						</tr> 
						<tr>
							<th>Last date of update</th>
							<td>...</td>
						</tr>                                                    
					</tbody>
    @else 
    <thead class="thead-primary">
        <tr>
            <th class="text-left" colspan="4" width="10%">Voter ID Not Verified</th>
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
    $(document).ready(function() {

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