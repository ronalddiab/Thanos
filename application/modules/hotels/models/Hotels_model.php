<?php

/**
 *  Hotels Model
 *
 *  To perform queries related to Hotel management.
 *
 * @package CIDemoApplication
 * @subpackage Users
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Hotels_model extends Base_Model {

    protected $_tbl_hotels = TBL_HOTELS;
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";
    public $_record_count;

    /**
     * Function save_hotel to add/update hotel
     * @param array $data for hotel table
     */
    public function save_hotel($data) {
        if (isset($data['id'])) {
            $hotel_data ['id'] = $data['id'];
        }
        if (isset($data['hotel_name'])) {
            $hotel_data ['hotel_name'] = $data['hotel_name'];
        }
        if (isset($data['hotel_logo'])) {
            $hotel_data ['hotel_logo'] = $data['hotel_logo'];
        }
        if (isset($data['hotel_address'])) {
            $hotel_data['hotel_address'] = $data['hotel_address'];
        }
        if (isset($data['hotel_phone'])) {
            $hotel_data ['hotel_phone'] = $data['hotel_phone'];
        }
        if (isset($data['status'])) {
            $hotel_data ['status'] = $data['status'];
        }
        if (isset($data['created_on'])) {
            $hotel_data ['created_on'] = $data['created_on'];
        }
        if (isset($data['modify_on'])) {
            $hotel_data ['modify_on'] = $data['modify_on'];
        }


        if (isset($hotel_data['id']) && $hotel_data['id'] != 0 && $hotel_data['id'] != "") {
            $this->db->where('id', $hotel_data['id']);
            $this->db->update($this->_tbl_hotels, $hotel_data);
            $id = $hotel_data['id'];
        } else {

            $this->db->set('created_on', 'NOW()', FALSE);
            if ($this->db->insert($this->_tbl_hotels, $data)) {
                $id = $this->db->insert_id();
            }
        }
        return $id;
    }
   
    /**
     * Function get_hotel_detail to return hotel array of particular id
     * @param integer $id
     */
    function get_hotel_detail($id = 0) {
        //Type Casting
        $id = intval($id);

        $this->db->where("id", $id);
        $this->db->where_in("status", array(1, 0));
        $tablehotels = $this->db->get($this->_tbl_hotels);
        $hotelArray = $tablehotels->row_array();

        if (!empty($hotelArray)) {
            return $hotelArray;
        } else {
            return '';
        }
    }


    /**
     * Function get_hotel_listing to fetch all records of hotels
     * @param integer $hotel_id default = 0
     */
    function get_hotel_listing() {

        if ($this->search_term != "") {
            $this->db->like("LOWER(h.hotel_name)", strtolower($this->search_term));
        }
        if ($this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select('h.*');
        $this->db->from($this->_tbl_hotels . ' AS h');
        $this->db->where('h.status !=', -1);

        $query = $this->db->get();
        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }
    }

    function get_hotel_list_helper() {
        $this->db->select('h.id,h.hotel_name');
        $this->db->from($this->_tbl_hotels . ' AS h');
        $this->db->where('h.status !=', -1);

        $result = $this->db->get();
        $return = array();
        if($result->num_rows() > 0){
            foreach ($result->result_array() as $value) {
                $return[$value['id']]   = $value['hotel_name'];
            }
        }

        return $return;
    }

    /**
     * Function delete_hotel to delete hotel
     * @param integer $id
     */
    public function delete_hotel($id) {
        //Type Casting
        $id = intval($id);

        $this->db->where('id', $id);
        $this->db->set('status', '-1');
        return $this->db->update($this->_tbl_hotels);
    }

    /**
     * Function inactive_records to inactive records
     * @param array $id
     */
    public function inactive_records($id = array()) {
        $this->db->set('modify_on', 'NOW()', FALSE);
        $this->db->set('status', 0);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_hotels);

        return $id;
    }

    /**
     * Function inactive_all_records to inactive all records without deleted records
     */
    public function inactive_all_records() {
        $this->db->set('modify_on', 'NOW()', FALSE);
        $this->db->set('status', 0);
        $this->db->where('status !=', -1);
        $this->db->where('id !=', 1);
        $this->db->update($this->_tbl_hotels);

        return true;
    }

    /**
     * Function active_records to active records
     * @param array $id
     */
    public function active_records($id = array()) {
        $this->db->set('modify_on', 'NOW()', FALSE);
        $this->db->set('status', 1);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_hotels);

        return $id;
    }

    /**
     * Function active_all_records to active all records without deleted records
     */
    public function active_all_records() {
        $this->db->set('modify_on', 'NOW()', FALSE);
        $this->db->set('status', 1);
        $this->db->where('status !=', -1);
        $this->db->where('id !=', 1);
        $this->db->update($this->_tbl_hotels);

        return true;
    }

    /**
     * Function delete_records to delete URL
     * @param integer $id
     */
    public function delete_records($id = array()) {
        $this->db->set('modify_on', 'NOW()', FALSE);
        $this->db->where_in('id', $id);
        $this->db->set('status', '-1');
        return $this->db->update($this->_tbl_hotels);
    }

}
