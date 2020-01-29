function copyAddress(id,th){
	console.log(id);
	if($(th).is(':checked')){
		$(id+' input[name*=biz_other_address]').val($('input[name=biz_address]').val());
		$(id+' input[name*=biz_other_city]').val($('input[name=biz_city]').val());
		$(id+' select[name*=biz_other_state]').val($('select[name=biz_state]').val());
		$(id+' input[name*=biz_other_pin]').val($('input[name=biz_pin]').val());
	}else{
		$(id+' input[name*=biz_other_address]').val('');
		$(id+' input[name*=biz_other_city]').val('');
		$(id+' select[name*=biz_other_state]').val('');
		$(id+' input[name*=biz_other_pin]').val('');
	}
}

$(document).ready(function(){
	$('.pan-verify').on('click',function(){
		let pan_no = $('input[name=biz_pan_number]').val().trim();
		if(pan_no.length != 10){
			$('input[name=biz_pan_number] +span').remove();
			$('input[name=biz_pan_number]').after('<span class="text-danger error">Enter valid PAN Number</span>');
			return false;
		}
		$('.isloader').show();
		$.ajax({
			url: messages.biz_pan_to_gst_karza,//https://gst.karza.in/prod/v1/search
			type: "POST",
			data: {"pan": pan_no,"_token": messages.token},
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
			    	$('#pan-msg').show();
			    	$('.pan-verify').text('Verified');
			    	$('.pan-verify').css('pointer-events','none');
			    	$('input[name=biz_pan_number]').attr('readonly',true);
			    	$('input[name=biz_pan_number] +span').remove();
			    	fillGSTinput(res.result);
			    }else{
			    	alert('No GST associated with the entered PAN.');
			    }
			    $('.isloader').hide();
			  }
		});
	})
})

function fillGSTinput(datas){
	let res ='';
	let option_html = '<option value="">Select GST Number</option>';
	$(datas).each(function(i,data){
		if(data.authStatus == 'Active'){
			res += data.gstinId+',';
			option_html += '<option value="'+data.gstinId+'">'+data.gstinId+'</option>';
		}
	})
	$('select[name=biz_gst_number]').html(option_html);
	$('input[name=pan_api_res]').val(res);
	//$('#business_information_form input[type=submit]').prop("disabled", false);
}

function fillEntity(gstinId){
	if(gstinId == ''){
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
			    	alert('Something went wrong, Try again later');
			    }
			    $('.isloader').hide();
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
			}else if(res['status-code'] == 101){
		    	$('input[name=biz_cin]').val(res.result[0].cin);
		    }else{
		    	alert('Something went wrong, Try again later');
		    }
		}
	});
}

function checkValidation(){
	unsetError('input[name=biz_pan_number]');
	unsetError('select[name=biz_gst_number]');
	unsetError('input[name=biz_entity_name]');
	unsetError('select[name=biz_type_id]');
	unsetError('input[name=incorporation_date]');
	unsetError('select[name=biz_constitution]');
	unsetError('select[name=entity_type_id]');
	unsetError('select[name=segment]');
	unsetError('input[name=biz_turnover]');
	unsetError('input[name=loan_amount]');
	unsetError('input[name=tenor_days]');
	unsetError('input[name=biz_address]');
	unsetError('select[name=biz_state]');
	unsetError('input[name=biz_city]');
	unsetError('input[name=biz_pin]');
	unsetError('#check_block');
	
	let flag = true;
	let biz_pan_number = $('input[name=biz_pan_number]').val().trim();
	let biz_gst_number = $('select[name=biz_gst_number]').val();
	let biz_entity_name = $('input[name=biz_entity_name]').val().trim();
	let biz_type_id = $('select[name=biz_type_id]').val();
	let incorporation_date = $('input[name=incorporation_date]').val();
	let biz_constitution = $('select[name=biz_constitution]').val();
	let entity_type_id = $('select[name=entity_type_id]').val();
	let segment = $('select[name=segment]').val();
	let biz_turnover = $('input[name=biz_turnover]').val().trim();
	let loan_amount = $('input[name=loan_amount]').val().trim();
	let tenor_days = $('input[name=tenor_days]').val().trim();
	let biz_address = $('input[name=biz_address]').val().trim();
	let biz_state = $('select[name=biz_state]').val();
	let biz_city = $('input[name=biz_city]').val().trim();
	let biz_pin = $('input[name=biz_pin]').val().trim();
	let product_id = $('input[name*=product_id]:checked');

	if(biz_pan_number.length != 10){
		setError('input[name=biz_pan_number]', 'Enter valid PAN Number');
		flag = false;
	}else if(!(/[a-zA-z]{5}\d{4}[a-zA-Z]{1}/.test(biz_pan_number))){
		setError('input[name=biz_pan_number]', 'Please fill correct PAN number');
		flag = false;
	}else if($('.pan-verify').text() == 'Verify'){
		setError('input[name=biz_pan_number]', 'Please verify Business PAN First');
		flag = false;
	}

	if(biz_gst_number == ''){
		setError('select[name=biz_gst_number]', 'Please select GST Number');
		flag = false;
	}
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

	if(biz_constitution == ''){
		setError('select[name=biz_constitution]', 'Business constitution is required');
		flag = false;
	}

	if(entity_type_id == ''){
		setError('select[name=entity_type_id]', 'Nature of Business is required');
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

	if(loan_amount.length == 0 || parseInt(loan_amount.replace(/,/g, '')) == 0){
		setError('input[name=loan_amount]', 'Applied Loan Amount is required');
		flag = false;
	}

	if(tenor_days == 0){
		// OK
	}else if(tenor_days.length != 0 && parseInt(tenor_days) == 0){
		setError('input[name=tenor_days]', 'Enter valid Tranche Tenor');
		flag = false;
	}

	if(product_id.length <= 0){
		setError('#check_block', 'Product type is required');
		flag = false;
	}

	if(biz_address.length == ''){
		setError('input[name=biz_address]', 'Registered address is required');
		flag = false;
	}

	if(biz_state == ''){
		setError('select[name=biz_state]', 'Registered State is required');
		flag = false;
	}

	if(biz_city.length == '' || !isNaN(biz_city)){
		setError('input[name=biz_city]', 'Registered City is required');
		flag = false;
	}

	if(biz_pin.length != 6){
		setError('input[name=biz_pin]', 'Registered Pin is required');
		flag = false;
	}else if(!(/^\d{6}$/.test(biz_pin)) || parseInt(biz_pin) < 100000){
		setError('input[name=biz_pin]', 'Registered Pin should be numeric only');
		flag = false;
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
		$('input[name=biz_address]').val(address);
		$('select[name=biz_state] option:contains('+state+')').prop('selected', true);
		$('input[name=biz_city]').val(city);
		$('input[name=biz_pin]').val(pin);
		//return {'address': address, 'city': city, 'state': state, 'pin': pin};
	}
	catch(err) {
	  console.error(err);
	}
}

$(document).ready(function(){
	//$.fn.handleIndustryChange = function () {
		function handleIndustryChange(intdustval,subIndId){
		//let selector = $(this);
		let currentValue = intdustval;
		let subIndus = $('.sub_industry');

		let selected = (subIndId)?subIndId:null;
		$.ajax({
			url: messages.get_sub_industry,
			type: 'POST',
			dataType: 'json',
			data: {
				id: currentValue,
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

	/**
	 *  Handle change event
	 */
	$(document).on('change', '.industry_change', function () {
		var industryVal=$("#biz_type_id").val();
		handleIndustryChange(industryVal,null);
	});
   handleIndustryChange($("#biz_type_id").val(),$(".sub_industry").val());
});