/* global messages, message */
try {
    jQuery(document).ready(function ($) {


        $('.datepicker').keydown(function (e) {
            e.preventDefault();
            return false;
        });
        
        $('.datepicker').on('paste',function (e) {
            e.preventDefault();
            return false;
        });
        
        
        $.validator.addMethod('alphnum_hyp_uscore_space', function (value) { 
            return /^[A-Za-z0-9\s_-]+$/.test(value); 
        },messages.alphnum_hyp_uscore_space);
        
        $.validator.addMethod('alpha_num', function (value) { 
            return /^[A-Za-z0-9]+$/.test(value); 
        },messages.alpha_num);

        $('#commercialInformationForm').validate({
	   ignore: [], 
	   rules: {
	        comm_name: {
	           required: true,
	        },
                date_of_establish:{
                    required:true,
                },
                country_establish_id:{
                    required:true,
                },
                comm_reg_no:{
                    required:true,
                },
                comm_reg_place:{
                    required:true,
                },
                country_activity:{
                    required:true,
                },
                syndicate_no:{
                    required:true,
                },
                taxation_no:{
                    required:true,
                },
                taxation_id:{
                    required:true,
                },
                annual_turnover:{
                    required:true,
                },
                main_suppliers:{
                    required:true,
                },
                main_clients:{
                    required:true,
                },
                authorized_signatory:{
                    required:true,
                },
                buss_country_id:{
                    required:true,
                },
                buss_city_id:{
                    required:true,
                },
                buss_region:{
                    required:true,
                },
                buss_building:{
                    required:true,
                },
                buss_floor:{
                    required:true,
                },
                buss_street:{
                    required:true,
                },
                buss_postal_code:{
                    required:true,
                },
                buss_po_box_no:{
                    required:true,
                },
                buss_email:{
                    required:true,
                },
                buss_telephone_no:{
                    required:true,
                },
                buss_mobile_no:{
                    required:true,
                },              
                is_hold_mail:{
                    required:true,
                },
                mailing_address:{
                    required:true,
                },
                relation_exchange_company:{
                    required:true,
                },

	    },
	    messages:{
	        
                comm_name: {
	           required:messages.req_this_field, 
	        },
                date_of_establish:{
                    required:messages.req_this_field, 
                },
                country_establish_id:{
                    required:messages.req_this_field, 
                },
                comm_reg_no:{
                    required:messages.req_this_field,
                },
                comm_reg_place:{
                    required:messages.req_this_field, 
                },
                country_activity:{
                    required:messages.req_this_field, 
                },
                syndicate_no:{
                    required:messages.req_this_field, 
                },
                taxation_no:{
                    required:messages.req_this_field, 
                },
                taxation_id:{
                    required:messages.req_this_field, 
                },
                annual_turnover:{
                    required:messages.req_this_field, 
                },
                main_suppliers:{
                    required:messages.req_this_field, 
                },
                main_clients:{
                    required:messages.req_this_field, 
                },
                authorized_signatory:{
                    required:messages.req_this_field, 
                },
                buss_country_id:{
                    required:messages.req_this_field, 
                },
                buss_city_id:{
                    required:messages.req_this_field, 
                },
                buss_region:{
                    required:messages.req_this_field, 
                },
                buss_building:{
                    required:messages.req_this_field, 
                },
                buss_floor:{
                    required:messages.req_this_field, 
                },
                buss_street:{
                    required:messages.req_this_field, 
                },
                buss_postal_code:{
                    required:messages.req_this_field, 
                },
                buss_po_box_no:{
                    required:messages.req_this_field, 
                },
                buss_email:{
                    required:messages.req_this_field, 
                },
                buss_telephone_no:{
                    required:messages.req_this_field, 
                },
                buss_mobile_no:{
                    required:messages.req_this_field, 
                },              
                is_hold_mail:{
                    required:messages.req_this_field, 
                },
                mailing_address:{
                    required:messages.req_this_field, 
                },
                relation_exchange_company:{
                    required:messages.req_this_field, 
                },
	    }
		  
	});
    });

} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
