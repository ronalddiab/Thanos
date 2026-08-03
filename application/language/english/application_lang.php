<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Copyright (c) 2011 Lonnie Ezell

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

//--------------------------------------------------------------------
// ! GENERAL SETTINGS
//--------------------------------------------------------------------

$lang['ci_site_name'] = 'Ci Demo';
$lang['ci_site_email'] = 'Site Email';
$lang['ci_site_email_help'] = 'The default email that system-generated emails are sent from.';
$lang['ci_site_status'] = 'Site Status';
$lang['ci_online'] = 'Online';
$lang['ci_offline'] = 'Offline';
$lang['ci_top_number'] = 'Items <em>per</em> page:';
$lang['ci_top_number_help'] = 'When viewing reports, how many items should be listed at a time?';
$lang['ci_home'] = 'Home';
$lang['ci_site_information'] = 'Site Information';
$lang['ci_timezone'] = 'Timezone';
$lang['ci_language'] = 'Language';
$lang['ci_language_help'] = 'Choose the languages available to the user.';

//--------------------------------------------------------------------
// ! AUTH SETTINGS
//--------------------------------------------------------------------

$lang['ci_security'] = 'Security';
$lang['ci_login_type'] = 'Login Type';
$lang['ci_login_type_email'] = 'Email Only';
$lang['ci_login_type_username'] = 'Username Only';
$lang['ci_allow_register'] = 'Allow User Registrations?';
$lang['ci_login_type_both'] = 'Email or Username';
$lang['ci_use_usernames'] = 'User display across bonfire:';
$lang['ci_use_own_name'] = 'Use Own Name';
$lang['ci_allow_remember'] = 'Allow \'Remember Me\'?';
$lang['ci_remember_time'] = 'Remember Users For';
$lang['ci_week'] = 'Week';
$lang['ci_weeks'] = 'Weeks';
$lang['ci_days'] = 'Days';
$lang['ci_username'] = 'Username';
$lang['ci_password'] = 'Password';
$lang['ci_password_confirm'] = 'Password (again)';
$lang['ci_display_name'] = 'Display Name';

//--------------------------------------------------------------------
// ! CRUD SETTINGS
//--------------------------------------------------------------------

$lang['ci_home_page'] = 'Home Page';
$lang['ci_pages'] = 'Pages';
$lang['ci_enable_rte'] = 'Enable RTE for pages?';
$lang['ci_rte_type'] = 'RTE Type';
$lang['ci_searchable_default'] = 'Searchable by default?';
$lang['ci_cacheable_default'] = 'Cacheable by default?';
$lang['ci_track_hits'] = 'Track Page Hits?';

$lang['ci_action_save'] = 'Save';
$lang['ci_action_delete'] = 'Delete';
$lang['ci_action_cancel'] = 'Cancel';
$lang['ci_action_download'] = 'Download';
$lang['ci_action_preview'] = 'Preview';
$lang['ci_action_view'] = 'View';
$lang['ci_action_search'] = 'Search';
$lang['ci_action_purge'] = 'Purge';
$lang['ci_action_restore'] = 'Restore';
$lang['ci_action_show'] = 'Show';
$lang['ci_action_login'] = 'Sign In';
$lang['ci_action_logout'] = 'Sign Out';
$lang['ci_actions'] = 'Actions';
$lang['ci_clear'] = 'Clear';
$lang['ci_action_list'] = 'List';
$lang['ci_action_create'] = 'Create';
$lang['ci_action_ban'] = 'Ban';
$lang['ci_action_reset'] = 'Reset';
$lang['ci_action_import'] = 'Import';

//--------------------------------------------------------------------
// ! SETTINGS LIB
//--------------------------------------------------------------------

$lang['ci_do_check'] = 'Check for updates?';
$lang['ci_do_check_edge'] = 'Must be enabled to see bleeding edge updates as well.';

$lang['ci_update_show_edge'] = 'View bleeding edge updates?';
$lang['ci_update_info_edge'] = 'Leave unchecked to only check for new tagged updates. Check to see any new commits to the official repository.';

$lang['ci_ext_profile_show'] = 'Does User accounts have extended profile?';
$lang['ci_ext_profile_info'] = 'Check "Extended Profiles" to have extra profile meta-data available option(wip), omiting some default bonfire fields (eg: address).';

$lang['ci_yes'] = 'Yes';
$lang['ci_no'] = 'No';
$lang['ci_none'] = 'None';
$lang['ci_id'] = 'ID';

$lang['ci_or'] = 'or';
$lang['ci_size'] = 'Size';
$lang['ci_files'] = 'Files';
$lang['ci_file'] = 'File';

$lang['ci_with_selected'] = 'With selected';

$lang['ci_env_dev'] = 'Development';
$lang['ci_env_test'] = 'Testing';
$lang['ci_env_prod'] = 'Production';

