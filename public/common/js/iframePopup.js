var iframe_ids = [
        'editLead',
        'createLeadForm',
        'noteFrame',
        'pickLead',
        'appStatusFrame',
        'assignCaseFrame',
        'addCaseNote',
        'addAppCopy',
        'addAnchorFrm',
        'uploadAnchLead',
        'editAnchorFrm',
        'sendNextstage',
        'addRoleFrm',
        'manageUserRole',
        'addmanageUserRole',
        'uploadSanctionLetter',
        'modalPromoter',
        'modalPromoter1',
        'modalPromoter2',
        'modalPromoter3',
        'modalPromoter7',
        'modalPromoter8',
        'assignFiFrame',
        'pdNoteFrame',
        'modalPromoter',
        'modalPromoter1',
        'modalPromoter2',
        'modalPromoter3',
        'modalPromoter7',
        'modalPromoter8',
        'modalPromoter9',
        'assignFiFrame',
        'assignRcuFrame',
        'uploadFiDocFrame',
        'uploadRcuDocFrame',
        'addAgencyFrame',
        'uploadDocument',
        'editAgencyFrame',
        'addAgencyUserFrame',
        'editAgencyUserFrame',
        'addChargesFrame',
        'editChargesFrame',
        'editChargesLmsFrame',
        'addChargesLmsFrame',
        'addDocumentsFrame',
        'editDocumentsFrame',
        'addIndustriesFrame',
        'editIndustriesFrame',
        'addEntityFrame',
        'editEntityFrame',
        'ppUploadDocument',
        'queryFrame',
        'queryDeatailsFrame',
        'addDoaLevelFrame',
        'editDoaLevelFrame',
        'assignRoleLevelFrame',
        'limitOfferFrame',
        'editLimitFrame',
        'modalInvoiceFailed',
        'add_bank_account',
        'modalInvoiceDisbursed',
        'modalUploadPayment',
        'add_bank_account',
        'viewDisbursalCustomerInvoice',
        'uploadBankDocument',
        'disbueseInvoices',
        'addcolenders',
        'uploadXLSXdoc',
        'addAddressFrame',
        'editAddressFrame',
        'addStateFrame',
        'editStatesFrame',
        'addCompaniesFrame',
        'viewApprovers',
        'viewSharedDetails',
        'previewSanctionLetter',
        'addGSTFrame',
        'editGSTFrame',
        'addSegmentFrame',
        'editSegmentFrame',
        'addConstiFrame',
        'editConstiFrame',
        'changeAppDisbursStatus',
        'addEquipmentFrame',
        'editEquipmentFrame',
        'shareColenderFrame',
        'viewSharedColenderFrame',
        'viewInterestAccrual',
        'manageUserRolePassword',
        'addBaseRateFrame',
        'editBaseRateFrame',
        'previewSupplyChainSanctionLetter',
        'addJiConfig',        
        'refund_amount',
        'adjust_amount',
        'edit_refund_amount',
        'edit_adjust_amount',
        'edit_waveoff_amount',
        'paymentRefundInvoice',
        'disburseInvoice',
        'lms_move_next_stage',
        'lms_move_prev_stage',
        'lms_update_request_status',
        'lms_view_process_refund',
        'edit_bank_account',
        'viewBatchSendToBankInvoice',
        'invoiceDisbursalTxnUpdate',
        'disburseInvoicePopUp',
        'EdituploadDocument',
        'addVoucherFrame',
        'previewUserInvoiceFrame',
        'viewDetailFrame',
        'editPaymentFrm',
        'addAdhocLimit',
        'approveAdhocLimit',
        'confirmCopyApp',
        'confirmEnhanceLimit',
    ];

iframe_ids.forEach(function(id) {
    $("#" + id).on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#"+ id +" iframe").attr({
            'src': url,
            'height': height,
            'width': width
        });
    });
});      
