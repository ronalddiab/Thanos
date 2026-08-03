<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['admin.modules'] = array('languages','menu','permissions','product','roles','role_management','settings','translate','urls','users');

/*
|--------------------------------------------------------------------
| MESSAGE TEMPLATE
|--------------------------------------------------------------------
| This is the template that Ocular will use when displaying messages
| through the message() function.
|
| To set the class for the type of message (error, success, etc),
| the {type} placeholder will be replaced. The message will replace
| the {message} placeholder.
|
*/
$CI = & get_instance();
$CI->load->helper('url');
$base_url = base_url();
$config['theme.message_template'] = <<<EOD
<div class="alert alert-warning alert-dismissible fade in alert-{type}" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true"><img alt="Close" src="$base_url/themes/default/images/{closeimage}"></span>
	</button> 
	<i><img alt="Success" src="$base_url/themes/default/images/{typeimage}"></i>
	{message}
</div>
EOD;