$lang['ci_show_profiler'] = 'Show Admin Profiler?';
$lang['ci_show_front_profiler'] = 'Show Front End Profiler?';

$lang['ci_cache_not_writable'] = 'The application cache folder is not writable';

$lang['ci_password_strength'] = 'Password Strength Settings';
$lang['ci_password_length_help'] = 'Minimum password length e.g. 8';
$lang['ci_password_force_numbers'] = 'Should password force numbers?';
$lang['ci_password_force_symbols'] = 'Should password force symbols?';
$lang['ci_password_force_mixed_case'] = 'Should password force mixed case?';
$lang['ci_password_show_labels'] = 'Display password validation labels';

//--------------------------------------------------------------------
// ! USER/PROFILE
//--------------------------------------------------------------------

$lang['ci_user'] = 'User';
$lang['ci_users'] = 'Users';
$lang['ci_description'] = 'Description';
$lang['ci_email'] = 'Email';
$lang['ci_user_settings'] = 'My Profile';

//--------------------------------------------------------------------
// !
//--------------------------------------------------------------------

$lang['ci_both'] = 'both';
$lang['ci_go_back'] = 'Go Back';
$lang['ci_new'] = 'New';
$lang['ci_required_note'] = 'Required fields are in <b>bold</b>.';
$lang['ci_form_label_required'] = '<span class="required">*</span>';

//--------------------------------------------------------------------
// MY_Model
//--------------------------------------------------------------------
$lang['ci_model_db_error'] = 'DB Error: ';
$lang['ci_model_no_data'] = 'No data available.';
$lang['ci_model_invalid_id'] = 'Invalid ID passed to model.';
$lang['ci_model_no_table'] = 'Model has unspecified database table.';
$lang['ci_model_fetch_error'] = 'Not enough information to fetch field.';
$lang['ci_model_count_error'] = 'Not enough information to count results.';
$lang['ci_model_unique_error'] = 'Not enough information to check uniqueness.';
$lang['ci_model_find_error'] = 'Not enough information to find by.';
$lang['ci_model_bad_select'] = 'Invalid selection.';

//--------------------------------------------------------------------
// Contexts
//--------------------------------------------------------------------
$lang['ci_no_contexts'] = 'The contexts array is not properly setup. Check your application config file.';
$lang['ci_context_content'] = 'Content';
$lang['ci_context_reports'] = 'Reports';
$lang['ci_context_settings'] = 'Settings';
$lang['ci_context_developer'] = 'Developer';

//--------------------------------------------------------------------
// Activities
//--------------------------------------------------------------------
$lang['ci_act_settings_saved'] = 'App settings saved from';
$lang['ci_unauthorized_attempt'] = 'unsuccessfully attempted to access a page which required the following permission "%s" from ';

$lang['ci_keyboard_shortcuts'] = 'Available keyboard shortcuts:';
$lang['ci_keyboard_shortcuts_none'] = 'There are no keyboard shortcuts assigned.';
$lang['ci_keyboard_shortcuts_edit'] = 'Update the keyboard shortcuts';

//--------------------------------------------------------------------
// Common
//--------------------------------------------------------------------
$lang['ci_question_mark'] = '?';
$lang['ci_language_direction'] = 'ltr';
$lang['log_intro'] = 'These are your log messages';
$lang['active'] = 'Active';
$lang['inactive'] = 'Inactive';
$lang['back'] = 'Back';

//--------------------------------------------------------------------
// Login
//--------------------------------------------------------------------
$lang['ci_action_register'] = 'Sign Up';
$lang['ci_forgot_password'] = 'Forgot your password?';
$lang['ci_remember_me'] = 'Remember me';

//--------------------------------------------------------------------
// Password Help Fields to be used as a warning on register
//--------------------------------------------------------------------
$lang['ci_password_number_required_help'] = 'Password must contain at least 1 punctuation mark.';
$lang['ci_password_caps_required_help'] = 'Password must contain at least 1 capital letter.';
$lang['ci_password_symbols_required_help'] = 'Password must contain at least 1 symbol.';

$lang['ci_password_min_length_help'] = 'Password must be at least %s characters long.';
$lang['ci_password_length'] = 'Password Length';

//--------------------------------------------------------------------
// User Meta examples
//--------------------------------------------------------------------

$lang['user_meta_street_name'] = 'Street Name';
$lang['user_meta_type'] = 'Type';
$lang['user_meta_country'] = 'Country';
$lang['user_meta_state'] = 'State';

// Activation
//--------------------------------------------------------------------
$lang['ci_activate_method'] = 'Activation Method';
$lang['ci_activate_none'] = 'None';
$lang['ci_activate_email'] = 'Email';
$lang['ci_activate_admin'] = 'Admin';
$lang['ci_activate'] = 'Activate';
$lang['ci_activate_resend'] = 'Resend Activation';

