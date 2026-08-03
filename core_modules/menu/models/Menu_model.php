<?php

/**
 *  Menu admin model
 *
 *  Menu admin model have business logic for retrive data from database.
 *  Also this menu changes contain the core menu generation functionality.
 *
 * @package CIDemoApplication
 *
 * @copyright	(c) 2013, TatvaSoft
 * @author Pankit Mehta <pankit.mehta@sparsh.com>
 */
class Menu_model extends Base_Model
{

    protected $_tabelname = TBL_MENU_NAVIGATION;
    protected $_urltablename = TBL_URL_MANAGEMENT;
    protected $_langtablename = TBL_LANGUAGES;
    protected $_cmstablename = TBL_CMS;

    /**
     * create an instance
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * get_menu() method is use for retrive data from database.
     * @param string $menu_name
     * @param string $status default ""
     * @return array $result
     */
    function get_menu($menu_name, $status = "")
    {
        $menu_name = trim(strip_tags($menu_name));
        if($status != "")
        {
            $this->db->where('status', $status);
        }
        $this->db->select('m.*,CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM ' . $this->_tabelname . ' AS sub 
                        WHERE sub.parent_id = m.id
                    ) 
                    THEN 1 
                    ELSE 0 
                END AS is_parent')
                ->from($this->_tabelname.' AS m')
                ->where('m.menu_name', $menu_name)
                ->where('m.status != ', -1)
                ->where('m.lang_id', $this->language_id)
                ->order_by('m.order', 'ASC');

        // Static menu id Condition for forum front for "site user" role and forum admin for all other role
        $ci = get_instance();
        $role_id = isset($ci->session->userdata['admin']['role_id'])?$ci->session->userdata['admin']['role_id']:'';
        $user_id = isset($ci->session->userdata['admin']['user_id'])?$ci->session->userdata['admin']['user_id']:0;

        if($role_id == 4 || $role_id == 3 || $role_id == 2){
            $this->db->where('m.id != ', 6);
        }else{
            $this->db->where('m.id != ', 30);
        }
        
        $query = $this->db->get();        
        $result = $this->db->custom_result($query);
        return $result;
    }

    /**
     * get_menu_list() method is use for retrive menu list.
     * @param integer $current_parent_id
     * @param integer $count
     * @param array   $extra default ""
     * @return array $option_results (parent/sub menu options array with lavel display in title key)
     */
    function get_menu_list($current_parent_id, $count, $extra = array())
    {
        //use static variable to load  previous array
        static $option_results = array();
        $indent_flag = '';
        $current_parent_id = intval($current_parent_id);
        // if there is no current menu id set, start off at the top level (zero)
        if(!isset($current_parent_id))
        {
            $current_parent_id = 0;
        }

        // increment the counter by 1
        $count = $count + 1;

        $this->db->select('m.id,m.menu_name,m.title,m.link,m.parent_id,m.lang_id,m.status,l.language_name,l.language_code');
        $this->db->from($this->_tabelname . ' as m');
        $this->db->where('m.parent_id', $current_parent_id);
        $this->db->where('m.status != ', -1);
        $this->db->join($this->_langtablename . ' as l', 'l.id = m.lang_id', 'left');

        //set condition to check menu_name
        if(!empty($extra))
        {
            foreach ($extra as $key => $val)
            {
                $this->db->where($key, $val);
            }
        }

        $this->db->where('lang_id', $this->language_id);
        $query = $this->db->get();

        $get_options = $this->db->custom_result($query);

        $num_options = count($get_options);

        if($num_options > 0)
        {
            foreach ($get_options as $row)
            {
                // if its not a top-level menu, indent it to
                //show that its a sub menu
                if($current_parent_id != 0)
                {
                    $indent_flag = '&nbsp;&nbsp;';
                    for ($x = 2; $x <= $count; $x++)
                    {
                        $indent_flag .= '>';
                    }
                }
                $row['m']['title'] = $menu_title = $indent_flag . $row['m']['title'];

                $option_results[$row['m']['menu_name']][] = array_merge($row['m'],$row['l']);

                // now call the function again, to recurse through the child menu
                $this->get_menu_list($row['m']['id'], $count, $extra);
            }
        }
        return $option_results;
    }

