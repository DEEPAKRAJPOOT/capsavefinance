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

        $('#personalInformationForm').validate({
	   ignore: [], 
	   rules: {
	        f_name: {
	           required: true,
	           pattern:"^[A-Za-z0-9_-]+$",
	           minlength:3,
	           maxlength:60,
	        },
                m_name: {
	            pattern:"^[A-Za-z0-9_-]+$",
	            minlength:3,
	            maxlength:60,
	        },
                l_name: {
	            required: true,
	            pattern:"^[A-Za-z0-9_-]+$",
	            minlength:3,
	            maxlength:60,
	        },
                gender: {
	            required: true,
	        },
                date_of_birth: {
	            required: true,
	        },
                father_name: {
	            required: true,
                    alphnum_hyp_uscore_space:true,
                    minlength:3,
	            maxlength:60,
	        },
                mother_f_name: {
	            required: true,
                    pattern:"^[A-Za-z0-9_-]+$",
	        },
                mother_m_name: {
	            required: true,
                    pattern:"^[A-Za-z0-9_-]+$",
                    minlength:3,
	            maxlength:60,
	        },
                birth_country_id: {
	            required: true,
	        },
                birth_city_id: {
	            required: true,
                    alphnum_hyp_uscore_space:true,
                    minlength:3,
	            maxlength:60,
	        },
                reg_no: {
	            required: true,
                    alpha_num:true,
                    minlength:3,
	            maxlength:20,
	        },
                reg_place: {
	            required: true,
                    alphnum_hyp_uscore_space:true,
                    minlength:3,
	            maxlength:60,
	        },
                f_nationality_id: {
	            required: true,
	        },
                residence_status: {
	            required: true,
	        },
                family_status: {
	            required: true,
	        },
                educational_level: {
	            required: true,
	        },
                is_residency_card: {
	            required: true,
	        },
                'document_type_id[]': {
	            required: true,
	        },
                'social_media_id[]': {
	            required: true,
	        },
                'social_media_link[]': {
	            url: true,
	        },
                'document_number[]': {
	           pattern:"^[A-Za-z0-9]+$",
	        },
 
	    },
	    messages:{
	        f_name: {              
	          required:messages.req_this_field, 
	          pattern:messages.alphnum_hyp_uscore,   
	          minlength:messages.least_3_chars,
	          maxlength:messages.max_60_chars,          
	        },
                m_name: {              
	           pattern:messages.alphnum_hyp_uscore,   
	           minlength:messages.least_3_chars,
	           maxlength:messages.max_60_chars,        
	        },
                l_name: {              
	           required:messages.req_this_field, 
	           pattern:messages.alphnum_hyp_uscore,   
	           minlength:messages.least_3_chars,
	           maxlength:messages.max_60_chars,          
	        },
                 gender: {
	            required:messages.req_this_field, 
	        },
                date_of_birth: {
	            required: messages.req_this_field, 
	        },
                father_name: {
	            required:messages.req_this_field, 
                    //pattern:messages.alphnum_hyp_uscore_space,   
	            minlength:messages.least_3_chars,
	            maxlength:messages.max_60_chars,
	        },
                mother_f_name: {
	            required: messages.req_this_field, 
                    pattern:messages.alphnum_hyp_uscore,   
	            minlength:messages.least_3_chars,
	            maxlength:messages.max_60_chars,  
	        },
                mother_m_name: {
	            required: messages.req_this_field,
                    pattern:messages.alphnum_hyp_uscore,   
	            minlength:messages.least_3_chars,
	            maxlength:messages.max_60_chars,  
	        },
                birth_country_id: {
	            required: messages.req_this_field, 
	        },
                birth_city_id: {
	            required: messages.req_this_field, 
                    //pattern:messages.alphnum_hyp_uscore_space,   
	            minlength:messages.least_3_chars,
	            maxlength:messages.max_60_chars,
	        },
                reg_no: {
	            required: messages.req_this_field,
                   // pattern:messages.alpha_num,   
	            minlength:messages.least_3_chars,
	            maxlength:messages.max_20_chars,
	        },
                reg_place: {
	            required: messages.req_this_field,
                    //pattern:messages.alphnum_hyp_uscore_space,   
	            minlength:messages.least_3_chars,
	            maxlength:messages.max_60_chars,
	        },
                f_nationality_id: {
	            required: messages.req_this_field, 
	        },
                residence_status: {
	            required: messages.req_this_field, 
	        },
                family_status: {
	            required: messages.req_this_field,
	        },
                educational_level: {
	            required: messages.req_this_field, 
	        },
                is_residency_card: {
	            required:messages.req_this_field, 
	        },
                'document_type_id[]': {
	            required: messages.req_this_field, 
	        },
                'social_media_id[]': {
	            required: messages.req_this_field, 
	        },
                'social_media_link[]': {
	            url: messages.enter_valid_url, 
	        },
                'document_number[]': {
	           pattern:messages.alpha_num, 
	        },
	    }
		  
	});
    });

} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
