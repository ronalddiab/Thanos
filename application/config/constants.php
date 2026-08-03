<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');
//by vivek product module
define("PRODUCT_MODULE_NO", 5);

define('STAR_MANDATORY',"<span class='starmandatory'>*</span>");
define('MODERATOR_DEFAULT_PERMISSION', "1,2,322,323,325,326,327,328,329,330,331,335,337");

//include database config
require_once( APPPATH . 'config/custom_config' . EXT);
require_once( APPPATH . 'config/db_constants' . EXT);

//require_once( BASEPATH .'database/DB'. EXT );
require_once( APPPATH . 'core/MY_DB' . EXT );

define('BASE_PATH_CUSTOM', SITE_BASE_URL);
define('BASE_ADMIN_URL_CUSTOM', '');
// define('BASE_URL_CUSTOM', '');

define('QUARTERLY_REPORT', 'quarterly');

define('ELECTRICITY', "kWh");
define('FUEL_OIL', "Liter");
define('LPG', "Kg,Liters,CCF,Therms,mmBTU,kWh");
define('WATER', "m3,ft3,CCF,IG,USG");
define('NATURAL_GAS', "m3,ft3,CCF,Therms,mmBTU");
define('DISTRICT_COOLING', "kWh,mmBTU,RTH");
define('DISTRICT_HEATING', "kWh,Steam lb,Steam Mlbs,mmBTU");

define('EMISSION_FACTOR_UNIT_PERCENTAGE', '%');
define('EMISSION_FACTOR_UNIT_WASTE_MT', 'KgCO2e/MT');
define('EMISSION_FACTOR_UNIT_PASSENGER_KM', 'KgCO2e/passenger-km');
define('EMISSION_FACTOR_UNIT_KM', 'KgCO2e/km');
define('EMISSION_FACTOR_UNIT_KG_LAUNDRY', 'KgCO2e/kg laundry');
define('EMISSION_FACTOR_UNIT_PURCHASED_GOODS', 'KgCO2e/unit, KgCO2e/kg or KgCO2e/currency');
define('EMISSION_FACTOR_UTILITY_ELECTRICITY', 'electricity');
define('EMISSION_FACTOR_UTILITY_FUEL_OIL', 'fuel_oil');
define('EMISSION_FACTOR_UTILITY_LPG', 'lpg');
define('EMISSION_FACTOR_UTILITY_NATURAL_GAS', 'natural_gas');
define('EMISSION_FACTOR_UTILITY_DISTRICT_COOLING', 'district_cooling');
define('EMISSION_FACTOR_UTILITY_DISTRICT_HEATING', 'district_heating');

define('QUESTION_TYPES', "dropdown,radio,textbox,textarea,checkbox,file,multiselect");
define('QUESTION_SECTIONS',"property|energy and carbon|water|health and environment|sourcing and F and B|community and social|waste|residences");
define('QUESTION_SOURCES',"corporate|tripadvisor|google|booking.com");


define('RENTAL_PROGRAM_RESIDENCE', 1);
define('PRIVATE_RESIDENCE', 2);
define('EMPLOYEE_LIVING_QUARTERS', 3);
define('EMPLOYEE_LIVING_QUARTERS_OFFSITE',4);

/* End of file constants.php */
/* Location: ./application/config/constants.php */