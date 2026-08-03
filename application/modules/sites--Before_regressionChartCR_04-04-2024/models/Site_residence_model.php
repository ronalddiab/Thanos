<?php

/**
 *  Site Residence Model
 *
 *  To perform queries related to user management.
 *
 * @package CIDemoApplication
 * @subpackage Site Residence
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Site_Residence_model extends Base_Model
{
    protected $_table = TBL_SITE_RESIDENCE;
    protected $_table_residence_split_log = 'site_utility_residence_split_log';
    public $site_id = "";
    public $user_id = "";
    public $year_id = "";
    public $utility_type = "";
    public $private_program_consumption = "";
    public $rental_program_residence_consumption = "";
    
    public function get_site_residence_model_detail_by_siteId()
    {
        if(isset($this->year_id) && !empty($this->year_id)) {
            $this->year_id = (int) $this->year_id;
        }

        if(isset($this->site_id) && !empty($this->site_id)) {
            $this->site_id = (int) $this->site_id;
        }

        if(isset($this->utility_type) && !empty($this->utility_type)) {
            $this->utility_type = $this->utility_type;
        }

        if(isset($this->private_program_consumption) && !empty($this->private_program_consumption)) {
            $this->private_program_consumption = $this->private_program_consumption;
        }

        if(isset($this->rental_program_residence_consumption) && !empty($this->rental_program_residence_consumption)) {
            $this->rental_program_residence_consumption = $this->rental_program_residence_consumption;
        }

        $this->db->select('s.*');
        $this->db->where('s.site_id', $this->site_id);
        
        if(isset($this->utility_type)) {
            $this->db->where('s.year_id', $this->year_id);
            $this->db->where('s.utility_type', $this->utility_type);
            $this->db->from($this->_table . ' AS s');
            $query = $this->db->get();
            return $this->db->custom_result($query);
        } else if (isset($this->site_id) && isset($this->year_id) ) {
            $this->db->where('s.year_id', $this->year_id);
            $this->db->from($this->_table . ' AS s');
            $query = $this->db->get();
            return $query->result_array();
        } else if (isset($this->site_id) && isset($this->private_program_consumption)) {
            $this->db->where('s.private_program_consumption', $this->private_program_consumption);
            $this->db->from($this->_table . ' AS s');
            $query = $this->db->get();
            return $this->db->custom_result($query);
        } else if(isset($this->site_id) && isset($this->rental_program_residence_consumption)) {
            $this->db->where('s.rental_program_residence_consumption', $this->rental_program_residence_consumption);
            $this->db->from($this->_table . ' AS s');
            $query = $this->db->get();
            return $this->db->custom_result($query);
        } else {
            return [];
        }
    }

    /**
     * Function insert_site_residence to insert record
     */
    function insert_site_residence() {

        $data_array = array();

        if(isset($this->site_id)) {
            $data_array['site_id'] = $this->site_id;
        }

        if(isset($this->user_id)) {
            $data_array['user_id'] = $this->user_id;
        }

        if(isset($this->year_id)) {
            $data_array['year_id'] = $this->year_id;
        }

        if(isset($this->utility_type)) {
            $data_array['utility_type'] = $this->utility_type;
        }

        if(isset($this->private_program_consumption)) {
            $data_array['private_program_consumption'] = $this->private_program_consumption;
        } else {
            $data_array['private_program_consumption'] = NULL;
        }

        if(isset($this->private_program_fixed)) {
            $data_array['private_program_fixed'] = $this->private_program_fixed;
        } else {
            $data_array['private_program_fixed'] = NULL;
        }

        if(isset($this->private_program_float)) {
            $data_array['private_program_float'] = $this->private_program_float;
        } else {
            $data_array['private_program_float'] = NULL;
        }

        if(isset($this->private_program_hotel_connected)) {
            $data_array['private_program_hotel_connected'] = $this->private_program_hotel_connected;
        } else {
            $data_array['private_program_hotel_connected'] = NULL;
        }

        if(isset($this->rental_program_residence_consumption)) {
            $data_array['rental_program_residence_consumption'] = $this->rental_program_residence_consumption;
        } else {
            $data_array['rental_program_residence_consumption'] = NULL;
        }

        if(isset($this->rental_program_residence_fixed)) {
            $data_array['rental_program_residence_fixed'] = $this->rental_program_residence_fixed;
        } else {
            $data_array['rental_program_residence_fixed'] = NULL;
        }

        if(isset($this->rental_program_residence_float)) {
            $data_array['rental_program_residence_float'] = $this->rental_program_residence_float;
        } else {
            $data_array['rental_program_residence_float'] = NULL;
        }

        if(isset($this->rental_program_residence_hotel_connected)) {
            $data_array['rental_program_residence_hotel_connected'] = $this->rental_program_residence_hotel_connected;
        } else {
            $data_array['rental_program_residence_hotel_connected'] = NULL;
        }
        
        $dataAlreadyExist = $this->get_site_residence_model_detail_by_siteId(); 
        if(empty($dataAlreadyExist)) {
            $data_array['created_at'] = GetCurrentDateTime();
            $data_array['created_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];
            $data_action = 'Create';
            
            $this->db->set($data_array);
            $id = $this->db->insert($this->_table);
        } else {
            $data_array['modify_at'] = GetCurrentDateTime();
            $data_array['modify_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];
            $data_action = 'Update';

            $this->db->where(array('site_id' => $this->site_id,'year_id' => $this->year_id,'utility_type' => $this->utility_type));
            if(isset($this->user_id) && !empty($this->user_id)) {
                $this->db->where(array('user_id' => $this->user_id));
            } else {
                $this->db->where(array('user_id' => NULL));
            }
            $this->db->set($data_array);
            $id = $this->db->update($this->_table);
        }

        // Save audit trail
        $site_id = $this->site_id;
        $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
        saveAuditTrail($this->user_id, $this->site_id, 'Site Residence', $data_action);

        return $id; 
    }

    public function delete_entry_ifexist($data){
        $site_id  = $data['site_id'];
        $year_id  = $data['year_id'];
        $utility_type  = $data['utility_type'];

        $this->db->where('site_id',$site_id);
        $this->db->where('year_id',$year_id); 
        $this->db->where('utility_type',$utility_type); 
       return $this->db->delete($this->_table);
    }

    // All Calculation logic to split utility data with residence data
    public function calculateSplits($site_residence_result,$utility,$residence_type,$site_detail) {
        if(isset($site_residence_result) && isset($utility) && !empty($residence_type)) {
            $selectedUtility = $site_residence_result['utility_type'];
            switch ($selectedUtility) {
                case 'electricity':
                    $utilityName =  'total_purchased_electricity';
                    $utilityRate =  'average_purchased_electricity';
                    $utilityCost =  'total_purchased_electricity_cost';

                    $utilityTotalName =  'total_electricity_kwh';
        	    $utilityTotalRate =  'average_cost_per_kwh';   

                    $utilityHotelName =  'electricity_hotel';
                    $utilityHotelRate =  'electricity_hotel_rate';
                    $utilityHotelCost =  'electricity_hotel_cost';
                    break;
                case 'fuel_oil':
                    $utilityName =  'total_fuel_oil';
                    $utilityRate =  'total_fuel_oil_rate';
                    $utilityCost =  'fuel_total_budget_cost';

                    $utilityHotelName =  'fuel_oil_hotel';
                    $utilityHotelRate =  'fuel_oil_hotel_rate';
                    $utilityHotelCost =  'fuel_oil_hotel_cost';
                    break;  
                case 'lpg':
                    $utilityName =  'total_lpg';
                    $utilityRate =  'total_lpg_rate';
                    $utilityCost =  'total_lpg_cost';

                    $utilityHotelName =  'lpg_hotel';
                    $utilityHotelRate =  'lpg_hotel_rate';
                    $utilityHotelCost =  'lpg_hotel_cost';
                    break;
                case 'water':
                    $utilityName =  'water_total_consumption';
                    $utilityRate =  'water_total_consumption_rate';
                    $utilityCost =  'water_total_consumption_cost';

                    $utilityHotelName =  'water_hotel';
                    $utilityHotelRate =  'water_hotel_rate';
                    $utilityHotelCost =  'water_hotel_cost';
                    break;
                case 'natural_gas':
                    $utilityName =  'total_natural_gas';
                    $utilityRate =  'total_natural_gas_rate';
                    $utilityCost =  'total_natural_gas_cost';

                    $utilityHotelName =  'natural_gas_hotel';
                    $utilityHotelRate =  'natural_gas_hotel_rate';
                    $utilityHotelCost =  'natural_gas_hotel_cost';
                    break;
                case 'district_cooling':
                    $utilityName = 'district_cooling_total_budget';
                    $utilityCost = 'district_cooling_total_budget_cost';
                    $utilityRate = 'district_cooling_rate';

                    $utilityHotelName = 'district_cooling_hotel';
                    $utilityHotelRate = 'district_cooling_hotel_rate';
                    $utilityHotelCost = 'district_cooling_hotel_cost';
                    break;
                case 'district_heating':
                    $utilityName = 'district_heating_total_budget';
                    $utilityCost = 'district_heating_total_budget_cost';
                    $utilityRate = 'district_heating_rate';

                    $utilityHotelName =  'district_heating_hotel';
                    $utilityHotelRate = 'district_heating_hotel_rate';
                    $utilityHotelCost = 'district_heating_hotel_cost';
                    break;
                default:
                    $utilityName = $utilityRate = $utilityCost = $utilityHotelName = $utilityHotelRate = $utilityHotelCost = $utilityReduceOnName = $utilityReduceOnRate = $utilityReduceOnCost = '';
                    break;
            }
            
            if($residence_type == RENTAL_PROGRAM_RESIDENCE) {
                $residenceUtility =  'rental_program_residence_'.$selectedUtility;
                $residenceUtilityRate =  'rental_program_residence_'.$selectedUtility.'_rate';
                $residenceUtilityCost =  'rental_program_residence_'.$selectedUtility.'_cost';
                $consumptionMethod = $site_residence_result['rental_program_residence_consumption'];
                $splitPercentage = ($consumptionMethod == 3) ? $site_residence_result['rental_program_residence_fixed'] : $site_residence_result['rental_program_residence_float'];
            } else if($residence_type == PRIVATE_RESIDENCE) {
                $residenceUtility =  'private_program_'.$selectedUtility;
                $residenceUtilityRate =  'private_program_'.$selectedUtility.'_rate';
                $residenceUtilityCost =  'private_program_'.$selectedUtility.'_cost';
                $consumptionMethod = $site_residence_result['private_program_consumption'];
                $splitPercentage = ($consumptionMethod == 3) ? $site_residence_result['private_program_fixed'] : $site_residence_result['private_program_float'];
            }

            $dataLogResidenceArray = [
                'site_id' => $utility['site_id'],
                'user_id' => $utility['user_id'],
                'year_id' => $utility['year'],
                'month_id' => $utility['month'],
                'utility' => $selectedUtility,
                'residence_type' => $residence_type
            ];

            if(isset($consumptionMethod) && !empty($consumptionMethod)) {
                $dataLogResidenceArray['consumption_method'] = $consumptionMethod;

                if($consumptionMethod == 1) {
                    $dataLogResidenceArray['is_hotel_connected'] = 1;
                }

                if(!$this->checkLogResidenceExist($dataLogResidenceArray)) {
                    // Calculation for Split by fixed/float percentage
                    if($consumptionMethod == 2 || $consumptionMethod == 3 && isset($splitPercentage) && !empty($splitPercentage)) {
                        $utility[$residenceUtility] = ((float)$utility[$utilityName] * $splitPercentage) / 100;
                        $utility[$residenceUtilityRate] = ((float)$utility[$utilityRate] * $splitPercentage) / 100;
                        $utility[$residenceUtilityCost] = ((float)$utility[$utilityCost] * $splitPercentage) / 100;
                    }

                    if($selectedUtility != 'electricity') {
                        $utility[$utilityName] = (float)$utility[$utilityName] + $utility[$residenceUtility];
                        $utility[$utilityRate] = (float)$utility[$utilityRate] + $utility[$residenceUtilityRate];
                        $utility[$utilityCost] = (float)$utility[$utilityCost] + $utility[$residenceUtilityCost];
                    } 
                    else {          
                        $utility[$utilityTotalName] = (float)$utility[$utilityTotalName] + $utility[$residenceUtility];
                        $utility[$utilityTotalRate] = (float)$utility[$utilityTotalRate] + $utility[$residenceUtilityRate];
                        $utility[$utilityCost] = (float)$utility[$utilityCost] + $utility[$residenceUtilityCost];
                    }

                    $utility[$utilityHotelName] = (float)$utility[$utilityHotelName] - $utility[$residenceUtility];
                    $utility[$utilityHotelRate] = (float)$utility[$utilityHotelRate] - $utility[$residenceUtilityRate];
                    $utility[$utilityHotelCost] = (float)$utility[$utilityHotelCost] - $utility[$residenceUtilityCost];

                }                
            }

            return $utility;
        }
    }

	public function calculateEqualForChargeByMeter($site_residence_result, $utility, $residence_type, $site_detail)
	{
		if (isset($site_residence_result) && isset($utility) && !empty($residence_type)) {
			$selectedUtility = $site_residence_result['utility_type'];
			switch ($selectedUtility) {
				case 'electricity':
					$utilityName =  'total_purchased_electricity';
		                        $utilityRate =  'average_purchased_electricity';
		                        $utilityCost =  'total_purchased_electricity_cost';

					$utilityHotelName =  'electricity_hotel';
					$utilityHotelRate =  'electricity_hotel_rate';
					$utilityHotelCost =  'electricity_hotel_cost';
					break;
				case 'fuel_oil':
					$utilityName =  'total_fuel_oil';
					$utilityRate =  'total_fuel_oil_rate';
					$utilityCost =  'fuel_total_budget_cost';

					$utilityHotelName =  'fuel_oil_hotel';
					$utilityHotelRate =  'fuel_oil_hotel_rate';
					$utilityHotelCost =  'fuel_oil_hotel_cost';
					break;
				case 'lpg':
					$utilityName =  'total_lpg';
					$utilityRate =  'total_lpg_rate';
					$utilityCost =  'total_lpg_cost';

					$utilityHotelName =  'lpg_hotel';
					$utilityHotelRate =  'lpg_hotel_rate';
					$utilityHotelCost =  'lpg_hotel_cost';
					break;
				case 'water':
					$utilityName =  'water_total_consumption';
					$utilityRate =  'water_total_consumption_rate';
					$utilityCost =  'water_total_consumption_cost';

					$utilityHotelName =  'water_hotel';
					$utilityHotelRate =  'water_hotel_rate';
					$utilityHotelCost =  'water_hotel_cost';
					break;
				case 'natural_gas':
					$utilityName =  'total_natural_gas';
					$utilityRate =  'total_natural_gas_rate';
					$utilityCost =  'total_natural_gas_cost';

					$utilityHotelName =  'natural_gas_hotel';
					$utilityHotelRate =  'natural_gas_hotel_rate';
					$utilityHotelCost =  'natural_gas_hotel_cost';
					break;
				case 'district_cooling':
					$utilityName = 'district_cooling_total_budget';
					$utilityCost = 'district_cooling_total_budget_cost';
					$utilityRate = 'district_cooling_rate';

					$utilityHotelName = 'district_cooling_hotel';
					$utilityHotelRate = 'district_cooling_hotel_rate';
					$utilityHotelCost = 'district_cooling_hotel_cost';
					break;
				case 'district_heating':
					$utilityName = 'district_heating_total_budget';
					$utilityCost = 'district_heating_total_budget_cost';
					$utilityRate = 'district_heating_rate';

					$utilityHotelName =  'district_heating_hotel';
					$utilityHotelRate = 'district_heating_hotel_rate';
					$utilityHotelCost = 'district_heating_hotel_cost';
					break;
				default:
					$utilityName = $utilityRate = $utilityCost = $utilityHotelName = $utilityHotelRate = $utilityHotelCost = $utilityReduceOnName = $utilityReduceOnRate = $utilityReduceOnCost = '';
					break;
			}
		}
		if ($residence_type == RENTAL_PROGRAM_RESIDENCE) {
			$consumptionMethod = $site_residence_result['rental_program_residence_consumption'];
		} else if ($residence_type == PRIVATE_RESIDENCE) {
			$consumptionMethod = $site_residence_result['private_program_consumption'];
		}
		$dataLogResidenceArray = [
			'site_id' => $utility['site_id'],
			'user_id' => $utility['user_id'],
			'year_id' => $utility['year'],
			'month_id' => $utility['month'],
			'utility' => $selectedUtility,
			'residence_type' => $residence_type
		];

		if (isset($consumptionMethod) && !empty($consumptionMethod)) {
			$dataLogResidenceArray['consumption_method'] = $consumptionMethod;
		        if($consumptionMethod == 1) {
		            $dataLogResidenceArray['is_hotel_connected'] = 0;
		        }
			if (!$this->checkLogResidenceExist($dataLogResidenceArray)) {
				// Calculation for Split by fixed/float percentage
				$utility[$utilityHotelName] = (float)$utility[$utilityName];
				$utility[$utilityHotelRate] = (float)$utility[$utilityRate];
				$utility[$utilityHotelCost] = (float)$utility[$utilityCost];
			}
		}
		return $utility;
	}
	// log entry once calculation for residence data is split and stored 
	public function logResidence($data_array)
	{
        	unset($data_array['is_hotel_connected']);
		$this->db->set($data_array);
		$id = $this->db->insert($this->_table_residence_split_log);
		return $id;
	}

    // check whether utility residence data calculations already updated to avoid repeated deduction
    public function checkLogResidenceExist($data_array) {
        $response = '';
        $this->db->select('*');
        $this->db->from($this->_table_residence_split_log);
        foreach ($data_array as $key => $value) {
            if($key != 'is_hotel_connected'){
                $this->db->where($key,$value);
            }
        }
        if ($this->db->get()->num_rows() > 0) {
            $response = true;
            if($data_array['consumption_method'] == 1 && $data_array['is_hotel_connected'] == 1) {
                $response = false;
            }
        } else {
            $response = false;            
            $this->logResidence($data_array);
        }
         return $response;
    }

    // calculate float area percentage w.r.t built up area and residences on site>edit.
    public function calculateFloatPercentage($site_detail,$residenceType) {
        $built_up_area = $site_detail['site_builtup_area'];
        // PRIVATE REISDENCE
        if($residenceType == PRIVATE_RESIDENCE) {
            $residenceValue = $site_detail['rental_private_residence'];
        }
        // RENTAL RESIDENCE
        if($residenceType == RENTAL_PROGRAM_RESIDENCE) {
            $residenceValue = $site_detail['rental_program_residence'];
        }
        if($residenceValue != '' && $residenceValue != 0 && $built_up_area != '' && $built_up_area != 0) {
	    return calculateDashboardPercentage($residenceValue, $built_up_area);
        } else {
            return 0;
        }
    }

    // Update float area percentage w.r.t site_id and consumptions 
    public function updatePercentage($percentage,$floatField, $consumptionField) {
        $data_array = [];
        $data_array[$floatField] = $percentage;
        $this->db->where(array('site_id' => $this->site_id,$consumptionField => 2));
        $this->db->set($data_array);
        $id = $this->db->update($this->_table);
        return $id;
    }
}