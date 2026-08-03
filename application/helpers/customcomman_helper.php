<?php

function dd($data) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
	exit;
}

function report_value_format($value, $options = array()) {
    $is_nagetive = false;
    if($value < 0){
	$is_nagetive = true;
    }

    if(is_float($value) && $options['parsefloat']){
	$value = number_format($value, 2);
    }else{
	$value = number_format($value);
    }

    if($options['currency']){
	if($is_nagetive){
	    $value = str_replace('-', '-'.CURRENCY_SYMBOL, $value);
	}else{
	    $value = CURRENCY_SYMBOL.$value;
	}

    }

    return $value;
}

function GetCurrentDate() {
    return date("Y-m-d");
}

function GetCurrentDateTime() {
    return date("Y-m-d H:i:s");
}

function encriptsha1($string = "") {
    $CI = & get_instance();
    return $CI->encrypt->sha1($string);
}

if (!function_exists('buildMenu')) {

    function buildMenu($parentId, $menuData) {
	$ci = &get_instance();
	$html = '';
	if (isset($menuData['parents'][$parentId]) && $menuData['items'][$parentId]['m']['slug'] != 'certification') {
	    if ($parentId == 0)
		{
		$html = '<ul class="menu-left menu-bar">';
		}
		else if($menuData['items'][$parentId]['m']['certification_menu'] ==1 && ($menuData['items'][$parentId]['m']['main_menu_id']==NULL ))
		{
		$html = '<ul class="certification">';
		} 
		else if($menuData['items'][$parentId]['custom']['is_parent']==1 && $menuData['items'][$parentId]['m']['main_menu_id']!=NULL)
		{
		$html = '<ul class="certification-parent">';
		}
	    elseif ($menuData['items'][$parentId]['m']['parent_id'] == 0) {
		$html = '<ul class="sub-menu-nav">';
	    }
		else
		$html = '<ul>';


	    $is_menu_url_exists = false;
	    $fullurl = $ci->uri->uri_string;
	    foreach ($menuData['parents'][$parentId] as $itemId) {


		// Static condition for site user's project menu selected
		if($menuData['items'][$itemId]['m']['id'] == "31" && $ci->session->userdata['admin']['role_id'] > 2) {
		    $menuData['items'][$itemId]['m']['link']="projects/actionplans";
		}
		/*if($menuData['items'][$itemId]['m']['id'] == "13" && $ci->session->userdata['admin']['role_id'] > 2) {
		    $menuData['items'][$itemId]['m']['link']="projects/actionplans";
		}*/

		if($menuData['items'][$itemId]['m']['link'] == $fullurl){
		    $is_menu_url_exists = true;
		}
	    }


	    $role_id = isset($ci->session->userdata['admin']['role_id'])?$ci->session->userdata['admin']['role_id']:'';
	    foreach ($menuData['parents'][$parentId] as $itemId) {

				if($menuData['items'][$itemId]['m']['id'] == 35 && !SHOW_GROUP_REPORT)
				{
					continue;
				}
				if($menuData['items'][$itemId]['m']['id'] == 41 && !UTILITIES_DAILY_MENU)
				{
					continue;
				}
				if($menuData['items'][$itemId]['m']['id'] == 48 && $ci->session->userdata['admin']['site_csr'] != 1) {
					continue;
				}
				//For Show/Hide Daily menu item
				$showDailyMenu=1;
				if($menuData['items'][$itemId]['m']['id'] == 2 && !UTILITIES_DAILY_MENU)
				{
					$showDailyMenu=0;
				}
				//For utility menu show/hide Daily menu item
				//
		// Menu which have only super admin access
		/*if($role_id!=1 && $itemId==32){
		    continue;
		}*/

		$menu_label_permission = check_user_permission_by_label($menuData['items'][$itemId]['m']['permission_label']);
		if(!$menu_label_permission && !empty($menuData['items'][$itemId]['m']['permission_label'])){
		    continue;
		}

		if($menuData['items'][$itemId]['m']['link'] == 'knowledgebase' && $ci->session->userdata['admin']['role_id'] == 5) {
		    continue;
		}

		// For Front Menu
		if ($menuData['items'][$itemId]['m']['menu_name'] == 'front_menu') {

		    $seluri = $ci->uri->segment(1);
		    $link = implode('/', array_slice(explode('/', $menuData['items'][$itemId]['m']['link']), 0, 1));

		    global $CFG;
		    if ($CFG->item('multilang_option') == 1) {
			$seluri_temp = $ci->uri->segment(2);

			$ci->db->select('GROUP_CONCAT(cms.slug_url) AS slug');
			$ci->db->from('cms');
			$ci->db->where('status', '1');

			$result_multilang = $ci->db->get();
			$row_multilang = $result_multilang->result();

			// if (strpos($row_multilang['slug'], $seluri_temp) !== FALSE) {
			if (strpos($row_multilang[0]->slug, $seluri_temp) !== FALSE) {
			    $seluri = $ci->uri->segment(2);
			    $link = implode('/', array_slice(explode('/', $menuData['items'][$itemId]['m']['link']), 1, 2));
			}
		    }
		}
		// For Admin Menu
		else if ($menuData['items'][$itemId]['m']['menu_name'] == 'admin_menu') {

		    $seluri = $ci->uri->segment(1);
		    //$link = implode('/', array_slice(explode('/', $menuData['items'][$itemId]['m']['link']), 0, 1));

		    if (isset($menuData['parents'][$itemId]) && ($seluri === $link)) {
			$seluri_temp = $ci->uri->segment(1);
			$link_temp = implode('/', array_slice(explode('/', $menuData['items'][$itemId]['m']['link']), 0, 1));

			if ($seluri_temp == $link_temp) {
			    $seluri = $ci->uri->segment(1);
			    //$link = implode('/', array_slice(explode('/', $menuData['items'][$itemId]['m']['link']), 0, 1));
			}
		    }

		    $seluri_temp = $ci->uri->segment(2);
		    if (empty($seluri_temp)) {
			$seluri = $ci->uri->segment(1);
			//$link = implode('/', array_slice(explode('/', $menuData['items'][$itemId]['m']['link']), 0, 1));
		    }else {
			if($is_menu_url_exists){
			    $seluri = $ci->uri->uri_string;
			}else{
			    $seluri = $ci->uri->segment(1);
			}
			//$is_menu_url_exists
		    }
		    $link = $menuData['items'][$itemId]['m']['link'];
		}

		//$seluri = implode('/', array_slice($ci->uri->segment_array(), 0, 2));
		if (isset($menuData['parents'][$itemId])) {
		    $selected = ($seluri == $link) ? 'active' : '';
            $extra_class = '';
		    if(empty($menuData['items'][$itemId]['m']['menu_icon'])) {
			$menuimage = '';
		    } else {
			$menuimage = '<img src="'.$menuData['items'][$itemId]['m']['menu_icon'].'" alt="">';
		    }

		    $menulink = ($menuData['items'][$itemId]['m']['link'] == '/') ? 'javascript:void(0);' :$menuData['items'][$itemId]['m']['link'];
			
			$menuparentdropdown = '<i class="fa fa-angle-right up-arrow"></i><i class="fa fa-angle-down down-arrow"></i>';
			if(!$showDailyMenu)
			{

						$menuparentdropdown="";
						$menulink = site_base_url() . "utilities";
			}
			if($menuData['items'][$itemId]['m']['slug'] == 'certification')
			{
				$selected = ($seluri=='certification')?'active':'';
				$extra_class = 'certification_menu';
				$menulink = ($menuData['items'][$itemId]['m']['link'] == '/') ? 'javascript:void(0);' :site_base_url().$menuData['items'][$itemId]['m']['link'];
				$html .= '<li id="menu' . $menuData['items'][$itemId]['m']['id'] . '" class="certification_li nav-parent '.$selected.'"><a href="' .$menulink . '" title="'.$menuData['items'][$itemId]['m']['title'].'" class=" haschild ' . $selected .' '.$extra_class. '" >'.$menuimage.$menuparentdropdown.'<span>' . $menuData['items'][$itemId]['m']['title'] .  "</span><div class='tooltip-content'>" .$menuData['items'][$itemId]['m']['title']."</div></a>";
			}
			else{
				$html .= '<li id="menu' . $menuData['items'][$itemId]['m']['id'] . '" class="nav-parent"><a href="' .$menulink . '" title="'.$menuData['items'][$itemId]['m']['title'].'" class="main_menu haschild ' . $selected . ' '.$extra_class.'" >'.$menuimage.$menuparentdropdown.'<span>' . $menuData['items'][$itemId]['m']['title'] .  "</span><div class='tooltip-content'>" .$menuData['items'][$itemId]['m']['title']."</div></a>";

			}
			
		} else {
		    $selected = ($seluri == $link) ? 'active' : '';

		    $menulink = ($menuData['items'][$itemId]['m']['link'] == '/') ? '' : $menuData['items'][$itemId]['m']['link'];

		    if ($menuData['items'][$itemId]['m']['id'] != 271) {
			if(empty($menuData['items'][$itemId]['m']['menu_icon'])) {
			    $menuimage = '';
			} else {
			    $menuimage = '<img src="'.$menuData['items'][$itemId]['m']['menu_icon'].'" alt="">';
			}

			if(isset($menuData['items'][$itemId]['m']['parent_id']) && $menuData['items'][$itemId]['m']['parent_id'] != "0") {
			    $menulink =  $menulink ;
			    if (!preg_match("~^(?:f|ht)tps?://~i",$menulink)) {
				$menulink = site_base_url() .$menulink;
			    }
			    if($menuData['items'][$itemId]['m']['id'] == "31" && $ci->session->userdata['admin']['role_id'] > 2) {
				$menulink ="projects/actionplans";
			    }
			    $html .= '<li id="menu' . $menuData['items'][$itemId]['m']['id'] . '" class="' . $selected . '"><a href="' . $menulink . '" title="'.$menuData['items'][$itemId]['m']['title'].'" class="main_menu ' . $selected . '">'.$menuimage. $menuData['items'][$itemId]['m']['title']."<div class='tooltip-content'>" . $menuData['items'][$itemId]['m']['title']."</div></a>";
			} else {
			    /*if($menuData['items'][$itemId]['m']['id'] == "13" && $ci->session->userdata['admin']['role_id'] > 2) {
				$menulink ="projects/actionplans";
			    }*/
			    $menulink =  $menulink ;
			    if (!preg_match("~^(?:f|ht)tps?://~i",$menulink)) {
				$menulink = site_base_url() .$menulink;
			    }
			    $html .= '<li id="menu' . $menuData['items'][$itemId]['m']['id'] . '" class="' . $selected . '"><a href="' . $menulink . '" title="'.$menuData['items'][$itemId]['m']['title'].'" class="main_menu ' . $selected . '">'.$menuimage.'<span>' .$menuData['items'][$itemId]['m']['title'] . "</span><div class='tooltip-content'>" . $menuData['items'][$itemId]['m']['title']."</div></a>";

			}
		    } else if (!empty($ci->session->userdata['front']['user_id']) && ($menuData['items'][$itemId]['m']['id'] == 271)) {
			$menulink =  $menulink ;
			if (!preg_match("~^(?:f|ht)tps?://~i",$menulink)) {
			    $menulink = site_base_url() .$menulink;
			}
			$html .= '<li id="menu' . $menuData['items'][$itemId]['m']['id'] . '" class="' . $selected . '"><a href="' . $menulink . '" title="'.$menuData['items'][$itemId]['m']['title'].'" class="main_menu ' . $selected . '"><i class="glyphicon glyphicon-tree-conifer"></i><span>' .$menuData['items'][$itemId]['m']['title'] . "</span><div class='tooltip-content'>" . $menuData['items'][$itemId]['m']['title']."</div></a>";
		    }
		}
		// find childitems recursively

			if($showDailyMenu)
			{
				$html .= buildMenu($itemId, $menuData);
			}

		$html .= '</li>';
	    }
	    $html .= '</ul>';
	}
	elseif($menuData['items'][$parentId]['m']['slug'] == 'certification'){
		$seluri = $ci->uri->uri_string;
		$segment1 = $ci->uri->segment(1);
		$html = generateMenuHtml($menuData, $parentId,$segment1,$seluri);

	}
	return $html;
    }

}
function generateMenuHtml($menuData, $parentId, $segment1, $url, $level = 0) {
    $selUrl = $url;
    if ($segment1 == 'certification') {
        $html = '<ul class="certification-ul">';
    } else {
        $html = '<ul class="certification-ul" style="display:none">';
    }

    foreach ($menuData['parents'][$parentId] as $itemId) {
        $link = $menuData['items'][$itemId]['m']['link'];
        $selected = ($link == $selUrl) ? 'active' : '';
        $item = $menuData['items'][$itemId];
        $isParent = isset($item['custom']['is_parent']) && $item['custom']['is_parent'] == 1;

        $hasActiveChildren = !empty($menuData['parents'][$itemId]) && hasActiveChildren($menuData, $itemId, $selUrl);

        $spaces = str_repeat('&nbsp;', $level * 3);

        $html .= '<li class="child-li ' . $selected . '">';

		
		$item['m']['link'] = $item['m']['link']=='#'?'javascript:void(0)':site_base_url() . $item['m']['link'];
		
        if ($isParent) {
            $html .= '<a href="' .$item['m']['link']. '">';
            $html .= '<i class="fa fa-angle-right ' . ($hasActiveChildren ? 'up-arrow' : 'down-arrow') . '" style="display:block"></i>';
            $html .= '<span>' .$spaces.$item['m']['title'] . '</span>';
            $html .= '</a>';
        } else {
            $html .= '<a href="' .$item['m']['link'] . '">';
            $html .= $spaces . $item['m']['title'];
            $html .= '</a>';
        }

        if (!empty($menuData['parents'][$itemId])) {
            // Recursive call with incremented level
            $html .= '<ul class="child-menu" style="' . ($hasActiveChildren ? 'display:block' : 'display:none') . '">';
            $html .= generateMenuHtml($menuData, $itemId, $segment1, $selUrl, $level + 1);
            $html .= '</ul>';
        }

        $html .= '</li>';
    }

    $html .= '</ul>';
    return $html;
}


