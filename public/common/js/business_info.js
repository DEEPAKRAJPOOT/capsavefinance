function copyAddress(id,th){
	console.log(id);
	if($(th).is(':checked')){
		$(id+' input[name*=biz_other_address]').val($('input[name=biz_address]').val());
		$(id+' input[name*=biz_other_city]').val($('input[name=biz_city]').val());
		$(id+' select[name*=biz_other_state]').val($('select[name=biz_state]').val());
		$(id+' input[name*=biz_other_pin]').val($('input[name=biz_pin]').val());
		$(id+' select[name*=location_other_id]').val($('select[name=location_id]').val());
	}else{
		$(id+' input[name*=biz_other_address]').val('');
		$(id+' input[name*=biz_other_city]').val('');
		$(id+' select[name*=biz_other_state]').val('');
		$(id+' input[name*=biz_other_pin]').val('');
		$(id+' select[name*=location_other_id]').val('');
	}
}

$(document).ready(function(){
	$('.pan-verify').on('click',function(){
		let pan_no = $('input[name=biz_pan_number]').val().trim();
		let user_id = $('#userId').val().trim();
		if(pan_no.length != 10){
			$('input[name=biz_pan_number] +span').remove();
			$('input[name=biz_pan_number]').after('<span class="text-danger error">Enter valid PAN Number</span>');
			return false;
		}
		$('.isloader').show();
		$.ajax({
			url: messages.biz_pan_to_gst_karza,//https://gst.karza.in/prod/v1/search
			type: "POST",
			data: {"pan": pan_no,"_token": messages.token, "userID":user_id},
			dataType:'json',
			error:function (xhr, status, errorThrown) {
				$('.isloader').hide();
    			alert(errorThrown);
			},
			success: function(res){ 
				ucic_code = res.ucic_code; 
				user_id = res.user_id;		  
				res = res.response;
				if(res == null){
					$('.isloader').hide();
				}else if(res['statusCode'] == 101){
					$('#ucic_code').val(ucic_code);
					$(".span_gst_text").hide();
					$(".span_gst_select").show();
					setUnsetError(0);
					$('input[name=is_gst_manual]').val('0');					
			    	$('#pan-msg').show();
			    	$('.pan-verify').text('Verified');
			    	$('.pan-verify').css('pointer-events','none');
			    	$('input[name=biz_pan_number]').attr('readonly',true);
			    	$('input[name=biz_pan_number] +span').remove();
			    	let gst_status = fillGSTinput(res.result);
			    	if(!gst_status){
			    		$(".span_gst_select").hide();
						$(".span_gst_text").show();		
						setUnsetError(1);
						$('input[name=is_gst_manual]').val('1');
			    	}
			    }else{
					$('#ucic_code').val(ucic_code);
					$(".span_gst_select").hide();
					$(".span_gst_text").show();		
					$("select[name='biz_cin']").hide();
					$("input[name='biz_cin']").show();
					setUnsetError(1);
					$('input[name=is_gst_manual]').val('1');			
			    	replaceAlert('No GST associated with the entered PAN.', 'error');
			    }
				if (typeof messages !== 'undefined' && messages.hasOwnProperty('get_ucic_data') && messages.get_ucic_data !== '') {
					if (!$.isEmptyObject(ucic_code)) {
						console.log(ucic_code);
						console.log(user_id);
						console.log(messages.returnurl)
						//alert(messages.returnurl);
						getUcicCodeData(ucic_code,user_id,messages.returnurl);
					}
				}
			    $('.isloader').hide();
			  }
		});
	})
})

function setUnsetError(is_gst_manual){
	if(is_gst_manual == 1) {
		$(".gst_address").html('');
		unsetError('input[name=biz_address]');
		unsetError('select[name=biz_state]');
		unsetError('input[name=biz_city]');
		unsetError('input[name=biz_pin]');
		unsetError('select[name=location_id]');
	} else {
		$(".gst_address").html('*');
		//setError('input[name=biz_address]', 'Registered address is required');
		//setError('select[name=biz_state]', 'Registered State is required');
		//setError('input[name=biz_city]', 'Registered City is required');
		//setError('input[name=biz_pin]', 'Registered Pin is required');
	}
}

