<?php



/**

 *  CRON Controller (Front)

 *

 *  To perform login,registration and forgot password process.

 *

 * @package CIDemoApplication

 * @subpackage Users

 * @copyright    (c) 2013, TatvaSoft

 * @author panks

 */

class Cron_admin extends Base_Admin_Controller {



    private $device_params = array('instUnitID', 'instShowValue', 'instStoreMinValue', 'instStoreAvgValue', 'instStoreMaxValue', 'instMinValue', 'instNominalValue', 'instMaxValue', 'accUnitID', 'accStoreValue', 'control', 'unitID', 'setPointValue', 'setPointMinValue', 'setPointMaxValue', 'percentcontrol', 'progress', 'calibrate', 'lock', 'redcontrol', 'redpercent', 'greencontrol', 'greenpercent', 'bluecontrol', 'bluepercent', 'whitecontrol', 'whitepercent');



    public function __construct() {

	parent::__construct();



	//load helpers

	$this->load->helper(array('url', 'cookie', 'captcha'));

	$this->load->library('form_validation');

	//$this->access_control($this->access_rules());

    }



    /**

     * Function access_rules to check login

     */

    private function access_rules() {

	return array(

	    array(

		'actions' => array('index', 'updateDailyCddHdd', 'updateMonthlyCddHdd', 'updateYearlyCddHdd'),

		'users' => array('*'),

	    ),

	    array(

		'actions' => array('alertForMonthlyEmptyRecords', 'monthlyUtilityConsumptionComparisionAlert', 'monthUtilityCostVsBudgetAlert', 'cumulativeUtilitiesConsumption', 'ytdUtilityCostVsBudgetAlert'),

		'users' => array('@'),

	    ),

	);

    }



    /*

     *  Function index

     */



