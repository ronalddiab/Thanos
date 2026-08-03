<?php

class Theme
{

    /**
     * Protected variables
     */
    protected $_ci = NULL;    //codeigniter instance
    protected $_config = array(); //the theme config
    protected $_content = '';      //the content (filled by the view/theme function)
    protected $_data = array(); //the data (variables passed to the theme and views)
    protected $messages = array(); //messages to display
    private $_module = NULL;          //current module
    private $_controller = NULL;      //current controller
    private $_method = NULL;          //current method
    private $_session;               //current session
    private $_template_locations = array();
    private $_meta_tag = array();
    /**
     * Theme::__construct()
     * @return void
     */
    function __construct()
    {

        //get the CI instance
        $this->_ci = &get_instance();

        //get the config
        $this->_config = config_item('theme');

        if (method_exists($this->_ci->router, 'fetch_module'))
        {
            $this->_module = $this->_ci->router->fetch_module();
        }

        // What controllers or methods are in use
        $this->_controller = $this->_ci->router->fetch_class();
        $this->_method = $this->_ci->router->fetch_method();
    }

    /**
     * Theme::set_theme()
     *
     * Sets the theme
     *
     * @param string $theme The theme
     * @return void
     */
    function set_theme($theme = 'default')
    {
        $this->set_config('theme', $theme);
        $this->set_theme_config('theme', $theme);

        $functions = $this->config('path') . $this->config('theme') . '/functions.php';
        //echo $functions;exit;
        if (file_exists($functions))
        {
            include($functions);
        }

        $this->_template_locations = array($this->config('path') . $theme . '/views/modules/' . $this->_module . '/',
            $this->config('path') . $theme . '/views/',
            $this->config('path') . $theme . '/views/modules/' . $this->_module . '/',
            $this->config('path') . $theme . '/views/',
            APPPATH . 'modules/' . $this->_module . '/views/'
        );
    }

    /**
     * Theme::set_layout()
     *
     * Sets the layout for the current theme (default: index => index.php)
     *
     * @param string $layout The layout for the theme
     * @return void
     */
    function set_layout($layout = 'index')
    {
        $path = $this->config('path') . $this->config('theme') . '/' . $layout . '.php';
        if (!file_exists($path))
        {
            $layout = 'index';
        }
        $this->set_config('layout', $layout);
    }
    
    function paging_layout($paging_data = array(),$paging_layout = 'default')
    {
        $path = $this->config('path') . $this->config('theme') . '/' . $paging_layout . '_paging_layout.php';
        
        if (!file_exists($path))
        {
            $path = $this->config('path') . $this->config('theme') . '/default_paging_layout.php';
        }
        
        if(!empty($paging_data))
        {
            include($path);
        }
    }

    /**
     * Sets a status message (for displaying small success/error messages).
     * This function is used in place of the session->flashdata function,
     * because you don't always want to have to refresh the page to get the
     * message to show up.
     *
     * @access public
     * @static
     *
     * @param string $message A string with the message to save.
     * @param string $type    A string to be included as the CSS class of the containing div.
     *
     * @return void
     */
    public function set_message($message = '', $type = 'info')
    {
        if (!empty($message))
        {
            if (isset($this->_ci->session))
            {
                $this->_ci->session->set_flashdata('message', $type . '::' . $message);
            }

            $this->message = array('type' => $type, 'message' => $message);
        }
    }

    /**
     * Displays a status message (small success/error messages).
     * If data exists in 'message' session flashdata, that will
     * override any other messages. The renders the message based
     * on the template provided in the config file ('OCU_message_template').
     *
     * @access public
     * @static
     *
     * @param string $message A string to be the message. (Optional) If included, will override any other messages in the system.
     * @param string $type    The class to attached to the div. (i.e. 'information', 'attention', 'error', 'success')
     *
     * @return string A string with the results of inserting the message into the message template.
     */
    public function message($message = '', $type = 'information')
    {
        // Does session data exist?
        if (empty($message) && class_exists('CI_Session'))
        {
            $message = $this->_ci->session->flashdata('message');

            if (!empty($message))
            {
                // Split out our message parts
                $temp_message = explode('::', $message);
                $type = $temp_message[0];
                $message = $temp_message[1];

                unset($temp_message);
            }
        }//end if
        // If message is empty, we need to check our own storage.
        if (empty($message))
        {
            if (empty($this->message['message']))
            {
                return '';
            }

            $message = $this->message['message'];
            $type = $this->message['type'];
        }

        $typeimage = 'bull-horn.png';
        $closeimage = 'close-icon.png';
        if($type=='success'){
            $typeimage = 'check-circle.png';
            $closeimage = 'close-icon-green.png';
        }else if($type=='error'){
            $typeimage = 'close-warning.png';
            $closeimage = 'close-icon-red.png';
        }else if($type=='warning'){
            $typeimage = 'notify-icon.png';
            $closeimage = 'close-icon-yellow.png';
        }


        // Grab out message template and replace the placeholders
        $template = str_replace('{type}', $type, $this->_ci->config->item('theme.message_template'));
        $template = str_replace('{message}', $message, $template);
        $template = str_replace('{typeimage}', $typeimage, $template);
        $template = str_replace('{closeimage}', $closeimage, $template);
        
        // Clear our session data so we don't get extra messages.
        // (This was a very rare occurence, but clearing should resolve the problem.
        if (class_exists('CI_Session'))
        {
            $this->_ci->session->set_flashdata('message', '');
        }

        return $template;
    }

