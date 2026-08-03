<?php

/**
 *  Site Waste Model
 *
 *  To perform queries related to user management.
 *
 * @package CIDemoApplication
 * @subpackage Site Waste
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Site_Waste_model extends Base_Model
{
    protected $_table = TBL_SITE_WASTE;
    protected $_table_waste_upload = TBL_SITE_WASTE_UPLOAD_INVOICE;
    public $site_id = "";
    public $user_id = "";
    public $year_id = "";
    public $month_id = "";
    
    public function get_site_waste_model_detail_by_siteId_userId()
    {
        if(!isset($this->year_id) && empty($this->year_id) && $this->year_id == 0) {
            $this->year_id = NULL;
        } else {
            $this->year_id = (int) $this->year_id;
        }
        if(!isset($this->month_id) && empty($this->month_id) && $this->month_id == 0) {
            $this->month_id = NULL;
        } else {
            $this->month_id = (int) $this->month_id;
        }
        $this->db->select('s.*');
        $this->db->where('s.deleted_at', null);
        $this->db->where('s.deleted_by', null);
        $this->db->where('s.site_id', $this->site_id);
        $this->db->where('s.year_id', $this->year_id);
        if($this->year_id && (!isset($this->month_id) && empty($this->month_id) && $this->month_id == 0)) {
            $this->db->where('s.month_id is NOT NULL', NULL, FALSE);
        } else {
            $this->db->where('s.month_id', $this->month_id);
        }
        $this->db->from($this->_table . ' AS s');
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    public function get_site_waste_utility_data($site_id) {
        $this->db->select('s.*');
        $this->db->where('s.deleted_at', null);
        $this->db->where('s.deleted_by', null);
        $this->db->where('s.site_id', $site_id);
        $this->db->where('s.year_id is NOT NULL', NULL, FALSE);
        $this->db->where('s.month_id is NOT NULL', NULL, FALSE);
        $this->db->from($this->_table . ' AS s');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Function insert_site_waste to insert record
     */
    function insert_site_waste() {

        $data_array = array();

        if(isset($this->site_id)) {
            $data_array['site_id'] = $this->site_id;
        }

        // if(isset($this->user_id)) {
        //     $data_array['user_id'] = $this->user_id;
        // }

        if(isset($this->year_id)) {
            $data_array['year_id'] = $this->year_id;
        }

        if(isset($this->month_id)) {
            $data_array['month_id'] = $this->month_id;
        }
        
        if(isset($this->typical_destination_bottles_cans)) {
            $data_array['typical_destination_bottles_cans'] = $this->typical_destination_bottles_cans;
        } else {
            $data_array['typical_destination_bottles_cans'] = 0;
        }

        if(isset($this->unit_measure_dropdown_bottles_cans)) {
            $data_array['unit_measure_dropdown_bottles_cans'] = $this->unit_measure_dropdown_bottles_cans;
        } else {
            $data_array['unit_measure_dropdown_bottles_cans'] = 0;
        }

        if(isset($this->source_bottles_cans)) {
            $data_array['source_bottles_cans'] = $this->source_bottles_cans;
        } else {
            $data_array['source_bottles_cans'] = 0;
        }

        if(isset($this->monthly_tracking_bottles_cans)) {
            $data_array['monthly_tracking_bottles_cans'] = $this->monthly_tracking_bottles_cans;
        } else {
            $data_array['monthly_tracking_bottles_cans'] = 0;
        }

        if(isset($this->unit_measure_bottles_cans)) {
            $data_array['unit_measure_bottles_cans'] = $this->unit_measure_bottles_cans;
        }

        if(isset($this->disposal_cost_bottles_cans)) {
            $data_array['disposal_cost_bottles_cans'] = $this->disposal_cost_bottles_cans;
        }

        if(isset($this->total_bottles_cans)) {
            $data_array['total_bottles_cans'] = $this->total_bottles_cans;
        }

        if(isset($this->is_check_bottles_cans) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_bottles_cans'] = $this->is_check_bottles_cans;
        }

        if(isset($this->typical_destination_cardboard)) {
            $data_array['typical_destination_cardboard'] = $this->typical_destination_cardboard;
        } else {
            $data_array['typical_destination_cardboard'] = 0;
        }

        if(isset($this->unit_measure_dropdown_cardboard)) {
            $data_array['unit_measure_dropdown_cardboard'] = $this->unit_measure_dropdown_cardboard;
        } else {
            $data_array['unit_measure_dropdown_cardboard'] = 0;
        }

        if(isset($this->source_cardboard)) {
            $data_array['source_cardboard'] = $this->source_cardboard;
        } else {
            $data_array['source_cardboard'] = 0;
        }

        if(isset($this->monthly_tracking_cardboard)) {
            $data_array['monthly_tracking_cardboard'] = $this->monthly_tracking_cardboard;
        } else {
            $data_array['monthly_tracking_cardboard'] = 0;
        }

        if(isset($this->unit_measure_cardboard)) {
            $data_array['unit_measure_cardboard'] = $this->unit_measure_cardboard;
        }

        if(isset($this->disposal_cost_cardboard)) {
            $data_array['disposal_cost_cardboard'] = $this->disposal_cost_cardboard;
        }

        if(isset($this->total_cardboard)) {
            $data_array['total_cardboard'] = $this->total_cardboard;
        }

        if(isset($this->is_check_cardboard) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_cardboard'] = $this->is_check_cardboard;
        }

        if(isset($this->typical_destination_paper)) {
            $data_array['typical_destination_paper'] = $this->typical_destination_paper;
        } else {
            $data_array['typical_destination_paper'] = 0;
        }

        if(isset($this->unit_measure_dropdown_paper)) {
            $data_array['unit_measure_dropdown_paper'] = $this->unit_measure_dropdown_paper;
        } else {
            $data_array['unit_measure_dropdown_paper'] = 0;
        }

        if(isset($this->source_paper)) {
            $data_array['source_paper'] = $this->source_paper;
        } else {
            $data_array['source_paper'] = 0;
        }

        if(isset($this->monthly_tracking_paper)) {
            $data_array['monthly_tracking_paper'] = $this->monthly_tracking_paper;
        } else {
            $data_array['monthly_tracking_paper'] = 0;
        }

        if(isset($this->unit_measure_paper)) {
            $data_array['unit_measure_paper'] = $this->unit_measure_paper;
        }

        if(isset($this->disposal_cost_paper)) {
            $data_array['disposal_cost_paper'] = $this->disposal_cost_paper;
        }

        if(isset($this->total_paper)) {
            $data_array['total_paper'] = $this->total_paper;
        }

        if(isset($this->is_check_paper) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_paper'] = $this->is_check_paper;
        }

        if(isset($this->typical_destination_mixed_glass)) {
            $data_array['typical_destination_mixed_glass'] = $this->typical_destination_mixed_glass;
        } else {
            $data_array['typical_destination_mixed_glass'] = 0;
        }

        if(isset($this->unit_measure_dropdown_mixed_glass)) {
            $data_array['unit_measure_dropdown_mixed_glass'] = $this->unit_measure_dropdown_mixed_glass;
        } else {
            $data_array['unit_measure_dropdown_mixed_glass'] = 0;
        }

        if(isset($this->source_mixed_glass)) {
            $data_array['source_mixed_glass'] = $this->source_mixed_glass;
        } else {
            $data_array['source_mixed_glass'] = 0;
        }

        if(isset($this->monthly_tracking_mixed_glass)) {
            $data_array['monthly_tracking_mixed_glass'] = $this->monthly_tracking_mixed_glass;
        } else {
            $data_array['monthly_tracking_mixed_glass'] = 0;
        }

        if(isset($this->unit_measure_mixed_glass)) {
            $data_array['unit_measure_mixed_glass'] = $this->unit_measure_mixed_glass;
        }

        if(isset($this->disposal_cost_mixed_glass)) {
            $data_array['disposal_cost_mixed_glass'] = $this->disposal_cost_mixed_glass;
        }

        if(isset($this->total_mixed_glass)) {
            $data_array['total_mixed_glass'] = $this->total_mixed_glass;
        }

        if(isset($this->is_check_mixed_glass) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_mixed_glass'] = $this->is_check_mixed_glass;
        }

        if(isset($this->typical_destination_alluminium)) {
            $data_array['typical_destination_alluminium'] = $this->typical_destination_alluminium;
        } else {
            $data_array['typical_destination_alluminium'] = 0;
        }

        if(isset($this->unit_measure_dropdown_alluminium)) {
            $data_array['unit_measure_dropdown_alluminium'] = $this->unit_measure_dropdown_alluminium;
        } else {
            $data_array['unit_measure_dropdown_alluminium'] = 0;
        }

        if(isset($this->source_alluminium)) {
            $data_array['source_alluminium'] = $this->source_alluminium;
        } else {
            $data_array['source_alluminium'] = 0;
        }

        if(isset($this->monthly_tracking_alluminium)) {
            $data_array['monthly_tracking_alluminium'] = $this->monthly_tracking_alluminium;
        } else {
            $data_array['monthly_tracking_alluminium'] = 0;
        }

        if(isset($this->unit_measure_alluminium)) {
            $data_array['unit_measure_alluminium'] = $this->unit_measure_alluminium;
        }

        if(isset($this->disposal_cost_alluminium)) {
            $data_array['disposal_cost_alluminium'] = $this->disposal_cost_alluminium;
        }

        if(isset($this->total_alluminium)) {
            $data_array['total_alluminium'] = $this->total_alluminium;
        }

        if(isset($this->is_check_alluminium) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_alluminium'] = $this->is_check_alluminium;
        }

        if(isset($this->typical_destination_pete_plastic_bottles)) {
            $data_array['typical_destination_pete_plastic_bottles'] = $this->typical_destination_pete_plastic_bottles;
        } else {
            $data_array['typical_destination_pete_plastic_bottles'] = 0;
        }

        if(isset($this->unit_measure_dropdown_pete_plastic_bottles)) {
            $data_array['unit_measure_dropdown_pete_plastic_bottles'] = $this->unit_measure_dropdown_pete_plastic_bottles;
        } else {
            $data_array['unit_measure_dropdown_pete_plastic_bottles'] = 0;
        }

        if(isset($this->source_pete_plastic_bottles)) {
            $data_array['source_pete_plastic_bottles'] = $this->source_pete_plastic_bottles;
        } else {
            $data_array['source_pete_plastic_bottles'] = 0;
        }

        if(isset($this->monthly_tracking_pete_plastic_bottles)) {
            $data_array['monthly_tracking_pete_plastic_bottles'] = $this->monthly_tracking_pete_plastic_bottles;
        } else {
            $data_array['monthly_tracking_pete_plastic_bottles'] = 0;
        }

        if(isset($this->unit_measure_pete_plastic_bottles)) {
            $data_array['unit_measure_pete_plastic_bottles'] = $this->unit_measure_pete_plastic_bottles;
        }

        if(isset($this->disposal_cost_pete_plastic_bottles)) {
            $data_array['disposal_cost_pete_plastic_bottles'] = $this->disposal_cost_pete_plastic_bottles;
        }

        if(isset($this->total_pete_plastic_bottles)) {
            $data_array['total_pete_plastic_bottles'] = $this->total_pete_plastic_bottles;
        }

        if(isset($this->is_check_pete_plastic_bottles) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_pete_plastic_bottles'] = $this->is_check_pete_plastic_bottles;
        }

        if(isset($this->typical_destination_hdpe)) {
            $data_array['typical_destination_hdpe'] = $this->typical_destination_hdpe;
        } else {
            $data_array['typical_destination_hdpe'] = 0;
        }

        if(isset($this->unit_measure_dropdown_hdpe)) {
            $data_array['unit_measure_dropdown_hdpe'] = $this->unit_measure_dropdown_hdpe;
        } else {
            $data_array['unit_measure_dropdown_hdpe'] = 0;
        }

        if(isset($this->source_hdpe)) {
            $data_array['source_hdpe'] = $this->source_hdpe;
        } else {
            $data_array['source_hdpe'] = 0;
        }

        if(isset($this->monthly_tracking_hdpe)) {
            $data_array['monthly_tracking_hdpe'] = $this->monthly_tracking_hdpe;
        } else {
            $data_array['monthly_tracking_hdpe'] = 0;
        }

        if(isset($this->unit_measure_hdpe)) {
            $data_array['unit_measure_hdpe'] = $this->unit_measure_hdpe;
        }

        if(isset($this->disposal_cost_hdpe)) {
            $data_array['disposal_cost_hdpe'] = $this->disposal_cost_hdpe;
        }

        if(isset($this->total_hdpe)) {
            $data_array['total_hdpe'] = $this->total_hdpe;
        }

        if(isset($this->is_check_hdpe) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_hdpe'] = $this->is_check_hdpe;
        }

        if(isset($this->typical_destination_other_plastics)) {
            $data_array['typical_destination_other_plastics'] = $this->typical_destination_other_plastics;
        } else {
            $data_array['typical_destination_other_plastics'] = 0;
        }

        if(isset($this->unit_measure_dropdown_other_plastics)) {
            $data_array['unit_measure_dropdown_other_plastics'] = $this->unit_measure_dropdown_other_plastics;
        } else {
            $data_array['unit_measure_dropdown_other_plastics'] = 0;
        }

        if(isset($this->source_other_plastics)) {
            $data_array['source_other_plastics'] = $this->source_other_plastics;
        } else {
            $data_array['source_other_plastics'] = 0;
        }

        if(isset($this->monthly_tracking_other_plastics)) {
            $data_array['monthly_tracking_other_plastics'] = $this->monthly_tracking_other_plastics;
        } else {
            $data_array['monthly_tracking_other_plastics'] = 0;
        }

        if(isset($this->unit_measure_other_plastics)) {
            $data_array['unit_measure_other_plastics'] = $this->unit_measure_other_plastics;
        }

        if(isset($this->disposal_cost_other_plastics)) {
            $data_array['disposal_cost_other_plastics'] = $this->disposal_cost_other_plastics;
        }

        if(isset($this->total_other_plastics)) {
            $data_array['total_other_plastics'] = $this->total_other_plastics;
        }

        if(isset($this->is_check_other_plastics) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_other_plastics'] = $this->is_check_other_plastics;
        }
        
        if(isset($this->typical_destination_bottled_amenities)) {
            $data_array['typical_destination_bottled_amenities'] = $this->typical_destination_bottled_amenities;
        } else {
            $data_array['typical_destination_bottled_amenities'] = 0;
        }

        if(isset($this->unit_measure_dropdown_bottled_amenities)) {
            $data_array['unit_measure_dropdown_bottled_amenities'] = $this->unit_measure_dropdown_bottled_amenities;
        } else {
            $data_array['unit_measure_dropdown_bottled_amenities'] = 0;
        }

        if(isset($this->source_bottled_amenities)) {
            $data_array['source_bottled_amenities'] = $this->source_bottled_amenities;
        } else {
            $data_array['source_bottled_amenities'] = 0;
        }

        if(isset($this->monthly_tracking_bottled_amenities)) {
            $data_array['monthly_tracking_bottled_amenities'] = $this->monthly_tracking_bottled_amenities;
        } else {
            $data_array['monthly_tracking_bottled_amenities'] = 0;
        }

        if(isset($this->unit_measure_bottled_amenities)) {
            $data_array['unit_measure_bottled_amenities'] = $this->unit_measure_bottled_amenities;
        }

        if(isset($this->disposal_cost_bottled_amenities)) {
            $data_array['disposal_cost_bottled_amenities'] = $this->disposal_cost_bottled_amenities;
        }

        if(isset($this->total_bottled_amenities)) {
            $data_array['total_bottled_amenities'] = $this->total_bottled_amenities;
        }

        if(isset($this->is_check_bottled_amenities) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_bottled_amenities'] = $this->is_check_bottled_amenities;
        }

        if(isset($this->typical_destination_soap_bars)) {
            $data_array['typical_destination_soap_bars'] = $this->typical_destination_soap_bars;
        } else {
            $data_array['typical_destination_soap_bars'] = 0;
        }

        if(isset($this->unit_measure_dropdown_soap_bars)) {
            $data_array['unit_measure_dropdown_soap_bars'] = $this->unit_measure_dropdown_soap_bars;
        } else {
            $data_array['unit_measure_dropdown_soap_bars'] = 0;
        }

        if(isset($this->source_soap_bars)) {
            $data_array['source_soap_bars'] = $this->source_soap_bars;
        } else {
            $data_array['source_soap_bars'] = 0;
        }

        if(isset($this->monthly_tracking_soap_bars)) {
            $data_array['monthly_tracking_soap_bars'] = $this->monthly_tracking_soap_bars;
        } else {
            $data_array['monthly_tracking_soap_bars'] = 0;
        }

        if(isset($this->unit_measure_soap_bars)) {
            $data_array['unit_measure_soap_bars'] = $this->unit_measure_soap_bars;
        }

        if(isset($this->disposal_cost_soap_bars)) {
            $data_array['disposal_cost_soap_bars'] = $this->disposal_cost_soap_bars;
        }

        if(isset($this->total_soap_bars)) {
            $data_array['total_soap_bars'] = $this->total_soap_bars;
        }

        if(isset($this->is_check_soap_bars) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_soap_bars'] = $this->is_check_soap_bars;
        }

        if(isset($this->typical_destination_palettes_and_crates)) {
            $data_array['typical_destination_palettes_and_crates'] = $this->typical_destination_palettes_and_crates;
        } else {
            $data_array['typical_destination_palettes_and_crates'] = 0;
        }

        if(isset($this->unit_measure_dropdown_palettes_and_crates)) {
            $data_array['unit_measure_dropdown_palettes_and_crates'] = $this->unit_measure_dropdown_palettes_and_crates;
        } else {
            $data_array['unit_measure_dropdown_palettes_and_crates'] = 0;
        }

        if(isset($this->source_palettes_and_crates)) {
            $data_array['source_palettes_and_crates'] = $this->source_palettes_and_crates;
        } else {
            $data_array['source_palettes_and_crates'] = 0;
        }

        if(isset($this->monthly_tracking_palettes_and_crates)) {
            $data_array['monthly_tracking_palettes_and_crates'] = $this->monthly_tracking_palettes_and_crates;
        } else {
            $data_array['monthly_tracking_palettes_and_crates'] = 0;
        }

        if(isset($this->unit_measure_palettes_and_crates)) {
            $data_array['unit_measure_palettes_and_crates'] = $this->unit_measure_palettes_and_crates;
        }

        if(isset($this->disposal_cost_palettes_and_crates)) {
            $data_array['disposal_cost_palettes_and_crates'] = $this->disposal_cost_palettes_and_crates;
        }

        if(isset($this->total_palettes_and_crates)) {
            $data_array['total_palettes_and_crates'] = $this->total_palettes_and_crates;
        }

        if(isset($this->is_check_palettes_and_crates) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_palettes_and_crates'] = $this->is_check_palettes_and_crates;
        }

        if(isset($this->typical_destination_e_waste)) {
            $data_array['typical_destination_e_waste'] = $this->typical_destination_e_waste;
        } else {
            $data_array['typical_destination_e_waste'] = 0;
        }

        if(isset($this->unit_measure_dropdown_e_waste)) {
            $data_array['unit_measure_dropdown_e_waste'] = $this->unit_measure_dropdown_e_waste;
                } else {
                    $data_array['unit_measure_dropdown_e_waste'] = 0;
                }


                if(isset($this->source_e_waste)) {
            $data_array['source_e_waste'] = $this->source_e_waste;
                } else {
                    $data_array['source_e_waste'] = 0;
                }

                if(isset($this->monthly_tracking_e_waste)) {
            $data_array['monthly_tracking_e_waste'] = $this->monthly_tracking_e_waste;
                } else {
                    $data_array['monthly_tracking_e_waste'] = 0;
                }

        if(isset($this->unit_measure_e_waste)) {
            $data_array['unit_measure_e_waste'] = $this->unit_measure_e_waste;
        }
        
        if(isset($this->disposal_cost_e_waste)) {
            $data_array['disposal_cost_e_waste'] = $this->disposal_cost_e_waste;
        }
        
        if(isset($this->total_e_waste)) {
            $data_array['total_e_waste'] = $this->total_e_waste;
        }

        if(isset($this->is_check_e_waste) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_e_waste'] = $this->is_check_e_waste;
        }
        
        if(isset($this->typical_destination_durable_goods)) {
            $data_array['typical_destination_durable_goods'] = $this->typical_destination_durable_goods;
        } else {
            $data_array['typical_destination_durable_goods'] = 0;
        }

        if(isset($this->unit_measure_dropdown_durable_goods)) {
            $data_array['unit_measure_dropdown_durable_goods'] = $this->unit_measure_dropdown_durable_goods;
        } else {
            $data_array['unit_measure_dropdown_durable_goods'] = 0;
        }

        if(isset($this->source_durable_goods)) {
            $data_array['source_durable_goods'] = $this->source_durable_goods;
        } else {
            $data_array['source_durable_goods'] = 0;
        }

        if(isset($this->monthly_tracking_durable_goods)) {
            $data_array['monthly_tracking_durable_goods'] = $this->monthly_tracking_durable_goods;
        } else {
            $data_array['monthly_tracking_durable_goods'] = 0;
        }

        if(isset($this->unit_measure_durable_goods)) {
            $data_array['unit_measure_durable_goods'] = $this->unit_measure_durable_goods;
        }

        if(isset($this->disposal_cost_durable_goods)) {
            $data_array['disposal_cost_durable_goods'] = $this->disposal_cost_durable_goods;
        }

        if(isset($this->total_durable_goods)) {
            $data_array['total_durable_goods'] = $this->total_durable_goods;
        }

        if(isset($this->is_check_durable_goods) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_durable_goods'] = $this->is_check_durable_goods;
        }

        if(isset($this->typical_destination_solid_food_waste)) {
            $data_array['typical_destination_solid_food_waste'] = $this->typical_destination_solid_food_waste;
        } else {
            $data_array['typical_destination_solid_food_waste'] = 0;
        }

        if(isset($this->unit_measure_dropdown_solid_food_waste)) {
            $data_array['unit_measure_dropdown_solid_food_waste'] = $this->unit_measure_dropdown_solid_food_waste;
        } else {
            $data_array['unit_measure_dropdown_solid_food_waste'] = 0;
        }

        if(isset($this->source_solid_food_waste)) {
            $data_array['source_solid_food_waste'] = $this->source_solid_food_waste;
        } else {
            $data_array['source_solid_food_waste'] = 0;
        }

        if(isset($this->monthly_tracking_solid_food_waste)) {
            $data_array['monthly_tracking_solid_food_waste'] = $this->monthly_tracking_solid_food_waste;
        } else {
            $data_array['monthly_tracking_solid_food_waste'] = 0;
        }

        if(isset($this->unit_measure_solid_food_waste)) {
            $data_array['unit_measure_solid_food_waste'] = $this->unit_measure_solid_food_waste;
        }

        if(isset($this->disposal_cost_solid_food_waste)) {
            $data_array['disposal_cost_solid_food_waste'] = $this->disposal_cost_solid_food_waste;
        }

        if(isset($this->total_solid_food_waste)) {
            $data_array['total_solid_food_waste'] = $this->total_solid_food_waste;
        }

        if(isset($this->is_check_solid_food_waste) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_solid_food_waste'] = $this->is_check_solid_food_waste;
        }

        if(isset($this->typical_destination_leftover_food)) {
            $data_array['typical_destination_leftover_food'] = $this->typical_destination_leftover_food;
        } else {
            $data_array['typical_destination_leftover_food'] = 0;
        }

        if(isset($this->unit_measure_dropdown_leftover_food)) {
            $data_array['unit_measure_dropdown_leftover_food'] = $this->unit_measure_dropdown_leftover_food;
        } else {
            $data_array['unit_measure_dropdown_leftover_food'] = 0;
        }

        if(isset($this->source_leftover_food)) {
            $data_array['source_leftover_food'] = $this->source_leftover_food;
        } else {
            $data_array['source_leftover_food'] = 0;
        }

        if(isset($this->monthly_tracking_leftover_food)) {
            $data_array['monthly_tracking_leftover_food'] = $this->monthly_tracking_leftover_food;
        } else {
            $data_array['monthly_tracking_leftover_food'] = 0;
        }

        if(isset($this->unit_measure_leftover_food)) {
            $data_array['unit_measure_leftover_food'] = $this->unit_measure_leftover_food;
        }

        if(isset($this->disposal_cost_leftover_food)) {
            $data_array['disposal_cost_leftover_food'] = $this->disposal_cost_leftover_food;
        }

        if(isset($this->total_leftover_food)) {
            $data_array['total_leftover_food'] = $this->total_leftover_food;
        }

        if(isset($this->is_check_leftover_food) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_leftover_food'] = $this->is_check_leftover_food;
        }

        if(isset($this->typical_destination_inedible_parts)) {
            $data_array['typical_destination_inedible_parts'] = $this->typical_destination_inedible_parts;
        } else {
            $data_array['typical_destination_inedible_parts'] = 0;
        }

        if(isset($this->unit_measure_dropdown_inedible_parts)) {
            $data_array['unit_measure_dropdown_inedible_parts'] = $this->unit_measure_dropdown_inedible_parts;
        } else {
            $data_array['unit_measure_dropdown_inedible_parts'] = 0;
        }

        if(isset($this->source_inedible_parts)) {
            $data_array['source_inedible_parts'] = $this->source_inedible_parts;
        } else {
            $data_array['source_inedible_parts'] = 0;
        }

        if(isset($this->monthly_tracking_inedible_parts)) {
            $data_array['monthly_tracking_inedible_parts'] = $this->monthly_tracking_inedible_parts;
        } else {
            $data_array['monthly_tracking_inedible_parts'] = 0;
        }

        if(isset($this->unit_measure_inedible_parts)) {
            $data_array['unit_measure_inedible_parts'] = $this->unit_measure_inedible_parts;
        }

        if(isset($this->disposal_cost_inedible_parts)) {
            $data_array['disposal_cost_inedible_parts'] = $this->disposal_cost_inedible_parts;
        }

        if(isset($this->total_inedible_parts)) {
            $data_array['total_inedible_parts'] = $this->total_inedible_parts;
        }

        if(isset($this->is_check_inedible_parts) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_inedible_parts'] = $this->is_check_inedible_parts;
        }

        if(isset($this->typical_destination_liquid_food_waste)) {
            $data_array['typical_destination_liquid_food_waste'] = $this->typical_destination_liquid_food_waste;
        } else {
            $data_array['typical_destination_liquid_food_waste'] = 0;
        }

        if(isset($this->unit_measure_dropdown_liquid_food_waste)) {
            $data_array['unit_measure_dropdown_liquid_food_waste'] = $this->unit_measure_dropdown_liquid_food_waste;
        } else {
            $data_array['unit_measure_dropdown_liquid_food_waste'] = 0;
        }

        if(isset($this->source_liquid_food_waste)) {
            $data_array['source_liquid_food_waste'] = $this->source_liquid_food_waste;
        } else {
            $data_array['source_liquid_food_waste'] = 0;
        }

        if(isset($this->monthly_tracking_liquid_food_waste)) {
            $data_array['monthly_tracking_liquid_food_waste'] = $this->monthly_tracking_liquid_food_waste;
        } else {
            $data_array['monthly_tracking_liquid_food_waste'] = 0;
        }

        if(isset($this->unit_measure_liquid_food_waste)) {
            $data_array['unit_measure_liquid_food_waste'] = $this->unit_measure_liquid_food_waste;
        }

        if(isset($this->disposal_cost_liquid_food_waste)) {
            $data_array['disposal_cost_liquid_food_waste'] = $this->disposal_cost_liquid_food_waste;
        }

        if(isset($this->total_liquid_food_waste)) {
            $data_array['total_liquid_food_waste'] = $this->total_liquid_food_waste;
        }

        if(isset($this->is_check_liquid_food_waste) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_liquid_food_waste'] = $this->is_check_liquid_food_waste;
        }

        if(isset($this->typical_destination_kitchen_grease)) {
            $data_array['typical_destination_kitchen_grease'] = $this->typical_destination_kitchen_grease;
        } else {
            $data_array['typical_destination_kitchen_grease'] = 0;
        }

        if(isset($this->unit_measure_dropdown_kitchen_grease)) {
            $data_array['unit_measure_dropdown_kitchen_grease'] = $this->unit_measure_dropdown_kitchen_grease;
        } else {
            $data_array['unit_measure_dropdown_kitchen_grease'] = 0;
        }

        if(isset($this->source_kitchen_grease)) {
            $data_array['source_kitchen_grease'] = $this->source_kitchen_grease;
        } else {
            $data_array['source_kitchen_grease'] = 0;
        }

        if(isset($this->monthly_tracking_kitchen_grease)) {
            $data_array['monthly_tracking_kitchen_grease'] = $this->monthly_tracking_kitchen_grease;
        } else {
            $data_array['monthly_tracking_kitchen_grease'] = 0;
        }

        if(isset($this->unit_measure_kitchen_grease)) {
            $data_array['unit_measure_kitchen_grease'] = $this->unit_measure_kitchen_grease;
        }

        if(isset($this->disposal_cost_kitchen_grease)) {
            $data_array['disposal_cost_kitchen_grease'] = $this->disposal_cost_kitchen_grease;
        }

        if(isset($this->total_kitchen_grease)) {
            $data_array['total_kitchen_grease'] = $this->total_kitchen_grease;
        }

        if(isset($this->is_check_kitchen_grease) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_kitchen_grease'] = $this->is_check_kitchen_grease;
        }

        if(isset($this->typical_destination_liquid_hazardous_waste)) {
            $data_array['typical_destination_liquid_hazardous_waste'] = $this->typical_destination_liquid_hazardous_waste;
        } else {
            $data_array['typical_destination_liquid_hazardous_waste'] = 0;
        }

        if(isset($this->unit_measure_dropdown_liquid_hazardous_waste)) {
            $data_array['unit_measure_dropdown_liquid_hazardous_waste'] = $this->unit_measure_dropdown_liquid_hazardous_waste;
        } else {
            $data_array['unit_measure_dropdown_liquid_hazardous_waste'] = 0;
        }

        if(isset($this->source_liquid_hazardous_waste)) {
            $data_array['source_liquid_hazardous_waste'] = $this->source_liquid_hazardous_waste;
        } else {
            $data_array['source_liquid_hazardous_waste'] = 0;
        }

        if(isset($this->monthly_tracking_liquid_hazardous_waste)) {
            $data_array['monthly_tracking_liquid_hazardous_waste'] = $this->monthly_tracking_liquid_hazardous_waste;
        } else {
            $data_array['monthly_tracking_liquid_hazardous_waste'] = 0;
        }

        if(isset($this->unit_measure_liquid_hazardous_waste)) {
            $data_array['unit_measure_liquid_hazardous_waste'] = $this->unit_measure_liquid_hazardous_waste;
        }

        if(isset($this->disposal_cost_liquid_hazardous_waste)) {
            $data_array['disposal_cost_liquid_hazardous_waste'] = $this->disposal_cost_liquid_hazardous_waste;
        }

        if(isset($this->total_liquid_hazardous_waste)) {
            $data_array['total_liquid_hazardous_waste'] = $this->total_liquid_hazardous_waste;
        }

        if(isset($this->is_check_liquid_hazardous_waste) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_liquid_hazardous_waste'] = $this->is_check_liquid_hazardous_waste;
        }

        if(isset($this->typical_destination_other_hazardous_waste)) {
            $data_array['typical_destination_other_hazardous_waste'] = $this->typical_destination_other_hazardous_waste;
        } else {
            $data_array['typical_destination_other_hazardous_waste'] = 0;
        }

        if(isset($this->unit_measure_dropdown_other_hazardous_waste)) {
            $data_array['unit_measure_dropdown_other_hazardous_waste'] = $this->unit_measure_dropdown_other_hazardous_waste;
        } else {
            $data_array['unit_measure_dropdown_other_hazardous_waste'] = 0;
        }

        if(isset($this->source_other_hazardous_waste)) {
            $data_array['source_other_hazardous_waste'] = $this->source_other_hazardous_waste;
        } else {
            $data_array['source_other_hazardous_waste'] = 0;
        }

        if(isset($this->monthly_tracking_other_hazardous_waste)) {
            $data_array['monthly_tracking_other_hazardous_waste'] = $this->monthly_tracking_other_hazardous_waste;
        } else {
            $data_array['monthly_tracking_other_hazardous_waste'] = 0;
        }

        if(isset($this->unit_measure_other_hazardous_waste)) {
            $data_array['unit_measure_other_hazardous_waste'] = $this->unit_measure_other_hazardous_waste;
        }

        if(isset($this->disposal_cost_other_hazardous_waste)) {
            $data_array['disposal_cost_other_hazardous_waste'] = $this->disposal_cost_other_hazardous_waste;
        }

        if(isset($this->total_other_hazardous_waste)) {
            $data_array['total_other_hazardous_waste'] = $this->total_other_hazardous_waste;
        }

        if(isset($this->is_check_other_hazardous_waste) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_other_hazardous_waste'] = $this->is_check_other_hazardous_waste;
        }

        if(isset($this->typical_destination_batteries)) {
            $data_array['typical_destination_batteries'] = $this->typical_destination_batteries;
        } else {
            $data_array['typical_destination_batteries'] = 0;
        }

        if(isset($this->unit_measure_dropdown_batteries)) {
            $data_array['unit_measure_dropdown_batteries'] = $this->unit_measure_dropdown_batteries;
        } else {
            $data_array['unit_measure_dropdown_batteries'] = 0;
        }

        if(isset($this->source_batteries)) {
            $data_array['source_batteries'] = $this->source_batteries;
        } else {
            $data_array['source_batteries'] = 0;
        }

        if(isset($this->monthly_tracking_batteries)) {
            $data_array['monthly_tracking_batteries'] = $this->monthly_tracking_batteries;
        } else {
            $data_array['monthly_tracking_batteries'] = 0;
        }

        if(isset($this->unit_measure_batteries)) {
            $data_array['unit_measure_batteries'] = $this->unit_measure_batteries;
        }

        if(isset($this->disposal_cost_batteries)) {
            $data_array['disposal_cost_batteries'] = $this->disposal_cost_batteries;
        }

        if(isset($this->total_batteries)) {
            $data_array['total_batteries'] = $this->total_batteries;
        }

        if(isset($this->is_check_batteries) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_batteries'] = $this->is_check_batteries;
        }

        if(isset($this->typical_destination_light_bulbs)) {
            $data_array['typical_destination_light_bulbs'] = $this->typical_destination_light_bulbs;
        } else {
            $data_array['typical_destination_light_bulbs'] = 0;
        }

        if(isset($this->unit_measure_dropdown_light_bulbs)) {
            $data_array['unit_measure_dropdown_light_bulbs'] = $this->unit_measure_dropdown_light_bulbs;
        } else {
            $data_array['unit_measure_dropdown_light_bulbs'] = 0;
        }

        if(isset($this->source_light_bulbs)) {
            $data_array['source_light_bulbs'] = $this->source_light_bulbs;
        } else {
            $data_array['source_light_bulbs'] = 0;
        }

        if(isset($this->monthly_tracking_light_bulbs)) {
            $data_array['monthly_tracking_light_bulbs'] = $this->monthly_tracking_light_bulbs;
        } else {
            $data_array['monthly_tracking_light_bulbs'] = 0;
        }

        if(isset($this->unit_measure_light_bulbs)) {
            $data_array['unit_measure_light_bulbs'] = $this->unit_measure_light_bulbs;
        }

        if(isset($this->disposal_cost_light_bulbs)) {
            $data_array['disposal_cost_light_bulbs'] = $this->disposal_cost_light_bulbs;
        }

        if(isset($this->total_light_bulbs)) {
            $data_array['total_light_bulbs'] = $this->total_light_bulbs;
        }

        if(isset($this->is_check_light_bulbs) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_light_bulbs'] = $this->is_check_light_bulbs;
        }

        if(isset($this->typical_destination_light_fixtures)) {
            $data_array['typical_destination_light_fixtures'] = $this->typical_destination_light_fixtures;
        } else {
            $data_array['typical_destination_light_fixtures'] = 0;
        }

        if(isset($this->unit_measure_dropdown_light_fixtures)) {
            $data_array['unit_measure_dropdown_light_fixtures'] = $this->unit_measure_dropdown_light_fixtures;
        } else {
            $data_array['unit_measure_dropdown_light_fixtures'] = 0;
        }

        if(isset($this->source_light_fixtures)) {
            $data_array['source_light_fixtures'] = $this->source_light_fixtures;
        } else {
            $data_array['source_light_fixtures'] = 0;
        }

        if(isset($this->monthly_tracking_light_fixtures)) {
            $data_array['monthly_tracking_light_fixtures'] = $this->monthly_tracking_light_fixtures;
        } else {
            $data_array['monthly_tracking_light_fixtures'] = 0;
        }

        if(isset($this->unit_measure_light_fixtures)) {
            $data_array['unit_measure_light_fixtures'] = $this->unit_measure_light_fixtures;
        }

        if(isset($this->disposal_cost_light_fixtures)) {
            $data_array['disposal_cost_light_fixtures'] = $this->disposal_cost_light_fixtures;
        }

        if(isset($this->total_light_fixtures)) {
            $data_array['total_light_fixtures'] = $this->total_light_fixtures;
        }

        if(isset($this->is_check_light_fixtures) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_light_fixtures'] = $this->is_check_light_fixtures;
        }

        if(isset($this->typical_destination_textiles)) {
            $data_array['typical_destination_textiles'] = $this->typical_destination_textiles;
        } else {
            $data_array['typical_destination_textiles'] = 0;
        }

        if(isset($this->unit_measure_dropdown_textiles)) {
            $data_array['unit_measure_dropdown_textiles'] = $this->unit_measure_dropdown_textiles;
        } else {
            $data_array['unit_measure_dropdown_textiles'] = 0;
        }

        if(isset($this->source_textiles)) {
            $data_array['source_textiles'] = $this->source_textiles;
        } else {
            $data_array['source_textiles'] = 0;
        }

        if(isset($this->monthly_tracking_textiles)) {
            $data_array['monthly_tracking_textiles'] = $this->monthly_tracking_textiles;
        } else {
            $data_array['monthly_tracking_textiles'] = 0;
        }

        if(isset($this->unit_measure_textiles)) {
            $data_array['unit_measure_textiles'] = $this->unit_measure_textiles;
        }

        if(isset($this->disposal_cost_textiles)) {
            $data_array['disposal_cost_textiles'] = $this->disposal_cost_textiles;
        }

        if(isset($this->total_textiles)) {
            $data_array['total_textiles'] = $this->total_textiles;
        }

        if(isset($this->is_check_textiles) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_textiles'] = $this->is_check_textiles;
        }

        if(isset($this->typical_destination_wood)) {
            $data_array['typical_destination_wood'] = $this->typical_destination_wood;
        } else {
            $data_array['typical_destination_wood'] = 0;
        }

        if(isset($this->unit_measure_dropdown_wood)) {
            $data_array['unit_measure_dropdown_wood'] = $this->unit_measure_dropdown_wood;
        } else {
            $data_array['unit_measure_dropdown_wood'] = 0;
        }

        if(isset($this->source_wood)) {
            $data_array['source_wood'] = $this->source_wood;
        } else {
            $data_array['source_wood'] = 0;
        }

        if(isset($this->monthly_tracking_wood)) {
            $data_array['monthly_tracking_wood'] = $this->monthly_tracking_wood;
        } else {
            $data_array['monthly_tracking_wood'] = 0;
        }

        if(isset($this->unit_measure_wood)) {
            $data_array['unit_measure_wood'] = $this->unit_measure_wood;
        }

        if(isset($this->disposal_cost_wood)) {
            $data_array['disposal_cost_wood'] = $this->disposal_cost_wood;
        }

        if(isset($this->total_wood)) {
            $data_array['total_wood'] = $this->total_wood;
        }

        if(isset($this->is_check_wood) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_wood'] = $this->is_check_wood;
        }
        if(isset($this->typical_destination_building_constructions)) {
            $data_array['typical_destination_building_constructions'] = $this->typical_destination_building_constructions;
        } else {
            $data_array['typical_destination_building_constructions'] = 0;
        }

        if(isset($this->unit_measure_dropdown_building_constructions)) {
            $data_array['unit_measure_dropdown_building_constructions'] = $this->unit_measure_dropdown_building_constructions;
        } else {
            $data_array['unit_measure_dropdown_building_constructions'] = 0;
        }

        if(isset($this->source_building_constructions)) {
            $data_array['source_building_constructions'] = $this->source_building_constructions;
        } else {
            $data_array['source_building_constructions'] = 0;
        }

        if(isset($this->monthly_tracking_building_constructions)) {
            $data_array['monthly_tracking_building_constructions'] = $this->monthly_tracking_building_constructions;
        } else {
            $data_array['monthly_tracking_building_constructions'] = 0;
        }

        if(isset($this->unit_measure_building_constructions)) {
            $data_array['unit_measure_building_constructions'] = $this->unit_measure_building_constructions;
        }

        if(isset($this->disposal_cost_building_constructions)) {
            $data_array['disposal_cost_building_constructions'] = $this->disposal_cost_building_constructions;
        }

        if(isset($this->total_building_constructions)) {
            $data_array['total_building_constructions'] = $this->total_building_constructions;
        }

        if(isset($this->is_check_building_constructions) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_building_constructions'] = $this->is_check_building_constructions;
        }

        if(isset($this->typical_destination_other)) {
            $data_array['typical_destination_other'] = $this->typical_destination_other;
        } else {
            $data_array['typical_destination_other'] = 0;
        }

        if(isset($this->unit_measure_dropdown_other)) {
            $data_array['unit_measure_dropdown_other'] = $this->unit_measure_dropdown_other;
        } else {
            $data_array['unit_measure_dropdown_other'] = 0;
        }

        if(isset($this->source_other)) {
            $data_array['source_other'] = $this->source_other;
        } else {
            $data_array['source_other'] = 0;
        }

        if(isset($this->monthly_tracking_other)) {
            $data_array['monthly_tracking_other'] = $this->monthly_tracking_other;
        } else {
            $data_array['monthly_tracking_other'] = 0;
        }

        if(isset($this->unit_measure_other)) {
            $data_array['unit_measure_other'] = $this->unit_measure_other;
        }

        if(isset($this->disposal_cost_other)) {
            $data_array['disposal_cost_other'] = $this->disposal_cost_other;
        }

        if(isset($this->total_other)) {
            $data_array['total_other'] = $this->total_other;
        }

        if(isset($this->is_check_other) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_other'] = $this->is_check_other;
        }        
        
        if (isset($this->typical_destination_recycling) && !empty($this->typical_destination_recycling)) {
            $data_array['typical_destination_recycling'] = $this->typical_destination_recycling;    
        } else {
            $data_array['typical_destination_recycling'] = 0;
        }

        if(isset($this->unit_measure_dropdown_recycling)) {
            $data_array['unit_measure_dropdown_recycling'] = $this->unit_measure_dropdown_recycling;
        } else {
            $data_array['unit_measure_dropdown_recycling'] = 0;
        }
        
        if (isset($this->source_recycling) && !empty($this->source_recycling)) {
            $data_array['source_recycling'] = $this->source_recycling;    
        } else {
            $data_array['source_recycling'] = 0;
        }
        
        if (isset($this->monthly_tracking_recycling) && !empty($this->monthly_tracking_recycling)) {
            $data_array['monthly_tracking_recycling'] = $this->monthly_tracking_recycling;    
        } else {
            $data_array['monthly_tracking_recycling'] = 0;
        }
        
        if (isset($this->unit_measure_recycling) && !empty($this->unit_measure_recycling)) {
            $data_array['unit_measure_recycling'] = $this->unit_measure_recycling;    
        }
        
        if (isset($this->disposal_cost_recycling) && !empty($this->disposal_cost_recycling)) {
            $data_array['disposal_cost_recycling'] = $this->disposal_cost_recycling;    
        }
        
        if (isset($this->total_recycling) && !empty($this->total_recycling)) {
            $data_array['total_recycling'] = $this->total_recycling;    
        }
        
        if (isset($this->is_check_recycling) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_recycling'] = $this->is_check_recycling;    
        }
        
        if (isset($this->typical_destination_commingled_recyclables) && !empty($this->typical_destination_commingled_recyclables)) {
            $data_array['typical_destination_commingled_recyclables'] = $this->typical_destination_commingled_recyclables;    
        } else {
            $data_array['typical_destination_commingled_recyclables'] = 0;
        }

        if(isset($this->unit_measure_dropdown_commingled_recyclables)) {
            $data_array['unit_measure_dropdown_commingled_recyclables'] = $this->unit_measure_dropdown_commingled_recyclables;
        } else {
            $data_array['unit_measure_dropdown_commingled_recyclables'] = 0;
        }
        
        if (isset($this->source_commingled_recyclables) && !empty($this->source_commingled_recyclables)) {
            $data_array['source_commingled_recyclables'] = $this->source_commingled_recyclables;    
        } else {
            $data_array['source_commingled_recyclables'] = 0;
        }
        
        if (isset($this->monthly_tracking_commingled_recyclables) && !empty($this->monthly_tracking_commingled_recyclables)) {
            $data_array['monthly_tracking_commingled_recyclables'] = $this->monthly_tracking_commingled_recyclables;    
        } else {
            $data_array['monthly_tracking_commingled_recyclables'] = 0;
        }
        
        if (isset($this->unit_measure_commingled_recyclables) && !empty($this->unit_measure_commingled_recyclables)) {
            $data_array['unit_measure_commingled_recyclables'] = $this->unit_measure_commingled_recyclables;    
        }
        
        if (isset($this->disposal_cost_commingled_recyclables) && !empty($this->disposal_cost_commingled_recyclables)) {
            $data_array['disposal_cost_commingled_recyclables'] = $this->disposal_cost_commingled_recyclables;    
        }
        
        if (isset($this->total_commingled_recyclables) && !empty($this->total_commingled_recyclables)) {
            $data_array['total_commingled_recyclables'] = $this->total_commingled_recyclables;    
        }
        
        if (isset($this->is_check_commingled_recyclables) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_commingled_recyclables'] = $this->is_check_commingled_recyclables;    
        }
        
        if (isset($this->typical_destination_paper_cardboard) && !empty($this->typical_destination_paper_cardboard)) {
            $data_array['typical_destination_paper_cardboard'] = $this->typical_destination_paper_cardboard;    
        } else {
            $data_array['typical_destination_paper_cardboard'] = 0;
        }

        if(isset($this->unit_measure_dropdown_paper_cardboard)) {
            $data_array['unit_measure_dropdown_paper_cardboard'] = $this->unit_measure_dropdown_paper_cardboard;
        } else {
            $data_array['unit_measure_dropdown_paper_cardboard'] = 0;
        }
        
        if (isset($this->source_paper_cardboard) && !empty($this->source_paper_cardboard)) {
            $data_array['source_paper_cardboard'] = $this->source_paper_cardboard;    
        } else {
            $data_array['source_paper_cardboard'] = 0;
        }
        
        if (isset($this->monthly_tracking_paper_cardboard) && !empty($this->monthly_tracking_paper_cardboard)) {
            $data_array['monthly_tracking_paper_cardboard'] = $this->monthly_tracking_paper_cardboard;    
        } else {
            $data_array['monthly_tracking_paper_cardboard'] = 0;
        }
        
        if (isset($this->unit_measure_paper_cardboard) && !empty($this->unit_measure_paper_cardboard)) {
            $data_array['unit_measure_paper_cardboard'] = $this->unit_measure_paper_cardboard;    
        }
        
        if (isset($this->disposal_cost_paper_cardboard) && !empty($this->disposal_cost_paper_cardboard)) {
            $data_array['disposal_cost_paper_cardboard'] = $this->disposal_cost_paper_cardboard;    
        }
        
        if (isset($this->total_paper_cardboard) && !empty($this->total_paper_cardboard)) {
            $data_array['total_paper_cardboard'] = $this->total_paper_cardboard;    
        }
        
        if (isset($this->is_check_paper_cardboard) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_paper_cardboard'] = $this->is_check_paper_cardboard;    
        }
        
        if (isset($this->typical_destination_mixed_metals) && !empty($this->typical_destination_mixed_metals)) {
            $data_array['typical_destination_mixed_metals'] = $this->typical_destination_mixed_metals;    
        } else {
            $data_array['typical_destination_mixed_metals'] = 0;
        }

        if(isset($this->unit_measure_dropdown_mixed_metals)) {
            $data_array['unit_measure_dropdown_mixed_metals'] = $this->unit_measure_dropdown_mixed_metals;
        } else {
            $data_array['unit_measure_dropdown_mixed_metals'] = 0;
        }
        
        if (isset($this->source_mixed_metals) && !empty($this->source_mixed_metals)) {
            $data_array['source_mixed_metals'] = $this->source_mixed_metals;    
        } else {
            $data_array['source_mixed_metals'] = 0;
        }
        
        if (isset($this->monthly_tracking_mixed_metals) && !empty($this->monthly_tracking_mixed_metals)) {
            $data_array['monthly_tracking_mixed_metals'] = $this->monthly_tracking_mixed_metals;    
        } else {
            $data_array['monthly_tracking_mixed_metals'] = 0;
        }
        
        if (isset($this->unit_measure_mixed_metals) && !empty($this->unit_measure_mixed_metals)) {
            $data_array['unit_measure_mixed_metals'] = $this->unit_measure_mixed_metals;    
        }
        
        if (isset($this->disposal_cost_mixed_metals) && !empty($this->disposal_cost_mixed_metals)) {
            $data_array['disposal_cost_mixed_metals'] = $this->disposal_cost_mixed_metals;    
        }
        
        if (isset($this->total_mixed_metals) && !empty($this->total_mixed_metals)) {
            $data_array['total_mixed_metals'] = $this->total_mixed_metals;    
        }
        
        if (isset($this->is_check_mixed_metals) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_mixed_metals'] = $this->is_check_mixed_metals;    
        }
        
        if (isset($this->typical_destination_plastics) && !empty($this->typical_destination_plastics)) {
            $data_array['typical_destination_plastics'] = $this->typical_destination_plastics;    
        } else {
            $data_array['typical_destination_plastics'] = 0;
        }

        if(isset($this->unit_measure_dropdown_plastics)) {
            $data_array['unit_measure_dropdown_plastics'] = $this->unit_measure_dropdown_plastics;
        } else {
            $data_array['unit_measure_dropdown_plastics'] = 0;
        }
        
        if (isset($this->source_plastics) && !empty($this->source_plastics)) {
            $data_array['source_plastics'] = $this->source_plastics;    
        } else {
            $data_array['source_plastics'] = 0;
        }
        
        if (isset($this->monthly_tracking_plastics) && !empty($this->monthly_tracking_plastics)) {
            $data_array['monthly_tracking_plastics'] = $this->monthly_tracking_plastics;    
        } else {
            $data_array['monthly_tracking_plastics'] = 0;
        }
        
        if (isset($this->unit_measure_plastics) && !empty($this->unit_measure_plastics)) {
            $data_array['unit_measure_plastics'] = $this->unit_measure_plastics;    
        }
        
        if (isset($this->disposal_cost_plastics) && !empty($this->disposal_cost_plastics)) {
            $data_array['disposal_cost_plastics'] = $this->disposal_cost_plastics;    
        }
        
        if (isset($this->total_plastics) && !empty($this->total_plastics)) {
            $data_array['total_plastics'] = $this->total_plastics;    
        }
        
        if (isset($this->is_check_plastics) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_plastics'] = $this->is_check_plastics;    
        }
        
        if (isset($this->typical_destination_donations) && !empty($this->typical_destination_donations)) {
            $data_array['typical_destination_donations'] = $this->typical_destination_donations;    
        } else {
            $data_array['typical_destination_donations'] = 0;
        }

        if(isset($this->unit_measure_dropdown_donations)) {
            $data_array['unit_measure_dropdown_donations'] = $this->unit_measure_dropdown_donations;
        } else {
            $data_array['unit_measure_dropdown_donations'] = 0;
        }
        
        if (isset($this->source_donations) && !empty($this->source_donations)) {
            $data_array['source_donations'] = $this->source_donations;    
        } else {
            $data_array['source_donations'] = 0;
        }
        
        if (isset($this->monthly_tracking_donations) && !empty($this->monthly_tracking_donations)) {
            $data_array['monthly_tracking_donations'] = $this->monthly_tracking_donations;    
        } else {
            $data_array['monthly_tracking_donations'] = 0;
        }
        
        if (isset($this->unit_measure_donations) && !empty($this->unit_measure_donations)) {
            $data_array['unit_measure_donations'] = $this->unit_measure_donations;    
        }
        
        if (isset($this->disposal_cost_donations) && !empty($this->disposal_cost_donations)) {
            $data_array['disposal_cost_donations'] = $this->disposal_cost_donations;    
        }
        
        if (isset($this->total_donations) && !empty($this->total_donations)) {
            $data_array['total_donations'] = $this->total_donations;    
        }
        
        if (isset($this->is_check_donations) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_donations'] = $this->is_check_donations;    
        }
        
        if (isset($this->typical_destination_toiletry_donations) && !empty($this->typical_destination_toiletry_donations)) {
            $data_array['typical_destination_toiletry_donations'] = $this->typical_destination_toiletry_donations;    
        } else {
            $data_array['typical_destination_toiletry_donations'] = 0;
        }

        if(isset($this->unit_measure_dropdown_toiletry_donations)) {
            $data_array['unit_measure_dropdown_toiletry_donations'] = $this->unit_measure_dropdown_toiletry_donations;
        } else {
            $data_array['unit_measure_dropdown_toiletry_donations'] = 0;
        }
        
        if (isset($this->source_toiletry_donations) && !empty($this->source_toiletry_donations)) {
            $data_array['source_toiletry_donations'] = $this->source_toiletry_donations;    
        } else {
            $data_array['source_toiletry_donations'] = 0;
        }
        
        if (isset($this->monthly_tracking_toiletry_donations) && !empty($this->monthly_tracking_toiletry_donations)) {
            $data_array['monthly_tracking_toiletry_donations'] = $this->monthly_tracking_toiletry_donations;    
        } else {
            $data_array['monthly_tracking_toiletry_donations'] = 0;
        }
        
        if (isset($this->unit_measure_toiletry_donations) && !empty($this->unit_measure_toiletry_donations)) {
            $data_array['unit_measure_toiletry_donations'] = $this->unit_measure_toiletry_donations;    
        }
        
        if (isset($this->disposal_cost_toiletry_donations) && !empty($this->disposal_cost_toiletry_donations)) {
            $data_array['disposal_cost_toiletry_donations'] = $this->disposal_cost_toiletry_donations;    
        }
        
        if (isset($this->total_toiletry_donations) && !empty($this->total_toiletry_donations)) {
            $data_array['total_toiletry_donations'] = $this->total_toiletry_donations;    
        }
        
        if (isset($this->is_check_toiletry_donations) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_toiletry_donations'] = $this->is_check_toiletry_donations;    
        }
        
        if (isset($this->typical_destination_biodegradable) && !empty($this->typical_destination_biodegradable)) {
            $data_array['typical_destination_biodegradable'] = $this->typical_destination_biodegradable;    
        } else {
            $data_array['typical_destination_biodegradable'] = 0;
        }

        if(isset($this->unit_measure_dropdown_biodegradable)) {
            $data_array['unit_measure_dropdown_biodegradable'] = $this->unit_measure_dropdown_biodegradable;
        } else {
            $data_array['unit_measure_dropdown_biodegradable'] = 0;
        }
        
        if (isset($this->source_biodegradable) && !empty($this->source_biodegradable)) {
            $data_array['source_biodegradable'] = $this->source_biodegradable;    
        } else {
            $data_array['source_biodegradable'] = 0;
        }
        
        if (isset($this->monthly_tracking_biodegradable) && !empty($this->monthly_tracking_biodegradable)) {
            $data_array['monthly_tracking_biodegradable'] = $this->monthly_tracking_biodegradable;    
        } else {
            $data_array['monthly_tracking_biodegradable'] = 0;
        }
        
        if (isset($this->unit_measure_biodegradable) && !empty($this->unit_measure_biodegradable)) {
            $data_array['unit_measure_biodegradable'] = $this->unit_measure_biodegradable;    
        }
        
        if (isset($this->disposal_cost_biodegradable) && !empty($this->disposal_cost_biodegradable)) {
            $data_array['disposal_cost_biodegradable'] = $this->disposal_cost_biodegradable;    
        }
        
        if (isset($this->total_biodegradable) && !empty($this->total_biodegradable)) {
            $data_array['total_biodegradable'] = $this->total_biodegradable;    
        }
        
        if (isset($this->is_check_biodegradable) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_biodegradable'] = $this->is_check_biodegradable;    
        }
        
        if (isset($this->typical_destination_mixed_organic) && !empty($this->typical_destination_mixed_organic)) {
            $data_array['typical_destination_mixed_organic'] = $this->typical_destination_mixed_organic;    
        } else {
            $data_array['typical_destination_mixed_organic'] = 0;
        }

        if(isset($this->unit_measure_dropdown_mixed_organic)) {
            $data_array['unit_measure_dropdown_mixed_organic'] = $this->unit_measure_dropdown_mixed_organic;
        } else {
            $data_array['unit_measure_dropdown_mixed_organic'] = 0;
        }
        
        if (isset($this->source_mixed_organic) && !empty($this->source_mixed_organic)) {
            $data_array['source_mixed_organic'] = $this->source_mixed_organic;    
        } else {
            $data_array['source_mixed_organic'] = 0;
        }
        
        if (isset($this->monthly_tracking_mixed_organic) && !empty($this->monthly_tracking_mixed_organic)) {
            $data_array['monthly_tracking_mixed_organic'] = $this->monthly_tracking_mixed_organic;    
        } else {
            $data_array['monthly_tracking_mixed_organic'] = 0;
        }
        
        if (isset($this->unit_measure_mixed_organic) && !empty($this->unit_measure_mixed_organic)) {
            $data_array['unit_measure_mixed_organic'] = $this->unit_measure_mixed_organic;    
        }
        
        if (isset($this->disposal_cost_mixed_organic) && !empty($this->disposal_cost_mixed_organic)) {
            $data_array['disposal_cost_mixed_organic'] = $this->disposal_cost_mixed_organic;    
        }
        
        if (isset($this->total_mixed_organic) && !empty($this->total_mixed_organic)) {
            $data_array['total_mixed_organic'] = $this->total_mixed_organic;    
        }
        
        if (isset($this->is_check_mixed_organic) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_mixed_organic'] = $this->is_check_mixed_organic;    
        }
        
        if (isset($this->typical_destination_food_waste) && !empty($this->typical_destination_food_waste)) {
            $data_array['typical_destination_food_waste'] = $this->typical_destination_food_waste;    
        } else {
            $data_array['typical_destination_food_waste'] = 0;
        }

        if(isset($this->unit_measure_dropdown_food_waste)) {
            $data_array['unit_measure_dropdown_food_waste'] = $this->unit_measure_dropdown_food_waste;
        } else {
            $data_array['unit_measure_dropdown_food_waste'] = 0;
        }
        
        if (isset($this->source_food_waste) && !empty($this->source_food_waste)) {
            $data_array['source_food_waste'] = $this->source_food_waste;    
        } else {
            $data_array['source_food_waste'] = 0;
        }
        
        if (isset($this->monthly_tracking_food_waste) && !empty($this->monthly_tracking_food_waste)) {
            $data_array['monthly_tracking_food_waste'] = $this->monthly_tracking_food_waste;    
        } else {
            $data_array['monthly_tracking_food_waste'] = 0;
        }
        
        if (isset($this->unit_measure_food_waste) && !empty($this->unit_measure_food_waste)) {
            $data_array['unit_measure_food_waste'] = $this->unit_measure_food_waste;    
        }
        
        if (isset($this->disposal_cost_food_waste) && !empty($this->disposal_cost_food_waste)) {
            $data_array['disposal_cost_food_waste'] = $this->disposal_cost_food_waste;    
        }
        
        if (isset($this->total_food_waste) && !empty($this->total_food_waste)) {
            $data_array['total_food_waste'] = $this->total_food_waste;    
        }
        
        if (isset($this->is_check_food_waste) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_food_waste'] = $this->is_check_food_waste;    
        }
        
        if (isset($this->typical_destination_landfill_other) && !empty($this->typical_destination_landfill_other)) {
            $data_array['typical_destination_landfill_other'] = $this->typical_destination_landfill_other;    
        } else {
            $data_array['typical_destination_landfill_other'] = 0;
        }

        if(isset($this->unit_measure_dropdown_landfill_other)) {
            $data_array['unit_measure_dropdown_landfill_other'] = $this->unit_measure_dropdown_landfill_other;
        } else {
            $data_array['unit_measure_dropdown_landfill_other'] = 0;
        }
        
        if (isset($this->source_landfill_other) && !empty($this->source_landfill_other)) {
            $data_array['source_landfill_other'] = $this->source_landfill_other;    
        } else {
            $data_array['source_landfill_other'] = 0;
        }
        
        if (isset($this->monthly_tracking_landfill_other) && !empty($this->monthly_tracking_landfill_other)) {
            $data_array['monthly_tracking_landfill_other'] = $this->monthly_tracking_landfill_other;    
        } else {
            $data_array['monthly_tracking_landfill_other'] = 0;
        }
        
        if (isset($this->unit_measure_landfill_other) && !empty($this->unit_measure_landfill_other)) {
            $data_array['unit_measure_landfill_other'] = $this->unit_measure_landfill_other;    
        }
        
        if (isset($this->disposal_cost_landfill_other) && !empty($this->disposal_cost_landfill_other)) {
            $data_array['disposal_cost_landfill_other'] = $this->disposal_cost_landfill_other;    
        }
        
        if (isset($this->total_landfill_other) && !empty($this->total_landfill_other)) {
            $data_array['total_landfill_other'] = $this->total_landfill_other;    
        }
        
        if (isset($this->is_check_landfill_other) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_landfill_other'] = $this->is_check_landfill_other;    
        }
        
        if (isset($this->typical_destination_hazardous_waste) && !empty($this->typical_destination_hazardous_waste)) {
            $data_array['typical_destination_hazardous_waste'] = $this->typical_destination_hazardous_waste;    
        } else {
            $data_array['typical_destination_hazardous_waste'] = 0;
        }

        if(isset($this->unit_measure_dropdown_hazardous_waste)) {
            $data_array['unit_measure_dropdown_hazardous_waste'] = $this->unit_measure_dropdown_hazardous_waste;
        } else {
            $data_array['unit_measure_dropdown_hazardous_waste'] = 0;
        }
        
        if (isset($this->source_hazardous_waste) && !empty($this->source_hazardous_waste)) {
            $data_array['source_hazardous_waste'] = $this->source_hazardous_waste;    
        } else {
            $data_array['source_hazardous_waste'] = 0;
        }
        
        if (isset($this->monthly_tracking_hazardous_waste) && !empty($this->monthly_tracking_hazardous_waste)) {
            $data_array['monthly_tracking_hazardous_waste'] = $this->monthly_tracking_hazardous_waste;    
        } else {
            $data_array['monthly_tracking_hazardous_waste'] = 0;
        }
        
        if (isset($this->unit_measure_hazardous_waste) && !empty($this->unit_measure_hazardous_waste)) {
            $data_array['unit_measure_hazardous_waste'] = $this->unit_measure_hazardous_waste;    
        }
        
        if (isset($this->disposal_cost_hazardous_waste) && !empty($this->disposal_cost_hazardous_waste)) {
            $data_array['disposal_cost_hazardous_waste'] = $this->disposal_cost_hazardous_waste;    
        }
        
        if (isset($this->total_hazardous_waste) && !empty($this->total_hazardous_waste)) {
            $data_array['total_hazardous_waste'] = $this->total_hazardous_waste;    
        }
        
        if (isset($this->is_check_hazardous_waste) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_hazardous_waste'] = $this->is_check_hazardous_waste;    
        }
        
        if (isset($this->typical_destination_universal_waste) && !empty($this->typical_destination_universal_waste)) {
            $data_array['typical_destination_universal_waste'] = $this->typical_destination_universal_waste;    
        } else {
            $data_array['typical_destination_universal_waste'] = 0;
        }

        if(isset($this->unit_measure_dropdown_universal_waste)) {
            $data_array['unit_measure_dropdown_universal_waste'] = $this->unit_measure_dropdown_universal_waste;
        } else {
            $data_array['unit_measure_dropdown_universal_waste'] = 0;
        }
        
        if (isset($this->source_universal_waste) && !empty($this->source_universal_waste)) {
            $data_array['source_universal_waste'] = $this->source_universal_waste;    
        } else {
            $data_array['source_universal_waste'] = 0;
        }
        
        if (isset($this->monthly_tracking_universal_waste) && !empty($this->monthly_tracking_universal_waste)) {
            $data_array['monthly_tracking_universal_waste'] = $this->monthly_tracking_universal_waste;    
        } else {
            $data_array['monthly_tracking_universal_waste'] = 0;
        }
        
        if (isset($this->unit_measure_universal_waste) && !empty($this->unit_measure_universal_waste)) {
            $data_array['unit_measure_universal_waste'] = $this->unit_measure_universal_waste;    
        }
        
        if (isset($this->disposal_cost_universal_waste) && !empty($this->disposal_cost_universal_waste)) {
            $data_array['disposal_cost_universal_waste'] = $this->disposal_cost_universal_waste;    
        }
        
        if (isset($this->total_universal_waste) && !empty($this->total_universal_waste)) {
            $data_array['total_universal_waste'] = $this->total_universal_waste;    
        }
        
        if (isset($this->is_check_universal_waste) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_universal_waste'] = $this->is_check_universal_waste;    
        }
        
        if (isset($this->typical_destination_other_materials) && !empty($this->typical_destination_other_materials)) {
            $data_array['typical_destination_other_materials'] = $this->typical_destination_other_materials;    
        } else {
            $data_array['typical_destination_other_materials'] = 0;
        }

        if(isset($this->unit_measure_dropdown_other_materials)) {
            $data_array['unit_measure_dropdown_other_materials'] = $this->unit_measure_dropdown_other_materials;
        } else {
            $data_array['unit_measure_dropdown_other_materials'] = 0;
        }
        
        if (isset($this->source_other_materials) && !empty($this->source_other_materials)) {
            $data_array['source_other_materials'] = $this->source_other_materials;    
        } else {
            $data_array['source_other_materials'] = 0;
        }
        
        if (isset($this->monthly_tracking_other_materials) && !empty($this->monthly_tracking_other_materials)) {
            $data_array['monthly_tracking_other_materials'] = $this->monthly_tracking_other_materials;    
        } else {
            $data_array['monthly_tracking_other_materials'] = 0;
        }
        
        if (isset($this->unit_measure_other_materials) && !empty($this->unit_measure_other_materials)) {
            $data_array['unit_measure_other_materials'] = $this->unit_measure_other_materials;    
        }
        
        if (isset($this->disposal_cost_other_materials) && !empty($this->disposal_cost_other_materials)) {
            $data_array['disposal_cost_other_materials'] = $this->disposal_cost_other_materials;    
        }
        
        if (isset($this->total_other_materials) && !empty($this->total_other_materials)) {
            $data_array['total_other_materials'] = $this->total_other_materials;    
        }
        
        if (isset($this->is_check_other_materials) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_other_materials'] = $this->is_check_other_materials;    
        }

        if(isset($this->typical_destination_hazardous_and_universal_waste)) {
            $data_array['typical_destination_hazardous_and_universal_waste'] = $this->typical_destination_hazardous_and_universal_waste;
        } else {
            $data_array['typical_destination_hazardous_and_universal_waste'] = 0;
        }

        if(isset($this->unit_measure_dropdown_hazardous_and_universal_waste)) {
            $data_array['unit_measure_dropdown_hazardous_and_universal_waste'] = $this->unit_measure_dropdown_hazardous_and_universal_waste;
        } else {
            $data_array['unit_measure_dropdown_hazardous_and_universal_waste'] = 0;
        }

        if(isset($this->source_hazardous_and_universal_waste)) {
            $data_array['source_hazardous_and_universal_waste'] = $this->source_hazardous_and_universal_waste;
        } else {
            $data_array['source_hazardous_and_universal_waste'] = 0;
        }

        if(isset($this->monthly_tracking_hazardous_and_universal_waste)) {
            $data_array['monthly_tracking_hazardous_and_universal_waste'] = $this->monthly_tracking_hazardous_and_universal_waste;
        } else {
            $data_array['monthly_tracking_hazardous_and_universal_waste'] = 0;
        }

        if(isset($this->unit_measure_hazardous_and_universal_waste)) {
            $data_array['unit_measure_hazardous_and_universal_waste'] = $this->unit_measure_hazardous_and_universal_waste;
        }

        if(isset($this->disposal_cost_hazardous_and_universal_waste)) {
            $data_array['disposal_cost_hazardous_and_universal_waste'] = $this->disposal_cost_hazardous_and_universal_waste;
        }

        if(isset($this->total_hazardous_and_universal_waste)) {
            $data_array['total_hazardous_and_universal_waste'] = $this->total_hazardous_and_universal_waste;
        }

        if(isset($this->is_check_hazardous_and_universal_waste) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_hazardous_and_universal_waste'] = $this->is_check_hazardous_and_universal_waste;
        }

        if(isset($this->typical_destination_medical_waste)) {
            $data_array['typical_destination_medical_waste'] = $this->typical_destination_medical_waste;
        } else {
            $data_array['typical_destination_medical_waste'] = 0;
        }

        if(isset($this->unit_measure_dropdown_medical_waste)) {
            $data_array['unit_measure_dropdown_medical_waste'] = $this->unit_measure_dropdown_medical_waste;
        } else {
            $data_array['unit_measure_dropdown_medical_waste'] = 0;
        }

        if(isset($this->source_medical_waste)) {
            $data_array['source_medical_waste'] = $this->source_medical_waste;
        } else {
            $data_array['source_medical_waste'] = 0;
        }

        if(isset($this->monthly_tracking_medical_waste)) {
            $data_array['monthly_tracking_medical_waste'] = $this->monthly_tracking_medical_waste;
        } else {
            $data_array['monthly_tracking_medical_waste'] = 0;
        }

        if(isset($this->unit_measure_medical_waste)) {
            $data_array['unit_measure_medical_waste'] = $this->unit_measure_medical_waste;
        }

        if(isset($this->disposal_cost_medical_waste)) {
            $data_array['disposal_cost_medical_waste'] = $this->disposal_cost_medical_waste;
        }

        if(isset($this->total_medical_waste)) {
            $data_array['total_medical_waste'] = $this->total_medical_waste;
        }

        if(isset($this->is_check_medical_waste) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_medical_waste'] = $this->is_check_medical_waste;
        }

        if(isset($this->typical_destination_tin)) {
            $data_array['typical_destination_tin'] = $this->typical_destination_tin;
        } else {
            $data_array['typical_destination_tin'] = 0;
        }

        if(isset($this->unit_measure_dropdown_tin)) {
            $data_array['unit_measure_dropdown_tin'] = $this->unit_measure_dropdown_tin;
        } else {
            $data_array['unit_measure_dropdown_tin'] = 0;
        }

        if(isset($this->source_tin)) {
            $data_array['source_tin'] = $this->source_tin;
        } else {
            $data_array['source_tin'] = 0;
        }

        if(isset($this->monthly_tracking_tin)) {
            $data_array['monthly_tracking_tin'] = $this->monthly_tracking_tin;
        } else {
            $data_array['monthly_tracking_tin'] = 0;
        }

        if(isset($this->unit_measure_tin)) {
            $data_array['unit_measure_tin'] = $this->unit_measure_tin;
        }

        if(isset($this->disposal_cost_tin)) {
            $data_array['disposal_cost_tin'] = $this->disposal_cost_tin;
        }

        if(isset($this->total_tin)) {
            $data_array['total_tin'] = $this->total_tin;
        }

        if(isset($this->is_check_tin) && empty($this->month_id) && empty($this->year_id)) {
            $data_array['is_check_tin'] = $this->is_check_tin;
        }
        
        $dataAlreadyExist = $this->get_site_waste_model_detail_by_siteId_userId(); 
        if(empty($dataAlreadyExist)) {
            $data_array['created_at'] = GetCurrentDateTime();
            $data_array['created_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];
            $data_action = 'Create';
            
            $this->db->set($data_array);
            $id = $this->db->insert($this->_table);
        } else {
            $data_array['modified_at'] = GetCurrentDateTime();
            $data_array['modified_at'] = $this->session->userdata[get_current_section($this, true)]['user_id'];
            $data_action = 'Update';

            $this->db->where(array('site_id' => $this->site_id));
            if(isset($this->year_id) && !empty($this->year_id) && isset($this->month_id) && !empty($this->month_id)) {
                $this->db->where(array('year_id' => $this->year_id,'month_id' => $this->month_id));
            } else {
                $this->db->where(array('year_id' => NULL,'month_id' => NULL));
            }
            $this->db->set($data_array);
            $this->db->update($this->_table);
            $id = $site_waste_id;
        }

        // Save audit trail
        $site_id = $this->site_id;
        $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
        saveAuditTrail($user_id, $this->site_id, 'Site Waste', $data_action);

        return $id; 
    }

    public function delete_entry_ifexist($data){
        $site_id  = $data['site_id'];
        $month_id = $data['month_id'];
        $year_id  = $data['year_id'];
        // $user_id  = $data['user_id'];

        $this->db->where('site_id',$site_id);
        $this->db->where('month_id',$month_id);
        $this->db->where('year_id',$year_id); 
        // $this->db->where('user_id',$user_id); 
        $this->db->delete($this->_table);
    }

    public function insert_waste_invoice($data) {
        
        if(isset($data['invoice_scan']) && !empty($data['invoice_scan'])) {

            $data_array = array();

            if(isset($this->site_id)) {
                $data_array['site_id'] = $this->site_id;
            }

            // if(isset($this->user_id)) {
            //     $data_array['user_id'] = $this->user_id;
            // }

            if(isset($this->year_id)) {
                $data_array['year_id'] = $this->year_id;
            }

            if(isset($this->month_id)) {
                $data_array['month_id'] = $this->month_id;
            }

            $value = $data['invoice_scan'];
            $uploads  = $value['name'];
            $type     = $value['type'];
            $tmp_name = $value['tmp_name'];
            $size     = $value['size'];

            if(($uploads != '') && ($type != '') && ($tmp_name != ''))
            {
                if (!file_exists(BASE_PATH_CUSTOM . "/assets/uploads/site_".$this->site_id."/waste_invoices/")) {
                    mkdir(BASE_PATH_CUSTOM . "/assets/uploads/site_".$this->site_id."/waste_invoices/", 0777, true);                                
                }
                
                $config['upload_path']    = BASE_PATH_CUSTOM . "/assets/uploads/";
                $config['max_size']       = '2048';
                $config['maintain_ratio'] = true;
                $config['width']          = 140;
                $config['height']         = 100;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                $valid_formats = array("jpg", "jpeg", "gif", "png", "mp3", "mp4", "wma", "pdf");

                $imagename     = $uploads;
                $cnt    = strrpos($imagename, ".");
                if (!$cnt) {
                    $ext = '';
                }
                $l                = strlen($imagename) - $cnt;
                $ext              = substr($imagename, $cnt + 1, $l);
                $upload_file_name = $id.'_'.$key . rand(11111, 9999999) . '.' . $ext;
            
                if ($ext) {
                    if (in_array($ext, $valid_formats)) {

                        $uploadedfile = $tmp_name;

                        $target_file  = BASE_PATH_CUSTOM . "/assets/uploads/site_".$this->site_id."/waste_invoices/".$upload_file_name;
        
                        $_movestatus  = move_uploaded_file($uploadedfile, $target_file);

                        if (!$_movestatus) {
                            $this->theme->set_message('waste image is not uploaded', 'error');
                        } else {

                            $data_array['invoice_scan']    = $upload_file_name;        
                        }

                    }
                }

                $dataAlreadyExist = $this->get_site_waste_upload_invoice(); 
                if(empty($dataAlreadyExist)) {
                    $data_array['created_at'] = GetCurrentDateTime();
                    $data_array['created_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];
                    $data_action = 'Create Waste Invoice';
                    
                    $this->db->set($data_array);
                    $id = $this->db->insert($this->_table_waste_upload);
                } else {
                    $data_array['modified_at'] = GetCurrentDateTime();
                    $data_array['modified_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];
                    $data_action = 'Update Waste Invoice';

                    $this->db->where(array('site_id' => $this->site_id)); //'user_id' => $this->user_id
                    $this->db->where(array('year_id' => $this->year_id,'month_id' => $this->month_id));
                    $this->db->set($data_array);
                    $this->db->update($this->_table_waste_upload);
                    $id = $site_waste_id;
                }

                // Save audit trail
                $site_id = $this->site_id;
                $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
                saveAuditTrail($this->user_id, $this->site_id, 'Site Waste', $data_action);

                return $id;
            }
        } 
    }

    public function get_site_waste_upload_invoice() {
        $this->db->select('u.*');
        $this->db->where('u.deleted_at', null);
        $this->db->where('u.deleted_by', null);
        // $this->db->where('u.user_id', $this->user_id);
        $this->db->where('u.site_id', $this->site_id);
        $this->db->where('u.year_id', $this->year_id);
        $this->db->where('u.month_id', $this->month_id);
        $this->db->from($this->_table_waste_upload . ' AS u');
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    public function update_untracked_record($siteId, $name) {
        $dataArray = [];
        $dataArray['unit_measure_'.$name] = NULL;
        $dataArray['disposal_cost_'.$name] = NULL;
        $dataArray['total_'.$name] = NULL;
        
        $this->db->where(array('site_id' => $siteId));
        $this->db->where('year_id is NOT NULL', NULL, FALSE);
        $this->db->where('month_id is NOT NULL', NULL, FALSE);
        $this->db->set($dataArray);
        return $this->db->update($this->_table);
    }

    public function get_site_waste_export_data()
    {
        $this->db->select('s.*');
        $this->db->where('s.deleted_at', null);
        $this->db->where('s.deleted_by', null);
        $this->db->where('s.site_id', $this->site_id);
        $this->db->where('s.year_id is NOT NULL', NULL, FALSE);
        $this->db->where('s.month_id is NOT NULL', NULL, FALSE);
        $this->db->from($this->_table . ' AS s');
        $this->db->order_by("s.year_id", "asc");        
        $this->db->order_by("s.month_id", "asc");
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    public function getAllSiteRegionWasteData(){
        $query = "SELECT 
            `sites`.`id` as `id`,
            `sites`.`attribute`,
            `sites`.`site_location_name` as property_name,
            `countries`.`country`,
            `regions`.`region_name` as region,
                case `sites`.`site_type`
                    when 1 then 'Resort'
                    when 2 then 'City Hotel'
                    when 3 then 'Residences'
                    when 4 then 'Corporate Office'
                end as property_type,
            site_waste.*
            FROM sites 
            LEFT JOIN `countries` ON countries.id = sites.country_id
            LEFT JOIN `regions` ON regions.id = sites.region_id
            LEFT JOIN `site_waste` ON site_waste.site_id = sites.id AND site_waste.year_id IS NULL AND site_waste.month_id IS NULL 
            WHERE sites.id IS NOT NULL
            AND `sites`.`status` != '-1' ";
        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function getLatestAuditWasteDetail($site_id) {
        $query = "SELECT 
            DATE_FORMAT(`audit_trail`.`created`, '%Y-%m-%d') as last_update_date,
            `users`.`username` as last_update_by 
            FROM audit_trail 
            LEFT JOIN `users` ON users.id = audit_trail.user_id 
            WHERE audit_trail.module_name = 'Site Waste' AND audit_trail.site_id =".$site_id." 
            ORDER BY audit_trail.id DESC 
            LIMIT 1";
        $result = $this->db->query($query);
        return $result->row_array();
    }

    public function getLastMonthForSite($site_id) {
        $this->db->select(['month_id','year_id']);
        $this->db->from($this->_table);
        $this->db->where('site_id', $site_id);
        $this->db->where('site_waste.month_id is NOT NULL', NULL, FALSE);
        $this->db->where('site_waste.site_id is NOT NULL', NULL, FALSE);
        $this->db->where('site_waste.year_id is NOT NULL', NULL, FALSE);
        $this->db->order_by('site_waste_id',"desc")->limit(1);
        $result = $this->db->get()->row();
        $result = json_decode(json_encode($result), true);
        $lastMonthUpdated = date('F', mktime(0, 0, 0, $result['month_id'])).' '.$result['year_id'];
        return $lastMonthUpdated;
    }
}
