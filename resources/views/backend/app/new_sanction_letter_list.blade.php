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
                        @can('create_new_sanction_letter')
                        @php
                                $display = '';
                            @endphp
                        @if(!empty($sanctionFirstData))
                            @php
                                $display = ' hide';
                            @endphp
                            @if(!empty($sanctionFirstData) && $sanctionFirstData->status == 3)
                                @php
                                    $display = '';
                                @endphp
                            @endif
                        @endif
                        <a href="{{ route('create_new_sanction_letter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'action_type' => 'add'] ) }}" id="createSanctionLetterA" class="add-btn-cls{{ $display }}">
                            <button class="add-btn-cls btn btn-success btn-sm float-right" type="button" id="createSanctionLetter"><i class="fa fa-plus">&nbsp;</i> Create Sanction Letter</button>
                        </a>
                        @endcan
                     </div>
                  </div>
                  <div class="row">
                    <div class="col-12 dataTables_wrapper mt-4">
                      <div class="overflow">
                        <div id="invoices_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                          <div class="row">
                            <div class="col-sm-12">
                              <table id="new_sanction_list" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="new_sanction_list_info" style="width: 100%;">
                                <thead>
                                  <tr role="row">
                                    <th width="15%">Reference No</th>
                                    <th width="15%">Final Submission Date</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">Created By</th>
                                    <th width="15%">Created Date</th>
                                    {{-- <th width="15%"><b>Updated By</b></th>
                                    <th width="15%"><b>Updated Date</b></th> --}}
                                    <th width="15%">Action</th>
                                  </tr>
                                </thead>
                                <tbody>
  
                                </tbody>
                              </table>
                              <div id="new_sanction_list_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                            </div>
                          </div>
  
                        </div>
                      </div>
  
                    </div>
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
            get_new_sanction_letter_list: "{{ URL::route('get_new_sanction_letter_list') }}",
            ajax_update_regenerate_sanction_letter: "{{ URL::route('update_regenerate_sanction_letter') }}",
            data_not_found: "{{ trans('error_messages.data_not_found') }}",
            token: "{{ csrf_token() }}",
            app_id: "{{ request()->get('app_id') }}",
            biz_id: "{{ request()->get('biz_id') }}"

        };
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
</script>
<script src="{{ asset('backend/js/ajax-js/new_sanction_list.js') }}"></script>
@endsection