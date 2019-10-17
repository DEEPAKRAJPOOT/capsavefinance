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
        
        
        $.validator.addMethod('alphnum_spacial1', function (value) { 
            return /^[A-Za-z0-9\s\/.&_-]+$/.test(value); 
        },messages.alphnum_spacial1);
        
        $.validator.addMethod('alpha_num_hype_uscor_space', function (value) { 
            return /^[A-Za-z0-9\s_-]+$/.test(value); 
        },messages.alphnum_hyp_uscore_space);
        
        $.validator.addMethod('alphnum_hyp_uscore_space_fslace', function (value) { 
            return /^[A-Za-z0-9\s\/.&_-]+$/.test(value); 
        },messages.alphnum_hyp_uscore_space_fslace);
        
        $.validator.addMethod('alphnum_spacial2', function (value) { 
            return /^[A-Za-z0-9\s\+_#.*!@&-]+$/.test(value); 
        },messages.alphnum_space_spacial_chars);
        
        $.validator.addMethod('num_hyp_space', function (value) { 
            return /^[0-9\s-]*$/.test(value); 
        },messages.num_hyp_space);
        
     //     ,   , 
        
        $.validator.addMethod('chk_post_box', function (value) { 
            return /^[A-Za-z0-9\s\/-]+$/.test(value); 
        },messages.invalid_post_box);
        
        $.validator.addMethod('chk_mob_no', function (value) { 
            return /^(\+\d{1,3}[- ]?)?\d{10}$/.test(value); 
        },messages.invalid_mobile);
        
        $.validator.addMethod('chk_fax_no', function (value) { 
            return /^\+?[0-9]+$/.test(value); 
        },messages.invalid_fax_no);
        
        $('#residentialInformationForm').validate({
	   ignore: [], 
	   rules: {
	        country_id: {
	           required: true,
	        },
                city_id: {
	           required: true,
                   alpha_num_hype_uscor_space:true,
	        },
                region: {
	           required: true,
                   alphnum_spacial1:true,
                   minlength:3,
	           maxlength:60,
	        },
                building_no: {
	           required: true,
                   alphnum_hyp_uscore_space_fslace:true,
                   maxlength:30,
	        },
                floor_no: {
	           required: true,
                   alphnum_hyp_uscore_space_fslace:true,
                   maxlength:30,
	        },
                street_addr: {
	           required: true,
                   alphnum_spacial2:true,
                   minlength:3,
	           maxlength:120,
	        },
                postal_code: {
	           required: true,
                   num_hyp_space:true,
                   minlength:6,
	           maxlength:20,
	        },
                post_box: {
	           required: true,
                   chk_post_box:true,
                   minlength:6,
	           maxlength:20,
	        },
                addr_mobile_no: {
	           required: true,
                   chk_mob_no:true,
                   minlength:10,
	           maxlength:15,
	        },
                addr_fax_no: {
	           required: true,
                   chk_fax_no:true,
                   minlength:6,
	           maxlength:15,
	        }
	    },
                  
	    messages:{
	        country_id: {              
	          required:messages.req_this_field,           
	        },
                city_id: {
	           required:messages.req_this_field,
                   
	        },
                region: {
	           required:messages.req_this_field, 
                   maxlength:messages.max_60_chars, 
                   minlength:messages.least_3_chars, 
	        },
                building_no: {
	           required:messages.req_this_field, 
                   maxlength:messages.max_30_chars,
	        },
                floor_no: {
	           required:messages.req_this_field, 
                   maxlength:messages.max_30_chars,
	        },
                street_addr: {
	           required:messages.req_this_field, 
                   maxlength:messages.max_120_chars, 
                   minlength:messages.least_3_chars, 
	        },
                postal_code: {
	           required:messages.req_this_field,
                   maxlength:messages.max_20_chars, 
                   minlength:messages.least_6_chars,
	        },
                post_box: {
	           required:messages.req_this_field, 
                   maxlength:messages.max_20_chars, 
                   minlength:messages.least_6_chars, 
	        },
                addr_mobile_no: {
	           required:messages.req_this_field, 
                   maxlength:messages.max_15_chars, 
                   minlength:messages.least_10_chars, 
	        },
                addr_fax_no: {
	           required:messages.req_this_field,
                   maxlength:messages.max_15_chars, 
                   minlength:messages.least_6_chars, 
	        }
	    }
		  
	});
    });

} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