    /**
     * Theme::config()
     *
     * Returns an item from the config array
     *
     * @param string $name
     * @param bool $default (optional: FALSE)
     * @return mixed or $default if not found
     */
    function config($name, $default = FALSE)
    {
        return isset($this->_config[$name]) ? $this->_config[$name] : $default;
    }

    /**
     * Theme::set_config()
     *
     * Sets an item in the config array
     * e.g. $this->theme->set_config('theme', 'other_theme');
     *
     * @param mixed $name
     * @param mixed $value
     * @return void
     */
    function set_theme_config($name, $value)
    {
        $this->_ci->config->config['theme'][$name] = $value;
    }

    function set_config($name, $value)
    {
        $this->_config[$name] = $value;
    }

    /**
     * Theme::get()
     *
     * Gets an item from the data array
     * e.g. $this->theme->get('current_user');
     *
     * @param string $name The value to get
     * @param bool $default (optional: FALSE)
     * @return mixed or $default if not found
     */
    function get($name, $default = FALSE)
    {
        return isset($this->_data[$name]) ? $this->_data[$name] : $default;
    }

    /**
     * Theme::set()
     *
     * Sets an item in the data array
     * e.g. $this->theme->set('current_user', $this->user);
     *
     * @param string $name The item to set
     * @param mixed $value The value to set
     * @return void
     */
    function set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    /**
     * Theme::content()
     *
     * Returns the content variable (filled by the view/theme function)
     *
     * @return string
     */
    function content()
    {
        return $this->_content;
    }

    /**
     * Theme::session()
     */
    function session()
    {
        return $this->_session;
    }

    /**
     * Theme::ci
     */
    function ci()
    {
        return $this->_ci;
    }

    /**
     * Theme::view()
     *
     * Loads the view just as CI would normally do and
     * passed it to the theme function wrapping the view into the theme
     *
     * @param string $view The view to load
     * @param array $data The data array to pass to the view
     * @param bool $return (optional) Return the output?
     * @return void or the HTML
     */
    function view($data = array(), $view = '', $return = false)
    {        
        $data = $this->filter_output($data);
        $data = is_array($data) ? $data : array();
        $data = array_merge($this->_data, $data);
        
        $prefix_view = '';
        
        if ($view == '')
        {
            $view = $this->_method;
        
            $current_section = get_section($this->_ci);
                    
            if($current_section)
                 $prefix_view = $current_section."_";
        }
        
        $content = $this->partial($prefix_view.$view, $data, true);
        if ($this->_ci->input->is_ajax_request())
        {
            $this->set_layout('ajax');
        } 
        return $this->render($content, $return);
    }