function fillGSTinput(datas){
	let res ='';
    let  active = 0;
	let option_html = '<option value="">Select GST Number</option>';
	$(datas).each(function(i,data){
		if(data.authStatus == 'Active'){
             res += data.gstinId+',';
			 option_html += '<option value="'+data.gstinId+'">'+data.gstinId+'</option>';
	         active=1;	
            }
	})

    if(active==0){
        alert('PAN is in '+datas[0].authStatus+' mode');            
        return false;         
    }
        
	$('select[name=biz_gst_number]').html(option_html);
	$('input[name=pan_api_res]').val(res);
	//$('#business_information_form input[type=submit]').prop("disabled", false);
	return true;
}

function fillEntity(gstinId){
	if(gstinId == '' || messages.configure_api == 0){
		return false;
	}
	$('.isloader').show();
	$.ajax({
			url: messages.biz_gst_to_entity_karza,//"https://gst.karza.in/prod/v1/gst-verification"
			type: "POST",
			data: {"consent": "Y","gstin": gstinId, "_token":messages.token},
			dataType:'json',
			error:function (xhr, status, errorThrown) {
				$('.isloader').hide();
    			alert(errorThrown);
			},
			success: function(res){
				res = res.response;
				if(res == null){
					$('.isloader').hide();
				}else if(res['statusCode'] == 101){
			    	$('input[name=biz_entity_name]').val(res.result.lgnm);
			    	getCIN(res.result.lgnm);
			    	fillRegisteredAddress(res.result.pradr.adr);
			    }else{
			    	replaceAlert('No Entity associated with the entered GST.', 'error');
			    	$('.isloader').hide();
			    }
			}
		});
}