    /**
     * get_child_menu_array() method is use for retrive child array of given parent id.
     * @param integer $parent_id
     * @param integer $count
     * @return array $option_results parent/sub ids list
     */
    function get_child_menu_array($parent_id, $count = 0)
    {
        //use static variable to load  previous array
        static $option_results = array();
        // if there is no current menu id set, start off at the top level (zero)
        if(!isset($parent_id))
        {
            $parent_id = 0;
        }
        // increment the counter by 1
        $count = $count + 1;

        $this->db->select('m.id,m.parent_id');
        $this->db->from($this->_tabelname.' as m');
        $this->db->where('m.parent_id', $parent_id);
        $this->db->where('m.status', 1);
        $this->db->where('m.lang_id', $this->language_id);
        $query = $this->db->get();

        $get_options = $this->db->custom_result($query);
        $num_options = count($get_options);

        //our permission is apparently valid
        if($num_options > 0)
        {
            foreach ($get_options as $row)
            {
                $option_results[$row['m']['id']] = $row['m']['id'];
                // now call the function again, to recurse through the child permissions
                $this->get_child_menu_array($row['m']['id'], $count);
            }
        }
        return $option_results;
    }

    /**
     * get_menu_detail() method is use for retrive menu detail.
     * @param array $data
     * @param string $selectparam default "*"
     * @return array result array
     */
    function get_menu_detail($data = array(), $selectparam = "*")
    {
        if(!empty($data))
        {
            //set values in where condition
            foreach ($data as $key => $val)
            {
                $this->db->where($key, $val);
            }
        }
        $this->db->select($selectparam)
                ->from($this->_tabelname.' AS m')
                ->where('m.lang_id', $this->language_id);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        return $result;
    }

    /**
     * get_menu_name() method is use for retrive unique menu name.
     * @return array result array
     */
    function get_menu_name()
    {
        $lang_id = intval($this->language_id);
        $this->db->distinct();
        $this->db->select('m.menu_name as id, m.menu_name as value')
                ->from($this->_tabelname.' AS m')
                ->where('m.status !=', -1)
                ->where('m.lang_id', $lang_id);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        return $result;
    }

    /**
     * get_pages() method is use for retrive modules actions
     * @param string $modulename
     * @return array $result
     */
    function get_pages($modulename)
    {
        $modulename = trim(strip_tags($modulename));
        $this->db->select('u.slug_url  as id, u.slug_url as value')
                ->from($this->_urltablename . ' as u')
                ->join($this->_cmstablename . ' as c', 'c.slug_url = u.slug_url', 'left')
                ->where('u.module_name', $modulename)
                ->where('u.status', 1)
                ->where('c.lang_id', $this->language_id)
                ->order_by('u.slug_url','asc');

        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        return $result;
    }

    /**
     * save_menu() method is use for add/update record
     * @param array $data
     * @return integer $id
     */
    public function save_menu($data)
    {
        $id = intval($data['id']);
        if($data['id'] != 0)
        {
            $this->db->where('id', $id);
            $this->db->update($this->_tabelname, $data);
        }
        else
        {
            $this->db->insert($this->_tabelname, $data);
            $id = $this->db->insert_id();
        }
        return $id;
    }

    /**
     * delete_menu() method is use for delete menu record
     * @param integer $id
     * @return integer
     */
    public function delete_menu($id)
    {
        $id = intval($id);
        //check parent menu
        $this->db->select('*')
                ->from($this->_tabelname)
                ->where('parent_id', $id)
                ->where('lang_id', $this->language_id);
        $result = $this->db->get()->num_rows();
        if($result != 0)
        {
            return 0;
        }
        else
        {
            $this->db->where('id', $id);
            $this->db->update($this->_tabelname, array('status' => -1));
            return 1;
        }
    }

    /**
     * check_unique_fields() method is use for checking unique values
     * @param integer $id
     * @return integer
     */
    public function check_unique_fields($data,$field){
        $id = intval($data['id']);
        $lang_id = intval($data['lang_id']);

        $this->db->select('id');
        $this->db->from($this->_tabelname);
        if((isset($id) && $id != 0))
        {
            $this->db->where('id != ', $id);
        }
        $this->db->where($field ,$data[$field]);
        $this->db->where('menu_name = ', $data['menu_name']);
        $this->db->where('lang_id = ', $lang_id);
        $this->db->where('status != ', -1);

        $this->db->limit(1);
        $result = $this->db->get()->num_rows();

        return $result;
    }

    /**
     * get_active_sub_menu() method is use for retrive active submenu data from database.
     * @param int $id default 0
     */
    function get_active_sub_menu($id = 0,$lang_id = 0)
    {
        //Type casting
        $id = intval($id);
        $lang_id = intval($lang_id);

        $this->db->select('m.*')
                ->from($this->_tabelname.' AS m')
                ->where('m.parent_id', $id)
                ->where('m.status', 1)
                ->where('m.lang_id', $lang_id);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        return $result;
    }

}