    /**
     * Theme::render()
     *
     * Wraps the theme around the $content
     *
     * @param string $content Raw HTML content
     * @param bool $return (optional) Return the output?
     * @return void or HTML
     */
    function render($content, $return = false)
    {
        $this->_content = $content;

        extract($this->_data);
        $theme = $this->config('path') . $this->_config['theme'] . '/' . $this->config('layout') . '.php';
        if (!file_exists($theme))
        {
            if ($this->config('theme') != "default")
            {
                //save the original requested themes for the error message, if the default theme also not exist
                $theme_requested = $theme;
                $theme = $this->config('path') . $this->config('theme') . '/index.php';

                if (!file_exists($theme))
                {
                    show_error('Make sure you configurate your theme <small>(did you copy the <u>themes</u> folder to your root?)</small><br><br>Requested Theme: ' . $theme_requested . ' not found.<br />Default Theme: ' . $theme . ' not found.');
                } else
                {
                    $this->set_theme();
                }
            } else
            {
                show_error('Make sure you configurate your theme <small>(did you copy the <u>themes</u> folder to your root?)</small><br><br>Default Theme: ' . $theme . ' not found.');
            }
        }

        ob_start();

        include ($theme);
        $html = ob_get_contents();        
        ob_end_clean();
        if(!preg_match('~(src\s*=\s*[\"\']+data:image.+)~i',$html,$img_match)) { //Do not apply the preg_replace_callback if the image contents only data:image/png;base64
            $html = preg_replace_callback('~((href|src)\s*=\s*[\"\'])([^\"\']+)~i', array($this, '_replace_url'), $html);
        }
        //$html = preg_replace_callback('~((href|src)\s*=\s*[\"\'])([^\"\']+)~i', array($this, '_replace_url'), $html);
        $html = str_replace('{template_url}', $this->config('url') . $this->_config['theme'], $html);

        if ($return)
        {
            return $html;
        }
        get_instance()->output->set_output($html);
    }

    /**
     * Theme::partial()
     *
     * Loads the view just as CI except this function will look
     * first into the theme's subdir 'views' to find the view
     *
     * @param string $view The view to load
     * @param array $data The data array to pass to the view
     * @param bool $return (optional) Return the output?
     * @return void or the HTML
     */
    function partial($view, $data = array(), $return = false)
    {
        $data = is_array($data) ? $data : array();
        $data = array_merge($this->_data, $data);
        $path = NULL;

        foreach ($this->_template_locations as $location)
        {
            if (file_exists($location . $view . '.php') && $path == NULL)
            {
                $path = $location . $view . '.php';
                extract($data);
                ob_start();
                include ($path);
                $output = ob_get_contents();
                ob_end_clean();
            }
        }

        if ($path == NULL)
        {
            $output = get_instance()->load->view($view, $data, TRUE);
        }

        if ($return)
        {
            return $output;
        }
        echo $output;
    }

    /**
     * Theme filter_output()
     * @return mixed $data
     */
    protected function filter_output($data)
    {
        $exclude = explode(',', EXCLUDE_KEYS_FILTEROUTPUT);
        if (is_array($data)):
            foreach ($data as $key => &$item)
            {
                if (!is_array($item) && !is_object($item) && !in_array($key, $exclude)):
                    $item = htmlentities($item);
                else:
                    $this->filter_output($item);
                endif;
            }
        elseif (is_object($data)):
            $data = $data;
        else:
            $data = htmlentities($data);
        endif;
        return $data;
    }

    /**
     * Theme::_replace_url()
     *
     * @param mixed $x
     * @return
     */
    private static function _replace_url($x)
    {
        $url = isset($x[3]) ? $x[3] : '';
        if (strpos($url, 'http') !== 0 &&
                strpos($url, 'mailto') !== 0 &&
                strpos($url, '/') !== 0 &&
                strpos($url, '#') !== 0 &&
                strpos($url, 'javascript') !== 0 &&
                strpos($url, '{') !== 0)
        {
            $url = '{template_url}/' . $url;
        }
        return isset($x[1]) ? ($x[1] . $url) : $url;
    }
    
    /**
     * Theme::get_page_title()
     *
     * Gets an item from the data array
     * e.g. $this->theme->get('current_user');
     * @param bool $default (optional: FALSE)
     * @return mixed or $default if not found
     */
    function get_page_title($default = FALSE)
    {
        return isset($this->_data['page_title']) ? $this->_data['page_title'] : $default;
    }

    /**
     * Theme::set_page_title()
     *
     * Sets an item in the data array
     * e.g. $this->theme->set('current_user', $this->user);
     * @param mixed $value The value to set
     * @return void
     */
    function set_page_title($value)
    {
        $this->_data['page_title'] = $value;
    }
    
    
    /**
     * Theme::set_meta()
     *
     * Sets an item in the data array
     * e.g. $this->theme->set('current_user', $this->user);
     * @param mixed $value The value to set
     * @return void
     */
    function set_meta($value)
    {
        $this->_meta_tag[] = $value;
    }
    
    /**
     * Theme::get_meta()
     *
     * Gets an item from the data array
     * e.g. $this->theme->get('current_user');
     * @param bool $default (optional: FALSE)
     * @return mixed or $default if not found
     */
    function get_meta($default = FALSE)
    {
        return $this->_meta_tag;
    }
    
    
}