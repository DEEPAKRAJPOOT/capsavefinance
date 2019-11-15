@extends('layouts.admin')

@section('content')

@include('layouts.user-inner.left-menu')      

<div class="content-wrapper">
			<section class="content-header">
				<div class="header-icon">
					<i class="fa  fa-list"></i>
				</div>
				<div class="header-title">
					<h3>Manage Lenders</h3>
					<small>Lenders List</small>
					<ol class="breadcrumb">
						<li><a href="https://admin.zuron.in/admin/dashboard"><i class="mdi mdi-home"></i> Home</a></li>
						<li class="active">Manage Suppliers</li>
					</ol>
				</div>

				<!--<div class="register-text"> <p><a href="javascript:void(0);" id="register" class="mt-10">Supplier register</a></p></div>-->
			</section>




			<div class="card">
				<div class="card-body">

					<input type="hidden" name="status" value="">
					<input type="hidden" name="head" value="">

					<!---
                        local permission_has =64
                        stage permission_has=64
                        live permission_has=65
                        --->

					<div class="head-sec">
						<div class="pull-right" style="margin-bottom: 10px;">
							<a href="javascript:void(0);" id="register">
								<button class="btn btn-labeled btn-success m-b-5" type="button">
									<span class="btn-label">
										<i class="fa fa-plus"></i>
									</span>
									Add New
								</button>
							</a>
						</div>
					</div>



					<div class="row">
						<div class="col-12 dataTables_wrapper">
							<div class="row filtersec">
								<div class="col-sm-12 col-md-6">
									<div class="dataTables_length" id="supplier-listing_length"><label>Show <select name="supplier-listing_length" aria-controls="supplier-listing" class="form-control form-control-sm">
												<option value="10">10</option>
												<option value="25">25</option>
												<option value="50">50</option>
												<option value="100">100</option>
											</select> entries</label></div>
								</div>
								<div class="col-sm-12 col-md-6">
									<div id="supplier-listing_filter" class="dataTables_filter"><label>Search:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="supplier-listing"></label></div>
								</div>
							</div>
							<div class="overflow">
								<div id="supplier-listing_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">

									<div class="row">
										<div class="col-sm-12">
											<table id="supplier-listing" class="table white-space table-striped cell-border dataTable no-footer" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
												<thead>
													<tr role="row">
														<th class="sorting_asc">Sr.No.</th>
														<th class="sorting">Name</th>
														<th class="sorting">Email</th>

														<th class="white-space sorting">Created On</th>
														<th class="sorting">Status</th>
														<th class="sorting">Action</th>
													</tr>
												</thead>
												<tbody>

													<tr role="row" class="odd">
														<td class="sorting_1">1.</td>
														<td>ANI TECHNOLOGIES PRIVATE LIMITED</td>
														<td>equinox@ajitrentals.com</td>

														<td class=" white-space">Master Admin<br> 15-Oct-2019</td>
														<td>
															<div class="btn-group ml-2 mb-1"><label class="badge badge-success current-status">Active&nbsp; &nbsp; <i class="fa fa-caret-down"></i></label>
																<ul class="change-status-toggle current-status" data-id="19">
																	<li><a href="javascript:void(0);" data-val="1" class="change-status">Inactive</a></li>
																	 
																</ul>
															</div>
														</td>

														<td><a href="#" class="btn btn-primary btn-sm">Edit</a>
															<a href="#" class="btn btn-danger btn-sm">Delete</a></td>
													</tr>
													<tr role="row" class="even">
														<td class="sorting_1">2.</td>
														<td>PROCON RMC PLANTS PRIVATE LIMITED</td>
														<td>gagan@proconrmc.com</td>

														<td class=" white-space">Master Admin<br> 15-Oct-2019</td>
														<td>
															<div class="btn-group ml-2 mb-1"><label class="badge badge-success current-status">Active&nbsp; &nbsp; <i class="fa fa-caret-down"></i></label>
																<ul class="change-status-toggle current-status" data-id="18">
																	<li><a href="javascript:void(0);" data-val="1" class="change-status">Inactive</a></li>
																	 
																</ul>
															</div>
														</td>

														<td><a href="#" class="btn btn-primary btn-sm">Edit</a>
															<a href="#" class="btn btn-danger btn-sm">Delete</a></td>
													</tr>
													<tr role="row" class="odd">
														<td class="sorting_1">3.</td>
														<td>SOFOCLE TECHNOLOGIES OPC PRIVATE LIMITED</td>
														<td>khushboo.sethi@sofodev.co</td>

														<td class=" white-space">Ravi<br> 03-Oct-2019</td>
														<td>
															<div class="btn-group ml-2 mb-1"><label class="badge badge-danger current-status">Inactive &nbsp; &nbsp; <i class="fa fa-caret-down"></i></label>
																<ul class="change-status-toggle current-status" data-id="17">
																	<li><a href="javascript:void(0);" data-val="3" class="change-status">Active</a></li>

																</ul>
															</div>
														</td>

														<td><a href="#" class="btn btn-primary btn-sm">Edit</a> <a href="#" class="btn btn-danger btn-sm">Delete</a></td>
													</tr>
													<tr role="row" class="even">
														<td class="sorting_1">4.</td>
														<td>Sofocle</td>
														<td>sofocle@sofodev.co</td>

														<td class=" white-space">Ravi Chamria<br> 03-Oct-2019</td>
														<td>
															<div class="btn-group ml-2 mb-1"><label class="badge badge-success current-status">Active&nbsp; &nbsp; <i class="fa fa-caret-down"></i></label>
																<ul class="change-status-toggle current-status" data-id="19">
																	<li><a href="javascript:void(0);" data-val="1" class="change-status">Inactive</a></li>
																	 
																</ul>
															</div>
														</td>
														<td><a href="#" class="btn btn-primary btn-sm">Edit</a> <a href="#" class="btn btn-danger btn-sm">Delete</a></td>
													</tr>
													<tr role="row" class="odd">
														<td class="sorting_1">5.</td>
														<td>ANNONA IT SOLUTIONS PRIVATE LIMITED</td>
														<td>partho@annona.in</td>

														<td class=" white-space">Master Admin<br> 24-Sep-2019</td>
														<td>
															<div class="btn-group ml-2 mb-1"><label class="badge badge-success current-status">Active&nbsp; &nbsp; <i class="fa fa-caret-down"></i></label>
																<ul class="change-status-toggle current-status" data-id="19">
																	<li><a href="javascript:void(0);" data-val="1" class="change-status">Inactive</a></li>
																	 
																</ul>
															</div>
														</td>

														<td><a href="#" class="btn btn-primary btn-sm">Edit</a> <a href="#" class="btn btn-danger btn-sm">Delete</a></td>
													</tr>


												</tbody>
											</table>
											<div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
										</div>
									</div>

								</div>
							</div>
							<div class="row mt-4">
								<div class="col-sm-12 col-md-5">
									<div class="dataTables_info" id="supplier-listing_info" role="status" aria-live="polite">Showing 1 to 10 of 12 entries</div>
								</div>
								<div class="col-sm-12 col-md-7">
									<div class="dataTables_paginate paging_simple_numbers" id="supplier-listing_paginate">
										<ul class="pagination">
											<li class="paginate_button page-item previous disabled" id="supplier-listing_previous"><a href="https://admin.zuron.in/admin/manage-supplier#" aria-controls="supplier-listing" data-dt-idx="0" tabindex="0" class="page-link">Previous</a></li>
											<li class="paginate_button page-item active"><a href="https://admin.zuron.in/admin/manage-supplier#" aria-controls="supplier-listing" data-dt-idx="1" tabindex="0" class="page-link">1</a></li>
											<li class="paginate_button page-item "><a href="https://admin.zuron.in/admin/manage-supplier#" aria-controls="supplier-listing" data-dt-idx="2" tabindex="0" class="page-link">2</a></li>
											<li class="paginate_button page-item next" id="supplier-listing_next"><a href="https://admin.zuron.in/admin/manage-supplier#" aria-controls="supplier-listing" data-dt-idx="3" tabindex="0" class="page-link">Next</a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- register modal -->
			<div class="modal fade" id="mySuppModal" tabindex="-1" role="dialog">
				<div id="modalSize" class="modal-dialog modal-lg" role="document">
					<div class="modal-content no-padding">
						<div class="modal-header modal-title-top">
							<h4 class="modal-title resiter"> Registration</h4>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">×</span>
							</button>
						</div>
						<div id="reg-box">
							<form name="frmRegister" id="frmRegister" action="https://admin.zuron.in/admin/sup-register" method="post" novalidate="novalidate">
								<div class="modal-body">
									<div class="row">
										<div class="col-md-12">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label for="txtCreditPeriod">Full Name
																<span class="mandatory">*</span>
															</label>
															<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Full Name" required="">
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label for="txtSupplierName">Business Name
																<span class="mandatory">*</span>
															</label>
															<input type="text" name="name" id="name" value="" class="form-control" tabindex="3" placeholder="Business Name" required="">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label for="txtEmail">Email
																<span class="mandatory">*</span>
															</label>
															<input type="hidden" name="send_otp" id="send-otp" value="">
															<input type="email" name="email" id="email" value="" class="form-control" tabindex="4" placeholder="Email" required="">
														</div>
													</div>
													<!-- <div class="col-md-6">
                              <div class="form-group password-input">
                                 <label for="txtPassword">Password
                                 <span class="mandatory">*</span>
                                 </label>
                     <input class="form-control" name="password" id="passwordRegistration" type="password" tabindex="5" placeholder="Password" oninput="removeSpace(this);">
                              </div>
                           </div>  -->
													<div class="col-md-6">
														<div class="form-group">
															<label for="txtMobile">Mobile
																<span class="mandatory">*</span>
															</label>
															<input class="form-control" name="phone-ext" id="phone-ext" type="text" value="+91" style="width: 60px; position: absolute;" readonly="">
															<input class="form-control numbercls" name="phone" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" required="">
															<div class="failed">
																<div style="color:#FF0000">
																	<small class="erro-sms" id="erro-sms">
																	</small>
																</div>
															</div>
														</div>
														<input name="password" id="passwordRegistration" type="hidden" oninput="removeSpace(this);" value="brl4bi88">
													</div>
												</div>
												<!--<div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtMobile">Mobile
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input class="form-control" name="phone-ext" id="phone-ext" type="text" value="+91" style="width: 60px; position: absolute;" readonly>
                                 <input class="form-control numbercls" name="phone" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" required>
                                 <div class="failed">
                                    <div style="color:#FF0000">
                                       <small class="erro-sms" id="erro-sms">
                                       </small>
                                    </div>
                                 </div>
                              </div>
                           </div>
                          </div>-->
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<div class="col-md-6">
											<label class="error">All fields are marked with * mandatory</label>
										</div>
										<div class="col-md-6">
											<button type="submit" class="btn btn-primary pull-right" id="submit-reg" tabindex="7" disabled="">Submit</button>
										</div>
									</div>

								</div>
							</form>

						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->


			</div>


			<script src="./Zuron - Manage Suppliers_files/jquery.validate.js.download"></script>
			<script src="./Zuron - Manage Suppliers_files/hideShowPassword.min.js.download" type="text/javascript"></script>

			<script>
				function removeSpace(obj) {
					var x = obj.value;
					x = x.split(' ').join('');
					obj.value = x;
				}

				$("#register").click(function() {
					$('#mySuppModal').modal('show');
				});

				function setTimer() {
					$('#s_timer').countdowntimer({
						minutes: 2,
						size: "sm"
					});
				}

				function handleErrors(text) {
					document.getElementById('show-err').innerHTML = text
				}

				function reg_handleErrors(text) {
					console.log('err', text)
					document.getElementById('erro-sms').innerHTML = text
					$('#otp-error').text(text);
					setTimeout(() => {
						document.getElementById('erro-sms').innerHTML = ''
						$('#otp-error').text('');
					}, 2000)
					//    document.getElementById('otp-error').innerHTML = text
				}
				/* $.validator.addMethod(
				    "is_mob",
				    function(value, element) {
				          return this.optional(element) || /^[0-9]\d{9}$|^[1-9]\d{9}$/.test(value);
				    },
				    "Please enter a valid Mobile Number."
				 );*/
				var _frm = $('#frmRegister');

				_frm.validate({
					rules: {
						employee: {
							required: true,
						},
						email: {
							required: true,
							email: true,
							remote: {
								url: '/ajaxroute/checkExistence',
								type: "POST",
								data: {
									type: "emailId"
								}
							}
						},
						name: {
							required: true,
							remote: {
								url: '/ajaxroute/checkExistence',
								type: "POST",
								data: {
									type: "supplierName"
								}
							}
						},
						phone: {
							required: true,
							// is_mob:true,
							remote: {
								url: '/ajaxroute/checkExistence',
								type: "POST",
								data: {
									type: "phone"
								}
							}
						}
					},
					messages: {
						email: {
							remote: 'Email already exist',
						},
						name: {
							remote: 'Business Name already exist',
						},
						phone: {
							remote: 'Mobile Number already exist',
						}
					},
					submitHandler: function(form) {
						/*var temflag = $("#send-otp").val();
                                           if(temflag == ""){
                                          reg_sendsOtp();
                                           }else if(temflag == "1"){                                            
                                               form.submit();
                                           }
                                    */
						if ($('#frmRegister').validate()) {
							form.submit();
							document.getElementById("submit-reg").disabled = true;
						}
					},
				});

				$(document).on('keydown', '.numbercls', function(e) {
					//alert(e.keyCode);
					if (e.keyCode == 8) {
						var phoneVal = $("#phone").val();
						if (phoneVal.length >= 11) {
							document.getElementById("submit-reg").disabled = false;
						} else {
							document.getElementById("submit-reg").disabled = true;
						}
					}
					// Allow: backspace, delete, tab, escape, enter and .
					if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
						// Allow: Ctrl/cmd+A
						(e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
						// Allow: Ctrl/cmd+C
						(e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
						// Allow: Ctrl/cmd+X
						(e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
						// Allow: home, end, left, right
						(e.keyCode >= 35 && e.keyCode <= 39)) {
						// let it happen, don't do anything

						return;
					}
					// Ensure that it is a number and stop the keypress
					if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
						e.preventDefault();
					}

				});
				$(document).ready(function() {
					$("#phone").keypress(function() {
						var phoneVal = $("#phone").val();
						if (phoneVal.length >= 9) {
							document.getElementById("submit-reg").disabled = false;
						} else {
							document.getElementById("submit-reg").disabled = true;
						}
					});

					var randStr = Math.random().toString(36).slice(-8);
					$("#passwordRegistration").val(randStr);
					$("form").keypress(function(e) {
						//Enter key
						if (e.which == 13) {
							return false;
						}
					});
				})

			</script>



		</div>


	</div>
</div>
</div>



@endsection
