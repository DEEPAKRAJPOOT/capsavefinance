<?php

return [

    /*
    |--------------------------------------------------------------------------
    | All master Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */
    //Heading
    'country_name' => 'Country Name',
    'country_code' => 'Country Code',
    'title' => 'Title',
    'status' => 'Status',
    'action' => 'Action',
    'id' => 'Id',
    'state_name' => 'State Name',
    'manage_country' =>'Manage Country',
    'manage_cluster' =>'Manage Cluster',
    'manage_right_type' =>'Manage Rights Type',
    'manage_source' =>'Manage Source',
    'manage_right'=>'manage rights',
    'country'=>[
        'country_name' => 'Country name field is required.',
        'country_code' => 'Country code field is required.',
        'is_active' => 'Status field is required.',
    ],
    
    //State master lang file
    'state_save_msg'=>'State save successfully!',
    'state_update_msg'=>'State updated successfully!',
    'cluster_save_msg'=>'Cluster save successfully!',
    'cluster_update_msg'=>'Cluster updated successfully!',
    'rt_save_msg'=>'Rights type save successfully!',
    'rt_update_msg'=>'Rights type updated successfully!',
    'source_save_msg'=>'Source save successfully!',
    'source_update_msg'=>'Source updated successfully!',
    'manage_state' =>'Manage State',
    'state'=>[
        'state_name' => 'State name field is required.',
    ],
    'cluster'=>[
        'title' => 'Title field is required.',
    ],

    
   //User sign up form lang file
        'register_as'=>'Register As',
        'individual'=>'Individual',
        'corporate'=>'Corporate',
        'country_of_registration'=>'Country of Registration',
        'select_nationality'=>'Select Nationality',
        'company_name'=>'Company Name',
        'signatory_company_authorized'=>'Signatory Company Authorized',
        'first_name'=>'First Name',
        'middle_name'=>'Middle Name',
        'last_name'=>'Last Name',
        'company_date_of_formation'=>'Company Date of Formation',
        'company_trade_license_number'=>'Company Trade License Number',
        'authorized_signatory_date_of_birth'=>'Authorized Signatory Date of Birth',
        'official_email_address'=>'Official Email Address',
        'official_mobile_no'=>'Official Mobile Number',
        'already_have_an_account'=>'Already have an account?',
        'sign_in'=>'Sign in',
        'sign_up'=>'Sign up',
        'sign_up_indivisual' => 'Sign up (Individual)',
        'sign_up_corporate'=>'Sign up (Corporate)',
        'nationality'=>'Nationality',
        'date_of_birth'=>'Date of Birth',
        'otp_heading'=>'OTP',
        'enter_oTP'=>'Enter OTP',
        'verify_oTP'=>'Verify OTP',
        'thanks_email_verify'=>'Thank you you for verifying your email ID!',
        'enter_otp_below'=>'We have sent you an OTP on your registered mobile number. Please enter OTP Below',



    //User Profile form lang file
        'personalProfile'=>[
            'kyc' => 'KYC',
            'text' => 'Individual Natural Person (director, shareholder, Ultimate Beneficial Owner)',
            'item1'=> 'Personal Information',
            'item2' => 'Professional Information',
            'item3' => 'Financial Information',
            'item4' => 'Documents',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name'=>'Last Name',
            'gender' => 'Gender',
            'date_of_birth' => 'Date of Birth',
            'nationality' => 'Nationality',
            'state_of_birth' => 'State of Birth',
            'city_of_birth' => 'City of Birth',
            'father_name' => "Father's Name",
            'mother_name' => "Mother's Name",
            'mother_maiden_name' => "Mother's Maiden Name",
            'registration_num' => 'Registration No',
            'registration_place' => 'Registration Place',
            'second_nationality' => 'Second Nationality',
            'document_type' => 'Document Type',
            'document_no' => 'Document No',
            'issuarance_date' => 'Issuarance Date',
            'expiry_date' => 'Expiry Date',
            'social_media' => 'Social Media',
            'social_media_link' => 'Social Media Link',
            'residence_status' => 'Residence Status',
            'family_status' => 'Family Status',
            'legal_guardians_name' =>  'Legal Guardians Name',
            'legal_maturity_date' => 'Legal Muturity Date',
            'educational_level' => 'Educational Level',
            'is_residency_card' => 'Do you have any residency card',
            'current_position' => 'Do you hold or have ever held a',
            'related_current_position' => 'Are you related directly or indirectly to a person currently holding or who has previously held a',
            'desc_position' => 'If yes, please specify position(s)',
            'save' => 'Save',
            'save_next' => 'Save & Next',
            'message1' => 'Dear Applicant;',
            'message2' => 'Welcome to the Compliance platform of Dexter Capital Financial Consultancy LLC.',
            'message3' => 'According to the United Arab Emirates rules and regulations and the International applicable laws, you are kindly requested to proceed with the due diligence application allowing you to validate your profile and access many financial platforms.',
            'message4' => 'Dexter Capital Financial Consultancy LLC being regulated by Securities and Commodities Authority in the UAE, is committed to maintain all your information confidential and highly protected by the most sophisticated security tools and is in full compliance with the requirements of the European Union related to the General Data Protection Regulation (GDPR).',
            'message5' => 'https://ec.europa.eu/info/law/law-topic/data-protection/data-protection-eu_en',
            'add' => '+Add',


            'enter_fname' => 'Enter First Name',
            'enter_mname' => 'Enter Middle Name',
            'enter_lname' => 'Enter Last Name',
            'enter_father_name' => "Enter Father's Name",
            'enter_mother_name' => "Enter Mother's Name",
            'enter_mother_mname' => "Enter Mother's Maiden Name",
            'enter_reg_no' => 'Enter Registration No',
            'enter_reg_place' => 'Enter Registration Place',
            'enter_social_mlink' => 'Enter Social Media Link',
            'enter_gardians_name' => "Enter Gardians Name",

            'select_gender' => 'Select Gender',
            'select_nationality' => 'Select Nationality',
            'select_state_birth' => 'Select State of Birth',
            'select_city_birth' => 'Select City of Birth',
            'select_second_nationality' => 'Select Second Nationality',
            'select_document_type' => 'Select Document Type',
            'select_social_media' => 'Select Social Media',
            'select_residence_status' => 'Select Residence Status',
            'select_family_status' => 'Select Family Status',
            'select_educational_level' => 'Select Eductional Level',
            'select_card_availability' => 'Select Residence Card',

        ],



    //Login form lang file
        'loginForm'=>[
            'heading' => 'Sign in (Individual)',
            'username' => 'Username',
            'enter_uname' => 'Enter Username',
            'password' => 'Password',
            'enter_pass' => 'Enter Password',
            'forgot_pass' => 'Forgot Password?',
            'ques' => "Don't have an account?",
            'sign_up' => 'Sign Up',
            'sign_in' => 'Sign in'
        ],

    //OTP form lang file
        'otpForm'=>[
            'otp_heading'=>'OTP',
            'enter_otp'=>'Enter OTP',
            'verify_otp'=>'Verify OTP',
            'thanks_email_verify'=>'Thank you you for verifying your email ID!',
            'enter_otp_below'=>'We have sent you an OTP on your registered mobile number. Please enter OTP Below',
        ],

    //Change password form lang file
        'chgPassForm'=>[
            'heading' => 'Change Password',
            'old_pass' => 'Old Password',
            'new_pass' => 'New Password',
            'conf_new_pass' => 'Confirm New Password',
            'enter_conf_pass' => 'Confirm New Password',
            'enter_new_pass' => 'Enter New Password',
            'enter_old_pass' => 'Enter Old Password',
            'submit' => 'SUBMIT'
        ],

    //Company details form lang file
        'companyDetails' => [
            'title' => 'DexterCapital',
            'heading' => 'Sign up (Individual)',
            'nationality' => 'Nationality',
            'fname' => 'First Name',
            'mname' => 'Middle Name',
            'lname' => 'Last Name',
            'dob' => 'Date of Birth',
            'official_email_add' => 'Official Email Address',
            'official_mob_no' => 'Official Mobile No',
            'ques' => 'Already have an account?',
            'sign_in' => 'Sign in',
            'sign_up' => 'Sign up',

            'select_nationality' => 'Select Nationality',
            'select_dob' => 'Select Date of Birth',
            'enter_email' => 'Enter Email',
            'enter_mob_no' => 'Enter Mobile No',
            'enter_lname' => 'Enter Last Name',
            'enter _mname' => 'Enter Middle Name',
            'enter_fname' => 'Enter First Name'
        ],

    //Education Detail Form lang file
        'eduDetails' => [
            'heading' => 'Education Details',
            'uni_name' => 'University Name',
            'course_name' => 'Course Name',
            'date_att' => 'Dates Attended',
            'add_info' => 'Additional Information',
            'add_edu' => '+Add Education',
            'pre' => 'Previous',
            'next' => 'Next',
            'sym' => '*',
            'remark' => 'Remark'
        ],

    //Otp Thanks lang file
        'otpThanks' => [
            'message' => 'Mobile Number Verification Successful!',
            'login' => 'Login'
        ],

    //register form lang file
        'regForm' => [
            'heading' => 'Basic Details',
            'fname' => 'First Name',
            'lname' => 'Last Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'country' => 'Country',
            'sign_up' => 'Sign up',
            'sym' => '*'
        ],

    //Research form lang file
        'researchForm' => [
            'heading' => 'Research Publication',
            'title' => 'Title',
            'jor_mag' => 'Journal/Magazine',
            'my_pub' => 'Month/Year of Publication',
            'att_file' => 'Attach File ( one attachment at a time )',
            'sym' => '*',
            'sym1' => '+',
            'add_research' => 'Add Research Publication',
            'pre' => 'Previous',
            'next' => 'Next',
        ],

    //Skill form lang file
        'skillsForm' => [
            'heading' => 'Skills',
            'add_skills' => 'Add Skills',
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'professional' => 'Professional',
            'sym' => '*'
        ],

    //thanks lang file
        'thanksPage' => [
            'message1' => 'Thank you for signing up with us!',
            'message2' => 'We have sent you an verification link on your email ID. Please verify your email ID to complete your registration.'
        ],
      
    //awards form lang file
        'awardsForm' => [
            'heading' => 'Awards & Honors',
            'title' => 'Title',
            'brief_desc' => 'Brief Description',
            'add_awards' => 'Add Awards & Honors',
            'sym1' => '*',
            'sym2' => '+',
            'pre' => 'Previous',
            'finish' => 'Finish'
        ],

    //email form lang file
        'emailForm' => [
            'heading' => 'Reset Password',
            'email' => 'E-Mail Address',
            'sent_pass_link' => 'Send Password Reset Link'
        ],

    //Reset password form lang file
        'resetForm' => [
            'heading' => 'Change password',
            'email' => 'E-Mail Address',
            'new_pass' => 'New Password',
            'conf_pass' => 'Confirm new Password',
            'reset_pass' => 'Reset Password',
        ],    

];
