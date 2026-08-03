<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MY_Session extends CI_Session
{
    function __construct() 
    {
        parent::__construct();
        //echo "In extended the session";
    }
    /**
     * Add or change data in the "userdata" array
     *
     * @access	public
     * @param	section  name
     * @param	string or array
     * @param   string
     */
    function set_custom_userdata($sction_name, $newdata = array(), $newval = '')
    {
        if (is_string($newdata))
        {
            $newdata = array($newdata => $newval);
        }
        if (count($newdata) > 0)
        {
            foreach ($newdata as $key => $val)
            {
                $this->userdata[$sction_name][$key] = $val;
            }
        }
//        $this->sess_write();
    }

    /**
     * remove data from session array
     *
     * @access	public
     * @param	section  name
     * @param   array
     */
    function unset_custom_userdata($sction_name, $newdata = array())
    {
        if (is_string($newdata))
        {
            $newdata = array($newdata => '');
        }

        if (count($newdata) > 0)
        {
            foreach ($newdata as $key => $val)
            {
                unset($this->userdata[$sction_name][$key]);
            }
        }

//        $this->sess_write();
    }

}