function hasActiveChildren($menuData, $parentId, $selUrl) {
    foreach ($menuData['parents'][$parentId] as $itemId) {
        $link = $menuData['items'][$itemId]['m']['link'];
        if ($link == $selUrl) {
            return true; 
        }
        if (!empty($menuData['parents'][$itemId]) && hasActiveChildren($menuData, $itemId, $selUrl)) {
            return true; 
        }
    }
    return false;
}



/**
 * $name should be widgetname,modulename or theme name
 * $type should be modules,themes,widgets
 * all js located in assets folder
 * @param type $filename
 * @param type $name
 * @param type $type
 */
if (!function_exists('add_js')) {

    function add_js($filename, $name = "", $type = "") {
	global $CFG;
	$cfgArr = $CFG->config;
	$theme = $cfgArr['theme']['theme'];

	$script = "";
	switch (strtolower($type)) {
	    case 'modules':
	    case 'widgets':
		$basepath = site_base_url() . 'themes/' . $theme . '/js/' . $type . '/' . $name . '/';
		break;
	    default:
		$basepath = site_base_url() . 'themes/' . $theme . '/js/';
		break;
	}

	if (is_array($filename)) {
	    foreach ($filename as $sname) {
		$filepath = $basepath . $sname . '.js';
		$script .= '<script type="text/javascript" src="' . $filepath . '" charset="' . $cfgArr['charset'] . '"></script>' . "\r\n";
	    }
	} else {
	    $basepath = $basepath . $filename . '.js';
	    $script .= '<script type="text/javascript" src="' . $basepath . '" charset="' . $cfgArr['charset'] . '"></script>' . "\r\n";
	}
	return $script;
    }

}
/**
 * $name should be widgetname,modulename or theme name
 * $type should be modules,themes,widgets
 * all css located in assets folder
 * @param type $filename
 * @param type $name
 * @param type $type
 */
if (!function_exists('add_css')) {

    function add_css($filename, $name = "", $type = "", $media = 'screen') {
	global $CFG;
	$cfgArr = $CFG->config;
	$theme = $cfgArr['theme']['theme'];

	$style = "";
	switch (strtolower($type)) {
	    case 'modules':
	    case 'widgets':
		$basepath = site_base_url() . 'themes/' . $theme . '/css/' . $type . '/' . $name . '/';
		break;
	    default:
		$basepath = site_base_url() . 'themes/' . $theme . '/css/';
		break;
	}
	if (is_array($filename)) {
	    foreach ($filename as $sname) {
		$filepath = $basepath . $sname . '.css';
		$style .= '<link type="text/css" rel="stylesheet" href="' . $filepath . '" media="' . $media . '" >' . "\r\n";
	    }
	} else {
	    $basepath = $basepath . $filename . '.css';
	    $style .= '<link type="text/css" rel="stylesheet" href="' . $basepath . '" media="' . $media . '" >' . "\r\n";
	}

	return $style;
    }

}
/**
 * $name should be widgetname,modulename or theme name
 * $type should be modules,themes,widgets
 * all images located in assets folder
 * @param type $filename
 * @param type $name
 * @param type $type
 */
if (!function_exists('add_image')) {

    function add_image($filename, $name = "", $type = "", $options = array()) {

	global $CFG;
	$cfgArr = $CFG->config;
	$theme = $cfgArr['theme']['theme'];
	$attributes = "";

	//var_dump($options); exit;
	if (isset($options) && !empty($options)) {
	    foreach ($options as $key => $value) {
		$attributes.= $key . '="' . $value . '"';
		$attributes.= " ";
	    }
	}


	$image = "";
	$basepath = "";

	switch (strtolower($type)) {
	    case 'modules':
	    case 'widgets':
		$basepath = site_base_url() . 'themes/' . $theme . '/images/' . $type . '/' . $name . '/';
		break;
	    default:
		$basepath = site_base_url() . 'themes/' . $theme . '/images/';
		break;
	}

	if (is_array($filename)) {
	    foreach ($filename as $sname) {

		$filepath = $basepath . $sname;

		//$title = (isset($options['title']))? $options['title']:'';
		//echo $attributes; exit;
		$image .= '<img src="' . $filepath . '" ' . $attributes . ' />';
		//$image .= '<img src="' . $filepath . '" title="'.$title.'" />';
//                $image .= '<img src="' . $filepath . '"' ;
//                foreach($options as $attKey => $attVal)
//                {
//                    $image .= $attKey." = ".$attVal." ";
//                }
//                $image .= ' />';
	    }
	} else {
	    $basepath = $basepath . $filename;
	    $image .= '<img src="' . $basepath . '" ' . $attributes . '  />';
	}
	return $image;
    }

}
/**
 * Lang
 *
 * Fetches a language variable and optionally outputs a form label
 *
 * @access	public
 * @param	string	the language line
 * @param	string	the id of the form element
 * @return	string
 */
if (!function_exists('lang')) {

    function lang($line, $id = '') {
	$CI = & get_instance();
	$line = $CI->lang->line($line);

	if ($id != '') {
	    $line = '<label for="' . $id . '">' . $line . "</label>";
	}

	return $line;
    }

}

