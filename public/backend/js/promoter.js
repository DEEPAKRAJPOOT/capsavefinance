/* check pan card verify before upload pan*/


function uploadFile(uploadId)
{
    var biz_id  = $('#biz_id').val();
    var biz_id  = $('#biz_id').val();
    var file  = $('#panfile'+uploadId).prop('files');
    var data = ({ 
        'file' : file,
        'app_id' : app_id,
        'biz_id' : biz_id
    });
    console.log(data);
    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url : '/application/promoter-document-save',
        type: "POST",
        data: data,
        processData: false,
        contentType: false,
        success: function(r){
           $(".isloader").hide();
            obj = result.result.directors;
            var count = 0;
            $(obj).each(function (k, v) {
                var dob = v.dob;
                var dateAr = dob.split('-');
                var newDate = '';
                if (dateAr != '')
                {
                    var newDate = dateAr[0] + '/' + dateAr[1] + '/' + dateAr[2];
                }
                count++;
                $("#first_name" + count).val(v.name);
                $("#address" + count).val(v.address);
                $("#date_of_birth1").prop("type", "text").val(newDate);
                if (k > 0)
                {
                    $(".form-fields-appand").append("<div class='fornm-sections'><div class='row'><div class='col-md-12'><button class='close clsdiv' type='button'>x</button><div class='col-md-12'><h3>Promoter</h3></div><div class='col-md-12'><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name'>Promoter Name<span class='mandatory'>*</span></label><input type='text' name='first_name[]' vname='first_name" + x + "' id='first_name" + x + "' value='' class='form-control first_name' placeholder='Enter First Name' ></div></div><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name' >Last Name</label><input type='text' name='last_name[]' id='last_name" + x + "' value='' class='form-control last_name' placeholder='Enter Last Name' ></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>DOB<span class='mandatory'>*</span></label><input type='date' name='date_of_birth[]'  id='date_of_birth" + x + "' value='' class='form-control date_of_birth' tabindex='1' placeholder='Enter Date Of Birth' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group password-input'><label for='gender'>Gender<span class='mandatory'>*</span></label><select class='form-control gender' name='gender[]'   id='gender" + x + "'><option value=''> Select Gender</option><option value='1'> Male </option><option value='2'>Female </option></select></div></div><div class='col-md-4'><div class='form-group'><label for='pan_no'>PAN Number<span class='mandatory'>*</span></label><a href='javascript:void(0);' data-id='" + x + "' id='pan_verify" + x + "' class='verify-owner-no promoter_pan_verify'>Verify</a><input type='text' name='pan_no[]'  id='pan_no" + x + "' value='' class='form-control pan_no' placeholder='Enter Pan No' ><input name='response[] id='response" + x + "' type='hidden' value=''></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>Shareholding (%)<span class='mandatory'>*</span></label><input type='text' name='share_per[]' id='share_per" + x + "' id='employee' value='' class='form-control share_per' tabindex='1' placeholder='Enter Shareholder' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Educational Qualification</label><input type='text' name='edu_qualification[]'  id='edu_qualification" + x + "' value='' class='form-control edu_qualification' tabindex='1' placeholder='Enter Education Qualification.'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Other Ownerships</label><input type='text' name='other_ownership[]' id='other_ownership" + x + "' value='' class='form-control other_ownership' tabindex='1' placeholder='Enter Other Ownership'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Networth </label><a href='javascript:void(0);' class='verify-owner-no'>INR</a><input type='text' name='networth[]' id='networth" + x + "' value='' class='form-control networth' tabindex='1' placeholder='Enter Networth'></div></div> </div></div><div class='col-md-8'><div class='form-group password-input'><label for='txtPassword'>Address<span class='mandatory'>*</span></label><textarea class='form-control textarea address' placeholder='Enter Address' name='owner_addr[]' id='address" + x + "'></textarea></div></div> <h5 class='card-title form-head-h5 mt-3'>Document </h5><div class='row mt-2 mb-4'><div class='col-md-12'> <div class='prtm-full-block'><div class='prtm-block-content'><div class='table-responsive ps ps--theme_default' data-ps-id='9615ce02-be28-0492-7403-d251d7f6339e'><table class='table text-center table-striped table-hover'><thead class='thead-primary'><tr><th class='text-left'>S.No</th><th>Document Name</th><th>Document ID No.</th><th>Action</th></tr></thead><tbody><tr><td class='text-left'>1</td><td width='30%'>Pan Card</td><td width='30%' ></td><td width='28%'><div class='file-browse float-left position-seta'><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button><input type='file' name='verifyfile[]' class='verifyfile' id='verifyfile" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' required='required' name='panfile[]' data-id='" + x + "' class='panfile' id='panfile" + x + "'> </div> </td> </tr> <tr> <td class='text-left'>2</td> <td width='30%'>Driving License</td> <td width='30%' > <div class='col-md-12'> <a href='javascript:void(0);' id='ddriving" + x + "' class='verify-owner-no verify-show' style='top:0px;'>Verify</a> <input type='text' name='verifydl[]' id='verifydl" + x + "' value='' class='form-control verifydl' tabindex='1' placeholder='Enter DL Number' required=''> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' id='downloaddl" + x + "' name='downloaddl[]' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple='' class='downloaddl'> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' required='required'  name='dlfile[]' data-id='" + x + "' class='dlfile' id='dlfile" + x + "'> </div> </td> </tr> <tr> <td class='text-left'>3</td> <td width='30%'>Voter ID</td> <td width='30%' > <div class='col-md-12'> <a href='javascript:void(0);' id='vvoter" + x + "'  class='verify-owner-no verify-show' style='top:0px;'>Verify</a> <input type='text' name='verifyvoter[]' id='verifyvoter" + x + "' value='' class='form-control verifyvoter' tabindex='1' placeholder='Enter Voter's Epic Number' required=''> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadvoter[]' class='downloadvoter' id='downloadvoter" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "' required='required'  class='voterfile' name='voterfile[]' id='voterfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>4</td> <td width='30%'>Passport</td> <td width='30%' > <div class='col-md-12'> <a href='javascript:void(0);' id='ppassport" + x + "' class='verify-owner-no verify-show' style='top:0px;'>Verify</a> <input type='text' name='verifypassport[]' id='verifypassport" + x + "' value='' class='form-control verifypassport' tabindex='1' placeholder='Enter File Number' required=''> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadpassport[]' class='downloadpassport'  id='downloadpassport" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "' required='required'  name='passportfile[]' class='passportfile' id='passportfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>5</td> <td width='30%'>Photo</td> <td width='30%' > </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadphoto[]' class='downloadphoto' id='downloadphoto" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "' required='required'  name='photofile[]' name='photofile' id='photofile" + x + "'> </div> </td> </tr> </tbody> </table> <div class='ps__scrollbar-x-rail' style='left: 0px; bottom: 0px;'><div class='ps__scrollbar-x' tabindex='0' style='left: 0px; width: 0px;'></div></div><div class='ps__scrollbar-y-rail' style='top: 0px; right: 0px;'><div class='ps__scrollbar-y' tabindex='0' style='top: 0px; height: 0px;'></div></div> </div> </div> </div> </div> </div> </div></div></div> ");
                    x++;
                }

            });
        }
    });
}



