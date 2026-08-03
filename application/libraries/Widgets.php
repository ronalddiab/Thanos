<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/** 
 * Widget 
 * Class is use for build widget. 
 *
 * @package CIDemoApplication
 * @subpackage widget
 * @copyright (c) 2013, TatvaSoft
 * @author Pankit Mehta <pankit.mehta@sparsh.com>
 * 
 */
class Widgets
{
    protected $_ci;
    protected $parser_enable = FALSE;
    /* create an instance */
    function __construct()
    {
        $this->_ci = & get_instance();
            
    }

    function build($view, $data = array())
    {
        $view = get_class($this) . '/' . $view;
        $subdir = '';
        if (strpos($view, '/') !== FALSE)
        {
            // explode the path so we can separate the filename from the path
            $x = explode('/', $view);

            // Reset the $class variable now that we know the actual filename
            $view = end($x);

            // Kill the filename from the array
            unset($x[count($x) - 1]);

            // Glue the path back together, sans filename
            $subdir = implode($x, '/') . '/';
        }
        $widget_view = APPPATH . 'widgets/' . $subdir . 'views/' . $view . EXT;
        
        
        if (file_exists($widget_view))
        {
            $widget_view = '../widgets/' . $subdir . 'views/' . $view;
                                                  
            if ($this->parser_enable)
            {
                $this->_ci->load->library('parser');
                
                return $this->_ci->parser->parse($widget_view . EXT, $data, TRUE);
            } else
            {
                return $this->_ci->load->view($widget_view, $data, TRUE);
            }
        }
        return FALSE;
    }

    function __get($var)
    {
        static $ci;
        isset($ci) OR $ci = get_instance();        
        return $ci->$var;
    }
}