if (!function_exists('custom_filter_input')) {
    function custom_filter_input($type, $string, $message = "Invalid input") {
	$flag = 0;
	switch ($type) {
	    case 'integer' : {
		    if (filter_var($string, FILTER_VALIDATE_INT) !== false) {
			$flag = 1;
		    }
		}
		break;
	    case 'float' : {
		    if (filter_var($string, FILTER_VALIDATE_FLOAT) !== false) {
			$flag = 1;
		    }
		}
		break;
	    case 'ip' : {
		    if (filter_var($string, FILTER_VALIDATE_IP) !== false) {
			$flag = 1;
		    }
		}
		break;
	    case 'url' : {
		    if (filter_var($string, FILTER_VALIDATE_URL) !== false) {
			$flag = 1;
		    }
		}
		break;
	    case 'email' : {
		    if (filter_var($string, FILTER_VALIDATE_EMAIL) !== false) {
			$flag = 1;
		    }
		}
		break;
	    case 'default': {
		    $flag = 0;
		}
		break;
	}

	if ($flag == 1) {
	    return true;
	} else if ($flag == 0) {
	    show_error($message);
	    exit;
	}
    }

}


if (!function_exists('getmodulelist')) {

    function getmodulelist() {
	$ci = &get_instance();
	$dir = APPPATH . '/modules';
	$dirlist = opendir($dir);
	while ($file = readdir($dirlist)) {
	    if ($file != '.' && $file != '..' && $file != '.DS_Store') {
		$modules[$file] = $file;
	    }
	}
	return array_diff($modules, $ci->config->item('admin.modules'));
    }

}

/**
 * Lang
 *
 * Fetches a language variable and optionally outputs a form label
 *
 * @access	public
 * @param	string	the language line
 * @param	string	the id of the form element
 * @return	string
 */
if (!function_exists('display_meta')) {

    function display_meta() {
	$ci = &get_instance();
	$meta_array = $ci->theme->get_meta();
	$meta = '';
	foreach ($meta_array as $key => $val) {
	    if (is_array($val)) {
		$meta .= '<meta ';
		foreach ($val as $attribute_name => $value) {
		    $meta .= $attribute_name . '="' . $value . '" ';
		}
		$meta .='>';
	    }
	}
	return $meta;
    }

}

/**
 * Error Handler
 *
 * This function lets us invoke the exception class and
 * display errors using the standard error template located
 * in application/errors/errors.php
 * This function will send the error page directly to the
 * browser and exit.
 *
 * @access	public
 * @return	void
 */
if (!function_exists('show_permission_error')) {

    function show_permission_error($message, $status_code = 500, $heading = 'An Error Was Encountered') {
	$_error = & load_class('Exceptions', 'core');
	//echo $_error->show_error($heading, $message, 'error_permission', $status_code);
	echo show_custom_permission_errors($heading, $message, 'error_custom_permission', $status_code, $_error);
	exit;
    }
}

function show_custom_permission_errors($heading, $message, $template = 'error_general', $status_code = 500, $_error) {
	set_status_header($status_code);

	$message = implode(', ', ( ! is_array($message)) ? array($message) : $message);

	if (ob_get_level() > $_error->ob_level + 1)
	{
		ob_end_flush();
	}
	ob_start();
	include(APPPATH.'errors/'.$template.'.php');
	$buffer = ob_get_contents();
	ob_end_clean();
	return $buffer;
}

if (!function_exists('getForumRules')) {

    function getForumRules() {

	$ci = &get_instance();
	$sql = "SELECT * FROM (`cms`) WHERE `id` = 9";
	$query = $ci->db->query($sql);

	$row = $query->row_array();

	return $row;
    }
}

if(!function_exists('check_user_permission_by_label')){
    function check_user_permission_by_label($label=''){
	$ci = &get_instance();

	$label = strtolower($label);

	if($ci->session->userdata['admin']['role_id'] == 1){
	    return true;
	}else if(in_array($label, $ci->session->userdata['admin']['permissions'])){
	    return true;
	}else{
	    return false;
	}
    }
}

if(!function_exists('currency_symbol')){
    function currency_symbol($isLocal=false){
	$ci = &get_instance();
	if($isLocal){
	    if(empty($ci->session->userdata['admin']['local_currency'])){
		return BASE_CURRENCY_SYMBOL;
	    }else{
		return $ci->session->userdata['admin']['local_currency'];
	    }

	}else{
	    return BASE_CURRENCY_SYMBOL;
	}
    }
}
//for cron
if(!function_exists('cur_symbol')){
    function cur_symbol($isLocal=false){
	$ci = &get_instance();
	if($isLocal){
	    if(empty($ci->session->userdata['hep_cron_session']['local_currency'])){
		return BASE_CURRENCY_SYMBOL;
	    }else{
		return $ci->session->userdata['hep_cron_session']['local_currency'];
	    }

	}else{
	    return BASE_CURRENCY_SYMBOL;
	}
    }
}

if (!function_exists('saveAuditTrail')) {

    function saveAuditTrail($userId='',$siteId='',$module='',$action='') {

	$ci = &get_instance();

	//Set values for audit trail
	$data_array = array();
	$data_array['user_id'] = $userId;
	$data_array['site_id'] = $siteId;
	$data_array['module_name'] = $module;
	$data_array['action'] = $action;
	$data_array['created'] = date('Y-m-d H:i:s');

	$ci->db->set($data_array);
	return $ci->db->insert('audit_trail');
	// return $ci->db->_error_number();
    }
}

if(!function_exists('isCallFromCron')){
    function isCallFromCron(){
	$cronRegEx = "FromCronCommand";
	$filename = FCPATH."application/logs/cronAccess.txt";
	$myfile = fopen($filename, "a+") or die("Unable to open file!");
	if($_SERVER['HTTP_USER_AGENT'] == $cronRegEx){
	    $userAgent = 'Accessed using cron from['.$_SERVER['REMOTE_ADDR'].']: '.$_SERVER['HTTP_USER_AGENT'].' @ '.date("Y-n-j H:i:sa").PHP_EOL;
	    fwrite($myfile, $userAgent);
	    fclose($myfile);
	    return true;
	}else{
	    $userAgent = 'Accessed using browser from['.$_SERVER['REMOTE_ADDR'].']: '.$_SERVER['HTTP_USER_AGENT'].' @ '.date("Y-n-j H:i:sa").PHP_EOL;
	    fwrite($myfile, $userAgent);
	    fclose($myfile);
	    return false;
	}
    }
}

if(!function_exists('saveBulkData')){
    function saveBulkData($data,$tableName, $dir = ''){
	$filename = FCPATH."application/logs/".$tableName."_".date('Y-n-j').".json";
	if ($dir != '') {
		if (!is_dir(FCPATH."application/logs/".$dir."/")) {
			mkdir(FCPATH."application/logs/".$dir."/", 0777, true);
		}
		$filename = FCPATH."application/logs/".$dir."/".$tableName."_".date('Y-n-j').".json";
	}
	$myfile = fopen($filename, "a+") or die("Unable to open file!");
	$content = [date('Y-n-j h:i:sa')].":".json_encode($data).PHP_EOL;
	fwrite($myfile, $content);
	fclose($myfile);
    }
}

/**
 * Function GetSiteUtilityUnitValue to return site array of particular id and utility
 * @param integer $id
 * @param string $utility
 * @return int utility_value from db
 */
function GetSiteUtilityUnitValue($id = 0, $utility)
{
    if(!empty($id) && !empty($utility)){
	$ci = &get_instance();
	$utility = 'utility_unit_'.$utility;
	//Type Casting
	$id = intval($id);
	$ci->db->where("id", $id);
	$ci->db->select("COALESCE({$utility}, 0) AS {$utility}", false);
	$ci->db->from(TBL_SITES);

	$siteArray = $ci->db->get()->row();

	if (!empty($siteArray)) {
	    return $siteArray->$utility;
	}
    } else {
	return [];
    }
}

/**
 * Function GetSiteUtilityUnitName to return site array of particular id and utility
 * @param integer $id
 * @param string $utility
 * @return string utility_name from constants array
 */
function GetSiteUtilityUnitName($id = 0, $utility)
{
    if(empty($id)) {
	$id = $_SESSION['admin']['site_id'];
    }
    if(!empty($id) && !empty($utility)) {
	$siteUtilityValue = GetSiteUtilityUnitValue($id, $utility);
	$utilityArray = GetUtilityDropdownFromConstant($utility);
	return $utilityArray[$siteUtilityValue];
    } else {
	return '';
    }
}

/**
 * Function GetSiteUtilityUnitNameRate to return site array of particular id and utility
 * @param integer $id
 * @param string $utility
 * @return string utility_name_rate from constants array
 */
function GetSiteUtilityUnitNameRate($id = 0, $utility)
{
    if(empty($id)) {
	$id = $_SESSION['admin']['site_id'];
    }
    if(!empty($id) && !empty($utility)) {
	$siteUtilityValue = GetSiteUtilityUnitValue($id, $utility);
	$utilityArray = GetUtilityDropdownFromConstant($utility);
	return ''.CURRENCY_SYMBOL.'/'.$utilityArray[$siteUtilityValue];
    } else {
	return '';
    }
}

/**
 * Function GetSiteUtilityUnitNameRate to return site array of particular id and utility
 * @param integer $id
 * @param string $utility
 * @return string utility_name_rate from constants array
 */
function GetSiteUtilityUnitNameKgCO2e($id = 0, $utility, $isEmission = false)
{
    if(empty($id)) {
	$id = $_SESSION['admin']['site_id'];
    }
    if(!empty($id) && !empty($utility)) {
	$siteUtilityValue = GetSiteUtilityUnitValue($id, $utility);
	$utilityArray = GetUtilityDropdownFromConstant($utility);
	if(!$isEmission) {
		return 'KgCO2e/'.$utilityArray[$siteUtilityValue];
	} else {
		return 'KgCO2e/KWh';
	}
    } else {
	return '';
    }
}

