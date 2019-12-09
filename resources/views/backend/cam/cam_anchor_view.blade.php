@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-sidebar')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    <div class="inner-container">
        <form name="Anchorview" method="post" action="{{route('save_anchor_view')}}">
            <div class="data">
                <h2 class="sub-title bg">RELATIONSHIP WITH ANCHOR COMPANY</h2>
                <div class="pl-4 pr-4 pb-4 pt-2">


                    <input type="hidden" id="biz_id" name="biz_id" value="{{$biz_id}}">
                    <input type="hidden" id="app_id" name="app_id" value="{{$app_id}}">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">

                    <table class="table overview-table">

                        <tbody>
                            <tr>
                                <td width="30%">Years of Association with Group</td>
                                <td><input type="text" id="year_of_association" name="year_of_association" class="form-control" value=""></td>

                                <td>Years</td>
                                <td><input type="text" id="year" name="yearss" class="form-control" value=""></td>
                            </tr>
                            <tr>
                                <td>Payment Terms with the Group</td>
                                <td><input type="text" id="payment_terms" name="payment_terms" class="form-control" value=""></td>

                                <td>Rating / Reference by the Group</td>
                                <td><input type="text" id="grp_rating" name="grp_rating" class="form-control" value=""></td>
                            </tr>
                            <tr>
                                <td>Contact Person in Group Co. / Contact No.</td>
                                <td><input type="text" id="contact_number" name="contact_number" class="form-control" value=""></td>

                                <td>Security Deposit with Anchor Company</td>
                                <td><input type="text" id="security_deposit" name="security_deposit" class="form-control" value=""></td>
                            </tr>
                        </tbody>
                    </table>


                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="data mt-4">
                <h2 class="sub-title bg">Month on Month Lifting with Anchor Company:</h2>
                <div class="pl-4 pr-4 pb-4 pt-2">
                    <table class="table overview-table">

                        <tbody>
                            <tr >
                       
                        <td><b>Year</b></td>
                        <td colspan="2"><b><input type="text" name="year[0]" value="" id="year1" class="form-control"></b></td>
                        <td colspan="2"><b><input type="text" name="year[1]" value="" id="year2" class="form-control"></b></td>
                        </tr>
                        <tr>
                            <td><b>Month</b></td>
                            <td>In MT
                                <select name="mt_type[0]" class="form-control" id="mt_1">
                                    <option value="">Select</option>
                                    <option value="Kg"> Kg</option>
                                    <option value="Ton">Ton</option>
                                </select>
                            </td>
                            <td>In Rs. Lakhs</td>
                            <td>In Mt
                                <select name="mt_type[1]" class="form-control" id="mt_2">
                                    <option value="">Select</option>
                                    <option value="Kg"> Kg</option>
                                    <option value="Ton">Ton</option>
                                </select>
                            </td>
                            <td>In Rs. Lakhs</td>
                        </tr>
                        @php $myKey = 0;@endphp
                        
                        @php $months = ['April', 'May' , 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March', 'Total'] @endphp
                        @foreach($months as $key => $month)
                        
                        @if($myKey > 0)
                        @php $key = $myKey+1 @endphp
                        @php $myKey = $key+1 @endphp
                        @else
                        @php $myKey = $key+1 @endphp
                        @endif
                        <tr>
                            <td>{{$month}}</td>
                            <td><input type="text" onfocusout="checkNumberr()" onkeyup="checkNumberr()" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" name="month[0][{{$key}}]" value="" id="c_{{$key}}_{{$key+1}}" class="form-control"></td>
                            <td><input type="text" onfocusout="checkNumberr()" onkeyup="checkNumberr()" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" name="month[0][{{$myKey}}]" value="" id="c_{{$key}}_{{$key+1}}" class="form-control"></td>
                            <td><input type="text" onfocusout="checkNumberr()" onkeyup="checkNumberr()" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" name="month[1][{{$key}}]" value="" id="c_{{$key}}_{{$key+1}}" class="form-control"></td>
                            <td><input type="text" onfocusout="checkNumberr()" onkeyup="checkNumberr()" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" name="month[1][{{$myKey}}]" value="" id="c_{{$key}}_{{$key+1}}" class="form-control"></td>
                        </tr>
                        @endforeach
                        
                        
                        <tr>
                            <td>Note on Lifting:</td>
                            <td colspan="4"><input type="text" id="note_on_lifting" class="form-control" name="note_on_lifting" value=""></td>
                        </tr>
                        <tr>
                            <td>Reference from Anchor:</td>
                            <td colspan="4"><input type="text" id="reference_from_anchor" class="form-control" name="reference_from_anchor" value=""></td>
                        </tr>
                        </tbody>
                    </table>


                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="data mt-4">
                <h2 class="sub-title bg">Risk Comments on Relationship with Anchor</h2>
                <div class="pl-4 pr-4 pb-4 pt-2">
                    <textarea class="form-control" name='anchor_risk_comments' id="anchor_risk_comments" rows="3" spellcheck="false"></textarea>


                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="form-group text-right">
                                <button  class="btn btn-primary btn-ext submitBtnBank" type="submit">Submit</button>                                        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>   
@endsection
@section('jscript')
<script type="text/javascript">
    _token = "{{ csrf_token() }}";
</script>

<script type="text/javascript">

</script>
@endsection