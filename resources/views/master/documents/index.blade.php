@extends('layouts.backend.admin-layout')

@section('additional_css')
<style type="text/css">
	.current-status {
		width: 60px;
		padding: 0.7em 1em;
	}
</style>
@endsection
@section('content')

<div class="content-wrapper">
	<section class="content-header">
		<div class="header-icon">
			<i class="fa  fa-list"></i>
		</div>
		<div class="header-title">
			<h3>Manage Documents</h3>
			<small>Documents List</small>
			<ol class="breadcrumb">
				<li style="color:#374767;"> Home </li>
				<li style="color:#374767;">Manage Documents</li>
				<li class="active">Documents List</li>
			</ol>
		</div>
	</section>
	<div class="card">
		<div class="card-body">
			<div class="row" style="margin-bottom: 25px;">
				<div class="col-md-3">

				{!!
	                Form::text('search_keyword',
					(isset($filter['filter_search_keyword'])) ? $filter['filter_search_keyword'] : null,
	                [
	                'class' => 'form-control',
	                'placeholder' => 'Search by Name',
	                'id'=>'search_keyword'
	                ])
                !!}
				</div>

				<div class="col-md-3">
				{!!
				Form::select('product_type',
				[ 	''=>'Product Type', 
					'1' => 'Supply Chain', 
					'2' => 'Term Loan', 
					'3' => 'Lease Loan'
				], 
				(isset($filter['filter_product_type'])) ? $filter['filter_product_type'] : null,
				array('id' => 'product_type',
				'class'=>'form-control'))
				!!}
                                {
				</div>
				<div class="col-md-3">

				{!!
				Form::select('doc_type_id',
				[	''=>'Document Type', 
					'1' => 'On Boarding', 
					'2' => 'Pre Sanction', 
					'3' => 'Post	 Sanction'
				],
				(isset($filter['filter_doc_type_id'])) ? $filter['filter_doc_type_id'] : null,
				array('id' => 'doc_type_id',
				'class'=>'form-control'))
				!!}
				</div>
				<div class="col-md-1">
					<button type="submit" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
				</div>
				<div class="col-md-2 text-right">
					<a data-toggle="modal" class="btn  btn-success btn-sm" data-target="#addDocumentsFrame" data-url ="{{route('add_documents')}}" data-height="320px" data-width="100%" data-placement="top" >
							<i class="fa fa-plus"></i> Add Documents
					</a>
				</div>
		   </div>

			<div class="row">
				<div class="col-sm-12">
					<div class="table-responsive">
						<table id="documentsList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
							<thead>
								<tr role="row">
									<th width="14%">Document Type</th>
									<th width="25%">Document Name</th>
									<th width="25%">product Types</th>
									<th width="6%">Is RCU</th>
									<th width="10%">Created At</th>
									<th width="10%">Created By</th>
									<th width="10%">Status</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						<div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
{!!Helpers::makeIframePopup('addDocumentsFrame','Add Document', 'modal-md')!!}
{!!Helpers::makeIframePopup('editDocumentsFrame','Edit Document Detail', 'modal-md')!!}
@endsection

@section('jscript')
<script>

var messages = {
	get_documents_list: "{{ URL::route('get_ajax_master_document_list') }}",       
	data_not_found: "{{ trans('error_messages.data_not_found') }}",
	token: "{{ csrf_token() }}",
	};
</script>
<script src="{{ asset('backend/js/ajax-js/documents.js') }}"></script>
@endsection