function GetUtilityDropdownFromConstant($utility)
{
    $dropdown_array = [];
    if(!empty($utility)){
	switch ($utility) {
	    case 'electricity':
		$dropdown_array = explode(',', ELECTRICITY);
		break;
	    case 'fuel_oil':
		$dropdown_array = explode(',', FUEL_OIL);
		break;
	    case 'lpg':
		$dropdown_array = explode(',', LPG);
		break;
	    case 'water':
		$dropdown_array = explode(',', WATER);
		break;
	    case 'natural_gas':
		$dropdown_array = explode(',', NATURAL_GAS);
		break;
	    case 'district_cooling':
		$dropdown_array = explode(',', DISTRICT_COOLING);
		break;
	    case 'district_heating':
		$dropdown_array = explode(',', DISTRICT_HEATING);
		break;
	    default:
		$dropdown_array = [];
		break;
	}
    }
    return $dropdown_array;
}
// OLD conversion units
/* function getUtilityUnitFactorForConversion($id = 0, $utility)
{
    if(!empty($id) && !empty($utility)) {
	$utility_name = GetSiteUtilityUnitName($id, $utility);
	switch ($utility) {
	    case 'electricity':
		$factor = 1;
		break;
	    case 'fuel_oil':
		$factor = 1;
		break;
	    case 'lpg':
		if ($utility_name == 'Kg') {
		    $factor = 1;
		} else if ($utility_name == 'Liters') {
		    $factor = 0.51;
		} else if ($utility_name == 'CCF') {
		    $factor = 5.18;
		} else if ($utility_name == 'Therms') {
		    $factor = 2.291;
		} else if ($utility_name == 'mmBTU') {
		    $factor = 21.13;
		}
		break;
	    case 'water':
		if ($utility_name == 'm3') {
		    $factor = 1;
		} else if ($utility_name == 'ft3') {
		    $factor = 0.0283;
		} else if ($utility_name == 'CCF') {
		    $factor = 2.83;
		} else if ($utility_name == 'IG') {
		    $factor = 0.0046;
		} else if ($utility_name == 'USG') {
		    $factor = 0.0038;
		}
		break;
	    case 'natural_gas':
		if ($utility_name == 'm3') {
		    $factor = 1;
		} else if ($utility_name == 'ft3') {
		    $factor = 0.0283;
		} else if ($utility_name == 'CCF') {
		    $factor = 2.83;
		} else if ($utility_name == 'Therms') {
		    $factor = 2.72;
		} else if ($utility_name == 'mmBTU') {
		    $factor = 28.26;
		}
		break;
	    case 'district_cooling':
		if ($utility_name == 'kWh') {
		    $factor = 1;
		} else if ($utility_name == 'mmBTU') {
		    $factor = 293.07;
		} else if ($utility_name == 'RTH') {
		    $factor = 3.517;
		}
		break;
	    case 'district_heating':
		if ($utility_name == 'kWh') {
		    $factor = 1;
		} else if ($utility_name == 'Steam lb') {
		    $factor = 4.12;
		} else if ($utility_name == 'Steam Mlbs') {
		    $factor = 293.07;
		} else if ($utility_name == 'mmBTU') {
		    $factor = 293.07;
		}
		break;
	    default:
		$factor = 1;
		break;
	}
	$factor = convertToKWH($factor, $utility);
	return $factor;
    } else {
	return 1;
    }
} */

function getUtilityUnitFactorForConversion($id = 0, $utility)
{
    if(!empty($id) && !empty($utility)) {
	$utility_name = GetSiteUtilityUnitName($id, $utility);
	switch ($utility) {
	    case 'electricity':
		$factor = 1;
		break;
	    case 'fuel_oil':
		$factor = 10.6846424384525;
		break;
	    case 'lpg':
		if ($utility_name == 'kWh') {
		    $factor = 1;
		} else if ($utility_name == 'Kg') {
			//if($id == 13) { //for Amman Rotana
				$factor = 6.9;
			//} else {
				// $factor = 13.1388888888889;
			//}
		} else if ($utility_name == 'Liters') {
		    $factor =  6.653; //7.12304438163687;
		} else if ($utility_name == 'CCF') {
		    $factor = 73.7397420867526;
		} else if ($utility_name == 'Therms') {
		    $factor = 29.3083235638921;
		} else if ($utility_name == 'mmBTU') {
		    $factor = 293.083235638921;
		}
		break;
	    case 'water':
		if ($utility_name == 'm3') {
		    $factor = 1;
		} else if ($utility_name == 'ft3') {
		    $factor = 0.028316846;
		} else if ($utility_name == 'CCF') {
		    $factor = 2.8316846;
		} else if ($utility_name == 'IG') {
		    $factor = 0.00454609;
		} else if ($utility_name == 'USG') {
		    $factor = 0.003785412;
		}
		break;
	    case 'natural_gas':
		if ($utility_name == 'm3') {
		    $factor = 10.6398007033998;
		} else if ($utility_name == 'ft3') {
		    $factor = 0.300703399765533;
		} else if ($utility_name == 'CCF') {
		    $factor = 30.0703399765533;
		} else if ($utility_name == 'Therms') {
		    $factor = 29.3083235638921;
		} else if ($utility_name == 'mmBTU') {
		    $factor = 293.083235638921;
		}
		break;
	    case 'district_cooling':
		if ($utility_name == 'kWh') {
		    $factor = 1;
		} else if ($utility_name == 'mmBTU') {
		    $factor = 293.083235638921;
		} else if ($utility_name == 'RTH') {
		    $factor = 3.516852842;
		}
		break;
	    case 'district_heating':
		if ($utility_name == 'kWh') {
		    $factor = 1;
		} else if ($utility_name == 'Steam lb') {
		    $factor = 0.349941383352872;
		} else if ($utility_name == 'Steam Mlbs') {
		    $factor = 349.941;
		} else if ($utility_name == 'mmBTU') {
		    $factor = 293.083235638921;
		}
		break;
	    default:
		$factor = 1;
		break;
	}
	return $factor;
    } else {
	return 1;
    }
}

function convertToKWH($factor, $utility)
{
    switch ($utility) {
	case 'electricity':
	    return $factor * 1;
	    break;
	case 'fuel_oil':
	    return $factor * 10;
	    break;
	case 'lpg':
	    return $factor * 6.9;//13.6;
	    break;
	case 'water':
	    return $factor * 1;//10.55
	    break;
	case 'natural_gas':
	    return $factor * 10.55;
	    break;
	case 'district_cooling':
	    return $factor * 1;
	    break;
	case 'district_heating':
	    return $factor * 1;
	    break;
	default:
	    return $factor * 1;
	    break;
    }
}

function CheckIfSurveyIsOpen()
{
    $ci = &get_instance();
    $todayDate = date("Y-m-d");
    $sql = "SELECT * FROM (`survey_access`) WHERE `user_id` = 1";
    $query = $ci->db->query($sql);
    $row = $query->row_array();

    if (count($row) > 0) {
	if($row['close_date'] < $todayDate) {
	    return 2;
	} else {
	    return 1;
	}
    } else {
	return 0;
    }
}

function SelectPromptDropdown($dropdownData) {
    $dropdownDataNew =[];
    foreach ($dropdownData as $key => $value) {
	// if($value == '') {
	//     $dropdownDataNew[''] = 'Select Value';
	// } else {
	    $dropdownDataNew[$key] = ucfirst($value);
	// }
    }
    return $dropdownDataNew;
}

function displayLabelOnSurveyForm($section, $number) {

    $questionLabel = [
	'property' => 'P',
	'energy and carbon' => 'E',
	'water' => 'WR',
	'waste' => 'WE',
	'health and environment' => 'H',
	'sourcing and F and B' => 'S',
	'community and social' => 'C',
	'residences' => 'R'
    ];

    return $questionLabel[$section].'-'.$number;
}

function getLocalUnitFullText($id) {
    $key = 0;
    $unit_array = [
	0 => 'm&#178;',
	1 => 'ft&#178;'
    ];
    if(!empty($id)){
	$ci = &get_instance();
	//Type Casting
	$id = intval($id);
	$ci->db->where("id", $id);
	$ci->db->select('local_unit');
	$ci->db->from(TBL_SITES);

	$siteArray = $ci->db->get()->row();

	if (!empty($siteArray)) {
	    $key = $siteArray->local_unit;
	}
    }
    if($key == 0) {
	return 'meter';
    } else {
	return 'footage';
    }
}

function getLocalUnitText($id) {
    $key = 0;
    $unit_array = [
	0 => 'm&#178;',
	1 => 'ft&#178;'
    ];
    if(!empty($id)){
	$ci = &get_instance();
	//Type Casting
	$id = intval($id);
	$ci->db->where("id", $id);
	$ci->db->select('local_unit');
	$ci->db->from(TBL_SITES);

	$siteArray = $ci->db->get()->row();

	if (!empty($siteArray)) {
	    $key = $siteArray->local_unit;
	}
    } else {
	$key = !empty($_SESSION['admin']['local_unit']) ? $_SESSION['admin']['local_unit'] : 0;
    }

    return html_entity_decode($unit_array[$key]);
}

function getUtilityConstant() {
    $utility_types = [
	'electricity' => 'Electricity',
	'fuel_oil' => 'Fuel Oil/Diesel',
	'lpg' => 'LPG',
	'water' => 'Water',
	'natural_gas' => 'Natural Gas',
	'district_cooling' => 'District Cooling',
	'district_heating' => 'District Heating',
    ];
    return $utility_types;
}