$lang['ci_reg_complete_error'] = 'An error occurred completing your registration. Please try again or contact the site administrator for help.';
$lang['ci_reg_activate_email'] = 'An email containing your activation code has been sent to [EMAIL].';
$lang['ci_reg_activate_admin'] = 'You will be notified when the site administrator has approved your membership.';
$lang['ci_reg_activate_none'] = 'Please login to begin using the site.';
$lang['ci_user_not_active'] = 'User account is not active.';
$lang['ci_login_activate_title'] = 'Need to activate your account?';
$lang['ci_login_activate_email'] = '<b>Have an activation code to enter to activate your membership?</b> Enter it on the [ACCOUNT_ACTIVATE_URL] page.<br /><br />    <b>Need your code again?</b> Request it again on the [ACTIVATE_RESEND_URL] page.';
$lang['ci_login_success_message'] = 'user has been login successfully';

$lang['urlexist'] = 'Invalid URL!';


//--------------------------------------------------------------------
// Login Widget Variable
//--------------------------------------------------------------------

$lang['email'] = 'Email';
$lang['password'] = 'Password';
$lang['login'] = 'Login';
$lang['create-account-link'] = 'Create new account?';
$lang['forgot-password'] = 'Forgot Password';
$lang['change-password'] = "Change Password";
$lang['welcome'] = "Welcome";
$lang['logout'] = "Logout";
$lang['home'] = "Home";


//--------------------------------------------------------------------
// Base controller  Variable
//--------------------------------------------------------------------

$lang['permission-error'] = "Permission Error";
$lang['permission-error-msg'] = "You do not have permission to access the requested page";
$lang['permission-error-login-msg'] = "Please login to the site and try again";
$lang['no-language-defind'] = "No Language defind in the database";

//Some dropdown common varibale
$lang['lbl_please_select_country'] = "Please Select Country";
$lang['lbl_please_select_province'] = "Please Select Province";
$lang['lbl_please_select_city'] = "Please Select City";
$lang['lbl_please_select_retail_store'] = "Please Select Retailer";
$lang['lbl_please_select_manager'] = "Please Select Manager";
$lang['lbl_please_select_company'] = "Please Select Company";
$lang['lbl_please_select_brand'] = "Please Select Brand";
$lang['lbl_please_select_contact'] = "Please Select Contact";
$lang['lbl_please_select_product_category'] = "Please Select Product Category";
$lang['lbl_please_select_channel'] = "Please Select Channel";
$lang['lbl_please_select_caller'] = "Please Select Caller";
$lang['lbl_please_select_dropdown_list'] = "Please Select Drodown List";
$lang['lbl_please_select_parent_category'] = "Please Select Parent Defect";
$lang['lbl_please_select_product'] = "Please Select Product";
$lang['lbl_please_select_user'] = "Please Select User";
$lang['lbl_please_select_store'] = "Please Select Store";
$lang['lbl_please_select_defect_category'] = 'Please Select Defect Category';
$lang['lbl_please_select_defect_sub_category'] = 'Please Select Defect Sub Category';
$lang['lbl_please_select_service_centre'] = 'Please Select Service Centre';
$lang['lbl_select_company_type'] = 'Please select Company Type';
$lang['lbl_please_select_supplier'] = "Please Select Supplier";
$lang['lbl_please_select_category'] = 'Please Select Category';
$lang['lbl_please_select_sub_category'] = 'Please Select Sub Category';
$lang['lbl_please_select_department'] = "Please Select Department";


$lang['lbl_address1'] = 'Address 1 (Number/Building)';
$lang['lbl_address2'] = 'Address 2 (Street)';
$lang['lbl_country'] = 'Country';
$lang['lbl_province'] = 'Province';
$lang['lbl_city'] = 'City';
$lang['lbl_post_code'] = 'Post Code';
$lang['lbl_email'] = 'Email';
$lang['lbl_mobile'] = 'Mobile';
$lang['lbl_landline'] = 'Landline';
$lang['lbl_fax'] = 'Fax';
$lang['lbl_linkedin'] = 'Linkedin';
$lang['lbl_facebook'] = 'Facebook';
$lang['lbl_url'] = 'URL';
$lang['lbl_twitter'] = 'Twitter';

//This is for company popup
$lang['lbl_add_company_info'] = 'Add Company Information';
$lang['lbl_company_type'] = 'Company Type';


//This is for Menu 
$lang['lbl_change_password'] = 'Change Password';
$lang['lbl_log_out'] = 'Log Out';

