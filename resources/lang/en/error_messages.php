<?php
return [
    //registration error messages
    'req_first_name' => 'This field is required.',
    'invalid_first_name' => 'Special characters  are not allowed',
    'first_name_max_length' => 'More than 50 characters are not allowed.',
    'first_name_allow_string' => 'Numeric value is not allowed',
    'req_middle_name' => 'Middle name is required.',
    'invalid_middle_name' => 'Please enter a valid middle name.',
    'middle_name_max_length' => 'More than 50 characters are not allowed.',
    'req_dob' =>'This field is required.',
    'invalid_age' => 'Sorry, you must be 18 years of age to apply.',
    'req_dob_name' => 'This field is required.',
    'req_otp' => 'OTP is required.',
    'invalid_otp' => 'Only numeric values are allowed.',
    'req_last_name' => 'This field is required.',
    'invalid_last_name' => 'Special characters or spaces is not allowed.',
    'last_name_max_length' => 'More than 50 characters are not allowed.',
    'req_email' => 'This field is required.',
    'valid_pan' => 'Please enter valid Pan number',
    'req_user_name' => 'Username is required',
    'req_password' => 'Password is required',
    'invalid_email' => 'Please enter a valid email address.',
    'email_max_length' => 'More than 50 characters are not allowed.',
    'email_already_exists' => 'The email is already registered.',
    'buis_business' => 'Please enter a valid business name',
    'req_pan' => 'Pan number is required',
    'pan_max_length' => 'More than 10 characters are not allowed.',
    'business_max_length' => 'More than 50 characters are not allowed.',
    'invalid_business' => 'Special characters or spaces are not allowed',
    'req_password' => 'The password field is required.',
    'req_confirm_password' => 'The confirm password field is required.',

    'req_dob' => 'This field is required.',
    'req_phone' => 'This field is required',
    'phone_minlength' => 'Please enter a valid phone number.',
    'phone_maxlength' => 'More than 10 digits are not allowed.',
    'positive_phone_no' => 'Please enter mobile number.',
    'invalid_phone' => 'Please enter a valid phone number.',
    'req_country' => 'Please select country.',
    'invalid_country' => 'Please select a valid country.',
    'zip_code_min_length' => 'Please enter 5 digits - no hyphens.',
    'zip_code_max_length' => 'More than 6 digits are not allowed.',
    'zip_code_numeric' => 'Please enter valid zip code.',
    'file_size_error' => 'File size should be 10 MB or below.',
    'user_id_not_found' => 'Session or user Id expire please refresh and try again.',
    'req_course_name' => 'Course name is required.',
    
    'req_right_title' =>'This field is required.',
    'req_right_type' =>'Please select type of rights.',
    'req_right_type_numeric' =>'Please select valid type of rights.',
    'req_right_number' =>'Rights number is required.',
    'req_right_inventor' =>'Please enter Inventor/Innovator.',
    'req_right_assignee' =>'Please enter Assignee/Owner.',    
    'req_right_cluster' =>'Please choose cluster.',
    'req_right_cluster_numeric' =>'Please choose valid cluster.',
    'req_right_date' =>'Please choose Date of rights.',
    'req_right_description' =>'Please enter description.',
    
    'app.incomplete' =>'Please upload all documents carefully.',
    'minlen_password' => 'Passwords must be at least 6 characters.',
    
    //Admin Error Messages
    'admin' => [
        'req_old_password' => 'Old password is required.',
        'req_new_password' => 'New password is required.',
        'req_confirm_password' => 'Confirm password is required.',
        'minlen_password' => 'Passwords must be at least 6 characters.',
        'same_confirm_password' => 'New password and confirm password should be the same.',
        'password_changed' => 'Password changed successfully.',
        'correct_old_password' => 'Please enter correct old password',
        'minlen_confirm_password' => 'Confirm passwords must be at least 6 characters.',
    ],
    'document' => 'Please save promoter details for further process.',
    'documentExRequire' => 'No Document required for this application.',
    //Common error messages
    'data_not_found' => 'No data found.',
    'noAppDoucment' => 'No documents found for your application.',
    'no_data_found' => 'No data found.',
    'send_array' => 'Please send array.',
    'invalid_data_type' => 'Invalid data type',
    'invalid_token' => 'Token is invalid.',
    'enter_corr_otp' => 'Please enter correct otp.',
    'email_already_verified' => 'Your email already verified please login to continue.',
    'generic.failure'=>'We have encountered a technical issue due to which operation is not completed. Please try after sometime.',
    'creadential_not_valid'=>'Invalid username or password.',
    'account_blocked'=>'Your account is blocked. Please contact support@inventrust.com',
    'login_verify_email' =>'Please verify your email.',
    'login_verify_otp' =>'Please verify otp.',
    'BlockChain_blocked'=>'Technical team is working on this issue.',
    
    /*************family information**********/
    'req_spouse_first_name'=>'Spouse first name is required.',
    'invalid_spouse_first_name' => 'Please enter valid Spouse first  Name.',
    'spouse_first_name_max_length' => 'More than 100 characters are not allowed.',
    
    'req_spouse_maiden_name' => 'Spouse maiden name is required.',
    'invalid_spouse_maiden_name' => 'Please enter valid Spouse maiden  Name.',
    'spouse_maiden_name_max_length' => 'More than 100 characters are not allowed.',
    
    'req_spouse_professional_status'=>'Spouse professional status is required.',
    'req_this_field'=>'This field is required',
    'contain_this_alpha_num'=>'This may only contain letters and numbers.',
    
     'addProduct'=> [
         'anchor_user_id'=>'Anchor Name is required',
         'anchor_limit_number'=>'Anchor limit must be a number.'
     ],
     'required'=>':field is required.',
    'first_name_regex' => 'Please enter only alpha characters with/without space.',
    'last_name_regex' => 'Please enter only alpha characters.',


    'apport_invalid_user_id' => "Invalid User",
    'apport_invalid_repayment_id' => 'Invalid Re-Payment',
    'apport_invalid_unapplied_amt' => 'Total requested amout to settle is greater than the repayment amt',
    'active_app_check' => 'You cannot create a new application until the filled application got sanctioned or rejected by the lender.'
    ];