function getUtilityPanelColor() {
    $utility_colors = [
	'electricity_heading' => 'background-image:linear-gradient(to bottom, #007856 0px, #007856 100%) !important;',
	'electricity' => 'border-color:#007856 !important;',
	'fuel_oil_heading' => 'background-image:linear-gradient(to bottom, #007856 0px, #007856 100%) !important;',
	'fuel_oil' => 'border-color:#007856 !important;',
	'lpg_heading' => 'background-image:linear-gradient(to bottom, #E0A800 0px, #E0A800 100%) !important;',
	'lpg' => 'border-color:#E0A800 !important;',
	'water_heading' => 'background-image:linear-gradient(to bottom, #007856 0px, #007856 100%) !important;',
	'water' => 'border-color:#007856 !important;',
	'natural_gas_heading' => 'background-image:linear-gradient(to bottom, #DC3545 0px, #DC3545 100%) !important;',
	'natural_gas' => 'border-color:#DC3545 !important;',
	'biomass_heading' => 'background-image:linear-gradient(to bottom, #59497c 0px, #59497c 100%) !important;',
	'biomass' => 'border-color:#59497c !important;',
	'district_cooling_heading' => 'background-image:linear-gradient(to bottom, #0069D9 0px, #0069D9 100%) !important;',
	'district_cooling' => 'border-color:#0069D9 !important;',
	'district_heating_heading' => 'background-image:linear-gradient(to bottom, #b74949 0px, #b74949 100%) !important;',
	'district_heating' => 'border-color:#b74949 !important;',
	'fleet_heading' => 'background-image:linear-gradient(to bottom, #b697cd 0px, #b697cd 100%) !important;',
	'fleet' => 'border-color:#b697cd !important;',
    ];
    return $utility_colors;
}

function getConsumptionConstant() {
    $consumption_constants = [
	1 => 'Charged by meter',
	2 => 'Split by floor %',
	3 => 'Split by fixed %'
    ];
    return $consumption_constants;
}

function getWasteTabData() {

    $tabData = [
	'General/Municipal Solid Waste #This refers to rubbish, garbage, or trash that is disposed of as general waste or municipal solid waste (MSW). #Landfill/Other' => [],
	'Waste to Energy # #WasteToEnergy' => [],
	'Recyclables #This refers to the collection of items that can be recycled. Select additional groupings from the below list based on the type of data that you are able to provide. #Recycling' => [
	    'Commingled recyclables #Is a single-stream (also known as “mixed recycling, “fully commingled” or “single-sort”) recycling refers to a system in which all paper fibers, plastics, metals, and other containers are mixed in a collection instead of being sorted by each section.This refers to single stream…' => [
		[
		    'label' => 'Bottles & cans',
		    'description' => 'When ‘Bottles and Cans’ are tracked and hauled as a subset of commingled recyclables, including glass jars/bottles, metal cans, and plastic bottles.Select this if bottles and cans are…'
		],
	    ],
	    'Paper & cardboard' => [
		[
		    'label' => 'Cardboard',
		    'description' => 'Cardboard boxes, fibreboard, mat board'
		],
		[
		    'label' => 'Paper',
		    'description' => 'Print paper, newspaper, magazines'
		],
	    ],
	    'Mixed glass #All types of glass, including glass bottles' => [],
	    'Mixed metals' => [
		[
		    'label' => 'Aluminum',
		    'name' => 'Alluminium',
		    'description' => 'Aluminum cans, aluminum bottles, clean foil or food service items'
		],
		[
		    'label' => 'Tin',
		    'description' => 'Tin cans, bottles or clean food service items'
		]
	    ],
	    'Plastics #This refers to commingled recyclable plastics. Select additional groupings from the below list if your waste hauler is able to provide detail on the types of plastics in your waste stream. Please note that not all of these types of plastic may be accepted by your recycling provider. We have listed Plastic 1 and 2 as the most commonly recycled plastics.' => [
		[
		    'label' => 'Plastic #1 (PET)',
		    'name' => 'PETE Plastic Bottles',
		    'description' => 'Could include clean beverage bottles, food bottles/jars, etc'
		],
		[
		    'label' => 'Plastic #2 (HDPE)',
		    'name' => 'HDPE',
		    'description' => 'Could include plastic milk containers, detergent bottles, buckets, etc'
		],
		[
		    'label' => 'Other plastics',
		    'description' => 'If your property recycles other types of plastic and your waste hauler can provide this breakdown, please use this category'
		]
	    ]
	],
	'Biodegradable #This refers to organic waste, including food waste and green/landscaping waste. Select additional groupings from the below list if you are able to provide detail on the types of organic waste in your waste stream.' => [
	    'Food waste' => [
		[
		    'label' => 'Solid food waste',
		    'description' => 'This may also include ‘wet waste’'
		],
		[
		    'label' => 'Leftover edible food',
		    'name' => 'Leftover Food',
		    'description' => 'Trimmed food and cooked leftovers that have been prepared but not served and are able to be donated for human consumption. Also includes, food tracked separately, usually for donation to external parties.'
		],
		[
		    'label' => 'Inedible parts',
		    'description' => 'Include if tracked separately'
		],
	    ],
	    'Liquid food waste #Liquid waste discarded directly via sewer or land applied without prior collection or treatment' => [],
	    'Kitchen grease #Include if tracked separately' => [],
	    'Green/landscaping waste #Encompasses all other types of organic waste, such as landscaping waste and cut flowers #Mixed organic' => [],
	],
	'Toiletries and durable goods # #Donations' => [
	    'Bath toiletries #This refers to used guest room toiletry items. Please indicate whether these items are thrown away, recycled, or donated. #Toiletry Donations' => [
		[
		    'label' => 'Bath amenity bottles',
		    'name' => 'Bottled Amenities',
		    'description' => 'Used bath amenity bottles, such as shampoo, conditioner, body wash or lotion. Items should be either fully cleaned out and recycled, or donated to an approved soap recycling partner, such as Clean the World'
		],
		[
		    'label' => 'Soap bars',
		    'description' => 'Used soap bars should be donated to an approved soap recycling partner, such as Clean the World. Do not donate used soap to unapproved vendors'
		],
	    ],
	    'Durable goods #Durable goods, either tracked collectively or by item, are waste sources found within properties that are often not routinely disposed of, such as FF&E items, and not considered ongoing consumable waste. These may be included if they represent a generally stable waste stream that will not skew the performance metrics over different time boundaries.' => [
		[
		    'label' => 'Pallet and crates',
		    'name' => 'Palettes and crates',
		    'description' => 'Wood & plastic shipping crates, wood & plastic shipping palettes'
		],
		[
		    'label' => 'E-waste',
		    'name' => 'E_waste',
		    'description' => 'Electronics such as tv, computers, monitors, calculators, telephones, cameras, photocopiers, printers, fax machines'
		]
	    ]
	],
	'Hazardous and Universal Waste' => [
	    'Hazardous Waste #Waste materials that may have a harmful effect on human health or the environment, such as household cleaners, paints, solvents, oil, pesticides, fertilizers, mercury items, and aerosol cans.' => [
		[
		    'label' => 'Liquid Hazardous Waste',
		    'description' => 'A subsection of hazardous waste, all items that are in liquid form such as household cleaners, liquid fertilizer, paints, and solvents.'
		],
		[
		    'label' => 'Medical waste',
		    'description' => 'Regulated medical waste that that may be contaminated by blood, body fluids or other potentially infectious materials, such as gloves, syringes and sharp items like needles.'
		],
		[
		    'label' => 'Other Hazardous Waste',
		    'description' => 'A subsection of hazardous waste, all items that are in solid form, such as mercury items, solid fertilizer and aerosol cans.'
		]
	    ],
	    'Universal waste' => [
		[
		    'label' => 'Batteries',
		    'description' => 'When batteries are tracked separately, otherwise they may be covered as a specific hazardous waste under the Universal Waste Grouping'
		],
		[
		    'label' => 'Light bulbs',
		    'description' => 'When light bulbs are tracked separately, otherwise they may be covered as a specific hazardous waste under the Universal Waste Grouping'
		],
		[
		    'label' => 'Light fixtures',
		    'description' => 'When light bulbs are tracked separately, otherwise they may be covered as a specific hazardous waste under the Universal Waste Grouping'
		],
	    ]
	],
	'Other materials' => [
	    'Textiles #Clothing, fabric, all textiles' => [],
	    'Wood #E.g. Wood building materials, logs, lumber, particle board' => [],
	    'Building construction #E.g: drywall, steel, framing, tiling, carpet, etc.Include if tracked separately, otherwise include in the other waste streams where it is disposed of #Building constructions' => [],
	    'Other #Any other material not included in above' => [],
	]
    ];
    return $tabData;
}

/**
 * Build site_waste column_key from tab label/key (same logic as admin_waste.php).
 */
function getWasteColumnKey($rawKey)
{
    $name = isset($rawKey) ? str_replace(' ', '_', str_replace(' & ', ' ', str_replace('(', '', str_replace(')', '', str_replace('/', ' ', strtolower($rawKey)))))) : '';
    if (strpos($name, '_#') !== false) {
        $parts = explode('_#', $name);
        $name = isset($parts[2]) ? $parts[2] : $parts[0];
    }
    return trim($name);
}

/**
 * Display label from getWasteTabData() key (text before first " #").
 */
function getWasteTabDisplayLabel($rawKey)
{
    if (strpos($rawKey, ' #') !== false) {
        $parts = explode(' #', $rawKey);
        return trim($parts[0]);
    }
    return trim($rawKey);
}

/**
 * Flat list of every category / group / stream node from getWasteTabData().
 * Used for emission-factor UI, import validation, and site_waste lookups.
 */
