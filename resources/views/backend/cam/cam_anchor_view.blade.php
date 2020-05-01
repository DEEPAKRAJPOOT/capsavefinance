@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-sidebar')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    <div class="inner-container">
        <form id="anchor_form" name="Anchorview" method="post" action="{{route('save_anchor_view')}}">
            @csrf
            <div class="data">
                <h2 class="sub-title bg">RELATIONSHIP WITH ANCHOR COMPANY</h2>
                <div class="pl-4 pr-4 pb-4 pt-2">
                    <input type="hidden" id="biz_id" name="biz_id" value="{{$biz_id}}">
                    <input type="hidden" id="app_id" name="app_id" value="{{$app_id}}">
                    <table class="table overview-table">

                        <tbody>
                            <tr>
                                <td width="30%">Years of Association with Group</td>
                                <td>
                                    <input type="text" id="year_of_association" name="year_of_association" class="form-control" value="{{isset($anchorRelationData['year_of_association']) ? $anchorRelationData['year_of_association'] : ''}}" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');" maxlength="3">
                                    {!! $errors->first('year_of_association', '<span class="error">:message</span>') !!}
                                </td>
                                <td>Payment Terms with the Group</td>
                                <td>
                                        <input type="text" id="payment_terms" name="payment_terms" class="form-control" value="{{isset($anchorRelationData['payment_terms']) ? $anchorRelationData['payment_terms'] : ''}}" maxlength="3" oninput="this.value = this.value.replace(/[^0-9A-Za-z]/g, '').replace(/(\..*)\./g, '$1');">
                                    {!! $errors->first('payment_terms', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>
                            <tr>
                                <td>Rating / Reference by the Group</td>
                                <td>
                                    <input type="text" id="grp_rating" name="grp_rating" class="form-control" value="{{isset($anchorRelationData['grp_rating']) ? $anchorRelationData['grp_rating'] : ''}}" maxlength="3" oninput="this.value = this.value.replace(/[^0-9A-Za-z.\+]/g, '').replace(/(\..*)\./g, '$1');">
                                    {!! $errors->first('grp_rating', '<span class="error">:message</span>') !!}
                                </td>
                                <td>Security Deposit with Anchor Company</td>
                                <td><span class="fa fa-inr" aria-hidden="true" style="position:absolute; margin:12px 5px; "></span><input type="text" id="security_deposit" name="security_deposit" class="number_format form-control" value="{{isset($anchorRelationData['security_deposit']) ? number_format($anchorRelationData['security_deposit']) : ''}}" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
                                    {!! $errors->first('security_deposit', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>
                            <tr>
                                <td>Contact Person in Group Co.</td>
                                <td>
                                    <input type="text" id="contact_person" name="contact_person" class="form-control" value="{{isset($anchorRelationData['contact_person']) ? $anchorRelationData['contact_person'] : ''}}" minlength="10" maxlength="50" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').replace(/(\..*)\./g, '$1');">
                                    {!! $errors->first('contact_person', '<span class="error">:message</span>') !!}
                                </td>
                                <td> Contact No.</td>
                                <td>
                                    <input type="text" id="contact_number" name="contact_number" class="form-control" value="{{isset($anchorRelationData['contact_number']) ? $anchorRelationData['contact_number'] : ''}}" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
                                    {!! $errors->first('contact_number', '<span class="error">:message</span>') !!}
                                </td>
                                
                            </tr>
                            <tr>
                                <td>Dependence On Anchor</td>
                                <td>
                                    <input type="text" id="dependence_on_anchor" name="dependence_on_anchor" class="form-control" value="{{isset($anchorRelationData['dependence_on_anchor']) ? $anchorRelationData['dependence_on_anchor'] : ''}}" maxlength="100">
                                    {!! $errors->first('dependence_on_anchor', '<span class="error">:message</span>') !!}
                                </td>
                                <td>Quarter on Quarter off-take from Anchor</td>
                                <td>
                                    <input type="text" id="qoq_ot_from_anchor" name="qoq_ot_from_anchor" class="form-control" value="{{isset($anchorRelationData['qoq_ot_from_anchor']) ? $anchorRelationData['qoq_ot_from_anchor'] : ''}}" maxlength="100">
                                    {!! $errors->first('qoq_ot_from_anchor', '<span class="error">:message</span>') !!}
                                </td>
                            </tr>
                            <tr>
                                <td>Categorization/ Relevance by Anchor</td>
                                <td>
                                    <input type="text" id="cat_relevance_by_anchor" name="cat_relevance_by_anchor" class="form-control" value="{{isset($anchorRelationData['cat_relevance_by_anchor']) ? $anchorRelationData['cat_relevance_by_anchor'] : ''}}" maxlength="100">
                                    {!! $errors->first('cat_relevance_by_anchor', '<span class="error">:message</span>') !!}
                                </td>
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
                        @if(count($data) > 0)
                            @php $j = 0 @endphp
                            @foreach($data as $key => $val)
                                <td colspan="2"><b>
                                    <input id="year_{{$key}}" type="text" name="year[]" value="{{$key}}" class="form-control" maxlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
                                    {!! $errors->first('year.'.$j, '<span class="error">:message</span>') !!}
                                </b></td>
                                @php 
                                $year = 'year_'.$j;
                                $$year = $key;
                                $j++;
                                @endphp
                            @endforeach
                        @else
                            @php 
                            $year_0 = 0;
                            $year_1 = 0;
                            @endphp
                            @for($k=0;$k<2;$k++)
                                <td colspan="2"><b>
                                    <input id="year_{{$k}}" type="text" name="year[]" value="{{old('year.'.$k)}}" class="form-control" maxlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');"></b>
                                    {!! $errors->first('year.'.$k, '<span class="error">:message</span>') !!}
                                </td>
                            @endfor
                        @endif
                        
                        </tr>
                        <tr>
                            <td><b>Month</b></td>

                            @php 

                            $year_0_kg = !empty($data[$year_0]['mt_type']) && $data[$year_0]['mt_type'] == 'KG'? 'selected' : '';
                            $year_0_ton = !empty($data[$year_0]['mt_type']) && $data[$year_0]['mt_type'] == 'TON'? 'selected' : '';
                            $year_0_unit = !empty($data[$year_0]['mt_type']) && $data[$year_0]['mt_type'] == 'UNIT'? 'selected' : '';
                            $year_1_kg = !empty($data[$year_1]['mt_type']) && $data[$year_1]['mt_type'] == 'KG'? 'selected' : '' ;
                            $year_1_ton = !empty($data[$year_1]['mt_type']) && $data[$year_1]['mt_type'] == 'TON'? 'selected' : '';
                            $year_1_unit = !empty($data[$year_1]['mt_type']) && $data[$year_1]['mt_type'] == 'UNIT'? 'selected' : '';


                            @endphp


                            <td>In MT
                                <select name="mt_type[]" class="form-control" id="mt_1">
                                    <option selected value="">Select</option>
                                    <option {{$year_0_kg}} value="KG"> Kg</option>
                                    <option {{$year_0_ton}} value="TON">Ton</option>
                                    <option {{$year_0_unit}} value="UNIT">Unit</option>
                                </select>
                                 {!! $errors->first('mt_type.0', '<span class="error">:message</span>') !!}

                            </td>
                            <td>In Rs. Mn</td>
                            <td>In Mt
                                <select name="mt_type[]" class="form-control" id="mt_2">
                                    <option selected value="">Select</option>
                                     <option {{$year_1_kg}} value="KG"> Kg</option>
                                    <option {{$year_1_ton}} value="TON">Ton</option>
                                    <option {{$year_1_unit}} value="UNIT">Unit</option>
                                </select>
                                 {!! $errors->first('mt_type.1', '<span class="error">:message</span>') !!}
                            </td>
                            <td>In Rs. Mn</td>
                        </tr>
                        @php $myKey = 0;@endphp
                        
                        @php $months = ['April', 'May' , 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March'] @endphp
                        @foreach($months as $key => $month)
                        <tr>
                            <td>{{$month}}</td>
                            <td>
                                <input type="hidden" name="month[0][anchor_lift_detail_id][{{$key}}]" value="{{!empty($data[$year_0]['anchor_lift_detail_id'][$key]) ? $data[$year_0]['anchor_lift_detail_id'][$key] : 0}}" class="form-control">

                                <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeyup="get_calc()" name="month[0][mt_value][{{$key}}]" value="{{!empty($data[$year_0]['mt_value'][$key]) ? $data[$year_0]['mt_value'][$key] : ''}}" class="form-control mt_value_0" maxlength="9">
                            </td>
                            <td>
                                <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeyup="get_calc()" name="month[0][mt_amount][{{$key}}]" value="{{!empty($data[$year_0]['mt_amount'][$key]) ? $data[$year_0]['mt_amount'][$key] : ''}}" class="form-control mt_amount_0" maxlength="20">
                            </td>
                            <td>
                                <input type="hidden" name="month[1][anchor_lift_detail_id][{{$key}}]" value="{{!empty($data[$year_1]['anchor_lift_detail_id'][$key]) ? $data[$year_1]['anchor_lift_detail_id'][$key] : 0}}" class="form-control">
                                
                                <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeyup="get_calc()" name="month[1][mt_value][{{$key}}]" value="{{!empty($data[$year_1]['mt_value'][$key]) ? $data[$year_1]['mt_value'][$key] : ''}}" class="form-control mt_value_1"  maxlength="9">
                            </td>
                            <td>
                                <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeyup="get_calc()" name="month[1][mt_amount][{{$key}}]" value="{{!empty($data[$year_1]['mt_amount'][$key]) ? $data[$year_1]['mt_amount'][$key] : ''}}" class="form-control mt_amount_1"  maxlength="20">
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td>Total</td>
                            <td>
                                <input type="text"  value="0" class="form-control mt_val_total_0" readonly>
                            </td>
                            <td>
                                <input type="text"  value="0" class="form-control mt_amt_total_0" readonly>
                            </td>
                            <td>
                                <input type="text"  value="0" class="form-control mt_val_total_1" readonly>
                            </td>
                            <td>
                                <input type="text"  value="0" class="form-control mt_amt_total_1" readonly>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Note on Lifting:</td>
                            <td colspan="4"><input type="text" id="note_on_lifting" class="form-control" name="note_on_lifting" value="{{isset($anchorRelationData['note_on_lifting']) ? $anchorRelationData['note_on_lifting'] : ''}}"></td>
                        </tr>
                        <tr>
                            <td>Reference from Anchor:</td>
                            <td colspan="4"><input type="text" id="reference_from_anchor" class="form-control" name="reference_from_anchor" value="{{isset($anchorRelationData['reference_from_anchor']) ? $anchorRelationData['reference_from_anchor'] : ''}}"></td>
                        </tr>
                        </tbody>
                    </table>


                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="data mt-4">
                <h2 class="sub-title bg">Risk Comments on Relationship with Anchor</h2>
                <div class="pl-4 pr-4 pb-4 pt-2">
                    <textarea class="form-control" name='anchor_risk_comments' id="anchor_risk_comments" rows="3" spellcheck="false">{{isset($anchorRelationData['anchor_risk_comments']) ? $anchorRelationData['anchor_risk_comments'] : ''}}</textarea>


                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="form-group text-right">
                                @if(request()->get('view_only'))
                                <button  class="btn btn-primary btn-ext submitBtnBank" type="submit">Submit</button>
                                @endif
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
    get_calc();
    function get_calc() {
        for (var i = 0; i <= 1; i++) {
            mt_val_total = 0;
            mt_amt_total = 0;
            $('.mt_value_'+i).each(function() {
               mt_val =  parseFloat($(this).val());
               mt_val_total += mt_val ? mt_val : 0;
            })
            $('.mt_amount_'+i).each(function() {
               mt_amt =  parseFloat($(this).val());
               mt_amt_total += mt_amt ? mt_amt : 0;
            })
            mt_amt_total = mt_amt_total ? mt_amt_total : 0;
            mt_val_total = mt_val_total ? mt_val_total : 0;
            $('.mt_amt_total_'+i).val(mt_amt_total);
            $('.mt_val_total_'+i).val(mt_val_total);
        }
        
    }
    
    $('#anchor_form').validate({
//        ignore: [],
        rules: {
            'year[]': {
               required: true 
            },
            'mt_type[]': {
                required: true
            },
            year_of_association: {
               required: true
            },
            contact_person: {
                required: true
            },
            payment_terms: {
                required: true
            },
            grp_rating: {
                required: true
            },
            contact_number: {
                required: true,
                minlength: 10,
                maxlength: 10
            },
            security_deposit: {
                required: true
            },
            dependence_on_anchor: {
                required: true
            },
            qoq_ot_from_anchor: {
                required: true
            },
            cat_relevance_by_anchor: {
                required: true
            }
        },
        messages: {
            'year[]': {
               required: 'Please enter year.' 
            },
            'mt_type[]': {
                required: 'Please slecet MT type.'
            },
            year_of_association: {
               required: 'Please enter year of association.'
            },
            contact_person: {
                required: 'Please enter contact person name.'
            },
            payment_terms: {
                required: 'Please enter payment terms.'
            },
            grp_rating: {
                required: 'Please enter group rating.'
            },
            contact_number: {
                required: 'Please enter contact number.',
                minlength: 'The contact number must be 10 digit.',
                maxlength: 'The contact number must be 10 digit.'
            },
            security_deposit: {
                required: 'Please enter security deposit.'
            },
            dependence_on_anchor: {
                required: 'Please enter dependence on anchor.'
            },
            qoq_ot_from_anchor: {
                required: 'Please enter Quarter on Quarter off-take from Anchor.'
            },
            cat_relevance_by_anchor: {
                required: 'Please enter Categorization/ Relevance by Anchor.'
            }
        }
    });
</script>
@endsection