function getCIN(entityName){
	$.ajax({
		url: messages.biz_entity_to_cin_karza,//"https://api.karza.in/v2/compsearch-lite"
		type: "POST",
		data: {"consent": "Y","companyName": entityName, "_token": messages.token},
		dataType:'json',
		error:function (xhr, status, errorThrown) {
			alert(errorThrown);
		},
		success: function(res){
			res = res.response
			if(res == null){
				$('.isloader').hide();                                
                                $('.mandatory-biz-cin').hide();
			}else if(res['status-code'] == 101){
				//$('input[name=biz_cin]').val(res.result[0].cin);
				fillCINInput(res.result);
		    	// $('input[name=biz_cin]').val(res.result.result[0].cin);
				$('.isloader').hide();
		    }else{
		    	console.error('CIN number not fetched successfully');
				$('.isloader').hide();
		    }
		}
	});
}
function fillCINInput(datas){
	let res ='';
	let option_html = '<option value="">Select CIN Number</option>';
	$(datas.result).each(function(i,data){
		if (data.score >= 0.80 ) {
         	res += data.cin+',';
		 	option_html += '<option value="'+data.cin+'">'+data.cin+'</option>';
		}
         return  active=1;	
	})
	$('select[name=biz_cin]').html(option_html);
	$('input[name=cin_api_res]').val(res);
        
        if ($('select[name=biz_cin] option').length > 1) {
            $('.mandatory-biz-cin').show();
        } else {
            $('.mandatory-biz-cin').hide();
        }
}
function checkValidation(){
	unsetError('input[name=biz_pan_number]');
	unsetError('select[name=biz_gst_number]');
	unsetError('select[name=biz_cin]');
	unsetError('input[name=biz_gst_number_text]');
	unsetError('input[name=biz_entity_name]');
	unsetError('select[name=biz_type_id]');
	unsetError('input[name=incorporation_date]');
	unsetError('select[name=biz_constitution]');
	//unsetError('select[name=entity_type_id]');
	unsetError('select[name=segment]');
	unsetError('input[name=biz_turnover]');

	unsetError('input[name=loan_amount]');
	unsetError('input[name=tenor_days]');
	
	unsetError('input[name=biz_address]');
	unsetError('select[name=biz_state]');
	unsetError('input[name=biz_city]');
	unsetError('input[name=biz_pin]');
	unsetError('#check_block');
	unsetError('input[name=share_holding_date]');
		unsetError('input[name=busi_pan_comm_date]');
	unsetError('select[name=location_id]');		
	
	unsetError('#product_type_1_loan');
	unsetError('#product_type_2_loan');
	unsetError('#product_type_3_loan');
	unsetError('#product_type_1_tenor');
	unsetError('#product_type_2_tenor');
	unsetError('#product_type_3_tenor');	
	unsetError('select[name=msme_type]');
	unsetError('input[name=msme_no]');
	unsetError('input[name=email]');
	unsetError('input[name=mobile]');
	unsetError('input[name=invoice_level_mail]');
	  
	let flag = true;
	let is_gst_manual = $('input[name=is_gst_manual]').val().trim();
	let biz_pan_number = $('input[name=biz_pan_number]').val().trim();
	let biz_gst_number = $('select[name=biz_gst_number]').val();
	let biz_cin = $('select[name=biz_cin]').val();
	if(is_gst_manual == 1) {
		biz_gst_number = $('input[name=biz_gst_number_text]').val();
	}
	let biz_entity_name = $('input[name=biz_entity_name]').val().trim();
	let biz_type_id = $('select[name=biz_type_id]').val();
	let incorporation_date = $('input[name=incorporation_date]').val();
	let biz_constitution = $('select[name=biz_constitution]').val();
	//let entity_type_id = $('select[name=entity_type_id]').val();
	let segment = $('select[name=segment]').val();
	let biz_turnover = $('input[name=biz_turnover]').val().trim();
	
	let product_id_supply  = $('input[name*="product_id[1][checkbox]"').prop("checked");
	let product_id_term    = $('input[name*="product_id[2][checkbox]"').prop("checked");
	let product_id_leasing = $('input[name*="product_id[3][checkbox]"').prop("checked");

	let loan_amount_1 = $('input[name*="product_id[1][loan_amount]"');
	let loan_amount_2 = $('input[name*="product_id[2][loan_amount]"');
	let loan_amount_3 = $('input[name*="product_id[3][loan_amount]"');
	let tenor_days_1 = $('input[name*="product_id[1][tenor_days]"');
	let tenor_days_2 = $('input[name*="product_id[2][tenor_days]"');
	let tenor_days_3 = $('input[name*="product_id[3][tenor_days]"');

	let loan_amount_supply =   (loan_amount_1.length)?parseInt(loan_amount_1.val().trim().replace(/,/g, '')):'';
	let loan_amount_term =  (loan_amount_2.length)?parseInt(loan_amount_2.val().trim().replace(/,/g, '')):'';
	let loan_amount_leasing =  (loan_amount_3.length)?parseInt(loan_amount_3.val().trim().replace(/,/g, '')):'';
	let tenor_days_supply =  (tenor_days_1.length)?tenor_days_1.val().trim():'';
	let tenor_days_term =  (tenor_days_2.length)?tenor_days_2.val().trim():'';
	let tenor_days_leasing =  (tenor_days_3.length)?tenor_days_3.val().trim():'';
	
	let biz_address = $('input[name=biz_address]').val().trim();
	let biz_state = $('select[name=biz_state]').val();
	let biz_city = $('input[name=biz_city]').val().trim();
	let biz_pin = $('input[name=biz_pin]').val().trim();
	let location_id = $('select[name=location_id]').val();
	
	let share_holding_date = $('input[name=share_holding_date]').val();
        let busi_pan_comm_date = $('input[name=busi_pan_comm_date]').val();
	let msme_type = $('select[name=msme_type]').val();
	let msme_number = $('input[name=msme_no]').val().trim();
	let email = $('input[name=email]').val().trim();
	let mobile = $('input[name=mobile]').val().trim();
	var invoiceLeveMail = $('#invoice_level_mail').val().split(',');

	if(biz_pan_number.length != 10){
		setError('input[name=biz_pan_number]', 'Enter valid PAN Number');
		flag = false;
	}else if(!(/[a-zA-z]{5}\d{4}[a-zA-Z]{1}/.test(biz_pan_number))){
		setError('input[name=biz_pan_number]', 'Please fill correct PAN number');
		flag = false;
	}else if($('.pan-verify').text() == 'Verify' && is_gst_manual!=1){
		setError('input[name=biz_pan_number]', 'Please verify Business PAN First');
		flag = false;
	}

	if((biz_gst_number == '' || biz_gst_number == null) && is_gst_manual!=1){
		setError('select[name=biz_gst_number]', 'Please select GST Number');
		flag = false;
	}else if((biz_gst_number == '' || biz_gst_number == null) && is_gst_manual==1){
		setError('input[name=biz_gst_number_text]', 'Please enter GST Number');
		flag = false;
	}

	/*if($('select[name=biz_cin] option').length > 1 && (biz_cin == '' || biz_cin == null) && is_gst_manual!=1){
		setError('select[name=biz_cin]', 'Please select CIN Number');
		flag = false;
	}*/
	/*else if($('input[name=biz_cin]').val()  == ''){
		setError('select[name=biz_gst_number]', 'Service unavailable!');
		flag = false;	
	}*/

	if(biz_entity_name.length == ''){
		setError('input[name=biz_entity_name]', 'Enter valid Entity Name');
		flag = false;
	}

	if(biz_type_id == ''){
		setError('select[name=biz_type_id]', 'Plese select Industry');
		flag = false;
	}

	if(incorporation_date == ''){
		setError('input[name=incorporation_date]', 'Incorporation date is required');
		flag = false;
	}

	if(share_holding_date == ''){
		setError('input[name=share_holding_date]', 'Share holding date is required.');
		flag = false;
	}

    //     if(busi_pan_comm_date == ''){
	// 	setError('input[name=busi_pan_comm_date]', 'Date of commencement of business is required.');
	// 	flag = false;
	// }
        
	if(biz_constitution == ''){
		setError('select[name=biz_constitution]', 'Business constitution is required');
		flag = false;
	}

	// if(entity_type_id == ''){
	// 	setError('select[name=entity_type_id]', 'Plese select Sub Industry');
	// 	flag = false;
	// }
        
        if(msme_type == ''){
	 	setError('select[name=msme_type]', 'Plese select MSME Type');
	 	flag = false;
        }
        
        if(msme_number == ''){
	 	setError('input[name=msme_no]', 'MSME Number is required');
	 	flag = false;
        }         

	if(segment== ''){
		setError('select[name=segment]', 'Segment is required');
		flag = false;
	}

	if(biz_turnover == 0){
		// OK
	}else if(biz_turnover.length != 0 && parseInt(biz_turnover.replace(/,/g, '')) == 0){
		setError('input[name=biz_turnover]', 'Business Turnover amount is not valid');
		flag = false;
	}

	if(product_id_supply && (loan_amount_supply == 0 || Number.isNaN(loan_amount_supply) == true )){
		setError('#product_type_1_loan', ' Supply Chain Loan amount is required');
		flag = false;
	}
	if(product_id_term && (loan_amount_term == 0 || Number.isNaN(loan_amount_term) == true )){
		setError('#product_type_2_loan', 'Term Loan Amount is required');
		flag = false;
	}
	if(product_id_leasing && (loan_amount_leasing == 0 || Number.isNaN(loan_amount_leasing) == true )){
                if (messages.is_anchor_lenevo ==1){
                    setError('#product_type_3_loan', 'Total value of Asset is required');
                } else {
                    setError('#product_type_3_loan', 'Leasing Loan Amount is required');
                }
		flag = false;
	}

	if(tenor_days_supply == 0){
		// OK
	}else if(tenor_days_supply.length != 0 && parseInt(tenor_days_supply) == 0){
		setError('#product_type_1_tenor', 'Enter valid Supply Chain Tenor (Days)');
		flag = false;
	}

	if(tenor_days_term == 0){
		// OK
	}else if(tenor_days_term.length != 0 && parseInt(tenor_days_term) == 0){
		setError('#product_type_2_tenor', 'Enter valid Term Tenor (Months)');
		flag = false;
	}
	if(tenor_days_leasing == 0){
		// OK
	}else if(tenor_days_leasing.length != 0 && parseInt(tenor_days_leasing) == 0){
		setError('#product_type_3_tenor', 'Enter valid Leasing Tenor (Months)');
		flag = false;
	}

	if( !(product_id_supply || product_id_term || product_id_leasing)){
		
		setError('#check_block', 'Product type is required');
		flag = false;
	}

	if(biz_address.length == '' && is_gst_manual!=1){
		setError('input[name=biz_address]', 'Registered address is required');
		flag = false;
	}

	if(biz_state == '' && is_gst_manual!=1){
		setError('select[name=biz_state]', 'Registered State is required');
		flag = false;
	}

	if (location_id == '' && is_gst_manual != 1) {
		setError('select[name=location_id]', 'Registered Location is required');
		flag = false;
	}	

	if((biz_city.length == '' || !isNaN(biz_city)) && is_gst_manual!=1){
		setError('input[name=biz_city]', 'Registered City is required');
		flag = false;
	}

	if(biz_pin.length != 6 && is_gst_manual!=1){
		setError('input[name=biz_pin]', 'Registered Pin is required');
		flag = false;
	}else if((!(/^\d{6}$/.test(biz_pin)) || parseInt(biz_pin) < 100000) && is_gst_manual!=1){
		setError('input[name=biz_pin]', 'Registered Pin should be numeric only');
		flag = false;
	}

	if (email.length == '') {
		setError('input[name=email]', 'Enter Email Address');
		flag = false;
	} else if (emailExtention(email) == null) {
		setError('input[name=email]', 'Please enter valid email');
		flag = false;
	} else if (mobile.length > 100) {
		setError('input[name=email]', 'Email Address should not greater than 100 characters');
		flag = false;
	}

	

	if (mobile.length == '') {
		setError('input[name=mobile]', 'Enter Mobile No');
		flag = false;
	} else if(mobile.length < 10) {
		setError('input[name=mobile]', 'Mobile No should be 10 digits');
		flag = false;
	} else if (mobile.length > 10) {
		setError('input[name=mobile]', 'Mobile No should not greater than 10 digits');
		flag = false;
	}

	if(invoiceLeveMail == ''){
		setError('input[name=invoice_level_mail]', 'Invoice level Mail Field cannont be left Blank');
		flag = false;
	}
	if (invoiceLeveMail !== '') {
		var validEmails = [];
		var invalidEmails = [];
		var duplicateEmails = [];
		var uniqueEmails = new Set();
	  
		var emailPattern = /^\w+([\.-]?\w+)@\w+([\.-]?\w+)(\.\w{2,3})+$/;
	  
		for (var i = 0; i < invoiceLeveMail.length; i++) {
		  var trimmedEmail = invoiceLeveMail[i].trim();
	  
		  if (emailPattern.test(trimmedEmail)) {
			if (uniqueEmails.has(trimmedEmail)) {
			  // Duplicate email ID found
			  if (!duplicateEmails.includes(trimmedEmail)) {
				duplicateEmails.push(trimmedEmail);
			  }
			} else {
			  validEmails.push(trimmedEmail);
			  uniqueEmails.add(trimmedEmail);
			}
		  } else {
			invalidEmails.push(trimmedEmail);
		  }
		}
	  
		if (invalidEmails.length !== 0) {
		  setError('input[name=invoice_level_mail]', 'Invalid email IDs: ' + invalidEmails.join(', '));
		  flag = false;
		}
	  
		if (duplicateEmails.length !== 0) {
		  setError('input[name=invoice_level_mail]', 'Duplicate email IDs: ' + duplicateEmails.join(', '));
		  flag = false;
		}
	  }
    

	if(flag){
		return true;
	}else{
		return false;
	}
}


