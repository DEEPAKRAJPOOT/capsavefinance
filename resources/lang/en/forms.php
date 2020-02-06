<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return [
    //Dashboard
    'DashboradIndividual' => [
       'heading'=>'Dashboard', 
    ],
    
    // Indivisual Registration Form lang file
    'Individual_Reg' => [

        'Label' => [
            'title' => 'Sign up (Individual)',
            'country_id' => 'Nationality',
            'f_name' => 'First Name',
            'm_name' => 'Middle Name',
            'l_name' => 'Last Name',
            'date_of_birth' => 'Date of Birth',
            'email' => 'Official Email Address',
            'phone_no' => 'Official Mobile Number',
            'already_acc' => 'Already have an account?',
        ],

        'placeholder' => [
            'select' => 'Select',
        ],

        'client_error' => [
            'req_first_name' => 'First name is required.',
            'invalid_first_name' => 'Please enter valid first name.',
            'first_name_max_length' => 'You are not allow to enter maximum 50 characters.',
            'req_middle_name' => 'Middle name is required.',
            'invalid_middle_name' => 'Please enter valid middle Name.',
            'middle_name_max_length' => 'please enter no more than 50 characters.',
            'req_dob' => 'This field is required.',
            'invalid_age' => 'Sorry, you must be 18 years of age to apply',
            'req_dob_name' => 'This field is required.',
            'req_last_name' => 'This field is required.',
            'invalid_last_name' => 'Please enter valid last name.',
            'last_name_max_length' => 'You are not allow to enter maximum 50 characters.',
            'req_email' => 'This field is required.',
            'invalid_email' => 'Please enter a valid email address.',
            'email_max_length' => 'Please enter no more than 50 characters.',
            'email_already_exists' => 'The email is already registered.',
            'req_phone' => 'This field is required',
            'phone_minlength' => 'Please enter valid phone number.',
            'phone_maxlength' => 'You are not allow to enter maximum 10 digits.',
            'positive_phone_no' => 'Please enter positive mobile no',
            'invalid_phone' => 'Please enter valid phone number.',
            'req_country' => 'Please select country.',
            'invalid_country' => 'Please select a valid country.',
        ],

        'server_error' => [
            'req_first_name' => 'This field is required.',
            'invalid_first_name' => 'Please enter valid first name.',
            'first_name_max_length' => 'You are not allow to enter maximum 50 characters.',
            'req_middle_name' => 'Middle name is required.',
            'invalid_middle_name' => 'Please enter valid middle Name.',
            'middle_name_max_length' => 'please enter no more than 50 characters.',
            'req_dob' => 'This field is required.',
            'invalid_age' => 'Sorry, you must be 18 years of age to apply',
            'req_dob_name' => 'This field is required.',
            'req_last_name' => 'This field is required.',
            'invalid_last_name' => 'Please enter valid last name.',
            'last_name_max_length' => 'You are not allow to enter maximum 50 characters.',
            'req_email' => 'This field is required.',
            'invalid_email' => 'Please enter a valid email address.',
            'email_max_length' => 'Please enter no more than 50 characters.',
            'email_already_exists' => 'The email is already registered.',
            'req_phone' => 'This field is required',
            'phone_minlength' => 'Please enter valid phone number.',
            'phone_maxlength' => 'You are not allow to enter maximum 10 digits.',
            'positive_phone_no' => 'Please enter positive mobile no',
            'invalid_phone' => 'Please enter valid phone number.',
            'req_country' => 'Please select country.',
            'invalid_country' => 'Please select a valid country.',
        ],
    ],

    // Corperate Registration Form lang file
    'Corperate_Reg' => [

        'Label' => [
            'title' => 'Sign up (Corporate)',
            'country_id' => 'Country of Registration',
            'corp_name' => 'Company Name',
            'corp_date_of_formation' => 'Company Date of Formation',
            'corp_license_number' => 'Company Trade License Number',
            'comp_owner' => 'Signatory Company Authorized',
            'f_name' => 'First Name',
            'm_name' => 'Middle Name',
            'l_name' => 'Last Name',
            'email' => 'Official Email Address',
            'phone_no' => 'Official Mobile Number',
            'already_acc' => 'Already have an account?',
            'date_of_birth' => 'Authorized Signatory Date of Birth'
        ],

        'placeholder' => [
            'select' => 'Select',
        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    // Indivisual/Corperate Login Form lang file
    'IndCorp_Login' => [
        'Label' => [
            'title' => 'Sign in (Individual or Corporate)',
            'username' => 'Username',
            'password' => 'Password',
            'forgot_pass' => 'Forgot Password?',
            'ques' => "Don't have an account?",
        ],

        'plc_holder' => [

        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    // Otp Form lang file
    'Otp_Form' => [
        'Label' => [
            'heading'=>'OTP',
            'is_verified'=>'Verify OTP',
            'is_otp_resent' => 'Resend OTP',
            'thanks_email_verify'=>'Thank you you for verifying your email ID!',
            'otp_no'=>'We have sent you an OTP on your registered mobile number. Please enter OTP Below',
        ],

        'plc_holder' => [
            'enter_otp'=>'Enter OTP',
        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    // Reset Password Form lang file
    'ChgPass_Form' => [
        'Label' => [
            'heading' => 'Change Password',
            'old_pass' => 'Old Password',
            'new_pass' => 'New Password',
            'confirm_pass' => 'Confirm New Password',
            'dashboard' => 'Dashboard'
        ],

        'plc_holder' => [
            'enter_conf_pass' => 'Confirm Your Password',
            'enter_new_pass' => 'Enter New Password',
            'enter_old_pass' => 'Enter Old Password',
        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    // Company Details Form lang file
    'Company_Details' => [
        'Label' => [
            'title' => 'DexterCapital',
            'heading' => 'Sign up (Individual)',
            'country_id' => 'Nationality',
            'f_name' => 'First Name',
            'm_name' => 'Middle Name',
            'l_name' => 'Last Name',
            'date_of_birth' => 'Date of Birth',
            'email' => 'Official Email Address',
            'phone_no' => 'Official Mobile No',
            'ques' => 'Already have an account?',
        ],

        'plc_holder' => [
            'select_nationality' => 'Select Nationality',
            'select_dob' => 'Select Date of Birth',
            'enter_email' => 'Enter Email',
            'enter_mob_no' => 'Enter Mobile No',
            'enter_lname' => 'Enter Last Name',
            'enter _mname' => 'Enter Middle Name',
            'enter_fname' => 'Enter First Name'
        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    // Education Details Form lang file
    'Edu_Details' => [
        'Label' => [
            'heading' => 'Education Details',
            'uni_name' => 'University Name',
            'course_name' => 'Course Name',
            'date_att' => 'Dates Attended',
            'add_info' => 'Additional Information',
            'add_edu' => '+Add Education',
            'remark' => 'Remark'
        ],

        'plc_holder' => [

        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    // Otp Feedback lang file
    'otpThanks' => [
        'Label' => [
            'message' => 'Mobile Number Verification Successful!',
        ],

        'plc_holder' => [

        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    //Register Form lang file
    'Reg_Form' => [
        'Label' => [
            'heading' => 'Basic Details',
            'fname' => 'First Name',
            'lname' => 'Last Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'country' => 'Country',
        ],

        'plc_holder' => [

        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

     //Research Form lang file
    'research_Form' => [
        'Label' => [
            'heading' => 'Research Publication',
            'title' => 'Title',
            'jor_mag' => 'Journal/Magazine',
            'my_pub' => 'Month/Year of Publication',
            'att_file' => 'Attach File ( one attachment at a time )',
            'add_research' => 'Add Research Publication',
        ],

        'plc_holder' => [

        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    //Skill Form lang file
    'skills_Form' => [
        'Label' => [
            'heading' => 'Skills',
            'add_skills' => 'Add Skills',
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'professional' => 'Professional',
        ],

        'plc_holder' => [

        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    //Thanks Blade lang file
    'thanks_Page' => [
        'Label' => [
            'message1' => 'Thank you for signing up with us!',
            'message2' => 'We have sent you an verification link on your email ID. Please verify your email ID to complete your registration.'
        ],

        'plc_holder' => [

        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    //Awards Form lang file
    'awards_Form' => [
        'Label' => [
            'heading' => 'Awards & Honors',
            'title' => 'Title',
            'brief_desc' => 'Brief Description',
            'add_awards' => 'Add Awards & Honors',
        ],

        'plc_holder' => [

        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    //Email Form lang file
    'email_Form' => [
        'Label' => [
            'heading' => 'Reset Password',
            'email' => 'E-Mail Address',
            'sent_pass_link' => 'Send Password Reset Link'
        ],

        'plc_holder' => [

        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    //Reset Password Form lang file
    'reset_Form' => [
        'Label' => [
            'heading' => 'Change password',
            'email' => 'E-Mail Address',
            'new_pass' => 'New Password',
            'conf_pass' => 'Confirm new Password',
            'reset_pass' => 'Reset Password',
        ],

        'plc_holder' => [

        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    //User Profile Form lang file
    'personal_Profile' => [
        'Label' => [
            'kyc' => 'KYC',
            'text' => 'Individual Natural Person (director, shareholder, Ultimate Beneficial Owner)',
            'item1'=> 'Personal Information',
            'item2' => 'Professional Information',
            'item3' => 'Financial Information',
            'item4' => 'Documents',
            'f_name' => 'First Name',
            'm_name' => 'Middle Name',
            'l_name'=>'Last Name',
            'gender' => 'Gender',
            'date_of_birth' => 'Date of Birth',
            'birth_country_id' => 'Nationality',
            'birth_state_id' => 'State of Birth',
            'birth_city_id' => 'City of Birth',
            'father_name' => "Father's Name",
            'mother_f_name' => "Mother's Name",
            'mother_m_name' => "Mother's Maiden Name",
            'reg_no' => 'Registration No',
            'reg_place' => 'Registration Place',
            'f_nationality_id' => 'Nationality',
            'sec_nationality_id' => 'Second Nationality',
            'document_type' => 'Document Type',
            'document_number' => 'Document No',
            'issuance_date' => 'Issuarance Date',
            'expire_date' => 'Expiry Date',
            'social_media' => 'Social Media',
            'social_media_link' => 'Social Media Link',
            'residence_status' => 'Residence Status',
            'family_status' => 'Family Status',
            'guardian_name' =>  'Legal Guardians Name',
            'legal_maturity_date' => 'Legal Muturity Date',
            'educational_level' => 'Educational Level',
            'is_residency_card' => 'Do you have any residency card',
            'current_position' => 'Do you hold or have ever held a',
            'political_position' => 'Are you related directly or indirectly to a person currently holding or who has previously held a',
            'political_position_dec' => 'If yes, please specify position(s)',
            'message1' => 'Dear Applicant;',
            'message2' => 'Welcome to the Compliance platform of Dexter Capital Financial Consultancy LLC.',
            'message3' => 'According to the United Arab Emirates rules and regulations and the International applicable laws, you are kindly requested to proceed with the due diligence application allowing you to validate your profile and access many financial platforms.',
            'message4' => 'Dexter Capital Financial Consultancy LLC being regulated by Securities and Commodities Authority in the UAE, is committed to maintain all your information confidential and highly protected by the most sophisticated security tools and is in full compliance with the requirements of the European Union related to the General Data Protection Regulation (GDPR).',
            'message5' => 'https://ec.europa.eu/info/law/law-topic/data-protection/data-protection-eu_en',
            'add' => '+Add',
        ],

        'plc_holder' => [
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

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    //Family Information lang file
    'family_Info' => [
        'Label' => [
            'heading' => 'Family Information',
            'spouse_f_name' => 'Spouse First Name',
            'spouse_m_name' => 'Spouse Maiden Name',
            'is_spouse_profession' => 'Spouse Professional Status',
            'spouse_profession' => "Spouse's Profession (if only)",
            'spouse_employer' => "Spouse's Employer (if any)",
            'children_info' => 'Children Information',
            'is_child' => 'No Children',
            'spouce_child_info' => 'Child 1',
            'child_name' => 'Child Name',
            'child_dob' => 'Date of Birth'
        ],

        'plc_holder' => [
            'child_dob' => 'Select Date of Birth',
            'child_name' => 'Enter Child Name',
            'spouse_employer' => 'Enter Employer Name',
            'spouse_profession' => 'Enter Profession Name',
            'spouse_m_name' => 'Enter Middle Name',
            'spouse_f_name' => 'Enter First Name'
        ],

        'client_error' => [

        ],                                                                                                                                                                              

        'server_error' => [

        ],
    ],

    //Professional Information lang file
    'profession_Info' => [
        'Label' => [
            'heading' => 'Professional Information',
            'prof_status' => 'Professional Status',
            'other_prof_status' => 'Other Profession Status',
            'prof_detail' => 'Profession/ Occupation in detail Previous Profession/ Occupation if retired',
            'position_title' => 'Position/ Job title Last Position/ Job title if retired',
            'date_employment' => 'Date of Employment/ Retirement',
            'last_monthly_salary' => 'Last Month Salary (if retired)',
        ],

        'plc_holder' => [
            'date_employment' => 'Select Date of Birth',
            'last_monthly_salary' => 'Enter here',
        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    //Financial Information lang file
    'financial_Info' => [
        'Label' => [
            'heading' => 'Financial Information',
            'source_funds' => 'Source of Funds',
            'other_source_fund' => 'Other Source Of Funds',
            'jurisdiction_funds' => 'Juridiction of Funds',
            'annual_income' => 'Annual Income (in USD)',
            'estimated_wealth' => 'Estimated Wealth (in USD)',
            'wealth_source'  => 'Kindly provide details on the source(s) of your wealth',
            'other_wealth_source' => 'Other source(s) of your wealth',
            'message' => 'Please fill the following details (If Applicable)',
            'tin_code' => 'US TIN Code',
            'is_abandoned' => 'Was US citizenship abandoned after June 2014?',
            'date_of_abandonment' => 'Please specify date of abandonment',
            'abandonment_reason' => 'Reason',
            'justification' => 'Justification (If reason B is selected)',
            'tin_country_name' => 'Tin Country',
            'tin_number' => 'TIN (Taxpayer Identification Number) or functional equivalent of the TIN',
        ],

        'plc_holder' => [
            'other_source_fund' => 'Enter other Source Of Funds',
            'other_wealth_source' => 'Enter other source(s) of your wealth',
            'tin_code' => 'Enter US TIN Code',
            'date_of_abandonment' => 'Enter date of abandonment',
            'abandonment_reason' => 'Enter Reason',
            'justification' => 'Enter Reason',
            'tin_number' => 'Enter TIN No.',
        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    //Commercial Information lang file
    'commercial_Info' => [
        'Label' => [
            'heading1' => 'For Sole Proprietorship/ Self Employed, Please Specify',
            'comm_name' => 'Commercial Name',
            'date_of_establish' => 'Date of Establishment',
            'country_establish_id' => 'Country of Establishment',
            'comm_reg_no' => 'Commercial Regitration No.',
            'comm_reg_place' => 'Place',
            'comm_country_id' => 'Country',
            'country_activity' => 'Country(ies) of Activity',
            'syndicate_no' => 'Syndicate No.',
            'taxation_no' => 'Taxation ID No.',
            'taxation_id' => 'Taxation ID',
            'annual_turnover' => 'Annual Business Turnover (in $)',
            'main_suppliers' => 'Main Suppliers',
            'main_clients' => 'Main Clients',
            'authorized_signatory' => 'Name of Authorized Signatory',

            'heading2' => 'Business Address',
            'buss_country_id' => 'Country',
            'buss_city_id' => 'City',
            'buss_region' => 'Region',
            'buss_building' => 'Building',
            'buss_floor' => 'Floor',
            'buss_street' => 'Street',
            'buss_postal_code' => 'Postal Code',
            'buss_po_box_no' => 'P.O Box',
            'buss_email' => 'Email',
            'buss_telephone_no' => 'Telephone No.',
            'buss_mobile_no' => 'Mobile No.',
            'buss_fax_no' => 'Fax No.',

            'heading3' => 'Mailing Address',
            'is_hold_mail' => 'Host Mail',
            'mailing_address' => 'In case of sending documents through mail, please specify mailing address',
            'relation_exchange_company' => 'Relation with Exchange Company/ Establishment',
            'concerned_party' => 'Name of Concerned Party',
            'details_of_company' => 'Name/Details of Establishment/Company',
        ],

        'plc_holder' => [
            'comm_name' => 'Enter Commercial name',
            'date_of_establish' => 'Select Date',
            'country_establish_id' => 'Enter Commercial Register No',
            'syndicate_no' => 'Enter Syndicate No.',
            'taxation_no' => 'Enter Taxation ID No.',
            'taxation_id' => 'Enter Taxation ID',
            'annual_turnover' => 'Enter Annual Business Turnover (in $)',
            'main_suppliers' => 'Enter Main Suppliers',
            'main_clients' => 'Enter Main Clients',
            'authorized_signatory' => 'Enter Name of authorized signatory',
            'buss_city_id' => 'Enter City',
            'buss_region' => 'Enter Region',
            'buss_building' => 'Enter building',
            'buss_floor' => 'Enter floor',
            'buss_street' => 'Enter Street',
            'buss_postal_code' => 'Enter postal code',
            'buss_po_box_no' => 'Enter P.O. Box no.',
            'buss_email' => 'Enter email',
            'buss_telephone_no' => 'Enter Telephone No.',
            'buss_mobile_no' => 'Enter mobile no.',
            'buss_fax_no' => 'Enter Fax No.',
            'concerned_party' => 'Enter Name of Concerned Party',
            'details_of_company' => 'Enter Name/Details of Establishment/Company',
        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    //Residensial Information lang file
    'residensial_Info' => [
        'Label' => [
            'heading' => 'Residential Information',
            'country_id' => 'Country',
            'city_id' => 'City',
            'region' => 'Region',
            'building_no' => 'Building',
            'floor_no' => 'Floor No',
            'street_addr' => 'Street',
            'postal_code' => 'Postal Code',
            'post_box' => 'P.O Box',
            'addr_email' => 'Email',
            'addr_phone_no' => 'Telephone No.',
            'addr_mobile_no' => 'Mobile No.',
            'addr_fax_no' => 'Fax No.',
        ],

        'plc_holder' => [
            'city_id' => 'Enter City',
            'region' => 'Enter Region',
            'building_no' => 'Enter Building',
            'floor_no' => 'Enter floor',
            'street_addr' => 'Enter Street',
            'postal_code' => 'Enter postal code',
            'post_box' => 'Enter P.O. Box no.',
            'addr_email' => 'Enter email',
            'addr_phone_no' => 'Enter Telephone No.',
            'addr_mobile_no' => 'Enter mobile no.',
            'addr_fax_no' => 'Enter fax no.',
        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    //Upload Documents lang file
    'upload_Docs' => [
        'Label' => [
            'heading' => 'Documents & Declaration',
            'message1' => 'Documents to be provided along with this form',
            'browse' => 'Browse',
            'declararion' => 'Declaration',
            'download' => 'Download',
            'message2' => 'We hereby declare that the particulars given herein are true, correct',
            'message3' => 'complete to the best of our knowledge',
            'message4' => 'belief. We undertake to promptly inform Dexter Capital Financial Consultancy LLC of any changes or information provided hereinabove.',
            'message5' => 'I accept the Terms and Conditions',
        ],

        'plc_holder' => [

        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],

    '#' => [
        'Label' => [

        ],

        'plc_holder' => [

        ],

        'client_error' => [

        ],

        'server_error' => [

        ],
    ],


];

