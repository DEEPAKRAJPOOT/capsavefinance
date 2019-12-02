try {
    var oTable;
    jQuery(document).ready(function ($) {
        
            $('#addAppNote').validate({
                rules: {
                    notes: {
                       required: true
                    }
                },
                messages: {
                }
            });
            
            
            $('#frmLimitAssessment').validate({
                rules: {
                    loan_offer: {
                       required: true,
                       range:[messages.min_loan_offer,messages.max_loan_offer]
                    },
                    interest_rate: {
                       required: true,
                       range:[messages.min_interest_rate,messages.max_interest_rate]
                    },
                    tenor: {
                       required: true,
                       range:[messages.min_tenor,messages.max_tenor]
                    },
                    tenor_old_invoice: {
                       required: true,
                       range:[messages.min_tenor_old_invoice,messages.max_tenor_old_invoice]
                    }, 
                    margin: {
                       required: true,
                       range:[messages.min_margin,messages.max_margin]
                    },
                    overdue_interest_rate: {
                       required: true,
                       range:[messages.min_overdue_interest_rate,messages.max_overdue_interest_rate]
                    },
                    adhoc_interest_rate: {
                       required: messages.required_adhoc_interest_rate == 1 ? true : false,
                       range:[messages.min_adhoc_interest_rate,messages.max_adhoc_interest_rate]
                    },
                    grace_period: {
                       required: messages.required_grace_period == 1 ? true : false,
                       range:[messages.min_grace_period,messages.max_grace_period]
                    },
                    processing_fee: {
                       required: true
                    },
                    check_bounce_fee: {
                       required: true
                    },    
                    comment: {
                       required: true
                    },
                },
                messages: {
                }
            });            
                             
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