$(document).on('blur', '.pan-validate', function(e){
	unsetError('input[name=biz_pan_number]');
	let pan = $('input[name=biz_pan_number]').val();

	if(/[a-zA-z]{5}\d{4}[a-zA-Z]{1}/.test(pan)){
		return true;
	}else{
		setError('input[name=biz_pan_number]', 'Enter valid PAN number');
		return false;
	}

	/*if(pan.length <= 5){
		if(/^[a-zA-Z]+$/.test(pan)){
			return true;
		}else{
			$(this).val(pan.slice(0, -1));
			setError('input[name=biz_pan_number]', 'Enter valid PAN number');
			return false;
		}
	}else if(pan.length > 5 && pan.length <=9){
		if(/[a-zA-z]{5}\d{1,4}/.test(pan)){
			return true;
		}else{
			$(this).val(pan.slice(0, -1));
			setError('input[name=biz_pan_number]', 'Enter valid PAN number');
			return false;
		}
	}else{
		if(/[a-zA-z]{5}\d{4}[a-zA-Z]{1}/.test(pan)){
			return true;
		}else{
			$(this).val(pan.slice(0, -1));
			setError('input[name=biz_pan_number]', 'Enter valid PAN number');
			return false;
		}
	}*/	
});

function fillRegisteredAddress(addr_str){
	try {
		let addr_array = addr_str.split(',');
		let pin = addr_array.pop().replace(/pin:/,'').trim();
		let state = addr_array.pop().trim();
		let city = addr_array.pop().trim();
		let address = addr_array.toString().trim();
		let location = addr_array.pop().trim();
		$('input[name=biz_address]').val(address);
		$('select[name=biz_state] option:contains('+state+')').prop('selected', true);
		$('input[name=biz_city]').val(city);
		$('input[name=biz_pin]').val(pin);
		$('select[name=location_id] option:contains('+location+')').prop('selected', true);
		//return {'address': address, 'city': city, 'state': state, 'pin': pin};
	}
	catch(err) {
	  console.error(err);
	}
}

