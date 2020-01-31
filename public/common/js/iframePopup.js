var iframe_ids = [
        'editLead',
        'createLeadForm',
        'noteFrame',
        'pickLead',
        'appStatusFrame',
        'assignCaseFrame',
        'addCaseNote',
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