function getWasteTabDataFlatList()
{
    $tabData = getWasteTabData();
    $rows = [];

    foreach ($tabData as $categoryKey => $groups) {
        $categoryLabel = getWasteTabDisplayLabel($categoryKey);
        $categoryColumnKey = getWasteColumnKey($categoryKey);

        $rows[] = [
            'node_level' => 'category',
            'column_key' => $categoryColumnKey,
            'category_label' => $categoryLabel,
            'group_label' => null,
            'stream_label' => null,
            'display_label' => $categoryLabel,
        ];

        if (!is_array($groups) || empty($groups)) {
            continue;
        }

        foreach ($groups as $groupKey => $streams) {
            $groupLabel = getWasteTabDisplayLabel($groupKey);
            $groupColumnKey = getWasteColumnKey($groupKey);

            $rows[] = [
                'node_level' => 'group',
                'column_key' => $groupColumnKey,
                'category_label' => $categoryLabel,
                'group_label' => $groupLabel,
                'stream_label' => null,
                'display_label' => $groupLabel,
            ];

            if (!is_array($streams) || empty($streams)) {
                continue;
            }

            foreach ($streams as $stream) {
                if (!is_array($stream)) {
                    continue;
                }
                $nameMapping = isset($stream['name']) ? $stream['name'] : $stream['label'];
                $streamColumnKey = getWasteColumnKey($nameMapping);
                $streamLabel = isset($stream['label']) ? $stream['label'] : $nameMapping;

                $rows[] = [
                    'node_level' => 'stream',
                    'column_key' => $streamColumnKey,
                    'category_label' => $categoryLabel,
                    'group_label' => $groupLabel,
                    'stream_label' => $streamLabel,
                    'display_label' => $streamLabel,
                ];
            }
        }
    }

    return $rows;
}

/**
 * Matrix seed rows: each tab node × each typical destination (excl. None Select).
 */
function getWasteEmissionFactorMatrixSeed($yearId)
{
    $nodes = getWasteTabDataFlatList();
    $destinations = getWasteTypicalDestinationArray();
    $seed = [];

    foreach ($nodes as $node) {
        foreach ($destinations as $destinationId => $destinationLabel) {
            if ((int) $destinationId === 0) {
                continue;
            }
            $seed[] = [
                'year_id' => (int) $yearId,
                'column_key' => $node['column_key'],
                'node_level' => $node['node_level'],
                'category_label' => $node['category_label'],
                'group_label' => $node['group_label'],
                'stream_label' => $node['stream_label'],
                'typical_destination_id' => (int) $destinationId,
                'typical_destination_label' => $destinationLabel,
                'status' => 1,
            ];
        }
    }

    return $seed;
}

/**
 * Map Excel / display destination label to getWasteTypicalDestinationArray() index.
 */
function mapWasteTypicalDestinationLabelToId($label)
{
    $label = strtolower(trim($label));
    if ($label === '') {
        return 0;
    }

    $aliases = [
        'landfill' => 1,
        'recycling' => 2,
        'donation/repurposed consumables' => 3,
        'donation / repurpose' => 3,
        'donation/repurpose' => 3,
        'composting on/off site' => 4,
        'composting' => 4,
        'compost/anaerobic digestion' => 4,
        'waste to energy/biodigester' => 5,
        'waste to energy / efw' => 5,
        'waste to energy' => 5,
        'incineration/combustion' => 6,
        'incineration / combustion' => 6,
        'unknown' => 7,
    ];

    if (isset($aliases[$label])) {
        return (int) $aliases[$label];
    }

    foreach (getWasteTypicalDestinationArray() as $id => $destLabel) {
        if ((int) $id === 0) {
            continue;
        }
        if (strcasecmp(trim($destLabel), trim($label)) === 0) {
            return (int) $id;
        }
    }

    return 0;
}

/**
 * Resolve import/UI row to a getWasteTabDataFlatList() node.
 */
function resolveWasteEmissionFactorNode($categoryLabel, $nodeLabel)
{
    $categoryLabel = trim($categoryLabel);
    $nodeLabel = trim($nodeLabel);

    foreach (getWasteTabDataFlatList() as $node) {
        if (strcasecmp($node['category_label'], $categoryLabel) !== 0) {
            continue;
        }
        if (strcasecmp($node['display_label'], $nodeLabel) === 0) {
            return $node;
        }
        if (!empty($node['stream_label']) && strcasecmp($node['stream_label'], $nodeLabel) === 0) {
            return $node;
        }
        if (!empty($node['group_label']) && strcasecmp($node['group_label'], $nodeLabel) === 0) {
            return $node;
        }
    }

    return null;
}

function getWasteUnitMeasuresArray() {
	$unit_measures = [
		'None Select',
		'Metric Ton (MT) 1,000 kg',
		'Short Ton (tn) 2,000 Ib',
		'm&#179;',
		'Ft&#179;',
		'Litre',
		'Gallon',
		'Compactor, small (1.5 m&#179;)',
		'Compactor, medium (5 m&#179;)',
		'Compactor, large (9 m&#179;)',
		'Skip, small (3m&#179;)',
		'Skip, medium(6m&#179;)',
		'Skip, large (9m&#179;)',
		'Bin, 2-wheel (0.2 m&#179;)',
		'Bin, 4-wheel (0.8 m&#179;)',
		'Bag, small (0.1 m&#179;)',
		'Bag, large (0.5 m&#179;)',
		'Grease Drum (55gal)',
		'Kilograms',
		'Pounds',
		'Skip container 7m&#179;',
		'Compactor 18m&#179;',
		'Compactor 20m&#179;',
		'Compactor 25m&#179;',
		'Yards',
		'Pieces'
	];
	return $unit_measures;
}

function getWasteTypicalDestinationArray()
{
	$typical_destinations = [
		'None Select',
		'Landfill',
		'Recycling',
		'Donation/Repurposed Consumables',
		'Composting On/Off Site',
		'Waste to Energy/Biodigester',
		'Incineration/Combustion',    
		'Unknown'
	]; 
	return $typical_destinations;
}

function getWasteSourceArray()
{
	$sources = [
		'None Select',
		'All services',
		'Hotel services',
		'F&B services',
		'Residential services',
		'None',
		'Unknown'
	];
	return $sources;
}

function getWasteMonthlyTrackingArray() {
	$monthly_trackings = [
		'None Select',
		// 'By Waste Stream',
		// 'With Grouping',
		// 'With Destination',
		// 'Unknown',
		'Not Tracked',
		'Tracked'
	];
	return $monthly_trackings;
}

function calculatePercentage($value, $total)
{
	if ($total != 0) {
		return ($value * 100 / $total) / 1000;
	}
}

function calculateDashboardPercentage($value, $total)
{
	if ($total != 0) {
		return ($value * 100 / $total);
	}
}

function getNotificationStaticList($site_id = 0) {
    $site_id = isset($site_id) && !empty($site_id) ? $site_id : $_SESSION['admin']['site_id'];
    $site_notification_lists = array(
	// 'electricity_tariff'                 => 'Electricity tariff',
	// 'total_onsite_generators_cost'       => 'Onsite Generators Cost',
	// 'total_renewable_energy_production'  => 'Total Renewable Energy Production',
	// 'fuel_oil_hot_water_boilers_cost'    => 'Hot-Water Boilers ('.GetSiteUtilityUnitName($site_id,'fuel_oil').')',
	// 'fuel_oil_steam_boilers_cost'        => 'Steam Boilers ('.GetSiteUtilityUnitName($site_id,'fuel_oil').')',
	// 'fuel_oil_others_cost'               => 'Fuel Oil/Diesel Oil Others ('.GetSiteUtilityUnitName($site_id,'fuel_oil').')',
	// 'lpg_hot_water_boilers_cost'         => 'LPG Hot-Water Boilers ('.GetSiteUtilityUnitName($site_id,'lpg').')',
	// 'lpg_steam_boilers_cost'             => 'LPG Steam Boilers ('.GetSiteUtilityUnitName($site_id,'lpg').')',
	// 'lpg_kitchen_cost'                   => 'LPG Kitchen ('.GetSiteUtilityUnitName($site_id,'lpg').')',
	// 'natural_gas_hot_water_boilers_cost' => 'Natural Gas Hot-Water Boilers ('.GetSiteUtilityUnitName($site_id,'natural_gas').')',
	// 'natural_gas_steam_boilers_cost'     => 'Natural Gas Steam Boilers ('.GetSiteUtilityUnitName($site_id,'natural_gas').')',
	// 'natural_gas_kitchen_cost'           => 'Natural Gas Kitchen ('.GetSiteUtilityUnitName($site_id,'natural_gas').')',
	// 'district_heating_cost'              => 'District Heating ('.GetSiteUtilityUnitName($site_id,'district_heating').')',
	// 'district_cooling_cost'              => 'District Cooling ('.GetSiteUtilityUnitName($site_id,'district_cooling').')',
	// 'water_utility_supply_cost'          => 'Water Utility Supply ('.GetSiteUtilityUnitName($site_id,'water').')',
	// 'waste_water_cost'                   => 'Wastewater ('.GetSiteUtilityUnitName($site_id,'water').')',
	// 'water_Cisterns_cost'                => 'Water Cisterns ('.GetSiteUtilityUnitName($site_id,'water').')',
	// 'total_room_night'                   => 'Room Nights',
	// 'total_guests'                       => 'Total Guests',
	// 'total_laundered'                    => 'Laundry Load',
	// 'total_fb_services'                  => 'Food Covers',
	// 'cdd'                                => 'Cooling Degree Day',
	// 'hdd'                                => 'Heating Degree Day',
	'total_electricity_kwh'              => 'Total Electricity ('.GetSiteUtilityUnitName($site_id,'electricity').')',
	'total_fuel_oil'                     => 'Total Fuel Oil ('.GetSiteUtilityUnitName($site_id,'fuel_oil').')',
	'total_lpg'                          => 'Total LPG ('.GetSiteUtilityUnitName($site_id,'lpg').')',
	'water_total_consumption'            => 'Total Water ('.GetSiteUtilityUnitName($site_id,'water').')',
	'total_natural_gas'                  => 'Total Natural Gas ('.GetSiteUtilityUnitName($site_id,'natural_gas').')',
	'district_cooling'                   => 'Total District Cooling ('.GetSiteUtilityUnitName($site_id,'district_cooling').')',
	'district_heating'                   => 'Total District Heating ('.GetSiteUtilityUnitName($site_id,'district_heating').')',
    );
    return $site_notification_lists;
}
function getMmbtuFactorConversionAllUtility($site_id) {
	$dataFactor['electricity'] = getUtilityUnitFactorForConversion($site_id, 'electricity');
	$dataFactor['fuel_oil'] = getUtilityUnitFactorForConversion($site_id, 'fuel_oil');
	$dataFactor['lpg'] = getUtilityUnitFactorForConversion($site_id, 'lpg');
	$dataFactor['natural_gas'] = getUtilityUnitFactorForConversion($site_id, 'natural_gas');
	$dataFactor['district_heating'] = getUtilityUnitFactorForConversion($site_id, 'district_heating');
	$dataFactor['district_cooling'] = getUtilityUnitFactorForConversion($site_id, 'district_cooling');
	$dataFactor['water'] = getUtilityUnitFactorForConversion($site_id, 'water');
	return $dataFactor;
}