$(document).ready(function(){
	//$.fn.handleIndustryChange = function () {
	/**
	 *  Handle change event
	 */
	$(document).on('change', '.industry_change', function () {
		var industryVal=$("#biz_type_id").val();
		var segmentId =$("#segmentId").val();
		unsetError('select[name=segment]');
		unsetError('select[name=biz_type_id]');
		if(segmentId == ''){
			setError('select[name=segment]', 'Segment is required');
			$("#segmentId").focus();
			return false;
		}else if(industryVal == ''){
			setError('select[name=biz_type_id]', 'Industry is required');
			$("#biz_type_id").focus();
			return false;
		}else{
			handleIndustryChange(industryVal,null,segmentId);
		}
	});
  //handleIndustryChange($("#biz_type_id").val(),$(".sub_industry").val());
});

function handleIndustryChange(intdustval,subIndId, segmentId){
	//let selector = $(this);
	// if(segmentId == ''){
	// 	unsetError('select[name=segment]');
	// 	setError('select[name=segment]', 'Segment is required');
	// 	$("#segmentId").focus();
	// 	return false;
	// }else{
	// 	unsetError('select[name=segment]');
	// }
	let currentValue = intdustval;
	let subIndus = $('.sub_industry');

	let selected = (subIndId)?subIndId:null;	
	$.ajax({
		url: messages.get_sub_industry,
		type: 'POST',
		dataType: 'json',
		data: {
			id: currentValue,
			segmentId: segmentId,
			_token: messages.token
		},
		success: function (data) {
			subIndus.removeClass('error');
			subIndus.find('option').remove().end();
			subIndus.append('<option value="">' + messages.please_select + '</option>');
			$.each(data, function (index, data) {
				var check = '';
				if (data.id == selected) {
					check = 'selected="selected"';
				}
				subIndus.append('<option  value="' + data.id + '" ' + check + ' >' + data.name + '</option>');
			});
		},
		error: function () {
			console.log('Error while getting city');
		}
	});
};

function emailExtention(value) {
	return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
}

function getUcicCodeData(ucic_code,user_id, returnurl) {
	if ($.isEmptyObject(ucic_code)) {
		alert('Empty UCIC CODE');
		return false;			
	}
	$('.isloader').show();
	$.ajax({
	  url: messages.get_ucic_data,
	  type: "POST",
	  dataType: 'json',
	  data: {"ucic": ucic_code,"_token": messages.token, "user_id":user_id,"returnurl":returnurl},
	  success: function(responseData) {
	  if (responseData.status == 1){
		$('form#business_information_form input[name=is_auto_populated_ucic_data]').val('1');
			window.location.href = responseData.redirectUrl;
		}else{
			$('form#business_information_form input[name=is_auto_populated_ucic_data]').val('0');
		}
		$('.isloader').hide();
	  },
	  error: function(xhr, textStatus, errorThrown) {
		$('.isloader').hide();
		alert(errorThrown);
	  }
	});
}