$lang['lbl_front_office'] = 'Front Office';
$lang['lbl_menu_rma'] = 'RMA';
$lang['lbl_menu_my_allocation'] = 'My Allocation';
$lang['lbl_menu_quotations'] = 'Quotations';
$lang['lbl_menu_non_conformance'] = 'Non Conformance';
$lang['lbl_menu_risk_register'] = 'Risk Register';
$lang['lbl_menu_support_tickets'] = 'Support Tickets';
$lang['lbl_menu_notices'] = 'Notices';
$lang['lbl_menu_customer_survey'] = 'Clients Survey';
$lang['lbl_menu_reports'] = 'Reports';
$lang['lbl_menu_group_reports'] = 'Group Reports';
$lang['lbl_menu_rma_directory'] = 'RMA Directory';
$lang['lbl_menu_department'] = 'Department';
$lang['lbl_menu_non_conference_directory'] = 'Non conference Directory';

$lang['lbl_manager_section'] = 'Management';
$lang['lbl_admin_section'] = 'Administration';
$lang['lbl_menu_directory'] = 'Directory';
$lang['lbl_menu_supplier'] = 'Supplier';
$lang['lbl_menu_customers'] = 'Customers';
$lang['lbl_menu_companies'] = 'Suppliers'; // Companies
$lang['lbl_menu_retailer'] = 'Retailer';
$lang['lbl_menu_products'] = 'Products';
$lang['lbl_menu_rfq'] = 'RFQ';
$lang['lbl_menu_store'] = 'Store';
$lang['lbl_menu_contact'] = 'Contact';
$lang['lbl_menu_dropDown'] = 'DropDown';
$lang['lbl_menu_dropdown_list'] = 'Dropdown List';
$lang['lbl_menu_dropdown_option'] = 'Dropdown Option';
$lang['lbl_menu_defect_list'] = 'Defects List';
$lang['lbl_menu_category_list'] = 'Category List';
$lang['lbl_menu_reports'] = 'Reports';
$lang['lbl_submenu_leads'] = 'Lead Reports';
$lang['lbl_submenu_rfq'] = 'RFQ Reports';
$lang['lbl_submenu_products'] = 'Product Reports';
$lang['lbl_submenu_non_conformance'] = 'Non Conformance Reports';
$lang['lbl_menu_calender'] = 'Calendar';


$lang['lbl_menu_back_office'] = 'Back Office';
$lang['lbl_menu_team_members'] = 'Team Members';
$lang['lbl_menu_brands'] = 'Brands';
$lang['lbl_menu_retail_store_brand_link'] = 'Retail Store Brand Link';
$lang['lbl_menu_products_categories'] = 'Products Categories';
$lang['lbl_menu_location'] = 'Location';
$lang['lbl_menu_country'] = 'Country';
$lang['lbl_menu_province'] = 'Province';
$lang['lbl_menu_city'] = 'City';
$lang['lbl_menu_job_description'] = 'Job Description';
$lang['lbl_menu_notification'] = 'Notification';
$lang['lbl_menu_complaint_management'] = 'Complaint Management';
$lang['lbl_menu_companies_type'] = 'Companies Type';

$lang['lbl_menu_power_auditor'] = 'Power auditor';
$lang['lbl_menu_power_auditor_dashboard'] = 'Dashboard';
$lang['lbl_menu_power_auditor_report'] = 'Report';
$lang['lbl_menu_configuration'] = 'Configuration';
$lang['lbl_menu_configuration_option'] = 'Configuration Option';
$lang['lbl_menu_not_started'] = 'Not Started';
$lang['lbl_menu_in_progress'] = 'In Progress';
$lang['lbl_menu_completed'] = 'Completed';
$lang['lbl_menu_differed'] = 'Differed';
$lang['lbl_menu_my_allocation'] = 'My Allocation';
//This is for Menu 

$lang['lbl_mies_job'] = 'Jendrad'; // Mies TS
$lang['lbl_mies_tickets'] = 'Place Order'; // Tickets
$lang['lbl_mies_allocation'] = 'My Allocation';
$lang['lbl_mies_report'] = 'Report';
$lang['lbl_all_report'] = 'Full Report';
$lang['lbl_mies_report_financial'] = 'Financial';
$lang['lbl_mies_ticket_progress'] = 'Ticket progress';
$lang['lbl_mies_report_defect'] = 'defect';
$lang['lbl_mies_quotations'] = 'Quotations';
$lang['lbl_mies_directory'] = 'Directory';
$lang['lbl_mies_work_type'] = 'Inventory';
$lang['lbl_mies_material_type'] = 'Build of Materials'; // Materials Type
$lang['lbl_mies_labour_type'] = 'Labour Type';


$lang['lbl_menu_dashboard'] = 'Dashboard';
$lang['lbl_menu_users'] = 'Users';
$lang['lbl_menu_notifications'] = 'Notifications';