function formatNumberAbbreviation($number) {
    if ($number >= 1000000000) {
        return rtrim(rtrim(number_format($number / 1000000000, 2), '0'), '.') . 'b';
    } elseif ($number >= 1000000) {
        return rtrim(rtrim(number_format($number / 1000000, 2), '0'), '.') . 'm';
    } elseif ($number >= 1000) {
        return rtrim(rtrim(number_format($number / 1000, 2), '0'), '.') . 'k';
    }
    return rtrim(rtrim(number_format($number, 2), '0'), '.');
}

// Helpers for waste report page in monthly pdf 
function findTopCategory($needle, $haystack, $topCategory = null)
{
    $needleNorm = normalize(cleanKey($needle));

    foreach ($haystack as $rawKey => $value) {

        $cleanKeyNorm = normalize(cleanKey($rawKey));

        $currentTop = $topCategory ?? $cleanKeyNorm;

        // direct match
        if ($needleNorm === $cleanKeyNorm) {
            return $currentTop;
        }

        // check children
        if (is_array($value)) {
            foreach ($value as $child) {

                if (isset($child['label'])) {
                    if ($needleNorm === normalize(cleanKey($child['label']))) {
                        return $currentTop;
                    }
                }

                if (isset($child['name'])) {
                    if ($needleNorm === normalize(cleanKey($child['name']))) {
                        return $currentTop;
                    }
                }
            }

            // deeper recursive
            $result = findTopCategory($needle, $value, $currentTop);
            if ($result !== null) {
                return $result;
            }
        }
    }

    return null;
}
function groupSelectedItems($selectedItems, $site_id = 0, $year_id = 0, $month_id = 0)
{
    $grouped = [];
    $master  = getWasteTabData();

    foreach ($selectedItems as $item) {
        if ($parent = findTopCategory($item, $master)) {
            $grouped[$parent][] = $item;
        }
    }

    if (empty($grouped)) {
        return [];
    }

    $ci = &get_instance();

    // Whitelist parent columns
    $parents = array_keys($grouped);
    $selects = array_map(function ($p) {
		return "COALESCE(unit_measure_{$p}, 0) AS unit_measure_{$p}";
	}, $parents);


    $row = $ci->db
        ->select($selects, false)
        ->from('site_waste')
        ->where([
            'site_id'  => $site_id,
            'year_id'  => $year_id,
            'month_id' => $month_id
        ])
        ->get()
        ->row_array();

    foreach ($parents as $parent) {
        if (!empty($row["unit_measure_{$parent}"])) {
            $grouped[$parent] = [$parent];
        }
    }
    return $grouped;
}
function cleanKey($key) {
    // lower-case
    $name = strtolower($key);

    // remove parentheses
    $name = str_replace(['(', ')'], '', $name);

    // replace "/" with space
    $name = str_replace('/', ' ', $name);

    // replace " & " with space
    $name = str_replace(' & ', ' ', $name);

    // replace spaces with underscores
    $name = str_replace(' ', '_', $name);

    // now handle "#"
    // your logic: if "_#" appears → explode and take index 2 or 0
    if (strpos($name, '_#') !== false) {
        $parts = explode('_#', $name);

        // match your original logic exactly:
        $name = isset($parts[2]) ? trim($parts[2]) : trim($parts[0]);
    }

    return trim($name);
}
function normalize($str) {
    $str = preg_replace('/[^a-z0-9]+/', '_', $str);
    $str = preg_replace('/_+/', '_', $str); // collapse multiple underscores
    return trim($str, '_');
}
// END: Helpers for waste report page in monthly pdf s
function variance($current, $previous) {
	if ((float)$previous === 0.0) return 0;
	return round((($current - $previous) / $previous) * 100, 2);
}
function varianceImg($current, $previous)
{
    if ((float)$previous === 0.0) {
        return null;
    }

    $diff = (($current - $previous) / $previous) * 100;

    if ($diff > 0) return 'up';
    if ($diff < 0) return 'down';

    return null;
}

function addVarianceImage($sheet, $columnIndex, $rowNum, $direction, $invertArrowColors = false, $useGreenRedArrows = false)
{
    if (!$direction) return;

    if ($useGreenRedArrows) {
        $imageFile = ($direction === 'up') ? 'upArrowGreen.png' : 'downArrowRed.png';
    } elseif ($invertArrowColors) {
        $imageFile = ($direction === 'up') ? 'downArrow.png' : 'upArrow.png';
    } else {
        $imageFile = ($direction === 'up') ? 'upArrow.png' : 'downArrow.png';
    }
    $imagePath = FCPATH . 'themes/default/images/' . $imageFile;
    if (!file_exists($imagePath)) return;

    $cell = PHPExcel_Cell::stringFromColumnIndex($columnIndex) . $rowNum;

    $drawing = new PHPExcel_Worksheet_Drawing();
    $drawing->setPath($imagePath);

    // fixed icon size
    $drawing->setHeight(10);
    $drawing->setWidth(10);
    $drawing->setCoordinates($cell);

    $drawing->setOffsetX(20); 
    $drawing->setWorksheet($sheet);
}

function fmt($num, $decimal= 0) {
	return number_format((float)$num, $decimal, '.', ',');
}

function HeadingColors() {
	$baseStyle = [
        'alignment' => [
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap'       => true,
        ],
        'borders' => [
            'allborders' => [
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ],
        ],
        'font' => ['bold' => true],
    ];

    $styles = [

        'pink' => [
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'F4CCCC'],
            ],
        ],

        'green' => [
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'D9EAD3'],
            ],
        ],

        'blue' => [
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'CFE2F3'],
            ],
        ],

        'dark_blue' => [
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => '6D9EEB'],
            ],
            'font' => ['color' => ['rgb' => 'FFFFFF']],
        ],

        'peach_light' => [
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFF2CC'],
            ],
        ],

        'peach' => [
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'FCE5CD'],
            ],
        ],

        'brown_light' => [
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'D5A6BD'],
            ],
        ],

        'dark_orange' => [
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'E69138'],
            ],
            'font' => ['color' => ['rgb' => 'FFFFFF']],
        ],
    ];

	return ['baseStyle' => $baseStyle, 'styles' => $styles];
}

// Group Utility Report Cell Header Colors
function applyHeaderColors($sheet)
{
    $baseStyle = HeadingColors()['baseStyle'];
	$styles = HeadingColors()['styles'];
    $groups = [
        'A:H'   => 'pink',
        'I:K'   => 'green',
        'L:N'   => 'blue',
        'O:Q'   => 'dark_blue',
        'R:T'   => 'peach_light',
        'U:W'   => 'peach',
        'X:Z'   => 'brown_light',
        'AA:AC'  => 'dark_orange',
        'AD:AF' => 'green',
        'AG:AI' => 'peach',
        'AJ:AL' => 'peach',
        'AM:AO' => 'blue',
    ];

	foreach ($groups as $range => $color) {

		[$startCol, $endCol] = explode(':', $range);

		// Row 1 (Parent header)
		$sheet->getStyle("{$startCol}1:{$endCol}1")
			->applyFromArray(array_merge_recursive($baseStyle, $styles[$color]));

		// Row 2 (Child header)
		$sheet->getStyle("{$startCol}2:{$endCol}2")
			->applyFromArray(array_merge_recursive($baseStyle, $styles[$color]));
			
	}
}


function autoSizeColumns(PHPExcel_Worksheet $sheet)
{
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    // Auto-size columns A -> lastColumn
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    for ($col = 0; $col < $highestColumnIndex; $col++) {
        $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col))
              ->setAutoSize(true);
    }

}

function applyHeaderColorsWaste($sheet)
{
    $baseStyle = HeadingColors()['baseStyle'];
	$styles = HeadingColors()['styles'];
    $groups = [
        'A:A'   => 'pink',
        'B:D'   => 'dark_orange',
        'E:G'   => 'peach',
        'H:J'   => 'green',
        'K:M'   => 'blue',
        'N:P'   => 'dark_blue',
        'Q:S'   => 'peach_light',
        'T:V'   => 'peach',
        'W:Y'   => 'brown_light'
    ];

	foreach ($groups as $range => $color) {

		[$startCol, $endCol] = explode(':', $range);

		// Row 1 (Parent header)
		$sheet->getStyle("{$startCol}1:{$endCol}1")
			->applyFromArray(array_merge_recursive($baseStyle, $styles[$color]));

		// Row 2 (Child header)
		$sheet->getStyle("{$startCol}2:{$endCol}2")
			->applyFromArray(array_merge_recursive($baseStyle, $styles[$color]));
			
	}
}

function cron_log($message)
{
    $file = APPPATH . 'logs/cron_' . date('Y-m-d') . '.php';
    $line = date('Y-m-d H:i:s') . ' --> ' . $message . PHP_EOL;

    file_put_contents($file, $line, FILE_APPEND);
}