    public function index($siteID) {

	if (!isset($siteID)) {

	    redirect("/");

	}



	//Data insertion starts

	//        $this->load->model('parameters/parameters_model');

	//        $parametersData = $this->parameters_model->get_parameters_data();

	//        //pre($parametersData);

	//

	//        foreach($parametersData as $parameter)

	//        {

	//            $dataArray = array();

	//            $dataArray['device_id'] = 81;

	//            $dataArray['parameter_id'] = $parameter['id'];

	//

	//            //load site model

	//            $this->load->model('device_parameters/device_parameters_model');

	//            $this->device_parameters_model->save_records($dataArray);

	//        }

	//        pre($parametersData);

	//Data insertion ends

	die('process intiated');

	//load site model

	$this->load->model('sites/sites_model');

	$siteData = $this->sites_model->get_site_detail_custom($siteID);



	//load devices model

	$this->load->model('devices/devices_model');

	$deviceData = $this->devices_model->get_device_data($siteID);



	//load site_zone_devices model

	$this->load->model('site_zone_devices/site_zone_devices_model');



	$gTokens = $objects = $jsonData = array();



	$gTokens['vGatewayId'] = $siteData['v_gateway_id'];

	$gTokens['gToken'] = $siteData['g_token'];



	foreach ($deviceData as $deviceDetails) {

	    $parametersArray = $this->device_params;



	    foreach ($parametersArray as $parameter) {

		$objects['vGatewayId'] = $siteData['v_gateway_id'];

		$objects['idname'] = $deviceDetails['device_code'] . "." . $parameter;

		$objects['data'] = "";



		$jsonData['gTokens'] = array($gTokens);

		$jsonData['objects'] = array($objects);



		$dataString = json_encode($jsonData);

		//$dataString = '{"gTokens": [{"vGatewayId": '.$siteData['v_gateway_id'].',"gToken": "'.$siteData['g_token'].'"}],"objects": [{"vGatewayId": '.$siteData['v_gateway_id'].',"idname": "BUILDING_MEN_TOILET_LIGHTING__CONTROL_.instShowValue","data": ""}]}';



		$ch = curl_init('http://iot.domaticasolutions.com:8443/api/v1/operation/monitorControl/get');

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

		curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(

		    'Content-Type: application/json',

		    'Content-Length: ' . strlen($dataString))

		);



		$result = curl_exec($ch);



		$resultArray = json_decode($result, true);



		if ($resultArray['objects'][0]['data'] != '[FAIL]') {

		    $dataArray = array();

		    $dataArray['day'] = date('d');

		    $dataArray['month'] = date('m');

		    $dataArray['year'] = date('Y');

		    $dataArray['created'] = date('Y-m-d H:i:s');



		    pre($dataArray);

		    $siteData = $this->site_zone_devices_model->save_records($data);

		    die('result fail');

		}

		pre($resultArray['objects'][0]);

	    }

	}

    }



    public function updateDailyCddHdd($id = 0) {



	/* ini_set('display_errors', 1);

	  error_reporting(E_ALL); */



	$this->load->model('cron/cron_model');

	$sites = $this->cron_model->get_all_sites_for_location($id);

	$dataList = array();



	if (!empty($sites)) {

	    foreach ($sites as $site) {



		$stationId = $site['station_id'];



		$baseCdd = floatval($site['base_cdd_temprature']);

		$baseHdd = floatval($site['base_hdd_temprature']);



		$baseCddTemprature = (!empty($baseCdd)) ? $baseCdd : 20.5;

		$baseHddTemprature = (!empty($baseHdd)) ? $baseHdd : 15.5;



		if (empty($stationId)) {

		    continue;

		}



		$postXml = '<LocationDataRequest>

		    <StationIdLocation>

			<StationId>' . $stationId . '</StationId>

		    </StationIdLocation>

		    <DataSpecs>

			<DatedDataSpec key="dailyHDD">

			    <HeatingDegreeDaysCalculation>

				<CelsiusBaseTemperature>' . $baseHddTemprature . '</CelsiusBaseTemperature>

			    </HeatingDegreeDaysCalculation>

			    <DailyBreakdown>

				<LatestValuesPeriod>

				    <NumberOfValues>31</NumberOfValues>

				</LatestValuesPeriod>

			    </DailyBreakdown>

			</DatedDataSpec>



			<DatedDataSpec key="dailyCDD">

			    <CoolingDegreeDaysCalculation>

				<CelsiusBaseTemperature>' . $baseCddTemprature . '</CelsiusBaseTemperature>

			    </CoolingDegreeDaysCalculation>

			    <DailyBreakdown>

				<LatestValuesPeriod>

				    <NumberOfValues>31</NumberOfValues>

				</LatestValuesPeriod>

			    </DailyBreakdown>

			</DatedDataSpec>

		    </DataSpecs>

		</LocationDataRequest>';



		$result = $this->callCddHddApi($postXml);



		if (isset($result->Failure) && !empty($result->Failure->Code->__toString())) {

		    continue;

		}



		foreach ($result->LocationDataResponse->DataSets->DatedDataSet[0]->Values->V as $v) {

		    $date = $v->attributes()->d->__toString();

		    $value = $v->__toString();

		    $dataList[$site['id']][$date]['hdd'] = $value;

		}



		foreach ($result->LocationDataResponse->DataSets->DatedDataSet[1]->Values->V as $v) {

		    $date = $v->attributes()->d->__toString();

		    $value = $v->__toString();

		    $dataList[$site['id']][$date]['cdd'] = $value;

		}

	    }

	}

	if (!empty($dataList)) {

	    $this->cron_model->insert_daily_utilities_cdd($dataList);

	}



	echo 'End';

	exit;

    }



    public function updateMonthlyCddHdd($id = 0) {

	ini_set('display_errors', 1);

	error_reporting(E_ALL);



	$myFile = "logs.txt";

	$fh = fopen($myFile, 'a') or die("can't open file");

	$stringData = "from get_image_from_uri function start " . date("Y-m-d H:i:s") . "\n";

	fwrite($fh, $stringData);

	fclose($fh);



	$lastmonth = date('Y-m-d', mktime(0, 0, 0, date("m") - 1, 1, date("Y")));

	$currentmonth = date('Y-m-d', mktime(0, 0, 0, date("m"), 1, date("Y")));



	$this->load->model('cron/cron_model');

	$sites = $this->cron_model->get_all_sites_for_location($id);

	$dataList = array();



	if (!empty($sites)) {

	    foreach ($sites as $site) {



		$stationId = $site['station_id'];



		$baseCdd = floatval($site['base_cdd_temprature']);

		$baseHdd = floatval($site['base_hdd_temprature']);



		$baseCddTemprature = (!empty($baseCdd)) ? $baseCdd : 20.5;

		$baseHddTemprature = (!empty($baseHdd)) ? $baseHdd : 15.5;



		if (empty($stationId)) {

		    continue;

		}



		$postXml = '<LocationDataRequest>

		    <StationIdLocation>

			<StationId>' . $stationId . '</StationId>

		    </StationIdLocation>

		    <DataSpecs>

			<DatedDataSpec key="dailyHDD">

			    <HeatingDegreeDaysCalculation>

				<CelsiusBaseTemperature>' . $baseHddTemprature . '</CelsiusBaseTemperature>

			    </HeatingDegreeDaysCalculation>

			    <MonthlyBreakdown>

				<DayRangePeriod>

				    <DayRange first="' . $lastmonth . '" last="' . $currentmonth . '"/>

				</DayRangePeriod>

			    </MonthlyBreakdown>

			</DatedDataSpec>



			<DatedDataSpec key="dailyCDD">

			    <CoolingDegreeDaysCalculation>

				<CelsiusBaseTemperature>' . $baseCddTemprature . '</CelsiusBaseTemperature>

			    </CoolingDegreeDaysCalculation>

			    <MonthlyBreakdown>

				<DayRangePeriod>

				    <DayRange first="' . $lastmonth . '" last="' . $currentmonth . '"/>

				</DayRangePeriod>

			    </MonthlyBreakdown>

			</DatedDataSpec>

		    </DataSpecs>

		</LocationDataRequest>';



		$result = $this->callCddHddApi($postXml);

		if (isset($result->Failure) && !empty($result->Failure->Code->__toString())) {

		    log_message('error', 'updateMonthlyCddHdd: CDD/HDD API failure for site_id ' . $site['id'] . ', code ' . $result->Failure->Code->__toString());

		    continue;

		}

		$dataSets = $result->LocationDataResponse->DataSets ?? null;

		if (empty($dataSets)) {
			log_message('error', 'updateMonthlyCddHdd: no DataSets for site_id ' . $site['id']);
			continue;
		}

		// Check for failures on either dataset
		$hasFailure = false;
		if (isset($dataSets->Failure)) {
			foreach ($dataSets->Failure as $failure) {
				$key = (string) $failure->attributes()->key;
				$code = (string) $failure->Code;
				$msg = (string) $failure->Message;
				log_message('error', "updateMonthlyCddHdd: API failure for site_id {$site['id']} key={$key} code={$code} msg={$msg}");
				$hasFailure = true;
			}
		}

		if ($hasFailure || !isset($dataSets->DatedDataSet)) {
			continue;
		}

		foreach ($result->LocationDataResponse->DataSets->DatedDataSet[0]->Values->V as $v) {

		    $date = $v->attributes()->d->__toString();

		    $value = $v->__toString();

		    $dataList[$site['id']][$date]['hdd'] = $value;

		}

		foreach ($result->LocationDataResponse->DataSets->DatedDataSet[1]->Values->V as $v) {

		    $date = $v->attributes()->d->__toString();

		    $value = $v->__toString();

		    $dataList[$site['id']][$date]['cdd'] = $value;

		}

	    }

	}



	if (!empty($dataList)) {

	    $this->cron_model->insert_monthly_utilities_cdd($dataList);

	}



	echo 'End';

	exit;

    }



    public function base64url_encode($unencoded) {

	return rtrim(strtr(base64_encode($unencoded), '+/', '-_'), '=');

    }



    public function callCddHddApi($postXml = '') {



	$accountKey = '293k-75q4-qzgm';

	$securityKey = 'fq9x-j53a-6k4x-nbvy-yvkh-3u99-nkhf-hkpw-3gwb-74rf-hmqr-f4yr-by2d';

	$url = 'http://apiv1.degreedays.net/xml';



	date_default_timezone_set('UTC');

	//$timestamp = date("Y-m-d\TH:i:s\Z", strtotime("+29 minutes"));

	$timestamp = gmdate("Y-m-d\TH:i:s\Z");

	$random = uniqid();



	$requestXml = '

	<RequestEnvelope>

	    <SecurityInfo>

		<Endpoint>' . $url . '</Endpoint>

		<AccountKey>' . $accountKey . '</AccountKey>

		<Timestamp>' . $timestamp . '</Timestamp>

		<Random>' . $random . '</Random>

	    </SecurityInfo>

	    ' . $postXml . '

	</RequestEnvelope>';



	$signatureBytes = hash_hmac('sha256', $requestXml, $securityKey, true);



	$requestParameters = array(

	    'request_encoding' => 'base64url',

	    'signature_method' => 'HmacSHA256',

	    'signature_encoding' => 'base64url',

	    'encoded_request' => $this->base64url_encode($requestXml),

	    'encoded_signature' => $this->base64url_encode($signatureBytes),

	);



	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_POST, true);

	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestParameters));

	if (defined('CURLOPT_ENCODING')) {

	    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

	}

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$responseXml = curl_exec($ch);
    $curlErrno = curl_errno($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // TEMP DEBUG - remove once root cause confirmed
    $fh = fopen('logs.txt', 'a');
    fwrite($fh, date("Y-m-d H:i:s") . " callCddHddApi curl_errno=$curlErrno curl_error=$curlError http_code=$httpCode\n");
    fwrite($fh, "raw_response: " . substr((string)$responseXml, 0, 2000) . "\n---\n");
    fclose($fh);

    if ($curlErrno !== 0 || empty($responseXml)) {
        log_message('error', 'callCddHddApi: curl failed - errno ' . $curlErrno . ' - ' . $curlError);
        return false;
    }

    try {
        $result = new SimpleXMLElement($responseXml);
    } catch (Exception $e) {
        log_message('error', 'callCddHddApi: XML parse failed - ' . $e->getMessage());
        return false;
    }

	return $result;

    }



    //url : /hotel_portal/cron/alertForMonthlyEmptyRecords

    public function alertForMonthlyEmptyRecords($date = '') {

	if (date('m') == 1) {

	    $month = 12;

	    $year = date('Y') - 1;

	} else {

	    $month = date('m') - 1;

	    $year = date('Y');

	}
	$date = isset($date) && !empty($date) ? $date : date('d');

	$alert_date = new DateTime( $date . "-" . $month . "-" . $year);

	/* if (date('d') < 5) {

	  echo "please run the command after DATE : <strong>" . date("d-m-Y", mktime(0, 0, 0, date('m'), 5, date('Y'))) . "</strong>";

	  exit;

	  } */



	$this->load->model('cron_model');

	$this->load->model('utilities/utilities_model');

	$user_sites = $this->cron_model->getUserSites();

	// Region-based site mapping used for Corporate users (role_id = 6).
	$corporate_user_sites = $this->cron_model->getCorporateUserSites();

	$sites = $this->cron_model->get_sites_list();

	$notifications = $this->cron_model->getUserCronNotifications();

	$users = $this->cron_model->getUserList();


	//month and year filter for utilities

	$this->utilities_model->utilities_month = $month;

	$this->utilities_model->utilities_year = $year;



	$NoDataAvailableSites = array();

	foreach ($sites as $siteKey => $site) {

	    $this->utilities_model->site_id = $siteKey;

	    $utilities = $this->utilities_model->getUtility();

		if (empty($utilities) || $utilities['total_electricity_kwh'] == 0) {

		$NoDataAvailableSites[] = $siteKey;

	    }

	}



	//Hotel detail

	$this->load->model('hotels/hotels_model');

	$hotel_detail = $this->hotels_model->get_hotel_detail(1);



	$this->load->library('mailer');

	$this->mailer->mail->IsHTML(true);



	// Role id for Corporate users. Corporate users receive ONE combined email
	// listing all sites they are responsible for that have not uploaded monthly
	// data, instead of one email per site.
	$corporate_role_id = 6;

	foreach ($users as $user) {
	    if (empty($notifications[$user['id']]) || !in_array('monthly_alert', $notifications[$user['id']])) {
			continue;
	    }

	    // Build the full set of sites this user is responsible for.
	    // Non-corporate users: direct site assignments from user_sites.
	    // Corporate users: direct site assignments + sites derived from their assigned regions.
	    $assigned_sites = isset($user_sites[$user['id']]) && is_array($user_sites[$user['id']]) ? $user_sites[$user['id']] : array();

	    if ($user['role_id'] == $corporate_role_id && isset($corporate_user_sites[$user['id']])) {
			$assigned_sites = $assigned_sites + $corporate_user_sites[$user['id']];
	    }

	    if (empty($assigned_sites)) {
			continue;
	    }

	    if ($user['role_id'] == $corporate_role_id) {
			// Corporate user: send ONE combined email listing all empty sites.
			$emptyAssignedSites = array();
			foreach ($assigned_sites as $site_id => $site_name) {
				if (in_array($site_id, $NoDataAvailableSites)) {
				$emptyAssignedSites[$site_id] = $site_name;
				}
			}

			if (empty($emptyAssignedSites)) {
				continue;
			}

			$subject = 'Empty Utilities Alert as on ' . $alert_date->format('d F Y') . ' - ' . $hotel_detail['hotel_name'];

			$bodyHtml  = '<div><h4>Dear ' . $user['firstname'] . ' ' . $user['lastname'] . '</h4></div>';

			$bodyHtml .= '<div>This is a kind reminder to update the utilities and waste data of <strong>' . $alert_date->format('F, Y') . '</strong> for the following <strong>' . $hotel_detail['hotel_name'] . '</strong> properties that have not yet uploaded their monthly data:</div>';

			$bodyHtml .= '<ul>';

			foreach ($emptyAssignedSites as $site_name) {

				$bodyHtml .= '<li>' . $site_name . '</li>';

			}
			$bodyHtml .= '</ul>';
			$bodyHtml .= '<div><a href="' . base_url() . '">Click here</a> to access the HEP portal. <br/>Monthly reporting of energy, water, and waste data is required for all properties. The HEP platform can be used to help manage utility consumption, with the potential to drive significant savings by reducing utility costs. If you have any questions or concerns, email rotana.support@heportal.net for technical assistance with the platform.</div>';
			$email_template['html'] = $bodyHtml;
			$body = $this->load->view('email_template', $email_template, true);
			$this->mailer->mail->AddAddress($user['email']);
			$this->mailer->mail->Subject = $subject;
			$this->mailer->mail->Body = $body;
			$this->mailer->mail->Send();
			$this->mailer->mail->ClearAllRecipients();

	    } else {
			// Non-corporate user: existing behavior - one email per empty site.
			foreach ($assigned_sites as $site_id => $site_name) {



				$subject = 'Empty Utilities Alert as on ' . $alert_date->format('d F Y') . ' - ' . $site_name;



				if (in_array($site_id, $NoDataAvailableSites)) {



				$bodyHtml = '<div><h4>Dear ' . $user['firstname'] . ' ' . $user['lastname'] . '</h4></div>';

				$bodyHtml .= '<div>This is a kind reminder to update your utilities and waste data of <strong>' . $alert_date->format('F, Y') . '</strong> for <strong>' . $hotel_detail['hotel_name'] . ' - ' . $site_name . '.</strong> <a href="'.base_url().'">Click here</a> to access the HEP portal. <br/>Monthly reporting of energy, water, and waste data is required for all properties. The HEP platform can be used to help manage your utility consumption, with the potential to drive significant savings for your property by reducing utility costs. If you have any questions or concerns, email rotana.support@heportal.net for technical assistance with the platform.</div>';

				$email_template['html'] = $bodyHtml;



				$body = $this->load->view('email_template', $email_template, true);



				$this->mailer->mail->AddAddress($user['email']);

				// $this->mailer->mail->AddAddress('dhaval.prajapati@tatvasoft.com');

				$this->mailer->mail->Subject = $subject;

				$this->mailer->mail->Body = $body;

				$this->mailer->mail->Send();

				$this->mailer->mail->ClearAllRecipients();

				//echo $body;

				}

			}
	    }
	}

	echo "END";
	exit;
    }



    //url : /hotel_portal/cron/monthlyUtilityConsumptionComparisionAlert

    public function monthlyUtilityConsumptionComparisionAlert() {

	if (date('m') == 1) {

	    $current_month = 12;

	    $current_year = date('Y') - 1;

	    $previous_month = 12;

	    $previous_year = date('Y') - 2;

	} else {

	    $current_month = date('m') - 1;

	    $current_year = date('Y');

	    $previous_month = date('m') - 1;

	    $previous_year = date('Y') - 1;

	}



	$current_date = new DateTime(date('d') . "-" . $current_month . "-" . $current_year);

	$previous_date = new DateTime(date('d') . "-" . $previous_month . "-" . $previous_year);



	$this->load->model('cron_model');

	$this->load->model('utilities/utilities_model');



	$this->load->library('mailer');

	$this->mailer->mail->IsHTML(true);



	//Hotel detail

	$this->load->model('hotels/hotels_model');

	$hotel_detail = $this->hotels_model->get_hotel_detail(1);



	$data = array();

	$comparision = array();

	$filedArray = array(

	    "total_electricity_kwh" => "Total Electricity Consumption",

	    "total_natural_gas" => "Total Natural Gas Consumption",

	    "water_total_consumption" => "Total Water Consumption",

	    "total_lpg" => "Total LPG Consumption",

	    "total_fuel_oil" => "Total Fuel Oil Consumption",

	    "district_heating" => "Total District Heating Consumption",

	    "district_cooling" => "Total District Cooling Consumption",

	);



	$user_sites = $this->cron_model->getUserSites();

	$sites = $this->cron_model->get_sites_list();

	$notifications = $this->cron_model->getUserCronNotifications();

	$users = $this->cron_model->getUserList();



	foreach ($sites as $site_id => $site_name) {

	    $this->utilities_model->site_id = $site_id;



	    //load utilities data month and year filter for utilities

	    $this->utilities_model->utilities_month = $current_month;

	    $this->utilities_model->utilities_year = $current_year;

	    $data[$site_id]['current'] = $this->utilities_model->getUtility();



	    $this->utilities_model->utilities_month = $previous_month;

	    $this->utilities_model->utilities_year = $previous_year;

	    $data[$site_id]['previous'] = $this->utilities_model->getUtility();



	    // Set data

	    foreach ($filedArray as $fkey => $fvalue) {

		$current = $data[$site_id]['current'][$fkey];

		$previous = $data[$site_id]['previous'][$fkey];

		$comparision[$site_id][$fkey] = round(($current - $previous) * 100 / $previous, 2);

	    }

	}



	$site_url = site_url();

	foreach ($users as $user) {



	    if (in_array('comparision_alert', $notifications[$user['id']])) {



		foreach ($user_sites[$user['id']] as $site_id => $site_name) {

		    $utility_greaters_10_flag = false;



		    $subject = 'Utilities Consumption Alert for ' . $current_date->format('F Y') . ' v/s ' . $previous_date->format('F Y') . ' - ' . $site_name;



		    $bodyHtml = '<div><h4>Dear ' . $user['firstname'] . ' ' . $user['lastname'] . '</h4></div>';

		    $bodyHtml .= '<div>This is to notify you that the monthly utilities of below listed utilizes of <strong>' . $current_date->format('F, Y') . '</strong> have exceeded their consumption of <strong>' . $previous_date->format('F, Y') . '</strong> for <strong>' . $hotel_detail['hotel_name'] . ' - ' . $site_name . '</strong> by the following percentage.</div>';



		    foreach ($comparision[$site_id] as $key => $value) {

			if ($value > 10) {

			    $bodyHtml .= '<li><strong>' . $filedArray[$key] . ' : </strong> <strong style="color:red;">' . $value . '%</strong>  <img width="15px" height="15px" src="' . $site_url . '/themes/default/images/upArrow.png"/></li>';

			    $utility_greaters_10_flag = true;

			}

		    }

		    $email_template['html'] = $bodyHtml;



		    if ($utility_greaters_10_flag) {

			$body = $this->load->view('email_template', $email_template, true);



			$this->mailer->mail->AddAddress($user['email']);

			// $this->mailer->mail->AddAddress('surbhi.ladhava@tatvasoft.com');

			$this->mailer->mail->Subject = $subject;

			$this->mailer->mail->Body = $body;

			$this->mailer->mail->Send();

			$this->mailer->mail->ClearAllRecipients();

			//echo $body;

		    }

		}

	    }

	}

	echo "END";

	exit;

    }



    //url : /hotel_portal/cron/cumulativeUtilitiesConsumption

    public function cumulativeUtilitiesConsumption() {

	$day = 20;

	$cur_day = intval(date('d'));



	if ($cur_day < 20) {

	    $day = 10;

	}



	$current_month = date('m');

	$current_year = date('Y');

	$previous_year = date('Y') - 1;



	$current_date = new DateTime(date('d') . "-" . $current_month . "-" . $current_year);



	$this->load->model('cron_model');

	$this->load->model('utilities/utilities_model');



	$this->load->library('mailer');

	$this->mailer->mail->IsHTML(true);

	$body = "";

	//Hotel detail

	$this->load->model('hotels/hotels_model');

	$hotel_detail = $this->hotels_model->get_hotel_detail(1);



	$data = array();

	$current_10days_utilities = array();

	$previous_10days_utilities = array();



	$user_sites = $this->cron_model->getUserSites();

	$sites = $this->cron_model->get_site_details();

	$notifications = $this->cron_model->getUserCronNotifications();

	$users = $this->cron_model->getUserList();



	foreach ($sites as $site_id => $site) {

	    $site_name = $site['site_location_name'];

	    $this->cron_model->site_id = $site_id;



	    $this->cron_model->month_id = $current_month;

	    $this->cron_model->year_id = $current_year;

	    $current_10days_utilities[$site_id] = $this->cron_model->getDaysUtility($day);



	    $this->cron_model->month_id = $current_month;

	    $this->cron_model->year_id = $previous_year;

	    $previous_10days_utilities[$site_id] = $this->cron_model->getDaysUtility($day);

	}



	foreach ($users as $user) {



	    if (in_array('cumulative_comparision_alert', $notifications[$user['id']])) {



		foreach ($user_sites[$user['id']] as $site_id => $site_name) {



		    $subject = 'Daily Utilities Consumption Alert for ' . $current_date->format('F, Y') . ' - ' . $site_name;



		    $sendMailFlag = false;

		    $ten_days_flag = false;

		    if (array_key_exists($site_id, $current_10days_utilities)) {



			$electricity_variation = round(($current_10days_utilities[$site_id]['total_electricity_consumption'] - $previous_10days_utilities[$site_id]['total_electricity_consumption']) * 100 / $previous_10days_utilities[$site_id]['total_electricity_consumption'], 2);

			$diesel_fuel_variation = round(($current_10days_utilities[$site_id]['total_diesel_fuel'] - $previous_10days_utilities[$site_id]['total_diesel_fuel']) * 100 / $previous_10days_utilities[$site_id]['total_diesel_fuel'], 2);

			$heavy_fuel_variation = round(($current_10days_utilities[$site_id]['total_heavy_fuel'] - $previous_10days_utilities[$site_id]['total_heavy_fuel']) * 100 / $previous_10days_utilities[$site_id]['total_heavy_fuel'], 2);

			$lpg_variation = round(($current_10days_utilities[$site_id]['total_lpg_consumption'] - $previous_10days_utilities[$site_id]['total_lpg_consumption']) * 100 / $previous_10days_utilities[$site_id]['total_lpg_consumption'], 2);

			$water_variation = round(($current_10days_utilities[$site_id]['total_water_consumption'] - $previous_10days_utilities[$site_id]['total_water_consumption']) * 100 / $previous_10days_utilities[$site_id]['total_water_consumption'], 2);

			$landscape_water_variation = round(($current_10days_utilities[$site_id]['total_landscape_water_consumption'] - $previous_10days_utilities[$site_id]['total_landscape_water_consumption']) * 100 / $previous_10days_utilities[$site_d]['total_landscape_water_consumption'], 2);

			$natural_gas_variation = round(($current_10days_utilities[$site_id]['total_natural_gas_consumption'] - $previous_10days_utilities[$site_id]['total_natural_gas_consumption']) * 100 / $previous_10days_utilities[$site_id]['total_natural_gas_consumption'], 2);

			$district_cooling_variation = round(($current_10days_utilities[$site_id]['total_district_cooling_consumption'] - $previous_10days_utilities[$site_id]['total_district_cooling_consumption']) * 100 / $previous_10days_utilities[$site_id]['total_district_cooling_consumption'], 2);

			$district_heating_variation = round(($current_10days_utilities[$site_id]['total_district_heating_consumption'] - $previous_10days_utilities[$site_id]['total_district_heating_consumption']) * 100 / $previous_10days_utilities[$site_id]['total_district_heating_consumption'], 2);

			$bodyHtml = '<div><h4>Dear ' . $user['firstname'] . ' ' . $user['lastname'] . ',</h4></div>';

			$bodyHtml .= '<div>This is to notify you that the cumulative utilities consumption of the below listed utilities for the first ' . $day . ' Days of <strong>' . $current_date->format('F, Y') . '</strong> exceeds by more than 10% those of the previous year for the similar period at <strong>' . $hotel_detail['hotel_name'] . ' - ' . $site_name . '.</strong></div>';

			$html = '';



			if ($electricity_variation > 10 && $sites[$site_id]['show_utility_electricity']) {

			    $html .= '<li>Electricity Consumption : <strong style="color:red;">' . $electricity_variation . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			    $sendMailFlag = true;

			    $ten_days_flag = true;

			}



			if ($diesel_fuel_variation > 10 && $sites[$site_id]["show_utility_fuel_oil"]) {

			    $html .= '<li>Diesel Fuel Consumption : <strong style="color:red;">' . $diesel_fuel_variation . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			    $sendMailFlag = true;

			    $ten_days_flag = true;

			}



			if ($heavy_fuel_variation > 10 && $sites[$site_id]["show_utility_fuel_oil"]) {

			    $html .= '<li>Heavy Fuel Consumption : <strong style="color:red;">' . $heavy_fuel_variation . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			    $sendMailFlag = true;

			    $ten_days_flag = true;

			}



			if ($lpg_variation > 10 && $sites[$site_id]["show_utility_lpg"]) {

			    $html .= '<li>LPG Consumption : <strong style="color:red;">' . $lpg_variation . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			    $sendMailFlag = true;

			    $ten_days_flag = true;

			}



			if ($water_variation > 10 && $sites[$site_id]["show_utility_water"]) {

			    $html .= '<li>Water Consumption : <strong style="color:red;">' . $water_variation . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			    $sendMailFlag = true;

			    $ten_days_flag = true;

			}



			if ($landscape_water_variation > 10 && $sites[$site_id]["show_utility_irrigation_water"]) {

			    $html .= '<li>Landscape Water Consumption : <strong style="color:red;">' . $landscape_water_variation . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			    $sendMailFlag = true;

			    $ten_days_flag = true;

			}



			if ($natural_gas_variation > 10 && $sites[$site_id]["show_utility_natural_gas"]) {

			    $html .= '<li>Natural Gas Consumption : <strong style="color:red;">' . $natural_gas_variation . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			    $sendMailFlag = true;

			    $ten_days_flag = true;

			}



			if ($district_cooling_variation > 10 && $sites[$site_id]["show_utility_district_cooling"]) {

			    $html .= '<li>District Cooling Consumption : <strong style="color:red;">' . $district_cooling_variation . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			    $sendMailFlag = true;

			    $ten_days_flag = true;

			}



			if ($district_heating_variation > 10 && $sites[$site_id]["show_utility_district_heating"]) {

			    $html .= '<li>District Heating Consumption : <strong style="color:red;">' . $district_heating_variation . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			    $sendMailFlag = true;

			    $ten_days_flag = true;

			}



			$bodyHtml .= $html;

		    }



		    if ($sendMailFlag) {

			$email_template['html'] = $bodyHtml;

			$body = $this->load->view('email_template', $email_template, true);

			$this->mailer->mail->AddAddress($user['email']);

			// $this->mailer->mail->AddAddress('surbhi.ladhava@tatvasoft.com');

			$this->mailer->mail->Subject = $subject;

			$this->mailer->mail->Body = $body;

			$this->mailer->mail->Send();

			$this->mailer->mail->ClearAllRecipients();

			/* echo $body; */

		    }

		}

	    }

	}

	echo "END";

	exit;

    }



    //url : /hotel_portal/cron/dailyTrendsUtilitiesConsumption

    public function dailyTrendsUtilitiesConsumption() {

	$this->load->model('sites/sites_model');

	$day = 20;

	$cur_day = intval(date('d'));



	if ($cur_day < 20) {

	    $day = 10;

	}



	$current_month = intval(date('m'));

	$current_year = date('Y');

	$previous_year = date('Y') - 1;



	$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

	$decimal_places = 2;



	$current_date = new DateTime(date('d') . "-" . $current_month . "-" . $current_year);

	$totalDays = (int) cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);



	$this->load->model('cron_model');

	$this->load->model('utilities/utilities_model');



	$this->load->library('mailer');

	$this->mailer->mail->IsHTML(true);

	$body = "";

	//Hotel detail

	$this->load->model('hotels/hotels_model');

	$hotel_detail = $this->hotels_model->get_hotel_detail(1);



	$data = array();

	$current_10days_utilities = array();

	$previous_10days_utilities = array();

	$variation = array();

	$forecast = array();



	$user_sites = $this->cron_model->getUserSites();

	$sites = $this->cron_model->get_site_details();

	$notifications = $this->cron_model->getUserCronNotifications();

	$users = $this->cron_model->getUserList();



	foreach ($sites as $site_id => $site) {

	    $siteCronSettings = $this->sites_model->getSiteCronSettings();

	    $dailyTrends = array();

	    $isdailyTrendsChecked = 0;



	    foreach ($siteCronSettings as $cronSettings) {

		if ($cronSettings['site_cron_settings']['cron_type'] == 'DAILY_TRENDS') {

		    array_push($dailyTrends,  $cronSettings['site_cron_settings']['site_id']);

		}

	    }

	    $site_name = $site['site_location_name'];



	    /*

	     * ***************************

	     * get cost consumption data

	     * ***************************

	     */

	    //Cosumption Variation

	    $this->cron_model->site_id = $site_id;



	    $this->cron_model->month_id = $current_month;

	    $this->cron_model->year_id = $current_year;



	    $current_10days_utilities[$site_id]['consumption'] = $this->cron_model->getDaysUtility($day);

	    $current_10days_utilities[$site_id]['cost'] = $this->cron_model->getDaysUtilityCost($day);


	    $this->cron_model->month_id = $current_month;

	    $this->cron_model->year_id = $previous_year;



	    $previous_10days_utilities[$site_id]['consumption'] = $this->cron_model->getDaysUtility($day);

	    $previous_10days_utilities[$site_id]['cost'] = $this->cron_model->getDaysUtilityCost($day);



	    foreach ($previous_10days_utilities[$site_id]['consumption'] as $k => $v) {

		if (empty($v))

		    $previous_10days_utilities[$site_id]['consumption'][$k] = 0;

	    }

	    foreach ($current_10days_utilities[$site_id]['consumption'] as $k => $v) {

		if (empty($v))

		    $current_10days_utilities[$site_id]['consumption'][$k] = 0;

	    }

	    // echo $day;


	    /*

	     * *******************************

	     * get forecasted data

	     * *******************************

	     */



	    //Consumption

	    //ELECTRICITY FORECAST

	    $forecast[$site_id]['current']['electricity']['consumption'] = round(($current_10days_utilities[$site_id]['consumption']['total_electricity_consumption'] / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['electricity']['consumption'] = round(($previous_10days_utilities[$site_id]['consumption']['total_electricity_consumption'] / $day) * $totalDays, $decimal_places);



	    //FUEL OIL FORECAST

	    $total_current_fuel_oil_consumption = $current_10days_utilities[$site_id]['consumption']['total_diesel_fuel'] +

		$current_10days_utilities[$site_id]['consumption']['total_heavy_fuel'];

	    $total_previous_fuel_oil_consumption = $previous_10days_utilities[$site_id]['consumption']['total_diesel_fuel'] +

		$previous_10days_utilities[$site_id]['consumption']['total_heavy_fuel'];

	    $forecast[$site_id]['current']['fuel_oil']['consumption'] = round(($total_current_fuel_oil_consumption / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['fuel_oil']['consumption'] = round(($total_previous_fuel_oil_consumption / $day) * $totalDays, $decimal_places);



	    //LPG FORECAST

	    $forecast[$site_id]['current']['lpg']['consumption'] = round(($current_10days_utilities[$site_id]['consumption']['total_lpg_consumption'] / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['lpg']['consumption'] = round(($previous_10days_utilities[$site_id]['consumption']['total_lpg_consumption'] / $day) * $totalDays, $decimal_places);



	    //NATURAL GAS FORECAST

	    $forecast[$site_id]['current']['natural_gas']['consumption'] = round(($current_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption'] / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['natural_gas']['consumption'] = round(($previous_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption'] / $day) * $totalDays, $decimal_places);



	    //DISTRICT COOLING FORECAST

	    $forecast[$site_id]['current']['district_cooling']['consumption'] = round(($current_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption'] / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['district_cooling']['consumption'] = round(($previous_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption'] / $day) * $totalDays, $decimal_places);



	    //DISTRICT HEATING FORECAST

	    $forecast[$site_id]['current']['district_heating']['consumption'] = round(($current_10days_utilities[$site_id]['consumption']['total_district_heating_consumption'] / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['district_heating']['consumption'] = round(($previous_10days_utilities[$site_id]['consumption']['total_district_heating_consumption'] / $day) * $totalDays, $decimal_places);



	    //WATER FORECAST

	    $total_water_current_consumption = $current_10days_utilities[$site_id]['consumption']['total_water_consumption'] + $current_10days_utilities[$site_id]['consumption']['total_landscape_water_consumption'];

	    $total_water_previous_consumption = $previous_10days_utilities[$site_id]['consumption']['total_water_consumption'] + $previous_10days_utilities[$site_id]['consumption']['total_landscape_water_consumption'];

	    $forecast[$site_id]['current']['water']['consumption'] = round(($total_water_current_consumption / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['water']['consumption'] = round(($total_water_previous_consumption / $day) * $totalDays, $decimal_places);



	    //Cost

	    //ELECTRICITY FORECAST

	    $forecast[$site_id]['current']['electricity']['cost'] = round(($current_10days_utilities[$site_id]['cost']['total_electricity_cost'] / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['electricity']['cost'] = round(($previous_10days_utilities[$site_id]['cost']['total_electricity_cost'] / $day) * $totalDays, $decimal_places);



	    //FUEL OIL FORECAST

	    $total_current_fuel_oil_cost = $current_10days_utilities[$site_id]['cost']['total_diesel_fuel_cost'] +

		$current_10days_utilities[$site_id]['cost']['total_heavy_fuel_cost'];

	    $total_previous_fuel_oil_cost = $previous_10days_utilities[$site_id]['cost']['total_diesel_fuel_cost'] +

		$previous_10days_utilities[$site_id]['cost']['total_heavy_fuel_cost'];

	    $forecast[$site_id]['current']['fuel_oil']['cost'] = round(($total_current_fuel_oil_cost / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['fuel_oil']['cost'] = round(($total_previous_fuel_oil_cost / $day) * $totalDays, $decimal_places);



	    //LPG FORECAST

	    $forecast[$site_id]['current']['lpg']['cost'] = round(($current_10days_utilities[$site_id]['cost']['total_lpg_cost'] / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['lpg']['cost'] = round(($previous_10days_utilities[$site_id]['cost']['total_lpg_cost'] / $day) * $totalDays, $decimal_places);



	    //NATURAL GAS FORECAST

	    $forecast[$site_id]['current']['natural_gas']['cost'] = round(($current_10days_utilities[$site_id]['cost']['total_natural_gas_cost'] / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['natural_gas']['cost'] = round(($previous_10days_utilities[$site_id]['cost']['total_natural_gas_cost'] / $day) * $totalDays, $decimal_places);



	    //DISTRICT COOLING FORECAST

	    $forecast[$site_id]['current']['district_cooling']['cost'] = round(($current_10days_utilities[$site_id]['cost']['total_district_cooling_cost'] / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['district_cooling']['cost'] = round(($previous_10days_utilities[$site_id]['cost']['total_district_cooling_cost'] / $day) * $totalDays, $decimal_places);



	    //DISTRICT HEATING FORECAST

	    $forecast[$site_id]['current']['district_heating']['cost'] = round(($current_10days_utilities[$site_id]['cost']['total_district_heating_cost'] / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['district_heating']['cost'] = round(($previous_10days_utilities[$site_id]['cost']['total_district_heating_cost'] / $day) * $totalDays, $decimal_places);



	    //WATER FORECAST

	    $total_water_current_cost = $current_10days_utilities[$site_id]['cost']['total_water_cost'] + $current_10days_utilities[$site_id]['cost']['total_landscape_water_cost'];

	    $total_water_previous_cost = $previous_10days_utilities[$site_id]['cost']['total_water_cost'] + $previous_10days_utilities[$site_id]['cost']['total_landscape_water_cost'];

	    $forecast[$site_id]['current']['water']['cost'] = round(($total_water_current_cost / $day) * $totalDays, $decimal_places);

	    $forecast[$site_id]['previous']['water']['cost'] = round(($total_water_previous_cost / $day) * $totalDays, $decimal_places);

	    /*

	     * *******************************

	     * get consumption difference data

	     * ******************************

	     * Variation calculation = (current utility - previous utility / current year utility) * 100;

	     * *******************************

	     */

	    // pre($previous_10days_utilities);

	    //ELECTRICITY VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['consumption']['total_electricity_consumption']) && $previous_10days_utilities[$site_id]['consumption']['total_electricity_consumption'] != 0) {



		$variation[$site_id]['electricity']['consumption'] = is_infinite(round(($current_10days_utilities[$site_id]['consumption']['total_electricity_consumption'] - $previous_10days_utilities[$site_id]['consumption']['total_electricity_consumption']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_electricity_consumption'], $decimal_places)) ? 0 : round(($current_10days_utilities[$site_id]['consumption']['total_electricity_consumption'] - $previous_10days_utilities[$site_id]['consumption']['total_electricity_consumption']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_electricity_consumption'], $decimal_places);



	    } else {

		$variation[$site_id]['electricity']['consumption'] = 0;

	    }

	    //FUEL OIL VARIATION

	    $total_current_fuel_oil_consumption = $current_10days_utilities[$site_id]['consumption']['total_diesel_fuel'] +

		$current_10days_utilities[$site_id]['consumption']['total_heavy_fuel'];

	    $total_previous_fuel_oil_consumption = $previous_10days_utilities[$site_id]['consumption']['total_diesel_fuel'] +

		$previous_10days_utilities[$site_id]['consumption']['total_heavy_fuel'];



	    if (!empty($total_previous_fuel_oil_consumption)) {

		$variation[$site_id]['fuel_oil']['consumption'] = is_infinite(round(($total_current_fuel_oil_consumption - $total_previous_fuel_oil_consumption) * 100 / $total_previous_fuel_oil_consumption, $decimal_places)) ? 0 : round(($total_current_fuel_oil_consumption - $total_previous_fuel_oil_consumption) * 100 / $total_previous_fuel_oil_consumption, $decimal_places);

	    } else {

		$variation[$site_id]['fuel_oil']['consumption'] = 0;

	    }



	    //LPG VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['consumption']['total_lpg_consumption'])) {

		$variation[$site_id]['lpg']['consumption'] = is_infinite(round(($current_10days_utilities[$site_id]['consumption']['total_lpg_consumption'] - $previous_10days_utilities[$site_id]['consumption']['total_lpg_consumption']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_lpg_consumption'], $decimal_places)) ? 0 : round(($current_10days_utilities[$site_id]['consumption']['total_lpg_consumption'] - $previous_10days_utilities[$site_id]['consumption']['total_lpg_consumption']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_lpg_consumption'], $decimal_places);

	    } else {

		$variation[$site_id]['lpg']['consumption'] = 0;

	    }



	    //NATURAL GAS VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption'])) {

		$variation[$site_id]['natural_gas']['consumption'] = is_infinite(round(($current_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption'] - $previous_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption'], $decimal_places)) ? 0 : round(($current_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption'] - $previous_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption'], $decimal_places);

	    } else {

		$variation[$site_id]['natural_gas']['consumption'] = 0;

	    }



	    //DISTRICT COOLING VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption'])) {

		$variation[$site_id]['district_cooling']['consumption'] = is_infinite(round(($current_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption'] - $previous_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption'], $decimal_places)) ? 0 : round(($current_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption'] - $previous_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption'], $decimal_places);

	    } else {

		$variation[$site_id]['district_cooling']['consumption'] = 0;

	    }



	    //DISTRICT HEATING VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['consumption']['total_district_heating_consumption'])) {

		$variation[$site_id]['district_heating']['consumption'] = is_infinite(round(($current_10days_utilities[$site_id]['consumption']['total_district_heating_consumption'] - $previous_10days_utilities[$site_id]['consumption']['total_district_heating_consumption']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_district_heating_consumption'], $decimal_places)) ? 0 : round(($current_10days_utilities[$site_id]['consumption']['total_district_heating_consumption'] - $previous_10days_utilities[$site_id]['consumption']['total_district_heating_consumption']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_district_heating_consumption'], $decimal_places);

	    } else {

		$variation[$site_id]['district_heating']['consumption'] = 0;

	    }



	    //WATER VARIATION

	    $total_water_current_consumption = $current_10days_utilities[$site_id]['consumption']['total_water_consumption'] + $current_10days_utilities[$site_id]['consumption']['total_landscape_water_consumption'];

	    $total_water_previous_consumption = $previous_10days_utilities[$site_id]['consumption']['total_water_consumption'] + $previous_10days_utilities[$site_id]['consumption']['total_landscape_water_consumption'];

	    if (!empty($total_water_previous_consumption)) {

		$variation[$site_id]['water']['consumption'] = is_infinite(round(($total_water_current_consumption - $total_water_previous_consumption) * 100 / $total_water_previous_consumption, $decimal_places)) ? 0 : round(($total_water_current_consumption - $total_water_previous_consumption) * 100 / $total_water_previous_consumption, $decimal_places);

	    } else {

		$variation[$site_id]['water']['consumption'] = 0;

	    }

	    //CDD VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['consumption']['total_cdd'])) {

		$variation[$site_id]['cdd']['consumption'] = is_infinite(round(($current_10days_utilities[$site_id]['consumption']['total_cdd'] - $previous_10days_utilities[$site_id]['consumption']['total_cdd']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_cdd'], $decimal_places)) ? 0 : round(($current_10days_utilities[$site_id]['consumption']['total_cdd'] - $previous_10days_utilities[$site_id]['consumption']['total_cdd']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_cdd'], $decimal_places);

	    } else {

		$previous_10days_utilities[$site_id]['consumption']['total_cdd'] = 0;

		if (empty($current_10days_utilities[$site_id]['consumption']['total_cdd'])) {

		    $current_10days_utilities[$site_id]['consumption']['total_cdd'] = 0;

		    $variation[$site_id]['cdd']['consumption'] = 0;

		} else {

		    $variation[$site_id]['cdd']['consumption'] = 0;

		}

	    }



	    //HDD VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['consumption']['total_hdd'])) {

		$variation[$site_id]['hdd']['consumption'] = is_infinite(round(($current_10days_utilities[$site_id]['consumption']['total_hdd'] - $previous_10days_utilities[$site_id]['consumption']['total_hdd']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_hdd'], $decimal_places)) ? 0 : round(($current_10days_utilities[$site_id]['consumption']['total_hdd'] - $previous_10days_utilities[$site_id]['consumption']['total_hdd']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_hdd'], $decimal_places);

	    } else {

		$previous_10days_utilities[$site_id]['consumption']['total_hdd'] = 0;

		if (empty($current_10days_utilities[$site_id]['consumption']['total_hdd'])) {

		    $current_10days_utilities[$site_id]['consumption']['total_hdd'] = 0;

		    $variation[$site_id]['hdd']['consumption'] = 0;

		} else {

		    $variation[$site_id]['hdd']['consumption'] = 0;

		}

	    }



	    //ROOM NIGHT VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['consumption']['total_room_night'])) {

		$variation[$site_id]['total_room_night']['consumption'] = is_infinite(round(($current_10days_utilities[$site_id]['consumption']['total_room_night'] - $previous_10days_utilities[$site_id]['consumption']['total_room_night']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_room_night'], $decimal_places)) ? 0 : round(($current_10days_utilities[$site_id]['consumption']['total_room_night'] - $previous_10days_utilities[$site_id]['consumption']['total_room_night']) * 100 / $previous_10days_utilities[$site_id]['consumption']['total_room_night'], $decimal_places);

	    } else {

		$previous_10days_utilities[$site_id]['consumption']['total_room_night'] = 0;

		if (empty($current_10days_utilities[$site_id]['consumption']['total_room_night'])) {

		    $current_10days_utilities[$site_id]['consumption']['total_room_night'] = 0;

		    $variation[$site_id]['total_room_night']['consumption'] = 0;

		} else {

		    $variation[$site_id]['total_room_night']['consumption'] = 0;

		}

	    }



	    //Cost Variation

	    //ELECTICITY VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['cost']['total_electricity_cost'])) {

		$variation[$site_id]['electricity']['cost'] = is_infinite(round(($current_10days_utilities[$site_id]['cost']['total_electricity_cost'] - $previous_10days_utilities[$site_id]['cost']['total_electricity_cost']) * 100 / $previous_10days_utilities[$site_id]['cost']['total_electricity_cost'], $decimal_places)) ? 0 : round(($current_10days_utilities[$site_id]['cost']['total_electricity_cost'] - $previous_10days_utilities[$site_id]['cost']['total_electricity_cost']) * 100 / $previous_10days_utilities[$site_id]['cost']['total_electricity_cost'], $decimal_places);

	    } else {

		$variation[$site_id]['electricity']['cost'] = 0;

	    }



	    //FUEL OIL VARIATION



	    $total_current_fuel_oil_cost = $current_10days_utilities[$site_id]['cost']['total_diesel_fuel_cost'] +

		$current_10days_utilities[$site_id]['cost']['total_heavy_fuel_cost'];

	    $total_previous_fuel_oil_cost = $previous_10days_utilities[$site_id]['cost']['total_diesel_fuel_cost'] +

		$previous_10days_utilities[$site_id]['cost']['total_heavy_fuel_cost'];



	    if (!empty($total_previous_fuel_oil_cost)) {

		$variation[$site_id]['fuel_oil']['cost'] = is_infinite(round(($total_current_fuel_oil_cost - $total_previous_fuel_oil_cost) * 100 / $total_previous_fuel_oil_cost, $decimal_places)) ? 0 : round(($total_current_fuel_oil_cost - $total_previous_fuel_oil_cost) * 100 / $total_previous_fuel_oil_cost, $decimal_places);

	    } else {

		$variation[$site_id]['fuel_oil']['cost'] = 0;

	    }



	    //LPG VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['cost']['total_lpg_cost'])) {

		$variation[$site_id]['lpg']['cost'] = is_infinite(round(($current_10days_utilities[$site_id]['cost']['total_lpg_cost'] - $previous_10days_utilities[$site_id]['cost']['total_lpg_cost']) * 100 / $previous_10days_utilities[$site_id]['cost']['total_lpg_cost'], $decimal_places)) ? 0 :  round(($current_10days_utilities[$site_id]['cost']['total_lpg_cost'] - $previous_10days_utilities[$site_id]['cost']['total_lpg_cost']) * 100 / $previous_10days_utilities[$site_id]['cost']['total_lpg_cost'], $decimal_places);

	    } else {

		$variation[$site_id]['lpg']['cost'] = 0;

	    }



	    //NATURAL GAS VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['cost']['total_natural_gas_cost'])) {

		$variation[$site_id]['natural_gas']['cost'] = is_infinite(round(($current_10days_utilities[$site_id]['cost']['total_natural_gas_cost'] - $previous_10days_utilities[$site_id]['cost']['total_lpg_cost']) * 100 / $previous_10days_utilities[$site_id]['cost']['total_lpg_cost'], $decimal_places)) ? 0 :  round(($current_10days_utilities[$site_id]['cost']['total_lpg_cost'] - $previous_10days_utilities[$site_id]['cost']['total_lpg_cost']) * 100 / $previous_10days_utilities[$site_id]['cost']['total_lpg_cost'], $decimal_places);

	    } else {

		$variation[$site_id]['natural_gas']['cost'] = 0;

	    }



	    //DISTRICT COOLING VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['cost']['total_district_cooling_cost'])) {

		$variation[$site_id]['district_cooling']['cost'] = is_infinite(round(($current_10days_utilities[$site_id]['cost']['total_district_cooling_cost'] - $previous_10days_utilities[$site_id]['cost']['total_district_cooling_cost']) * 100 / $previous_10days_utilities[$site_id]['cost']['total_district_cooling_cost'], $decimal_places)) ? 0 :  round(($current_10days_utilities[$site_id]['cost']['total_district_cooling_cost'] - $previous_10days_utilities[$site_id]['cost']['total_district_cooling_cost']) * 100 / $previous_10days_utilities[$site_id]['cost']['total_district_cooling_cost'], $decimal_places);

	    } else {

		$variation[$site_id]['district_cooling']['cost'] = 0;

	    }



	    //DISTRICT HEATING VARIATION

	    if (!empty($previous_10days_utilities[$site_id]['cost']['total_district_heating_cost'])) {

		$variation[$site_id]['district_heating']['cost'] = is_infinite(round(($current_10days_utilities[$site_id]['cost']['total_district_heating_cost'] - $previous_10days_utilities[$site_id]['cost']['total_district_heating_cost']) * 100 / $previous_10days_utilities[$site_id]['cost']['total_district_heating_cost'], $decimal_places)) ? 0 :  round(($current_10days_utilities[$site_id]['cost']['total_district_heating_cost'] - $previous_10days_utilities[$site_id]['cost']['total_district_heating_cost']) * 100 / $previous_10days_utilities[$site_id]['cost']['total_district_heating_cost'], $decimal_places);

	    } else {

		$variation[$site_id]['district_heating']['cost'] = 0;

	    }



	    //WATER VARIATION

	    $total_water_current_cost = $current_10days_utilities[$site_id]['cost']['total_water_cost'] + $current_10days_utilities[$site_id]['cost']['total_landscape_water_cost'];

	    $total_water_previous_cost = $previous_10days_utilities[$site_id]['cost']['total_water_cost'] + $previous_10days_utilities[$site_id]['cost']['total_landscape_water_cost'];

	    if (!empty($total_water_previous_cost)) {

		$variation[$site_id]['water']['cost'] = is_infinite(round(($total_water_current_cost - $total_water_previous_cost) * 100 / $total_water_previous_cost, $decimal_places)) ? 0 :  round(($total_water_current_cost - $total_water_previous_cost) * 100 / $total_water_previous_cost, $decimal_places);

	    } else {

		$variation[$site_id]['water']['cost'] = 0;

	    }

		$variation[$site_id]['electricity']['consumption_current_year'] = $current_10days_utilities[$site_id]['consumption']['total_electricity_consumption'];
		$variation[$site_id]['electricity']['consumption_previous_year'] = $previous_10days_utilities[$site_id]['consumption']['total_electricity_consumption'];
		$variation[$site_id]['fuel_oil']['consumption_current_year'] = $total_current_fuel_oil_consumption;
		$variation[$site_id]['fuel_oil']['consumption_previous_year'] = $total_previous_fuel_oil_consumption;		
		$variation[$site_id]['lpg']['consumption_current_year'] = $current_10days_utilities[$site_id]['consumption']['total_lpg_consumption'];
		$variation[$site_id]['lpg']['consumption_previous_year'] = $previous_10days_utilities[$site_id]['consumption']['total_lpg_consumption'];
		$variation[$site_id]['natural_gas']['consumption_current_year'] = $current_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption'];
		$variation[$site_id]['natural_gas']['consumption_previous_year'] = $previous_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption'];
		$variation[$site_id]['district_cooling']['consumption_current_year'] = $current_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption'];
		$variation[$site_id]['district_cooling']['consumption_previous_year'] = $previous_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption'];
		$variation[$site_id]['district_heating']['consumption_current_year'] = $current_10days_utilities[$site_id]['consumption']['total_district_heating_consumption'];
		$variation[$site_id]['district_heating']['consumption_previous_year'] = $previous_10days_utilities[$site_id]['consumption']['total_district_heating_consumption'];
		$variation[$site_id]['water']['consumption_current_year'] = $total_water_current_consumption;
		$variation[$site_id]['water']['consumption_previous_year'] = $total_water_previous_consumption;

		$variation[$site_id]['electricity']['cost_current_year'] = $current_10days_utilities[$site_id]['cost']['total_electricity_cost']; 
		$variation[$site_id]['electricity']['cost_previous_year'] = $previous_10days_utilities[$site_id]['cost']['total_electricity_cost'];
		$variation[$site_id]['fuel_oil']['cost_current_year'] = $total_current_fuel_oil_cost; 
		$variation[$site_id]['fuel_oil']['cost_previous_year'] = $total_previous_fuel_oil_cost;
		$variation[$site_id]['lpg']['cost_current_year'] = $current_10days_utilities[$site_id]['cost']['total_lpg_cost']; 
		$variation[$site_id]['lpg']['cost_previous_year'] = $previous_10days_utilities[$site_id]['cost']['total_lpg_cost'];
		$variation[$site_id]['natural_gas']['cost_current_year'] = $current_10days_utilities[$site_id]['cost']['total_natural_gas_cost']; 
		$variation[$site_id]['natural_gas']['cost_previous_year'] = $previous_10days_utilities[$site_id]['cost']['total_natural_gas_cost'];
		$variation[$site_id]['district_cooling']['cost_current_year'] = $current_10days_utilities[$site_id]['cost']['total_district_cooling_cost']; 
		$variation[$site_id]['district_cooling']['cost_previous_year'] = $previous_10days_utilities[$site_id]['cost']['total_district_cooling_cost'];
		$variation[$site_id]['district_heating']['cost_current_year'] = $current_10days_utilities[$site_id]['cost']['total_district_heating_cost']; 
		$variation[$site_id]['district_heating']['cost_previous_year'] = $previous_10days_utilities[$site_id]['cost']['total_district_heating_cost'];
		$variation[$site_id]['water']['cost_current_year'] = $total_water_current_cost;
		$variation[$site_id]['water']['cost_previous_year'] = $total_water_previous_cost;

	    //Cosumption Variation

	    $this->load->model('reports/reports_model');

	    $filters = [

		'site_id' => $site_id,

		'start_month' => $current_month,

		'start_year' => $current_year,

		'end_month' => $current_month,

		'end_year' => $current_year,

	    ];

	    $budgetActualData = $this->reports_model->getUtilityActualBudgetData($filters);

	    $current_month_budget_atual_data = $budgetActualData[0];


	    /*

	     * *******************************

	     * get forecast cost budget data

	     * ******************************

	     */



	    //ELECTRICITY

	    $forecastBudget[$site_id]['electricity']['consumption'] = $current_month_budget_atual_data['total_electricity_kwh_budget'] ? $current_month_budget_atual_data['total_electricity_kwh_budget'] : 0;

	    $forecastBudget[$site_id]['electricity']['cost'] = $current_month_budget_atual_data['total_electricity_cost_budget'] ? $current_month_budget_atual_data['total_electricity_cost_budget'] : 0;





	    //FUEL OIL

	    $forecastBudget[$site_id]['fuel_oil']['consumption'] = $current_month_budget_atual_data['total_fuel_oil_budget'] ? $current_month_budget_atual_data['total_fuel_oil_budget'] : 0;

	    $forecastBudget[$site_id]['fuel_oil']['cost'] = $current_month_budget_atual_data['total_fuel_oil_cost_budget'] ? $current_month_budget_atual_data['total_fuel_oil_cost_budget'] : 0;



	    //LPG

	    $forecastBudget[$site_id]['lpg']['consumption'] = $current_month_budget_atual_data['total_lpg_budget'] ? $current_month_budget_atual_data['total_lpg_budget'] : 0;

	    $forecastBudget[$site_id]['lpg']['cost'] = $current_month_budget_atual_data['total_lpg_cost_budget'] ? $current_month_budget_atual_data['total_lpg_cost_budget'] : 0;



	    //NATURAL GAS

	    $forecastBudget[$site_id]['natural_gas']['consumption'] = $current_month_budget_atual_data['total_natural_gas_budget'] ? $current_month_budget_atual_data['total_natural_gas_budget'] : 0;

	    $forecastBudget[$site_id]['natural_gas']['cost'] = $current_month_budget_atual_data['total_natural_gas_cost_budget'] ? $current_month_budget_atual_data['total_natural_gas_cost_budget'] : 0;



	    //DISTRICT COOLING

	    $forecastBudget[$site_id]['district_cooling']['consumption'] = $current_month_budget_atual_data['district_cooling_budget'] ? $current_month_budget_atual_data['district_cooling_budget'] : 0;

	    $forecastBudget[$site_id]['district_cooling']['cost'] = $current_month_budget_atual_data['district_cooling_cost_budget'] ? $current_month_budget_atual_data['district_cooling_cost_budget'] : 0;



	    //DISTRICT HEATING

	    $forecastBudget[$site_id]['district_heating']['consumption'] = $current_month_budget_atual_data['district_heating_budget'] ? $current_month_budget_atual_data['district_heating_budget'] : 0;

	    $forecastBudget[$site_id]['district_heating']['cost'] = $current_month_budget_atual_data['district_heating_cost_budget'] ? $current_month_budget_atual_data['district_heating_cost_budget'] : 0;



	    //WATER

	    $forecastBudget[$site_id]['water']['consumption'] = $current_month_budget_atual_data['water_total_consumption_budget'] ? $current_month_budget_atual_data['water_total_consumption_budget'] : 0;

	    $forecastBudget[$site_id]['water']['cost'] = $current_month_budget_atual_data['water_total_consumption_cost_budget'] ? $current_month_budget_atual_data['water_total_consumption_cost_budget'] : 0;



	    /*

	     * *******************************

	     * get cost budget data

	     * ******************************

	     * Budget calculation = (monthlyBudget / totalDays) * day;

	     * day = 10, 20;

	     * totalDays = totalDays of month;

	     * *******************************

	     */



	    //ELECTRICITY VARIATION

	    $electricity_current_consumption = $current_10days_utilities[$site_id]['consumption']['total_electricity_consumption'];

	    $electricity_consumption_budget = is_infinite(round(($current_month_budget_atual_data['total_electricity_kwh_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['total_electricity_kwh_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($electricity_consumption_budget)) {

		$budgetVariation[$site_id]['electricity']['consumption'] = is_infinite(round(($electricity_current_consumption - $electricity_consumption_budget) * 100 / $electricity_consumption_budget, $decimal_places)) ? 0 :  round(($electricity_current_consumption - $electricity_consumption_budget) * 100 / $electricity_consumption_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['electricity']['consumption'] = 0;

	    }



	    //FUEL OIL VARIATION

	    $total_current_fuel_oil_consumption = $current_10days_utilities[$site_id]['consumption']['total_diesel_fuel'] +

		$current_10days_utilities[$site_id]['consumption']['total_heavy_fuel'];

	    $fuel_oil_consumption_budget = is_infinite(round(($current_month_budget_atual_data['total_fuel_oil_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['total_fuel_oil_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($fuel_oil_consumption_budget)) {

		$budgetVariation[$site_id]['fuel_oil']['consumption'] = is_infinite(round(($total_current_fuel_oil_consumption - $fuel_oil_consumption_budget) * 100 / $fuel_oil_consumption_budget, $decimal_places)) ? 0 :  round(($total_current_fuel_oil_consumption - $fuel_oil_consumption_budget) * 100 / $fuel_oil_consumption_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['fuel_oil']['consumption'] = 0;

	    }



	    //LPG VARIATION

	    $lgp_current_consumption = $current_10days_utilities[$site_id]['consumption']['total_lpg_consumption'];

	    $lpg_consumption_budget = is_infinite(round(($current_month_budget_atual_data['total_lpg_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['total_lpg_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($lpg_consumption_budget)) {

		$budgetVariation[$site_id]['lpg']['consumption'] = is_infinite(round(($current_month_budget_atual_data['total_lpg_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['total_lpg_budget'] / $totalDays) * $day, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['lpg']['consumption'] = 0;

	    }



	    //NATURAL GAS VARIATION

	    $natural_gas_current_consumption = $current_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption'];

	    $natural_gas_consumption_budget = is_infinite(round(($current_month_budget_atual_data['total_natural_gas_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['total_natural_gas_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($natural_gas_consumption_budget)) {

		$budgetVariation[$site_id]['natural_gas']['consumption'] = is_infinite(round(($natural_gas_current_consumption - $natural_gas_consumption_budget) * 100 / $natural_gas_consumption_budget, $decimal_places)) ? 0 :  round(($natural_gas_current_consumption - $natural_gas_consumption_budget) * 100 / $natural_gas_consumption_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['natural_gas']['consumption'] = 0;

	    }



	    //DISTRICT COOLING VARIATION

	    $district_cooling_current_consumption = $current_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption'];

	    $district_cooling_consumption_budget = is_infinite(round(($current_month_budget_atual_data['district_cooling_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['district_cooling_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($district_cooling_consumption_budget)) {

		$budgetVariation[$site_id]['district_cooling']['consumption'] = is_infinite(round(($district_cooling_current_consumption - $district_cooling_consumption_budget) * 100 / $district_cooling_consumption_budget, $decimal_places)) ? 0 :  round(($district_cooling_current_consumption - $district_cooling_consumption_budget) * 100 / $district_cooling_consumption_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['district_cooling']['consumption'] = 0;

	    }



	    //DISTRICT HEATING VARIATION

	    $district_heating_current_consumption = $current_10days_utilities[$site_id]['consumption']['total_district_heating_consumption'];

	    $district_heating_consumption_budget = is_infinite(round(($current_month_budget_atual_data['district_heating_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['district_heating_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($district_heating_consumption_budget)) {

		$budgetVariation[$site_id]['district_heating']['consumption'] = is_infinite(round(($district_heating_current_consumption - $district_heating_consumption_budget) * 100 / $district_heating_consumption_budget, $decimal_places)) ? 0 :  round(($district_heating_current_consumption - $district_heating_consumption_budget) * 100 / $district_heating_consumption_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['district_heating']['consumption'] = 0;

	    }





	    //WATER VARIATION

	    $water_consumption_current_consumption = $total_water_current_consumption;

	    $water_consumption_budget = is_infinite(round(($current_month_budget_atual_data['water_total_consumption_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['water_total_consumption_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($water_consumption_budget)) {

		$budgetVariation[$site_id]['water']['consumption'] = is_infinite(round(($water_consumption_current_consumption - $water_consumption_budget) * 100 / $water_consumption_budget, $decimal_places)) ? 0 :  round(($water_consumption_current_consumption - $water_consumption_budget) * 100 / $water_consumption_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['water']['consumption'] = 0;

	    }



	    //Cost Variation

	    //ELECTRICITY VARIATION

	    $electricity_current_cost = $current_10days_utilities[$site_id]['cost']['total_electricity_cost'];

	    $electricity_cost_budget = is_infinite(round(($current_month_budget_atual_data['total_electricity_cost_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['total_electricity_cost_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($electricity_cost_budget)) {

		$budgetVariation[$site_id]['electricity']['cost'] = is_infinite(round(($electricity_current_cost - $electricity_cost_budget) * 100 / $electricity_cost_budget, $decimal_places)) ? 0 :  round(($electricity_current_cost - $electricity_cost_budget) * 100 / $electricity_cost_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['electricity']['cost'] = 0;

	    }



	    //FUEL OIL VARIATION

	    $total_current_fuel_oil_cost = $current_10days_utilities[$site_id]['cost']['total_diesel_fuel_cost'] +

		$current_10days_utilities[$site_id]['cost']['total_heavy_fuel_cost'];

	    $fuel_oil_cost_budget = is_infinite(round(($current_month_budget_atual_data['total_fuel_oil_cost_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['total_fuel_oil_cost_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($fuel_oil_cost_budget)) {

		$budgetVariation[$site_id]['fuel_oil']['cost'] = is_infinite(round(($total_current_fuel_oil_cost - $fuel_oil_cost_budget) * 100 / $fuel_oil_cost_budget, $decimal_places)) ? 0 :  round(($total_current_fuel_oil_cost - $fuel_oil_cost_budget) * 100 / $fuel_oil_cost_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['fuel_oil']['cost'] = 0;

	    }



	    //LPG VARIATION

	    $lgp_current_cost = $current_10days_utilities[$site_id]['cost']['total_lpg_cost'];

	    $lpg_cost_budget = is_infinite(round(($current_month_budget_atual_data['total_lpg_cost_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['total_lpg_cost_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($lpg_cost_budget)) {

		$budgetVariation[$site_id]['lpg']['cost'] = is_infinite(round(($lgp_current_cost - $lpg_cost_budget) * 100 / $lpg_cost_budget, $decimal_places)) ? 0 :  round(($lgp_current_cost - $lpg_cost_budget) * 100 / $lpg_cost_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['lpg']['cost'] = 0;

	    }



	    //NATURAL GAS VARIATION

	    $natural_gas_current_cost = $current_10days_utilities[$site_id]['cost']['total_natural_gas_cost'];

	    $natural_gas_cost_budget = is_infinite(round(($current_month_budget_atual_data['total_natural_gas_cost_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['total_natural_gas_cost_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($natural_gas_cost_budget)) {

		$budgetVariation[$site_id]['natural_gas']['cost'] = is_infinite(round(($natural_gas_current_cost - $natural_gas_cost_budget) * 100 / $natural_gas_cost_budget, $decimal_places)) ? 0 :  round(($natural_gas_current_cost - $natural_gas_cost_budget) * 100 / $natural_gas_cost_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['natural_gas']['cost'] = 0;

	    }



	    //DISTRICT COOLING VARIATION

	    $district_cooling_current_cost = $current_10days_utilities[$site_id]['cost']['total_district_cooling_cost'];

	    $district_cooling_cost_budget = is_infinite(round(($current_month_budget_atual_data['district_cooling_cost_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['district_cooling_cost_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($district_cooling_cost_budget)) {

		$budgetVariation[$site_id]['district_cooling']['cost'] = is_infinite(round(($district_cooling_current_cost - $district_cooling_cost_budget) * 100 / $district_cooling_cost_budget, $decimal_places)) ? 0 :  round(($district_cooling_current_cost - $district_cooling_cost_budget) * 100 / $district_cooling_cost_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['district_cooling']['cost'] = 0;

	    }



	    //DISTRICT HEATING VARIATION

	    $district_heating_current_cost = $current_10days_utilities[$site_id]['cost']['total_district_heating_cost'];

	    $district_heating_cost_budget = is_infinite(round(($current_month_budget_atual_data['district_heating_cost_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['district_heating_cost_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($district_heating_cost_budget)) {

		$budgetVariation[$site_id]['district_heating']['cost'] = is_infinite(round(($district_heating_current_cost - $district_heating_cost_budget) * 100 / $district_heating_cost_budget, $decimal_places)) ? 0 :  round(($district_heating_current_cost - $district_heating_cost_budget) * 100 / $district_heating_cost_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['district_heating']['cost'] = 0;

	    }



	    //WATER VARIATION

	    $water_cost_current = $total_water_current_cost;

	    $water_cost_budget = is_infinite(round(($current_month_budget_atual_data['water_total_consumption_cost_budget'] / $totalDays) * $day, $decimal_places)) ? 0 :  round(($current_month_budget_atual_data['water_total_consumption_cost_budget'] / $totalDays) * $day, $decimal_places);



	    if (!empty($water_cost_budget)) {

		$budgetVariation[$site_id]['water']['cost'] = is_infinite(round(($water_cost_current - $water_cost_budget) * 100 / $water_cost_budget, $decimal_places)) ? 0 :  round(($water_cost_current - $water_cost_budget) * 100 / $water_cost_budget, $decimal_places);

	    } else {

		$budgetVariation[$site_id]['water']['cost'] = 0;

	    }
		
		$budgetVariation[$site_id]['electricity']['consumption_current_year'] = $current_10days_utilities[$site_id]['consumption']['total_electricity_consumption'];
		$budgetVariation[$site_id]['electricity']['consumption_previous_year'] = $current_month_budget_atual_data['total_electricity_kwh_budget'];
		$budgetVariation[$site_id]['fuel_oil']['consumption_current_year'] = $current_10days_utilities[$site_id]['consumption']['total_diesel_fuel'] +	$current_10days_utilities[$site_id]['consumption']['total_heavy_fuel'];
		$budgetVariation[$site_id]['fuel_oil']['consumption_previous_year'] = $current_month_budget_atual_data['total_fuel_oil_budget'];
		$budgetVariation[$site_id]['lpg']['consumption_current_year'] = $current_10days_utilities[$site_id]['consumption']['total_lpg_consumption'];
		$budgetVariation[$site_id]['lpg']['consumption_previous_year'] = $current_month_budget_atual_data['total_lpg_budget'];
		$budgetVariation[$site_id]['natural_gas']['consumption_current_year'] = $current_10days_utilities[$site_id]['consumption']['total_natural_gas_consumption'];
		$budgetVariation[$site_id]['natural_gas']['consumption_previous_year'] = $current_month_budget_atual_data['total_natural_gas_budget'];
		$budgetVariation[$site_id]['district_cooling']['consumption_current_year'] = $current_10days_utilities[$site_id]['consumption']['total_district_cooling_consumption'];
		$budgetVariation[$site_id]['district_cooling']['consumption_previous_year'] = $current_month_budget_atual_data['district_cooling_budget'];
		$budgetVariation[$site_id]['district_heating']['consumption_current_year'] = $current_10days_utilities[$site_id]['consumption']['total_district_heating_consumption'];
		$budgetVariation[$site_id]['district_heating']['consumption_previous_year'] = $current_month_budget_atual_data['district_heating_budget'];
		$budgetVariation[$site_id]['water']['consumption_current_year'] = $total_water_current_consumption;
		$budgetVariation[$site_id]['water']['consumption_previous_year'] = $current_month_budget_atual_data['water_total_consumption_budget'];

		$budgetVariation[$site_id]['electricity']['cost_current_year'] = $electricity_current_cost; 
		$budgetVariation[$site_id]['electricity']['cost_previous_year'] = $current_month_budget_atual_data['total_electricity_cost_budget'];
		$budgetVariation[$site_id]['fuel_oil']['cost_current_year'] = $total_current_fuel_oil_cost; 
		$budgetVariation[$site_id]['fuel_oil']['cost_previous_year'] = $current_month_budget_atual_data['total_fuel_oil_cost_budget'];
		$budgetVariation[$site_id]['lpg']['cost_current_year'] = $lgp_current_cost; 
		$budgetVariation[$site_id]['lpg']['cost_previous_year'] = $current_month_budget_atual_data['total_lpg_cost_budget'];
		$budgetVariation[$site_id]['natural_gas']['cost_current_year'] = $natural_gas_current_cost; 
		$budgetVariation[$site_id]['natural_gas']['cost_previous_year'] = $current_month_budget_atual_data['total_natural_gas_cost_budget'];
		$budgetVariation[$site_id]['district_cooling']['cost_current_year'] = $district_cooling_current_cost; 
		$budgetVariation[$site_id]['district_cooling']['cost_previous_year'] = $current_month_budget_atual_data['district_cooling_cost_budget'];
		$budgetVariation[$site_id]['district_heating']['cost_current_year'] = $district_heating_current_cost; 
		$budgetVariation[$site_id]['district_heating']['cost_previous_year'] = $current_month_budget_atual_data['district_heating_cost_budget'];
		$budgetVariation[$site_id]['water']['cost_current_year'] = $water_cost_current;
		$budgetVariation[$site_id]['water']['cost_previous_year'] = $current_month_budget_atual_data['water_total_consumption_cost_budget'];
	}

	foreach ($variation as $site_id => $data) {

	    foreach ($data as $k => $value) {

		if ($k == 'total_room_night') {

		    $arrow[$site_id][$k]['consumption'] = ($value['consumption'] <= 0) ? "downArrowRed.png" : "upArrowGreen.png";

		} else {

		    $arrow[$site_id][$k]['consumption'] = ($value['consumption'] <= 0) ? "downArrow.png" : "upArrow.png";

		}

		$arrow[$site_id][$k]['cost'] = ($value['cost'] <= 0) ? "downArrow.png" : "upArrow.png";

	    }

	}



	foreach ($budgetVariation as $site_id => $data) {

	    foreach ($data as $k => $value) {

		$arrow[$site_id][$k]['budgetConsumption'] = ($value['consumption'] <= 0) ? "downArrow.png" : "upArrow.png";

		$arrow[$site_id][$k]['budgetCost'] = ($value['cost'] <= 0) ? "downArrow.png" : "upArrow.png";

	    }

	}

	// pre($budgetVariation);




	foreach ($users as $user) {



	    if (in_array('daily_trends_alert', $notifications[$user['id']])) {

		foreach ($user_sites[$user['id']] as $site_id => $site_name) {



		    $isdailyTrendsChecked = 0;

		    $isdailyTrendsChecked = in_array($site_id, $dailyTrends);

		    if (!$isdailyTrendsChecked) {

			continue;

		    }



		    $subject = $day . ' Days Utilities Trends & Forecast Alert for ' . $current_date->format('F, Y') . ' - ' . $site_name;



		    $bodyHtml = '<div><h4>Dear ' . $user['firstname'] . ' ' . $user['lastname'] . ',</h4></div>';

		    $bodyHtml .= '<div>You will find here below the Key Utilities Trends for the first <strong>' . $day . ' Days</strong> of <strong>' . $current_date->format('F, Y') . '</strong> as compared to the similar period of last year at <strong>' . $hotel_detail['hotel_name'] . ' - ' . $site_name . '.</strong></div><br>';

		    $html = '

			<div>

			<table border=1 cellpadding=0 cellspacing=0 width="80%" style="border-radius">

			    <tr>

				<td align="center" width="100%"">

				    <table cellpadding=5 cellspacing=0 width="100%">

					<tr>
					    <th align="center" width="33%" style="border-right:1px solid black;border-bottom:1px solid black; " colspan="2" rowspan="2">Utility</th>

					    <th align="center" width="33%" colspan="3" style="border-right:1px solid black;border-bottom:1px solid black;">' . $fullmontharray[$current_month] . ' ' . $current_year . ' v/s ' . $fullmontharray[$current_month] . ' ' . $previous_year . '</th>

					    <th align="center" width="33%" colspan="3" style="border-bottom:1px solid black;">' . $fullmontharray[$current_month] . ' ' . $current_year . ' v/s Budget</th>

					</tr>

					<tr>
					    <th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.$previous_year.'</th>
					    <th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.$current_year.'</th>
						<th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">Variance</th>
					    <th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">Budget</th>
					    <th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.$current_year.'</th>
					    <th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">Variance</th>
					</tr>';



		    if ($sites[$site_id]['show_utility_electricity']) {



			$cost_image = '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['electricity']['budgetCost'] . '"/>';



			$consumption_image = '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['electricity']['budgetConsumption'] . '"/>';



			if ($arrow[$site_id]['electricity']['consumption'] == 0) {

			    $consumption_image = '';

			}

			if ($arrow[$site_id]['electricity']['cost'] == 0) {

			    $cost_image = '';

			}

			$arrow[$site_id]['electricity']['cost'];

			$arrow[$site_id]['electricity']['consumption'];

			$consumption_image;



			$html .= '

			    <tr>

				<th align="center" width="12%" style="border-right:1px solid black;border-bottom:1px solid black; " rowspan="3">Electricity</th>
				</tr>
				<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;">Consumption('.GetSiteUtilityUnitName($site_id,'electricity').')</th>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['electricity']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['electricity']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($variation[$site_id]['electricity']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['electricity']['consumption'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['electricity']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['electricity']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($budgetVariation[$site_id]['electricity']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['electricity']['budgetConsumption'] . '"/></td>
				</tr>
				<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">Cost('.$site['local_currency'].')</th>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['electricity']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['electricity']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($variation[$site_id]['electricity']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['electricity']['cost'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['electricity']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['electricity']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($budgetVariation[$site_id]['electricity']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['electricity']['budgetCost'] . '"/></td>
			    </tr>';

		    }

		    if ($sites[$site_id]['show_utility_water']) {

			$html .= '

			    <tr>

				<th align="center" width="12%" style="border-right:1px solid black;border-bottom:1px solid black; " rowspan="3">Water</th>
				</tr>
					<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;">Consumption('.GetSiteUtilityUnitName($site_id,'water').')</th>
					<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['water']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['water']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($variation[$site_id]['water']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['water']['consumption'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['water']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['water']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($budgetVariation[$site_id]['water']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['water']['budgetConsumption'] . '"/></td>
					</tr>
				<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">Cost('.$site['local_currency'].')</th>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['water']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['water']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($variation[$site_id]['water']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['water']['cost'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['water']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['water']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($budgetVariation[$site_id]['water']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['water']['budgetCost'] . '"/></td>
			    </tr>';

		    }

		    if ($sites[$site_id]['show_utility_fuel_oil']) {

			$html .= '

			    <tr>

				<th align="center" width="12%" style="border-right:1px solid black; border-bottom:1px solid black;" rowspan="3">Fuel Oil</th>
				</tr>
					<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;">Consumption('.GetSiteUtilityUnitName($site_id,'fuel_oil').')</th>
					<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['fuel_oil']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['fuel_oil']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($variation[$site_id]['fuel_oil']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['fuel_oil']['consumption'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['fuel_oil']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['fuel_oil']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($budgetVariation[$site_id]['fuel_oil']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['fuel_oil']['budgetConsumption'] . '"/></td>
					</tr>
				<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">Cost('.$site['local_currency'].')</th>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['fuel_oil']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['fuel_oil']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($variation[$site_id]['fuel_oil']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['fuel_oil']['cost'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['fuel_oil']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['fuel_oil']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($budgetVariation[$site_id]['fuel_oil']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['fuel_oil']['budgetCost'] . '"/></td>
			    </tr>';

		    }

		    if ($sites[$site_id]['show_utility_lpg']) {

			$html .= '

			    <tr>

				<th align="center" width="12%" style="border-right:1px solid black;border-bottom:1px solid black; " rowspan="3">LPG</th>
				</tr>
					<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;">Consumption('.GetSiteUtilityUnitName($site_id,'lpg').')</th>
					<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['lpg']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['lpg']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($variation[$site_id]['lpg']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['lpg']['consumption'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['lpg']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['lpg']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($budgetVariation[$site_id]['lpg']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['lpg']['budgetConsumption'] . '"/></td>
					</tr>
				<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">Cost('.$site['local_currency'].')</th>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['lpg']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['lpg']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($variation[$site_id]['lpg']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['lpg']['cost'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['lpg']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['lpg']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($budgetVariation[$site_id]['lpg']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['lpg']['budgetCost'] . '"/></td>
			    </tr>';

		    }

		    if ($sites[$site_id]['show_utility_natural_gas']) {

			$html .= '

			    <tr>

				<th align="center" width="12%" style="border-right:1px solid black; border-bottom:1px solid black;" rowspan="3">Natural Gas</th>
				</tr>
					<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;">Consumption('.GetSiteUtilityUnitName($site_id,'natural_gas').')</th>
					<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['natural_gas']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['natural_gas']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($variation[$site_id]['natural_gas']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['natural_gas']['consumption'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['natural_gas']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['natural_gas']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($budgetVariation[$site_id]['natural_gas']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['natural_gas']['budgetConsumption'] . '"/></td>
					</tr>
				<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">Cost('.$site['local_currency'].')</th>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['natural_gas']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['natural_gas']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($variation[$site_id]['natural_gas']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['natural_gas']['cost'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['natural_gas']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['natural_gas']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($budgetVariation[$site_id]['natural_gas']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['natural_gas']['budgetCost'] . '"/></td>
			    </tr>';

		    }

		    if ($sites[$site_id]['show_utility_district_cooling']) {

			$html .= '

			    <tr>

				<th align="center" width="12%" style="border-right:1px solid black; border-bottom:1px solid black;" rowspan="3">District Cooling</th>
				</tr>
					<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;">Consumption('.GetSiteUtilityUnitName($site_id,'district_cooling').')</th>
					<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['district_cooling']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['district_cooling']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($variation[$site_id]['district_cooling']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['district_cooling']['consumption'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['district_cooling']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['district_cooling']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($budgetVariation[$site_id]['district_cooling']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['district_cooling']['budgetConsumption'] . '"/></td>
					</tr>
				<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">Cost('.$site['local_currency'].')</th>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['district_cooling']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['district_cooling']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($variation[$site_id]['district_cooling']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['district_cooling']['cost'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['district_cooling']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['district_cooling']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($budgetVariation[$site_id]['district_cooling']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['district_cooling']['budgetCost'] . '"/></td>
			    </tr>';

		    }

		    if ($sites[$site_id]['show_utility_district_heating']) {

			$html .= '

			    <tr>

				<th align="center" width="12%" style="border-right:1px solid black; border-bottom:1px solid black;" rowspan="3">District Heating</th>
				</tr>
					<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;">Consumption('.GetSiteUtilityUnitName($site_id,'district_heating').')</th>
					<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['district_heating']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($variation[$site_id]['district_heating']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($variation[$site_id]['district_heating']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['district_heating']['consumption'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['district_heating']['consumption_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">'.(number_format($budgetVariation[$site_id]['district_heating']['consumption_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;">' . abs($budgetVariation[$site_id]['district_heating']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['district_heating']['budgetConsumption'] . '"/></td>
					</tr>
				<tr>
				<th align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">Cost('.$site['local_currency'].')</th>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['district_heating']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($variation[$site_id]['district_heating']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($variation[$site_id]['district_heating']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['district_heating']['cost'] . '"/></td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['district_heating']['cost_previous_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">'.(number_format($budgetVariation[$site_id]['district_heating']['cost_current_year'])).'</td>
				<td align="center" width="12%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . abs($budgetVariation[$site_id]['district_heating']['cost']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['district_heating']['budgetCost'] . '"/></td>
			    </tr>';

		    }



		    $html .= '</table>

			    </td>

			</tr>

		    </table></div><br>';

		    $bodyHtml .= $html;



		    //for daily hdd,cd and room night

		    $html = '

			<div>

			<table border=1 cellpadding="0" cellspacing="0" width="50%" >

			    <tr>

				<td align="center" width="100%"">

				    <table cellpadding=5 cellspacing=0 width="100%">

					<tr>

					    <th align="center" width="25%" style="border-right:1px solid black;border-bottom:1px solid black;">&nbsp;</th>

					    <th align="center" width="25%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . $fullmontharray[$current_month] . ' ' . $current_year . '</th>

					    <th align="center" width="25%" style="border-right: 1px solid black !important;border-bottom:1px solid black;">' . $fullmontharray[$current_month] . ' ' . $previous_year . '</th>

					    <th align="center" width="25%" style="border-bottom:1px solid black;">Variance (%)</th>

					</tr>

					<tr>

					    <th align="center" width="25%" style="border-right:1px solid black; ">CDD</th>

					    <td align="center" width="25%" style="border-right: 1px solid black !important;">' . round($current_10days_utilities[$site_id]['consumption']['total_cdd'], $decimal_places) . '</td>

					    <td align="center" width="25%" style="border-right: 1px solid black !important;">' . round($previous_10days_utilities[$site_id]['consumption']['total_cdd'], $decimal_places) . '</td>

					    <td align="center" width="25%">' . abs($variation[$site_id]['cdd']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['cdd']['consumption'] . '"/></td>

					</tr>

					<tr>

					    <th align="center" width="25%" style="border-right:1px solid black; ">HDD</th>

					    <td align="center" width="25%" style="border-right: 1px solid black !important;">' . round($current_10days_utilities[$site_id]['consumption']['total_hdd'], $decimal_places) . '</td>

					    <td align="center" width="25%" style="border-right: 1px solid black !important;">' . round($previous_10days_utilities[$site_id]['consumption']['total_hdd'], $decimal_places) . '</td>

					    <td align="center" width="25%">' . abs($variation[$site_id]['hdd']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['hdd']['consumption'] . '"/></td>

					</tr>

					<tr>

					    <th align="center" width="25%" style="border-right:1px solid black; ">Room nights</th>

					    <td align="center" width="25%" style="border-right: 1px solid black !important;">' . round($current_10days_utilities[$site_id]['consumption']['total_room_night'], $decimal_places) . '</td>

					    <td align="center" width="25%" style="border-right: 1px solid black !important;">' . round($previous_10days_utilities[$site_id]['consumption']['total_room_night'], $decimal_places) . '</td>

					    <td align="center" width="25%">' . abs($variation[$site_id]['total_room_night']['consumption']) . ' % <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $arrow[$site_id]['total_room_night']['consumption'] . '"/></td>

					</tr>

					';



		    $html .= '</table>

			    </td>

			</tr>

		    </table></div><br><br><h3>Forecasted Utilities Consumption/Cost : </h3></br></br>';

		    $bodyHtml .= $html;

		    $bodyHtml .= '<div>Based on the <strong>' . $day . ' Days</strong> Trends, you will find below an end of ' . $fullmontharray[$current_month] . ' ' . $current_year . ' Forecasted Consumption and Cost for every Utility in comparison to ' . $fullmontharray[$current_month] . ' ' . $previous_year . ' and the Budget of ' . $fullmontharray[$current_month] . ' ' . $current_year . '.</div><br>';

		    $currencySymbol = $sites[$site_id]['local_currency'] ? $sites[$site_id]['local_currency'] : BASE_CURRENCY_SYMBOL;



		    $forcast_this_year = 0;

		    $forcast_previous_year = 0;

		    $forcast_budget = 0;

		    $electricity_unit = GetSiteUtilityUnitName($site_id,'electricity');
		    $fuel_oil_unit = GetSiteUtilityUnitName($site_id,'fuel_oil');
		    $lpg_unit = GetSiteUtilityUnitName($site_id,'lpg');
		    $water_unit = GetSiteUtilityUnitName($site_id,'water');
		    $natural_gas_unit = GetSiteUtilityUnitName($site_id,'natural_gas');
		    $district_cooling_unit = GetSiteUtilityUnitName($site_id,'district_cooling');
		    $district_heating_unit = GetSiteUtilityUnitName($site_id,'district_heating');

		    $html = '<div>

				<table style="border-bottom:0; border-top:1px solid; border-right:1px solid; border-left:1px solid;" width="100%" cellspacing="0" cellpadding="0" >

				    <tbody>

					<tr>

					    <td "="" width="100%" align="center">

						<table width="100%" cellspacing="0" cellpadding="5" style="border-bottom:0; border-top:1px solid; border-right:1px solid; border-left:1px solid;" >

						    <tbody>

							<tr>

							    <th style="border-right:1px solid black;border-bottom:1px solid black;" align="center">Utilities</th>

							    <th colspan="2" style="border-right: 1px solid black !important;border-bottom:1px solid black;" align="center">Forecasted - ' . $fullmontharray[$current_month] . ' ' . $current_year . '</th>

							    <th colspan="2" style="border-right: 1px solid black !important;border-bottom:1px solid black;" align="center">' . $fullmontharray[$current_month] . ' ' . $previous_year . '</th>

							    <th colspan="2" style="border-right: 1px solid black !important;border-bottom:1px solid black;" align="center">Budget</th>

							</tr>

							<tr>

							    <th style="border-right:1px solid black;border-bottom:1px solid black;" align="center">&nbsp;</th>

							    <th style="border-right: 1px solid black !important;border-bottom:1px solid black;" align="center">Consumption</th>

							    <th style="border-right: 1px solid black !important;border-bottom:1px solid black;" align="center">Cost (' . $currencySymbol . ')</th>

							    <th style="border-right: 1px solid black !important;border-bottom:1px solid black;" align="center">Consumption</th>

							    <th style="border-right: 1px solid black !important;border-bottom:1px solid black;" align="center">Cost (' . $currencySymbol . ')</th>

							    <th style="border-right: 1px solid black !important;border-bottom:1px solid black;" align="center">Consumption</th>

							    <th style="border-right: 1px solid black !important;border-bottom:1px solid black;" align="center">Cost (' . $currencySymbol . ')</th>

							</tr>';

		    if ($sites[$site_id]['show_utility_electricity']) {



			$forcast_this_year += $forecast[$site_id]['current']['electricity']['cost'];

			$forcast_previous_year += $forecast[$site_id]['previous']['electricity']['cost'];

			$forcast_budget += $forecastBudget[$site_id]['electricity']['cost'];



			$html .= '

								<tr>
								    <td align="center" style="border-right:1px solid black; "><b>Electricity ('.$electricity_unit.')</b></td>
								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['electricity']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['electricity']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['electricity']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['electricity']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['electricity']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['electricity']['cost']) . '</td>

								</tr>';

		    }

		    if ($sites[$site_id]['show_utility_water']) {



			$forcast_this_year += $forecast[$site_id]['current']['water']['cost'];

			$forcast_previous_year += $forecast[$site_id]['previous']['water']['cost'];

			$forcast_budget += $forecastBudget[$site_id]['water']['cost'];



			$html .= '

								<tr>
								    <td align="center" style="border-right:1px solid black; "><b>Water ('.$water_unit.')</b></td>
								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['water']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['water']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['water']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['water']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['water']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['water']['cost']) . '</td>

								</tr>';

		    }



		    if ($sites[$site_id]['show_utility_fuel_oil']) {



			$forcast_this_year += $forecast[$site_id]['current']['fuel_oil']['cost'];

			$forcast_previous_year += $forecast[$site_id]['previous']['fuel_oil']['cost'];

			$forcast_budget += $forecastBudget[$site_id]['fuel_oil']['cost'];



			$html .= '

								<tr>
								<td align="center" style="border-right:1px solid black; "><b>Fuel Oil ('.$fuel_oil_unit.')</b></td>
								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['fuel_oil']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['fuel_oil']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['fuel_oil']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['fuel_oil']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['fuel_oil']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['fuel_oil']['cost']) . '</td>

								</tr>';

		    }



		    if ($sites[$site_id]['show_utility_lpg']) {



			$forcast_this_year += $forecast[$site_id]['current']['lpg']['cost'];

			$forcast_previous_year += $forecast[$site_id]['previous']['lpg']['cost'];

			$forcast_budget += $forecastBudget[$site_id]['lpg']['cost'];



			$html .= '

								<tr>
								<td align="center" style="border-right:1px solid black; "><b>LPG ('.$lpg_unit.')</b></td>
								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['lpg']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['lpg']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['lpg']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['lpg']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['lpg']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['lpg']['cost']) . '</td>

								</tr>';

		    }



		    if ($sites[$site_id]['show_utility_natural_gas']) {



			$forcast_this_year += $forecast[$site_id]['current']['natural_gas']['cost'];

			$forcast_previous_year += $forecast[$site_id]['previous']['natural_gas']['cost'];

			$forcast_budget += $forecastBudget[$site_id]['natural_gas']['cost'];





			$html .= '

								<tr>
								    <td align="center" style="border-right:1px solid black; "><b>Natural Gas ('.$natural_gas_unit.')</b></td>
								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['natural_gas']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['natural_gas']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['natural_gas']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['natural_gas']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['natural_gas']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['natural_gas']['cost']) . '</td>

								</tr>';

		    }



		    if ($sites[$site_id]['show_utility_district_cooling']) {



			$forcast_this_year += $forecast[$site_id]['current']['district_cooling']['cost'];

			$forcast_previous_year += $forecast[$site_id]['previous']['district_cooling']['cost'];

			$forcast_budget += $forecastBudget[$site_id]['district_cooling']['cost'];



			$html .= '

								<tr>
								    <td align="center" style="border-right:1px solid black; "><b>District Cooling ('.$district_cooling_unit.')</b></td>
								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['district_cooling']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['district_cooling']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['district_cooling']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['district_cooling']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['district_cooling']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['district_cooling']['cost']) . '</td>

								</tr>';

		    }



		    if ($sites[$site_id]['show_utility_district_heating']) {



			$forcast_this_year += $forecast[$site_id]['current']['district_heating']['cost'];

			$forcast_previous_year += $forecast[$site_id]['previous']['district_heating']['cost'];

			$forcast_budget += $forecastBudget[$site_id]['district_heating']['cost'];



			$html .= '

								<tr>
								    <td align="center" style="border-right:1px solid black; "><b>District Heating ('.$district_heating_unit.')</b></td>
								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['district_heating']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['current']['district_heating']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['district_heating']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecast[$site_id]['previous']['district_heating']['cost']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['district_heating']['consumption']) . '</td>

								    <td align="center" style="border-right:1px solid black; ">' . number_format($forecastBudget[$site_id]['district_heating']['cost']) . '</td>

								</tr>';

		    }

		    $html .= '<tr>

			    <td style="border-right: 1px black solid; border-top: 1px black solid; border-bottom: 1px black solid;" align="center"><strong>Total</strong></td>

			    <td style="border-right: 1px black solid; border-top: 1px black solid;" align="center">&nbsp;</td>

			    <td style="border-right: 1px black solid; border-top: 1px black solid; border-bottom: 1px black solid;" align="center"><strong>' . number_format($forcast_this_year) . '</strong></td>

			    <td style="border-right: 1px black solid; border-top: 1px black solid;"  align="center">&nbsp;</td>

			    <td style="border-right: 1px black solid; border-top: 1px black solid; border-bottom: 1px black solid;" align="center"><strong>' . number_format($forcast_previous_year) . '</strong></td>

			    <td style="border-right: 1px black solid; border-top: 1px black solid;" align="center">&nbsp;</td>

			    <td style="border-right: 1px black solid; border-top: 1px black solid; border-bottom: 1px black solid;"  align="center"><strong>' . number_format($forcast_budget) . '</strong></td>

			</tr>';





		    $html .= '                      </tbody>

						 </table>

					     </td>

					 </tr>

				     </tbody>

				 </table>

			     </div>';



		    $bodyHtml .= $html;



		    $email_template['html'] = $bodyHtml;

		    $email_template['more_info'] = true;

		    $body = $this->load->view('email_template', $email_template, true);

		    // mail("surbhi.ladhava@tatvasoft.com",$subject,$body);

		    // exit;
		    $this->mailer->mail->AddAddress($user['email']);

		    // $this->mailer->mail->AddAddress("garima.pandey@tatvasoft.com");

		    $this->mailer->mail->Subject = $subject;

		    $this->mailer->mail->Body = $body;

		    $this->mailer->mail->Send();

		    $this->mailer->mail->ClearAllRecipients();

		}

	    }

	}

	echo "END";

	exit;

    }



    //url : /hotel_portal/cron/AverageConsumption7days



    public function AverageConsumption7days() {

	$utilityNames = array();

	$site_url = site_url();

	$user_sites = $this->cron_model->getUserSites();

	$users = $this->cron_model->getUserList();

	$notifications = $this->cron_model->getUserCronNotifications();

	$sites = $this->cron_model->get_site_details();

	$isUsedInCronUtility = $this->cron_model->read_daily_reading_utilites_setting();

	$mainUtilities = $this->cron_model->read_main_utilites();

	$this->load->model('sites/sites_model');

	$this->load->library('mailer');

	$this->mailer->mail->IsHTML(true);

	$thresoldAvg = array();

	$mainThresoldAvg = array();

	$thresoldAvg_chr = array();

	$mainThresoldAvg_chr = array();

	$utilities_array = array('total_room_night', 'cdd', 'hdd');

	$utilities_array_lang = array(lang('total_room_night'), lang('cdd'), lang('hdd'));



	$day = 7;

	$decimal_places = 2;

	$current_month = intval(date('m'));

	$current_year = date('Y');

	$previous_year = date('Y') - 1;

	$this->load->model('utilities/utilities_model');



	$data = array();

	$current_7days_utilities = array();

	$previous_7days_utilities = array();

	$variation = array();

	$forecast = array();



	foreach ($sites as $key => $site) {

	    if ($site['is_used_in_cron'] == 1) {

		$finalResponse = array();

		$data['id'] = $site['id'];

		$site_id = $site['id'];



		$data['endMonth'] = date("m", strtotime("-3day"));

		$data['endDay'] = date("d", strtotime("-3day"));

		$data['endYear'] = date("Y", strtotime("-3day"));



		$data['startMonth'] = date("m", strtotime("-9day"));

		$data['startDay'] = date("d", strtotime("-9day"));

		$data['startYear'] = date("Y", strtotime("-9day"));

		$data['site_id'] = $site_id;





		$this->cron_model->month_id = $current_month;

		$this->cron_model->year_id = $previous_year;

		$this->cron_model->site_id = $site_id;



		$daily_reading_settings_current = $this->cron_model->get_daily_reading_cron_settings($data);

		$daily_utility_current = $this->cron_model->get_daily_utility_cron_settings($data);



		$daily_reading_settings_current_chr = $this->cron_model->get_daily_reading_cron_settings_chr($data);

		$daily_utility_current_chr = $this->cron_model->get_daily_utility_cron_settings_chr($data);



		if ($daily_reading_settings_current) {

		    foreach ($daily_reading_settings_current as $utilityData) {

			if (in_array($utilityData['utility_id'], $isUsedInCronUtility[$site['id']])) {

			    $finalResponse['current'][$utilityData['utility_title_id']] = $utilityData['Avg'];

			    $utilityNames[$utilityData['utility_title_id']] = $utilityData['title'];

			}

		    }

		}



		if ($daily_reading_settings_current_chr) {

		    foreach ($daily_reading_settings_current_chr as $utilityData_chr) {



			$finalResponse_chr['current'][$utilityData_chr['utility_title_id']] = $utilityData_chr['Avg'];

			$utilityNames_chr[$utilityData_chr['utility_title_id']] = $utilityData_chr['title'];

		    }

		}





		$data['endMonth'] = date("m", strtotime("-10day"));

		$data['endDay'] = date("d", strtotime("-10day"));

		$data['endYear'] = date("Y", strtotime("-10day"));



		$data['startMonth'] = date("m", strtotime("-16day"));

		$data['startDay'] = date("d", strtotime("-16day"));

		$data['startYear'] = date("Y", strtotime("-16day"));



		$daily_reading_settings_previous = $this->cron_model->get_daily_reading_cron_settings($data);

		$daily_utilitys_previous = $this->cron_model->get_daily_utility_cron_settings($data);



		$daily_reading_settings_previous_chr = $this->cron_model->get_daily_reading_cron_settings_chr($data);

		$daily_utilitys_previous_chr = $this->cron_model->get_daily_utility_cron_settings_chr($data);



		if ($daily_utility_current) {



		    foreach ($daily_utility_current as $mainUtilityKey => $mainUtilityData) {



			if (in_array($mainUtilities[$mainUtilityKey], $isUsedInCronUtility[$site['id']])) {



			    if (isset($daily_utilitys_previous[$mainUtilityKey])) {



				$difference = $mainUtilityData - $daily_utilitys_previous[$mainUtilityKey];

				$upPrecent = round(($difference * 100) / $mainUtilityData);



				if ($difference > 0 && $upPrecent > $site['threshold']) {

				    $temp = array();

				    $temp['utility'] = lang($mainUtilityKey);

				    $temp['percentage'] = $upPrecent;

				    $mainThresoldAvg[$site['id']][] = $temp;

				}

			    } else {

				$upPrecent = 100;

				if ($upPrecent > $site['threshold']) {

				    $temp = array();

				    $temp['utility'] = lang($mainUtilityKey);

				    $temp['percentage'] = $upPrecent;

				    $mainThresoldAvg[$site['id']][] = $temp;

				}

			    }

			}

		    }

		}

		if ($daily_utility_current_chr) {

		    foreach ($daily_utility_current_chr as $mainUtilityKey_chr => $mainUtilityData_chr) {



			if (in_array($mainUtilityKey_chr, $utilities_array)) {



			    if (isset($daily_utilitys_previous_chr[$mainUtilityKey_chr])) {



				$difference = $mainUtilityData_chr - $daily_utilitys_previous_chr[$mainUtilityKey_chr];

				$upPrecent = round(($difference * 100) / $mainUtilityData_chr);



				$temp_chr = array();

				$temp_chr['utility'] = lang($mainUtilityKey_chr);

				$temp_chr['percentage'] = $upPrecent;

				$mainThresoldAvg_chr[$site['id']][] = $temp_chr;



				/* if($difference > 0 && $upPrecent > $site['threshold']){

				  }

				  else

				  {

				  $temp_chr = array();

				  $temp_chr['utility'] = lang($mainUtilityKey_chr);

				  $temp_chr['percentage'] = 0;

				  $mainThresoldAvg_chr[$site['id']][] = $temp_chr;

				  } */

			    } else {

				// $upPrecent = 100;

				$temp_chr = array();

				$temp_chr['utility'] = lang($mainUtilityKey_chr);

				$temp_chr['percentage'] = 100;

				$mainThresoldAvg_chr[$site['id']][] = $temp_chr;



				/* if($upPrecent > $site['threshold']){

				  } */

			    }

			}

		    }

		}



		if ($daily_reading_settings_previous) {

		    foreach ($daily_reading_settings_previous as $utilityData) {

			if (in_array($utilityData['utility_id'], $isUsedInCronUtility[$site['id']])) {

			    $finalResponse['previous'][$utilityData['utility_title_id']] = $utilityData['Avg'];

			    $utilityNames[$utilityData['utility_title_id']] = $utilityData['title'];

			}

		    }

		}



		if ($daily_reading_settings_previous_chr) {

		    foreach ($daily_reading_settings_previous_chr as $utilityData_chr) {

			$finalResponse_chr['previous'][$utilityData_chr['utility_title_id']] = $utilityData_chr['Avg'];

			$utilityNames_chr[$utilityData_chr['utility_title_id']] = $utilityData_chr['title'];

		    }

		}



		foreach ($finalResponse as $key => $values) {

		    foreach ($values as $utilityId => $utilityVal) {

			if ($key == 'current') {

			    if (isset($finalResponse['previous'][$utilityId])) {

				if ($finalResponse['previous'][$utilityId] < $utilityVal) {

				    $difference = $utilityVal - $finalResponse['previous'][$utilityId];

				    $upPrecent = round(($difference * 100) / $utilityVal);

				    if ($difference > 0 && $upPrecent > $site['threshold']) {

					$temp = array();

					$temp['utility'] = $utilityNames[$utilityId];

					$temp['percentage'] = $upPrecent;

					$thresoldAvg[$site['id']][] = $temp;

				    }

				}

			    } else {

				if ($utilityVal) {

				    $upPrecent = 100;

				    if ($upPrecent > $site['threshold']) {

					$temp = array();

					$temp['utility'] = $utilityNames[$utilityId];

					$temp['percentage'] = $upPrecent;

					$thresoldAvg[$site['id']][] = $temp;

				    }

				}

			    }

			}

		    }

		}



		foreach ($finalResponse_chr as $key => $values) {

		    foreach ($values as $utilityId => $utilityVal) {

			if ($key == 'current') {

			    if (isset($finalResponse_chr['previous'][$utilityId])) {

				if ($finalResponse_chr['previous'][$utilityId] < $utilityVal) {

				    $difference = $utilityVal - $finalResponse_chr['previous'][$utilityId];

				    $upPrecent = round(($difference * 100) / $utilityVal);

				    $temp_chr = array();

				    $temp_chr['utility'] = $utilityNames[$utilityId];

				    $temp_chr['percentage'] = $upPrecent;

				    $thresoldAvg_chr[$site['id']][] = $temp_chr;



				    /* if($difference > 0 && $upPrecent > $site['threshold']){

				      } */

				}

			    } else {

				if ($utilityVal) {

				    // $upPrecent = 100;

				    $temp_chr = array();

				    $temp_chr['utility'] = $utilityNames[$utilityId];

				    $temp_chr['percentage'] = 100;

				    $thresoldAvg_chr[$site['id']][] = $temp_chr;



				    /* if($upPrecent > $site['threshold']){

				      } */

				}

			    }

			}

		    }

		}

	    }

	}



	foreach ($users as $user) {

	    if (in_array('7_days_average_consumption', $notifications[$user['id']])) {

		foreach ($user_sites[$user['id']] as $site_id => $site_name) {

		    if ((isset($thresoldAvg[$site_id]) && $thresoldAvg[$site_id]) || (isset($mainThresoldAvg[$site_id]) && $mainThresoldAvg[$site_id])) {

			$subject = '7 Days Average Consumption Alert for ' . $site_name;

			$bodyHtml = '<div><h4>Dear ' . $user['firstname'] . ' ' . $user['lastname'] . '</h4></div>';

			$bodyHtml .= '<div>This is to notify you that the 7 Days Average Consumption of the below listed Utilities have exceeded the Previous 7 Days Average by more than the threshold of ' . $sites[$site_id]['threshold'] . '%</div>';

			if (isset($mainThresoldAvg[$site_id]) && $mainThresoldAvg[$site_id]) {

			    foreach ($mainThresoldAvg[$site_id] as $mainThresoldData) {

				$bodyHtml .= '<li><strong>' . $mainThresoldData['utility'] . ' : </strong> <img width="15px" height="15px" src="' . $site_url . '/themes/default/images/upArrow.png"/> &nbsp; ' . $mainThresoldData['percentage'] . '%</li>';

			    }

			}

			if (isset($thresoldAvg[$site_id]) && $thresoldAvg[$site_id]) {

			    foreach ($thresoldAvg[$site_id] as $thresoldData) {

				$bodyHtml .= '<li><strong>' . $thresoldData['utility'] . ' : </strong> <img width="15px" height="15px" src="' . $site_url . '/themes/default/images/upArrow.png"/> &nbsp; ' . $thresoldData['percentage'] . '%</li>';

			    }

			}



			$bodyHtml .= "The variable parameters' trends  affecting your utilities consumption are as follows";



			if (isset($mainThresoldAvg_chr[$site_id]) && $mainThresoldAvg_chr[$site_id]) {

			    foreach ($mainThresoldAvg_chr[$site_id] as $mainThresoldData) {

				if (in_array($mainThresoldData['utility'], $utilities_array_lang)) {

				    if ($mainThresoldData['utility'] == 'Room Nights') {

					$arrowimage = "upArrowGreen.png";

				    } else {

					$arrowimage = "upArrow.png";

				    }

				    $bodyHtml .= '<li><strong>' . $mainThresoldData['utility'] . ' : </strong> <img width="15px" height="15px" src="' . $site_url . '/themes/default/images/' . $arrowimage . '"/> &nbsp; ' . abs($mainThresoldData['percentage']) . '%</li>';

				}

			    }

			}

			if (isset($thresoldAvg_chr[$site_id]) && $thresoldAvg_chr[$site_id]) {

			    foreach ($thresoldAvg_chr[$site_id] as $thresoldData) {

				if (in_array($thresoldData['utility'], $utilities_array_lang)) {

				    if ($thresoldData['utility'] == 'Room Nights') {

					$arrowimage = "upArrowGreen.png";

				    } else {

					$arrowimage = "upArrow.png";

				    }

				    $bodyHtml .= '<li><strong>' . $thresoldData['utility'] . ' : </strong> <img width="15px" height="15px" src="' . $site_url . '/themes/default/images/' . $arrowimage . '"/> &nbsp; ' . abs($thresoldData['percentage']) . '%</li>';

				}

			    }

			}



			$email_template['html'] = $bodyHtml;

			$body = $this->load->view('email_template', $email_template, true);



			$this->mailer->mail->AddAddress($user['email']);

			// $this->mailer->mail->AddAddress('surbhi.ladhava@tatvasoft.com');

			$this->mailer->mail->Subject = $subject;

			$this->mailer->mail->Body = $body;

			$this->mailer->mail->Send();

			$this->mailer->mail->ClearAllRecipients();

		    }

		}

	    }

	}



	echo 'End';

    }



    //url : /hotel_portal/cron/monthUtilityCostVsBudgetAlert

    public function monthUtilityCostVsBudgetAlert() {

	if (date('m') == 1) {

	    $current_month = 12;

	    $current_year = date('Y') - 1;

	} else {

	    $current_month = date('m') - 1;

	    $current_year = date('Y');

	}



	$current_date = new DateTime(date('d') . "-" . $current_month . "-" . $current_year);



	$this->load->model('cron_model');

	$this->load->model('utilities/utilities_model');



	$this->load->library('mailer');

	$this->mailer->mail->IsHTML(true);



	//Hotel detail

	$this->load->model('hotels/hotels_model');

	$hotel_detail = $this->hotels_model->get_hotel_detail(1);



	$data = array();

	$comparision = array();



	$user_sites = $this->cron_model->getUserSites();

	$sites = $this->cron_model->get_sites_list();

	$notifications = $this->cron_model->getUserCronNotifications();

	$users = $this->cron_model->getUserList();

	$variation = array();



	foreach ($sites as $site_id => $site_name) {

	    $this->utilities_model->site_id = $site_id;



	    //month and year filter for utilities

	    $this->utilities_model->utilities_month = $current_month;

	    $this->utilities_model->utilities_year = $current_year;



	    $data[$site_id] = $this->utilities_model->getUtility();



	    $variation[$site_id]['electricity'] = is_infinite(round(($data[$site_id]["total_electricity_cost"] - $data[$site_id]["electricity_total_budget_cost"]) * 100 / $data[$site_id]["electricity_total_budget_cost"], 2)) ? 0 : round(($data[$site_id]["total_electricity_cost"] - $data[$site_id]["electricity_total_budget_cost"]) * 100 / $data[$site_id]["electricity_total_budget_cost"], 2);

	    $variation[$site_id]['electricity'] = is_nan($variation[$site_id]['electricity']) ? 0 : $variation[$site_id]['electricity'];

	    $variation[$site_id]['water'] = is_infinite(round(($data[$site_id]["water_total_consumption_cost"] - $data[$site_id]["water_total_consumption_budget_cost"]) * 100 / $data[$site_id]["water_total_consumption_budget_cost"], 2)) ? 0 : round(($data[$site_id]["water_total_consumption_cost"] - $data[$site_id]["water_total_consumption_budget_cost"]) * 100 / $data[$site_id]["water_total_consumption_budget_cost"], 2);



	    $variation[$site_id]['water'] = is_nan($variation[$site_id]['water']) ? 0 : $variation[$site_id]['water'];

	    $variation[$site_id]['fuel_oil'] = is_infinite(round(($data[$site_id]["total_fuel_oil_cost"] - $data[$site_id]["fuel_total_budget_cost"]) * 100 / $data[$site_id]["fuel_total_budget_cost"], 2)) ? 0 : round(($data[$site_id]["total_fuel_oil_cost"] - $data[$site_id]["fuel_total_budget_cost"]) * 100 / $data[$site_id]["fuel_total_budget_cost"], 2);

	    $variation[$site_id]['fuel_oil'] = is_nan($variation[$site_id]['fuel_oil']) ? 0 : $variation[$site_id]['fuel_oil'];

	    $variation[$site_id]['lpg'] = is_infinite(round(($data[$site_id]["total_lpg_cost"] - $data[$site_id]["lpg_total_budget_cost"]) * 100 / $data[$site_id]["lpg_total_budget_cost"], 2)) ? 0 : round(($data[$site_id]["total_lpg_cost"] - $data[$site_id]["lpg_total_budget_cost"]) * 100 / $data[$site_id]["lpg_total_budget_cost"], 2);

	    $variation[$site_id]['lpg'] = is_nan($variation[$site_id]['lpg']) ? 0 : $variation[$site_id]['lpg'];

	    $variation[$site_id]['natural_gas'] = is_infinite(round(($data[$site_id]["total_natural_gas_cost"] - $data[$site_id]["natural_gas_total_budget_cost"]) * 100 / $data[$site_id]["natural_gas_total_budget_cost"], 2)) ? 0 : round(($data[$site_id]["total_natural_gas_cost"] - $data[$site_id]["natural_gas_total_budget_cost"]) * 100 / $data[$site_id]["natural_gas_total_budget_cost"], 2);

	    $variation[$site_id]['natural_gas'] = is_nan($variation[$site_id]['natural_gas']) ? 0 : $variation[$site_id]['natural_gas'];

	    $variation[$site_id]['district_heating'] = is_infinite(round(($data[$site_id]["district_heating_cost"] - $data[$site_id]["district_heating_total_budget_cost"]) * 100 / $data[$site_id]["district_heating_total_budget_cost"], 2)) ? 0 : round(($data[$site_id]["district_heating_cost"] - $data[$site_id]["district_heating_total_budget_cost"]) * 100 / $data[$site_id]["district_heating_total_budget_cost"], 2);

	    $variation[$site_id]['district_heating'] = is_nan($variation[$site_id]['district_heating']) ? 0 : $variation[$site_id]['district_heating'];

	    $variation[$site_id]['district_cooling'] = is_infinite(round(($data[$site_id]["district_cooling_cost"] - $data[$site_id]["district_cooling_total_budget_cost"]) * 100 / $data[$site_id]["district_cooling_total_budget_cost"], 2)) ? 0 : round(($data[$site_id]["district_cooling_cost"] - $data[$site_id]["district_cooling_total_budget_cost"]) * 100 / $data[$site_id]["district_cooling_total_budget_cost"], 2);

	    $variation[$site_id]['district_cooling'] = is_nan($variation[$site_id]['district_cooling']) ? 0 : $variation[$site_id]['district_cooling'];



	    $total_cost = $data[$site_id]["total_electricity_cost"] + $data[$site_id]["water_total_consumption_cost"] + $data[$site_id]["total_fuel_oil_cost"] + $data[$site_id]["total_lpg_cost"] + $data[$site_id]["total_natural_gas_cost"] + $data[$site_id]["district_heating_cost"] + $data[$site_id]["district_cooling_cost"];

	    $total_budget = $data[$site_id]["electricity_total_budget_cost"] + $data[$site_id]["water_total_consumption_budget_cost"] + $data[$site_id]["fuel_total_budget_cost"] + $data[$site_id]["lpg_total_budget_cost"] + $data[$site_id]["natural_gas_total_budget_cost"] + $data[$site_id]["district_heating_total_budget_cost"] + $data[$site_id]["district_cooling_total_budget_cost"];



	    $variation[$site_id]['total_variation'] = is_infinite(round(($total_cost - $total_budget) * 100 / $total_cost, 2)) ? 0 : round(($total_cost - $total_budget) * 100 / $total_cost, 2);

	    $variation[$site_id]['total_variation'] = is_nan($variation[$site_id]['total_variation']) ? 0 : $variation[$site_id]['total_variation'];

	}



	foreach ($users as $user) {



	    if (in_array('budget_comparision_alert', $notifications[$user['id']])) {



		foreach ($user_sites[$user['id']] as $site_id => $site_name) {

		    $subject = 'Utilities Cost v/s Budget Alert for ' . $current_date->format('F, Y') . ' - ' . $site_name;



		    $budget_varience_flag = false;



		    if (array_key_exists($site_id, $variation)) {



			$bodyHtml = '<div><h4>Dear ' . $user['firstname'] . ' ' . $user['lastname'] . ',</h4></div>';

			$bodyHtml .= '<div>This is to notify you that the <strong>Monthly Utilities Costs</strong> of the below  listed utilities of <strong>' . $current_date->format('F, Y') . '</strong> have exceeded the budgeted ones for the same month in <strong>' . $hotel_detail['hotel_name'] . ' - ' . $site_name . '</strong> with the below percentages.</div>';

			foreach ($variation[$site_id] as $type => $single_variation) {

			    if ($type != "total_variation" && $single_variation > 10) {

				$bodyHtml .= '<li><strong>' . ucwords(str_replace('_', ' ', $type)) . ' Cost : </strong><strong style="color:red;">' . $single_variation . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

				$budget_varience_flag = true;

			    } else if ($type == "total_variation") {

				if ($single_variation > 0) {

				    $img = 'upArrow.png';

				    $style = "color:red;";

				} else {

				    $img = 'downArrow.png';

				    $style = "color:green;";

				    $single_variation = ($single_variation == 0) ? $single_variation : -$single_variation;

				}

				$bodyHtml .= '<p><div><strong>Your Utilities Cost for ' . $current_date->format('F Y') . ' v/s Budget : <span style= "' . $style . '">' . $single_variation . '%</span></strong> <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $img . '"/></p></div>';

			    }

			}



			if ($budget_varience_flag) {

			    $email_template['html'] = $bodyHtml;

			    $body = $this->load->view('email_template', $email_template, true);



			    $this->mailer->mail->AddAddress($user['email']);

			    // $this->mailer->mail->AddAddress('surbhi.ladhava@tatvasoft.com');

			    $this->mailer->mail->Subject = $subject;

			    $this->mailer->mail->Body = $body;

			    $this->mailer->mail->Send();

			    $this->mailer->mail->ClearAllRecipients();

			    //echo $body;

			}

		    }

		}

	    }

	}

	echo "END";

	exit;

    }



    //url : /hotel_portal/cron/ytdUtilityCostVsBudgetAlert

    public function ytdUtilityCostVsBudgetAlert() {

	$start_month = 1;

	$end_month = date('m') - 1;

	if ($end_month == 0) {

	    // Not a YTD

	    exit;

	}

	$current_year = date('Y');

	$current_date = new DateTime(date('d') . "-" . $end_month . "-" . $current_year);



	$this->load->model('cron_model');

	$this->load->model('utilities/utilities_model');



	$this->load->library('mailer');

	$this->mailer->mail->IsHTML(true);



	//Hotel detail

	$this->load->model('hotels/hotels_model');

	$hotel_detail = $this->hotels_model->get_hotel_detail(1);



	$data = array();



	$user_sites = $this->cron_model->getUserSites();

	$sites = $this->cron_model->get_sites_list();

	$notifications = $this->cron_model->getUserCronNotifications();

	$users = $this->cron_model->getUserList();



	foreach ($sites as $site_id => $site_name) {

	    $this->cron_model->site_id = $site_id;



	    //ytd data

	    $ytd_data = $this->cron_model->get_ytd_data();

	    $data[$site_id]['electricity_variation'] = round(($ytd_data['total_electricity_cost'] - $ytd_data['electricity_total_budget_cost']) * 100 / $ytd_data['electricity_total_budget_cost'], 2);

	    $data[$site_id]['water_variation'] = round(($ytd_data['water_total_consumption_cost'] - $ytd_data['water_total_consumption_budget_cost']) * 100 / $ytd_data['water_total_consumption_budget_cost'], 2);

	    $data[$site_id]['fuel_oil_variation'] = round(($ytd_data['total_fuel_oil_cost'] - $ytd_data['fuel_total_budget_cost']) * 100 / $ytd_data['fuel_total_budget_cost'], 2);

	    $data[$site_id]['lpg_variation'] = round(($ytd_data['total_lpg_cost'] - $ytd_data['lpg_total_budget_cost']) * 100 / $ytd_data['lpg_total_budget_cost'], 2);

	    $data[$site_id]['natural_gas_variation'] = round(($ytd_data['total_natural_gas_cost'] - $ytd_data['natural_gas_total_budget_cost']) * 100 / $ytd_data['natural_gas_total_budget_cost'], 2);

	    $data[$site_id]['district_heating_variation'] = round(($ytd_data['district_heating_cost'] - $ytd_data['district_heating_total_budget_cost']) * 100 / $ytd_data['district_heating_total_budget_cost'], 2);

	    $data[$site_id]['district_cooling_variation'] = round(($ytd_data['district_cooling_cost'] - $ytd_data['district_cooling_total_budget_cost']) * 100 / $ytd_data['district_cooling_total_budget_cost'], 2);



	    $total_cost = $ytd_data['total_electricity_cost'] + $ytd_data['water_total_consumption_cost'] + $ytd_data['total_fuel_oil_cost'] + $ytd_data['total_lpg_cost'] + $ytd_data['total_natural_gas_cost'] + $ytd_data['district_heating_cost'] + $ytd_data['district_cooling_cost'];

	    $total_budget = $ytd_data['electricity_total_budget_cost'] + $ytd_data['water_total_consumption_budget_cost'] + $ytd_data['fuel_total_budget_cost'] + $ytd_data['lpg_total_budget_cost'] + $ytd_data['natural_gas_total_budget_cost'] + $ytd_data['district_heating_total_budget_cost'] + $ytd_data['district_cooling_total_budget_cost'];

	    $data[$site_id]['total_variation'] = round(($total_cost - $total_budget) * 100 / $total_cost, 2);

	}



	foreach ($users as $user) {



	    if (in_array('ytd_budget_alert', $notifications[$user['id']])) {



		foreach ($user_sites[$user['id']] as $site_id => $site_name) {

		    $subject = 'YTD Utilities Cost v/s Budget Alert ' . $current_date->format('Y') . ' - ' . $site_name;



		    $ytd_budget_flag = false;



		    if (array_key_exists($site_id, $data)) {



			$bodyHtml = '<div><h4>Dear ' . $user['firstname'] . ' ' . $user['lastname'] . ',</h4></div>';

			$bodyHtml .= '<div>This is to notify you that the <strong>YTD Utilities Costs</strong> of <strong>' . $current_date->format('Y') . '</strong> exceeds the Budgeted Figures for <strong>' . $hotel_detail['hotel_name'] . ' - ' . $site_name . '</strong> by more than 10%.</div><div>Percentage increase is as follows:</div>';



			/* if ($data[$site_id]['electricity_variation'] > 10) {

			  $bodyHtml .= '<li>Electricity Consumption : <strong style="color:red;">' . $data[$site_id]['electricity_variation'] . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			  $ytd_budget_flag = true;

			  }

			  if ($data[$site_id]['water_variation'] > 10) {

			  $bodyHtml .= '<li>Water Consumption : <strong style="color:red;">' . $data[$site_id]['water_variation'] . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			  $ytd_budget_flag = true;

			  }

			  if ($data[$site_id]['fuel_oil_variation'] > 10) {

			  $bodyHtml .= '<li>Fuel Oil Consumption : <strong style="color:red;">' . $data[$site_id]['fuel_oil_variation'] . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			  $ytd_budget_flag = true;

			  }

			  if ($data[$site_id]['lpg_variation'] > 10) {

			  $bodyHtml .= '<li>LPG Consumption : <strong style="color:red;">' . $data[$site_id]['lpg_variation'] . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			  $ytd_budget_flag = true;

			  }

			  if ($data[$site_id]['natural_gas_variation'] > 10) {

			  $bodyHtml .= '<li>Natural Gas Consumption : <strong style="color:red;">' . $data[$site_id]['natural_gas_variation'] . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			  $ytd_budget_flag = true;

			  }

			  if ($data[$site_id]['district_heating_variation'] > 10) {

			  $bodyHtml .= '<li>District Heating Consumption : <strong style="color:red;">' . $data[$site_id]['district_heating_variation'] . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			  $ytd_budget_flag = true;

			  }

			  if ($data[$site_id]['district_cooling_variation'] > 10) {

			  $bodyHtml .= '<li>District Cooling Consumption : <strong style="color:red;">' . $data[$site_id]['district_cooling_variation'] . '%</strong>  <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/upArrow.png"/></li>';

			  $ytd_budget_flag = true;

			  } */



			if ($data[$site_id]['total_variation'] > 10) {

			    $img = 'upArrow.png';

			    $style = "color:red;";

			    $bodyHtml .= '<p><div><strong>Your Year To Date Utilities Cost v/s Budget : <span style= "' . $style . '">' . $data[$site_id]['total_variation'] . '%</span></strong> <img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $img . '"/></p></div>';

			    $ytd_budget_flag = true;

			}



			if ($ytd_budget_flag) {

			    $email_template['html'] = $bodyHtml;

			    $body = $this->load->view('email_template', $email_template, true);



			    $this->mailer->mail->AddAddress($user['email']);

			    // $this->mailer->mail->AddAddress('surbhi.ladhava@tatvasoft.com');

			    $this->mailer->mail->Subject = $subject;

			    $this->mailer->mail->Body = $body;

			    $this->mailer->mail->Send();

			    $this->mailer->mail->ClearAllRecipients();

			    //echo $body;

			}

		    }

		}

	    }

	}

	echo "END";

	exit;

    }



    //url : /hotel_portal/cron/updateFullYearDailyCddHdd/[2015, 2016, 2017,......]

    public function updateFullYearDailyCddHdd($year) {



	$this->load->model('cron/cron_model');

	$sites = $this->cron_model->get_all_sites_for_location();

	$dataList = array();



	$start_date = $year . "-01-01";

	$end_date = "";

	if ($year == date("Y")) {

	    $end_date = date('Y-m-d');

	} else {

	    $end_date = $year . '-12-31';

	}



	if (!empty($sites) && is_numeric($year)) {

	    foreach ($sites as $site) {



		$stationId = $site['station_id'];



		$baseCdd = floatval($site['base_cdd_temprature']);

		$baseHdd = floatval($site['base_hdd_temprature']);



		$baseCddTemprature = (!empty($baseCdd)) ? $baseCdd : 20.5;

		$baseHddTemprature = (!empty($baseHdd)) ? $baseHdd : 15.5;



		if (empty($stationId)) {

		    continue;

		}



		$postXml = '<LocationDataRequest>

				<StationIdLocation>

				    <StationId>' . $stationId . '</StationId>

				</StationIdLocation>

				<DataSpecs>

				    <DatedDataSpec key="dailyHDD">

					<HeatingDegreeDaysCalculation>

					    <CelsiusBaseTemperature>' . $baseHddTemprature . '</CelsiusBaseTemperature>

					</HeatingDegreeDaysCalculation>

					<DailyBreakdown>

					    <DayRangePeriod>

						<DayRange first="' . $start_date . '" last="' . $end_date . '"/>

					    </DayRangePeriod>

					</DailyBreakdown>

				    </DatedDataSpec>



				    <DatedDataSpec key="dailyCDD">

					<CoolingDegreeDaysCalculation>

					    <CelsiusBaseTemperature>' . $baseCddTemprature . '</CelsiusBaseTemperature>

					</CoolingDegreeDaysCalculation>

					<DailyBreakdown>

					    <DayRangePeriod>

						<DayRange first="' . $start_date . '" last="' . $end_date . '"/>

					    </DayRangePeriod>

					</DailyBreakdown>

				    </DatedDataSpec>

				</DataSpecs>

			    </LocationDataRequest>';



		$result = $this->callCddHddApi($postXml);



		if (isset($result->Failure) && !empty($result->Failure->Code->__toString())) {

		    continue;

		}



		foreach ($result->LocationDataResponse->DataSets->DatedDataSet[0]->Values->V as $v) {

		    $date = $v->attributes()->d->__toString();

		    $value = $v->__toString();

		    $dataList[$site['id']][$date]['hdd'] = $value;

		}



		foreach ($result->LocationDataResponse->DataSets->DatedDataSet[1]->Values->V as $v) {

		    $date = $v->attributes()->d->__toString();

		    $value = $v->__toString();

		    $dataList[$site['id']][$date]['cdd'] = $value;

		}

	    }

	}



	if (!empty($dataList)) {

	    $this->cron_model->insert_daily_utilities_cdd($dataList);

	}



	echo 'End';

	exit;

    }



    //url : /hotel_portal/cron/updateFullYearMonthlyCddHdd/[2015, 2016,2017,......]

    public function updateFullYearMonthlyCddHdd($year) {



	$this->load->model('cron/cron_model');

	$sites = $this->cron_model->get_all_sites_for_location();

	$dataList = array();



	$start_date = $year . "-01-01";

	$end_date = "";

	if ($year == date("Y")) {

	    $end_date = date('Y-m-d');

	} else {

	    $end_date = $year . '-12-31';

	}



	if (!empty($sites) && is_numeric($year)) {

	    foreach ($sites as $site) {



		$stationId = $site['station_id'];



		$baseCdd = floatval($site['base_cdd_temprature']);

		$baseHdd = floatval($site['base_hdd_temprature']);



		$baseCddTemprature = (!empty($baseCdd)) ? $baseCdd : 20.5;

		$baseHddTemprature = (!empty($baseHdd)) ? $baseHdd : 15.5;



		if (empty($stationId)) {

		    continue;

		}



		$postXml = '<LocationDataRequest>

				<StationIdLocation>

				    <StationId>' . $stationId . '</StationId>

				</StationIdLocation>

				<DataSpecs>

				    <DatedDataSpec key="dailyHDD">

					<HeatingDegreeDaysCalculation>

					    <CelsiusBaseTemperature>' . $baseHddTemprature . '</CelsiusBaseTemperature>

					</HeatingDegreeDaysCalculation>

					<MonthlyBreakdown>

					    <DayRangePeriod>

						<DayRange first="' . $start_date . '" last="' . $end_date . '"/>

					    </DayRangePeriod>

					</MonthlyBreakdown>

				    </DatedDataSpec>



				    <DatedDataSpec key="dailyCDD">

					<CoolingDegreeDaysCalculation>

					    <CelsiusBaseTemperature>' . $baseCddTemprature . '</CelsiusBaseTemperature>

					</CoolingDegreeDaysCalculation>

					<MonthlyBreakdown>

					    <DayRangePeriod>

						<DayRange first="' . $start_date . '" last="' . $end_date . '"/>

					    </DayRangePeriod>

					</MonthlyBreakdown>

				    </DatedDataSpec>

				</DataSpecs>

			    </LocationDataRequest>';



		$result = $this->callCddHddApi($postXml);



		if (isset($result->Failure) && !empty($result->Failure->Code->__toString())) {

		    continue;

		}



		foreach ($result->LocationDataResponse->DataSets->DatedDataSet[0]->Values->V as $v) {

		    $date = $v->attributes()->d->__toString();

		    $value = $v->__toString();

		    $dataList[$site['id']][$date]['hdd'] = $value;

		}



		foreach ($result->LocationDataResponse->DataSets->DatedDataSet[1]->Values->V as $v) {

		    $date = $v->attributes()->d->__toString();

		    $value = $v->__toString();

		    $dataList[$site['id']][$date]['cdd'] = $value;

		}

	    }

	}



	if (isset($dataList) && !empty($dataList)) {

	    $this->cron_model->insert_monthly_utilities_cdd($dataList);

	}



	echo 'End';

	exit;

    }



}

