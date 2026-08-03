<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
/*$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;*/
//default controller to set
//$route['default_controller'] = "cms/defaultpage";  //change admin url to front
$route['default_controller'] = "users/users_admin/login"; //change admin url to front
$route['404_override'] = '';

//URL Management
include_once( APPPATH .'config/custom_routes'. EXT );

// quarterly cron route
$route['quarterly-report'] = 'reportscron/quarterlycron_admin/index';

$route['forum/posts/(:any)'] = 'forum/posts/$1'; //call to front forum page

//mapping of manual urls for admin section from modules folder
$route['(.+?)/((.+))'] = '$1/$1_admin/$2'; //change admin url to front
$route['(:any)'] = '$1/$1_admin'; //change admin url to front


$route['services'] = 'cms/index/services'; //call to cms page
$route['about-us'] = 'cms/index/about-us'; //call to cms page

$route['forum/forum_listing'] = 'forum/forum_listing'; //call to front forum page
$route['forum/action'] = 'forum/action'; //call to front forum page
$route['forum/myforum'] = 'forum/myforum'; //call to front forum page
$route['forum/mycontribution'] = 'forum/mycontribution'; //call to front forum page
$route['forum/today_thread'] = 'forum/today_thread'; //call to front forum page
$route['forum/popular_post'] = 'forum/popular_post'; //call to front forum page
$route['discussion-rules'] = 'forum/discussion_rules'; //call to front forum page


//$route['admin/(.+?)/((.+))'] = '$1/$1_admin/$2';
//$route['admin/(:any)'] = '$1/$1_admin';
//$route['admin'] = "users/users_admin/login";


//map all urls with core routing by their name which are not mapped above
//$route['^(en|es|ar)/(.+)$'] = "$2";
//$route['^(en|es|ar)$'] = $route['default_controller'];

global $CFG;
if ($CFG->item('multilang_option') == 1) {

    $languages_list = get_languages(); // Fn defines in functions.php in core folder

    $route['^(' . $languages_list . ')/(.+)$'] = "$2";
    $route['^(' . $languages_list . ')$'] = $route['default_controller'];
} else if ($CFG->item('multilang_option') == 0) {
    $route['/(:any)'] = "/$1";
}

//
//    // '/en', '/de', '/fr' and '/nl' URIs -> use default controller
//    $route['^(en|es|ar|nl)$'] = $route['default_controller'];
/* End of file routes.php */
/* Location: ./application/config/routes.php */