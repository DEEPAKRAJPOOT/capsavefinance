@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
   <div class="row">
      <div class="col-12">
         <div class="card">
            <div class="card-body">
                @if(!empty($supplyChaindata['offerData']) && $supplyChaindata['offerData']->count())
               <div class=" form-fields">
                  <div class="row">
                     <div class="col-6">
                        <h5 class="card-title form-head-h5 mb-0">New Sanction Letter</h5>
                     </div>
                     <div class="col-6">
                        {{-- @can('create_new_sanction_letter') --}}
                        <a href="{{ route('create_new_sanction_letter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'action_type' => 'add'] ) }}" >
                            <button class="add-btn-cls btn btn-success btn-sm float-right" type="button"><i class="fa fa-plus">&nbsp;</i> Create Sanction Letter</button>
                        </a>
                        {{-- @endcan --}}
                     </div>
                  </div>
                  <div class="table-responsive">
                  <table class="table table-striped table-hover mt-4">
                     <thead class="thead-primary">
                     </thead>
                     <tbody>
                        <tr>
                           <th width="15%">Reference No</th>
                           <th width="15%">Final Submission Date</th>
                           <th width="15%">Status</th>
                           <th width="15%">Created By</th>
                           <th width="15%">Created Date</th>
                           {{-- <th width="15%"><b>Updated By</b></th>
                           <th width="15%"><b>Updated Date</b></th> --}}
                           <th width="15%">Action</th>
                        </tr>
                        <tr>
                            <td>CFPL/Aug20/463</td>
                            <td>Test</td>
                            <td>Pending</td>
                            <td>Test</td>
                            <td>Test</td>
                            {{-- <td>Test</td> --}}
                            <td>
                               {{-- <a href="#" title="View" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-eye" aria-hidden="true"></i></a> --}}
                               <a href="{{ route('create_new_sanction_letter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'sanction_letter_id' => null, 'action_type' => 'edit'] ) }}" title="Edit" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                               {{-- <a href="#" title="Download" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-download aria-hidden="true"></i></a>
                               <a href="#" title="Regenerate" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-repeat" aria-hidden="true"></i></a> --}}
                            </td>
                         </tr>
                        <tr>
                           <td>CFPL/Aug20/463</td>
                           <td>Test</td>
                           <td>In Complete</td>
                           <td>Test</td>
                           <td>Test</td>
                           {{-- <td>Test</td> --}}
                           <td>
                              {{-- <a href="#" title="View" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-eye" aria-hidden="true"></i></a> --}}
                              <a href="{{ route('create_new_sanction_letter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'sanction_letter_id' => null, 'action_type' => 'edit'] ) }}" title="Edit" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                              <a href="#" title="Download" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-download aria-hidden="true"></i></a>
                              {{-- <a href="#" title="Regenerate" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-repeat" aria-hidden="true"></i></a> --}}
                           </td>
                        </tr>
                        <tr>
                           <td>CFPL/Aug20/463</td>
                           <td>Test</td>
                           <td>Complete</td>
                           <td>Test</td>
                           <td>Test</td>
                           {{-- <td>Test</td> --}}
                           <td>
                            <a href="{{ route('view_new_sanction_letter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'sanction_letter_id' => null, 'action_type' => 'view'] ) }}" title="View" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            {{-- <a href="#" title="Edit" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> --}}
                            <a href="#" title="Download" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-download aria-hidden="true"></i></a>
                            <a href="#" title="Regenerate" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-repeat" aria-hidden="true"></i></a>
                           </td>
                        </tr>
                        <tr>
                           <td>CFPL/Aug20/463</td>
                           <td>Test</td>
                           <td>Expired</td>
                           <td>Test</td>
                           <td>Test</td>
                           {{-- <td>Test</td> --}}
                           <td>
                            <a href="{{ route('view_new_sanction_letter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'sanction_letter_id' => null, 'action_type' => 'view'] ) }}" title="View" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a href="#" title="Download" class="btn btn-action-btn btn-sm mr-1"><i class="fa fa-download aria-hidden="true"></i></a>
                           </td>
                        </tr>
                     </tbody>
                  </table>
                  </div>
               </div>
               @else 
                <div class="card card-color mb-0">
                <div class="card-header">
                    <a class="card-title ">Sanction letter cannot be generated for this application as limit offer has not be added.</a>
                </div>
                </div>
                @endif
            </div>
         </div>
      </div>
   </div>
</div>
</div>
{!! Helpers::makeIframePopup('previewSanctionLetter', 'Preview/Send Mail Sanction Letter', 'modal-lg') !!}
{!! Helpers::makeIframePopup('previewSupplyChainSanctionLetter', 'Send Mail Supply Chain Letter', 'modal-lg') !!}
{!! Helpers::makeIframePopup('uploadSanctionLetter', 'Upload Sanction Letter', 'modal-md') !!}
@endsection
@section('jscript')
<script>
   var messages = {
            get_applications: "{{ URL::route('ajax_app_list') }}",
            data_not_found: "{{ trans('error_messages.data_not_found') }}",
            token: "{{ csrf_token() }}",

        };

        var ckeditorOptions = {
            filebrowserUploadUrl: "{{ route('upload_ckeditor_image', ['_token' => csrf_token(), 'type' => 'file']) }}",
            filebrowserUploadMethod: 'form',
            imageUploadUrl: "{{ route('upload_ckeditor_image', ['_token' => csrf_token(), 'type' => 'image']) }}",
            disallowedContent: 'img{width,height};'
        };

        CKEDITOR.replace('delay_pymt_chrg', ckeditorOptions);
        CKEDITOR.replace('insurance', ckeditorOptions);
        CKEDITOR.replace('bank_chrg', ckeditorOptions);
        CKEDITOR.replace('legal_cost', ckeditorOptions);
        CKEDITOR.replace('po', ckeditorOptions);
        CKEDITOR.replace('pdp', ckeditorOptions);
        CKEDITOR.replace('disburs_guide', ckeditorOptions);
        CKEDITOR.replace('other_cond', ckeditorOptions);
        CKEDITOR.replace('covenants', ckeditorOptions);
        CKEDITOR.replace('rating_rational', ckeditorOptions);
        $(document).ready(function() {
            $('#payment_type').on('change', function() {
                $('#payment_type_comment').val('');
                if ($(this).val() == '5') {
                    $('#payment_type_comment').removeClass('hide');
                } else {
                    $('#payment_type_comment').addClass('hide');
                }
            })

            $("input[name='sanction_validity_date']").datetimepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                minView: 2,
                startDate: '-0m',
            }).on('changeDate', function(e) {
                $("input[name='sanction_expire_date']").val(ChangeDateFormat(e.date, 'dmy', '/', 30));

            });

            $("input[name='sanction_expire_date']").datetimepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                minView: 2,
                startDate: '+1m'
            });
        });


        function ChangeDateFormat(dateObj, out_format = 'ymd', out_separator = '/', dateAddMinus = 0) {
            dateObj.setDate(dateObj.getDate() + dateAddMinus);
            var twoDigitMonth = ((dateObj.getMonth().length + 1) === 1) ? (dateObj.getMonth() + 1) : '0' + (dateObj
                .getMonth() + 1);
            var twoDigitDate = dateObj.getDate() + "";
            if (twoDigitDate.length == 1) twoDigitDate = "0" + twoDigitDate;
            var Digityear = dateObj.getFullYear();
            switch (out_format) {
                case 'myd':
                    outdate = twoDigitMonth + out_separator + Digityear + out_separator + twoDigitDate;
                    break;
                case 'ydm':
                    outdate = Digityear + out_separator + twoDigitDate + out_separator + twoDigitMonth;
                    break;
                case 'dmy':
                    outdate = twoDigitDate + out_separator + twoDigitMonth + out_separator + Digityear;
                    break;
                case 'dym':
                    outdate = twoDigitDate + out_separator + Digityear + out_separator + twoDigitMonth;
                    break;
                case 'mdy':
                    outdate = twoDigitMonth + out_separator + twoDigitDate + out_separator + Digityear;
                    break;
                default:
                    outdate = Digityear + out_separator + twoDigitMonth + out_separator + twoDigitDate;
                    break;
            }
            return outdate;
        }

        $(document).on('click', '.clone_covenants', function() {
            // covenants_clone_tr_html =  $('.covenants_clone_tr').html();
            covenants_clone_tr_html =
                '<td><input maxlength="100" value="" type="text" name="covenants[name][]" class="input_sanc" placeholder="Enter Covenants"></td><td><input maxlength="10" value="" type="text" name="covenants[ratio][]" class="input_sanc" placeholder="Enter Minimum/Maximum ratio"></td><td><select class="select" name="covenants[ratio_applicability][]"><option selected="">Applicable</option><option>Not Applicable</option></select></td>';
            $('.FinancialCovenantsTBody').append("<tr>" + covenants_clone_tr_html + "</tr>");
        })
        $(document).on('click', '.remove_covenants', function() {
            totalrows = $('.FinancialCovenantsTBody').children().length;
            if (totalrows > 1) {
                $('.FinancialCovenantsTBody tr:last-child').remove();
            }
        })

        $(document).ready(function() {
            jQuery.validator.addMethod("alphanumeric", function(value, element) {
                return this.optional(element) || /^[\w\s.]+$/i.test(value);
            }, "Letters, numbers, and underscores only please");

            jQuery.validator.addMethod("ratio", function(value, element) {
                return this.optional(element) || /^[0-9:]+$/i.test(value);
            }, "Numbers and colon only please");

            $('#frmSanctionLetter').validate({
                rules: {
                    "pdc_facility_no": {
                        number: true
                    },
                    "pdc_facility_name": {
                        alphanumeric: true
                    },
                    "pdc_facility_amt": {
                        number: true
                    },
                    "pdc_facility_purpose": {
                        alphanumeric: true
                    },
                    "pdc_no_of_cheque[]": {
                        number: true
                    },
                    "pdc_not_above[]": {
                        alphanumeric: true
                    },
                    "nach_facility_no": {
                        number: true
                    },
                    "nach_facility_name": {
                        alphanumeric: true
                    },
                    "nach_facility_amt": {
                        number: true
                    },
                    "nach_facility_purpose": {
                        alphanumeric: true
                    },
                    "nach_no_of_cheque[]": {
                        number: true
                    },
                    "nach_not_above[]": {
                        alphanumeric: true
                    },
                    "dsra_amt": {
                        number: true
                    },
                    "dsra_tenure": {
                        number: true
                    },
                    "dsra_comment": {
                        alphanumeric: true
                    },
                    "other_sucurities": {
                        alphanumeric: true
                    },
                    "covenants[name][]": {
                        alphanumeric: true
                    },
                    "covenants[ratio][]": {
                        number: true,
                        min: 0,
                        max: 1.24
                    }
                }
            });
        });
</script>
@endsection