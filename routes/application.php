<?php
/**
 * This route have all backend guest and auth routes
 *
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.frontend_uri'))->group(function () {
	 Route::group(
		['prefix' => 'dashboard'],
		function () {
		Route::group(
			['middleware' => ['auth']],   //,'CheckWorkflow'
			function () {
			Route::get(
				'/',
				[
				'as' => 'front_dashboard',
				'uses' => 'Application\DashboardController@index'
				]
			);

			Route::get('/frontend-download-file', [
				'as' => 'frontend_download_storage_file',
				'uses' => 'Backend\DocumentController@downloadStorageFile'
			]);

			Route::get('frontend-view-uploaded-doc',[
				'as' => 'frontend_view_uploaded_doc',
				'uses' => 'Backend\DocumentController@seeUploadFile'
			]);
		});
	});
	
	Route::group(
		['prefix' => 'application'],
		function () {
		Route::group(
			['middleware' => 'auth'],
			function () {  
			Route::get('business-information',
				[
				'as' => 'business_information_open',
				'uses' => 'Application\ApplicationController@showBusinessInformationForm'
			]);

			Route::post('business-information-save',
				[
				'as' => 'business_information_save',
				'uses' => 'Application\ApplicationController@saveBusinessInformation'
			]);

			Route::get('promoter-detail',
				[
				'as' => 'promoter-detail',
				'uses' => 'Application\ApplicationController@showPromoterDetail'
			]);
			
			Route::post('front_promoter_detail_update',
				[
					'as' => 'front_promoter_detail_update',
					'uses' => 'Application\ApplicationController@updatePromoterDetail'
			]); 

			Route::get('verify_mobile_front', [
				'as' => 'verify_mobile_front',
				'uses' => 'Application\ApplicationController@mobileModel'
			]);
			
			Route::post('front-promoter-save',
				[
					'as' => 'front_promoter_save',
					'uses' => 'Application\ApplicationController@savePromoter'
			]);
			
			
//////////////// For Promoter Iframe////////////////////////
			Route::get('front_show-pan-verify-data', [
				'as' => 'front_show_pan_verify_data',
				'uses' => 'Application\ApplicationController@showPanVerifyResponseData'
			]);

			Route::get('front_show-pan-data', [
				'as' => 'front_show_pan_data',
				'uses' => 'Application\ApplicationController@showPanResponseData'
			]);

			Route::get('front_show-dl-data', [
				'as' => 'front_show_dl_data',
				'uses' => 'Application\ApplicationController@showDlResponseData'
			]);
			Route::get('front_show-voter-data', [
				'as' => 'front_show_voter_data',
				'uses' => 'Application\ApplicationController@showVoterResponseData'
			]);
			Route::get('front_show-pass-data', [
				'as' => 'front_show_pass_data',
				'uses' => 'Application\ApplicationController@showPassResponseData'
			]);
			 Route::get('front_verify_mobile', [
				'as' => 'front_verify_mobile',
				'uses' => 'Application\ApplicationController@mobileModel'
			]);
			  Route::get('front_mobile_otp_view', [
				'as' => 'front_mobile_otp_view',
				'uses' => 'Application\ApplicationController@mobileOtpModel'
			]);
			
			   Route::post('front_promoter_document_save', [
				'as' => 'front_promoter_document_save',
				'uses' => 'Application\ApplicationController@promoterDocumentSave'
			]); 
			
			Route::get('document',
				[
				'as' => 'document',
				'uses' => 'Application\ApplicationController@showDocument'
			]);


			Route::get('documents/upload-document', [
				'as' => 'front_show_upload_document',
				'uses' => 'Application\ApplicationController@uploadDocument'
			]);
			
			 Route::get('front_invoice_failed_status', [
				 'as' => 'front_invoice_failed_status',
				'uses' => 'Application\InvoiceController@invoiceFailedStatus'
			]); 
			Route::get('front_invoice_success_status', [
				 'as' => 'front_invoice_success_status',
				'uses' => 'Application\InvoiceController@invoiceSuccessStatus'
			]); 

			Route::get('/edit-upload-document', [
				'as' => 'front_edit_upload_document',
				'uses' => 'Application\ApplicationController@editUploadDocument'
			]);

			Route::post('/update-edit-upload-document', [
				'as' => 'front_update_edit_upload_document',
				'uses' => 'Application\ApplicationController@updateEditUploadDocument'
			]);
			
			Route::post('document-save',
				[
				'as' => 'document-save',
				'uses' => 'Application\ApplicationController@saveDocument'
			]);
			
			Route::get('document-delete/{appDocFileId}',
				[
				'as' => 'document-delete',
				'uses' => 'Application\ApplicationController@documentDelete'
			]);
			
			Route::get('document-view',
				[
				'as' => 'document-view',
				'uses' => 'Application\ApplicationController@documentView'
			]);
			
			Route::get('document-download',
				[
				'as' => 'document-download',
				'uses' => 'Application\ApplicationController@documentDownload'
			]);
			
			Route::post('application-save',
				[
				'as' => 'front_application_save',
				'uses' => 'Application\ApplicationController@applicationSave'
			]);

			Route::get('/', [
				'as' => 'front_application_list',
				'uses' => 'Application\ApplicationController@index'
			]);

			Route::get('/gstin', [
				'as' => 'front_gstin',
				'uses' => 'Application\ApplicationController@gstinForm'
			]);
		});

		Route::post('promoter-document-ovd', [
			'as' => 'promoter_document_ovd',
			'uses' => 'Application\ApplicationController@promoterDocumentOVD'
		]); 
	});
	
	
	 ///////////////////////// Route for invoice controller///////////////////////
		   Route::group(['prefix' => 'invoice'], function () {
			   Route::get('front_upload_invoice', [
				 'as' => 'front_upload_invoice',
				'uses' => 'Application\InvoiceController@getInvoice'
			]); 
			Route::get('front_upload_all_invoice', [
				 'as' => 'front_upload_all_invoice',
				'uses' => 'Application\InvoiceController@getAllInvoice'
			]); 
			
		  Route::get('get_invoice', [
				 'as' => 'get_invoice',
				'uses' => 'Application\InvoiceController@viewInvoice'
			]); 
			Route::POST('front_save_invoice', [
				 'as' => 'front_save_invoice',
				'uses' => 'Application\InvoiceController@saveInvoice'
			]); 
		   
			 Route::get('frontend_bulk_invoice', [
				 'as' => 'frontend_bulk_invoice',
				'uses' => 'Application\InvoiceController@getBulkInvoice'
			]); 
			 Route::POST('frontend_save_bulk_invoice', [
				 'as' => 'frontend_save_bulk_invoice',
				'uses' => 'Application\InvoiceController@saveBulkInvoice'
			]); 
			   Route::POST('frontend_update_invoice_amount', [
				 'as' => 'frontend_update_invoice_amount',
				'uses' => 'Application\InvoiceController@saveInvoiceAmount'
			]);    
			 
			  Route::get('frontend_view_invoice_details', [
				 'as' => 'frontend_view_invoice_details',
				'uses' => 'Application\InvoiceController@viewInvoiceDetails'
			]); 
			 
			  Route::post('front_update_invoice_amount', [
				 'as' => 'front_update_invoice_amount',
				'uses' => 'Application\InvoiceController@saveFrontInvoiceAmount'
			]); 
			   
			   
		  });

		Route::group(['prefix' => 'nach'], function () {
			Route::get('nach-list', [
				 'as' => 'front_nach_list',
				'uses' => 'Application\NACHController@nachList'
			]);
			Route::get('create-nach', [
				 'as' => 'front_create_nach',
				'uses' => 'Application\NACHController@createNACH'
			]);
			Route::post('/add-nach-detail', [
				'as' => 'front_add_nach_detail',
				'uses' => 'Application\NACHController@addNachDetail',
			]);
			
			Route::get('/edit-nach-detail', [
				'as' => 'front_edit_nach_detail',
				'uses' => 'Application\NACHController@EditNachDetail',
			]);
			
			Route::post('/save-nach-detail', [
				'as' => 'front_save_nach_detail',
				'uses' => 'Application\NACHController@saveNachDetail',
			]);

			Route::get('/nach-detail-preview', [
				'as' => 'front_nach_detail_preview',
				'uses' => 'Application\NACHController@nachDetailPreview',
			]); 
					
			Route::get('generate-nach', [
				'as' => 'front_generate_nach',
				'uses' => 'Application\NACHController@generateNach'
			]);
			
			Route::get('/upload-nach-document', [
				'as' => 'front_upload_nach_document',
				'uses' => 'Application\NACHController@uploadNachDocument'
			]);

			Route::post('/nach_document-save', [
				'as' => 'front_nach_document_save',
				'uses' => 'Application\NACHController@saveNachDocument'
			]);
			
		});
		  
	 
});  