/**
 * Get Progress Widget Date Parameters
 * 
 * Returns the "current month" and "current year" for progress widget calculations.
 * Logic: current_month = running month - 1
 *        current_year = current year (or previous year if running month is January)
 * 
 * Example: If today is February 2026, returns ['month' => 1, 'year' => 2026]
 *          If today is January 2026, returns ['month' => 12, 'year' => 2025]
 * 
 * @return array ['month' => int, 'year' => int, 'previous_year' => int, 'baseline_previous_year' => int]
 */
function getProgressWidgetDateParams($selectedMonth = null, $selectedYear = null)
{
    if ($selectedMonth !== null && $selectedYear !== null) {
        $runningMonth = (int) $selectedMonth;
        $runningYear = (int) $selectedYear;
        
        if ($runningMonth == 1) {
            $currentMonth = 12;
            $currentYear = $runningYear - 1;
            $previousYear = $runningYear - 2;
        } else {
            $currentMonth = $runningMonth;
            $currentYear = $runningYear;
            $previousYear = $runningYear - 1;
        }
    } else {
        $runningMonth = (int) date('n');
        $runningYear = (int) date('Y');
        
        if ($runningMonth == 1) {
            $currentMonth = 12;
            $currentYear = $runningYear - 1;
            $previousYear = $runningYear - 2;
        } else {
            $currentMonth = $runningMonth - 1;
            $currentYear = $runningYear;
            $previousYear = $runningYear - 1;
        }
    }
    
    return [
        'month' => $currentMonth,
        'year' => $currentYear,
        'previous_year' => $previousYear,
        'running_month' => $runningMonth,
        'running_year' => $runningYear
    ];
}

/**
 * Calculate Progress On Target data for Energy, Water, Waste, and Carbon
 * 
 * @param array $progressOnTarget Raw progress data from database
 * @param int $current_month Current month (1-12)
 * @param int $current_year Current year
 * @param array $siteTargets Site annual targets with keys:
 *        - energy_intensity_annual_target
 *        - water_intensity_annual_target
 *        - ghg_intensity_annual_target
 *        - waste_intensity_annual_target
 * @param array $carbonData Carbon footprint data with keys:
 *        - carbon_footprint_currentMonth
 *        - carbon_footprint_SameMonthPreviousYear
 *        - ytd_carbon_footprint_new
 *        - ytd_carbon_footprintPreviousYear
 *        - ytd_carbon_footprint_baseline_new
 * @param array $progressOnTargetWasteYtd Optional waste YTD data with keys:
 *        - total_waste_target
 *        - total_waste_baseline_target
 * @return array Contains 'ProgressTargetPercentage' and supporting data
 */
function calculateProgressOnTarget($progressOnTarget, $current_month, $current_year, $siteTargets, $carbonData, $progressOnTargetWasteYtd = [])
{
    $previousYear = $current_year - 1;
    $baselineYear = $siteTargets['baseline_regression_year'] ?? null;

    // Fetch data safely
    $current  = $progressOnTarget[$current_year] ?? [];
    $previous = $progressOnTarget[$previousYear] ?? [];
    $baseline = $progressOnTarget[$baselineYear] ?? [];
	// echo ($progressOnTarget[$baselineYear]['energy']);
    // ---------------------------
    // Progress Target Structure
    // ---------------------------
	// dd($carbonData);
	// echo $carbonData['carbon_footprint_currentMonth'] ."====". $carbonData['ytd_carbon_footprint_baseline_new'];exit;
    $progressTarget = [
        'energy' => [
            'energy_YTD' => $current['energy'] ?? 0,
            'energy_last_YTD' => $previous['energy'] ?? 0,
            'energy_baseline_YTD' => $baseline['energy'] ?? 0
        ],
		'carbon' => [
            'carbon_monthly' => $carbonData['carbon_footprint_currentMonth'] ?? 0,
            'carbon_last_monthly' => $carbonData['carbon_footprint_SameMonthPreviousYear'] ?? 0,
            'carbon_YTD' => $carbonData['ytd_carbon_footprint_new'] ?? 0,
            'carbon_last_YTD' => $carbonData['ytd_carbon_footprintPreviousYear'] ?? 0,
            'carbon_baseline_YTD' => $carbonData['ytd_carbon_footprint_baseline_new'] ?? 0
        ],
        'water' => [
            'water_YTD' => $current['water'] ?? 0,
            'water_last_YTD' => $previous['water'] ?? 0,
            'water_baseline_YTD' => $baseline['water'] ?? 0
        ],
        /* Diversion % = (Recyclables + Waste to Energy) / Total waste — typical_destination 2 & 5 */
        'waste' => [
            'waste_YTD' => (!empty($current['total_waste_target']))
                ? (($current['waste_diversion_numerator'] ?? 0) / $current['total_waste_target']) * 100 : 0,

            'waste_last_YTD' => (!empty($previous['total_waste_target']))
                ? (($previous['waste_diversion_numerator'] ?? 0) / $previous['total_waste_target']) * 100 : 0,

            'waste_baseline_YTD' => (!empty($baseline['total_waste_target']))
                ? (($baseline['waste_diversion_numerator'] ?? 0) / $baseline['total_waste_target']) * 100 : 0
        ],        
    ];

    // ---------------------------
    // Target Config
    // ---------------------------
    $targetConfig = [
        'energy' => ['key' => 'Energy', 'target' => $siteTargets['energy_intensity_annual_target'] ?? 0],
        'carbon' => ['key' => 'Carbon', 'target' => $siteTargets['ghg_intensity_annual_target'] ?? 0],
        'water'  => ['key' => 'Water',  'target' => $siteTargets['water_intensity_annual_target'] ?? 0],
        'waste'  => ['key' => 'Waste',  'target' => $siteTargets['waste_intensity_annual_target'] ?? 0]
    ];

    // ---------------------------
    // Calculate Percentages
    // ---------------------------
    $ProgressTargetPercentage = [];

    foreach ($targetConfig as $type => $config) {

        $data = $progressTarget[$type];
        $key = $config['key'];
        $annualTarget = $config['target'];

        $currentVal = $data[$type . '_YTD'] ?? 0;
        $lastVal    = $data[$type . '_last_YTD'] ?? 0;
        $baselineVal = $data[$type . '_baseline_YTD'] ?? 0;

		// YTD comparison
        $differenceYTD = $lastVal - $currentVal;

        $ProgressTargetPercentage[$key]['YTD'] =
            calculateDashboardPercentage($differenceYTD, $lastVal);

        $ProgressTargetPercentage[$key]['ACTUAL_YTD'] = $currentVal;
        $ProgressTargetPercentage[$key]['TARGET_BASELINE_YTD'] = $baselineVal;
        $ProgressTargetPercentage[$key]['site_saving_target'] = $annualTarget;

        // Target YTD = Baseline reduced by annual target %
        // Energy/Carbon/Water: absolute consumption; Waste diversion % kept as baseline for col 2
        // e.g. annual target 3 => Target = Baseline * 0.97
        if (is_numeric($annualTarget)) {
            $ProgressTargetPercentage[$key]['TARGET_YTD'] =
                $baselineVal * (1 - ((float) $annualTarget / 100));
        } else {
            $ProgressTargetPercentage[$key]['TARGET_YTD'] = $baselineVal;
        }

    }

    // ---------------------------
    // Waste Extra Fields
    // ---------------------------
    $ProgressTargetPercentage['Waste']['WASTE_DIVERSION_NUMERATOR_YTD'] =
        $current['waste_diversion_numerator'] ?? 0;

    $ProgressTargetPercentage['Waste']['WASTE_DIVERSION_NUMERATOR_BASELINE_YTD'] =
        $baseline['waste_diversion_numerator'] ?? 0;

    $ProgressTargetPercentage['Waste']['TOTAL_WASTE_YTD'] =
        $progressOnTargetWasteYtd['total_waste_target'] ?? 0;

    $ProgressTargetPercentage['Waste']['TOTAL_WASTE_BASELINE_YTD'] =
        $progressOnTargetWasteYtd['total_waste_baseline_target'] ?? 0;

    // Waste Target YTD (absolute) for /RN intensity = baseline total waste * (1 - target%)
    $wasteAnnualTarget = (float) ($siteTargets['waste_intensity_annual_target'] ?? 0);
    $wasteBaselineTotal = (float) ($progressOnTargetWasteYtd['total_waste_baseline_target'] ?? 0);
    $ProgressTargetPercentage['Waste']['TOTAL_WASTE_TARGET_YTD'] =
        $wasteBaselineTotal * (1 - ($wasteAnnualTarget / 100));

    // Waste TARGET_BASELINE_YTD stays as baseline diversion % (col 2);
    // TARGET_YTD for Waste diversion is not used for intensity — intensity uses TOTAL_WASTE_TARGET_YTD
    $ProgressTargetPercentage['Waste']['TARGET_YTD'] = $ProgressTargetPercentage['Waste']['TARGET_BASELINE_YTD'];

    // ---------------------------
    // Final Return
    // ---------------------------
    return [
        'ProgressTargetPercentage' => $ProgressTargetPercentage,
        'progressTarget' => $progressTarget,

        'progress_roomnight_YTD' => $current['room_night'] ?? 0,
        'progress_last_roomnight_YTD' => $previous['room_night'] ?? 0,
        'progress_baseline_roomnight_YTD' => $baseline['room_night'] ?? 0,

        'progress_guestnight_YTD' => $current['guest_night'] ?? 0,
        'progress_last_guestnight_YTD' => $previous['guest_night'] ?? 0,
        'progress_baseline_guestnight_YTD' => $baseline['guest_night'] ?? 0,

        'waste_diversion_numerator_YTD' => $current['waste_diversion_numerator'] ?? 0,
        'waste_diversion_numerator_Baseline_YTD' => $baseline['waste_diversion_numerator'] ?? 0,
        'total_waste_YTD' => $current['total_waste_target'] ?? 0,
        'total_waste_Baseline_YTD' => $baseline['total_waste_target'] ?? 0
    ];
}