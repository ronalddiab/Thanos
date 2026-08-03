<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * MySQL Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the active record
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class MY_DB_mysqli_driver extends CI_DB_mysqli_driver
{

    protected $result;
    public $_record_count;
    /**
     * Function custom_result to generate database result in custom array
     * @param  $dbObject mysql result query object
     */
    public function custom_result($dbObject)
    {        
        $table = array();
        $field = array();
        $result_array = array();
        $result = $dbObject->result();
        $tempResults = array();

        $numOfFields = $this->listing_fields();
        
        for ($i = 0; $i < count($numOfFields); ++$i)
        {
            $result_table = $this->field_table($i);
            if ($result_table == '')
            {
                $result_table = 'custom';
            }
            array_push($table, $result_table);

            array_push($field, $this->field_name($i));
            
        }
         
        if ($dbObject->num_rows() > 0)
        {
            mysqli_data_seek( $this->result_id, 0);
            //$dbObject->_data_seek(0);
            
            while ($row = $this->fetch_row())
            {
                for ($i = 0; $i < count($numOfFields); ++$i)
                {
                    $tempResults[$table[$i]][$field[$i]] = $row[$i];
                }

                array_push($result_array, $tempResults);
            }
        }
        $this->clear();
        
        return $result_array;
    }

    function field_table($i)
    {
        return @mysqli_fetch_field_direct($this->result_id, $i)->table;
    }

    function field_name($i)
    {
        return mysqli_fetch_field_direct($this->result_id, $i)->name;
    }

    function fetch_row()
    {
        return @mysqli_fetch_row($this->result_id);
    }

    function listing_fields()
    {
        $field_names = array();
        $i = 0;
        while ($i < mysqli_num_fields($this->result_id))
        {
            $field = mysqli_fetch_field_direct($this->result_id, $i);
            if ($field)
            {
                $field_names[$i] = $field->name;
            }
            $i++;
        }
        return $field_names;
    }
    
    /**
     * Function clear to clear all set variable after run query.
    */
    public function clear()
    {        
        $this->_record_count = '';
    }

}
