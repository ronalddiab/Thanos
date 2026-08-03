<?php

/**
 * Description of category dropdown
 *
 * @author Hitesh Dodiya <hitesh.dodiya@sparsh.com>
 
 */
class category_dropdown extends widgets {

    
    public function __construct() {
        parent::__construct();
        $this->load->model('categories/categories_model');
    }
    
    function run($options = array()) {
        $name = !empty($options) ? $options['name'] : '';
        $id = !empty($options) ? $options['id'] : '';
        $value = !empty($options) ? $options['value'] : '';
        $language_id = !empty($options) ? $options['language_id'] : '';
        if(!empty($options) && isset($options['module_id']))
        {
            $this->categories_model->module_id = $options['module_id'];
        }
        if(!empty($options) && isset($options['first_option']))
        {
            $this->categories_model->first_option = $options['first_option'];
        }
        $category_list = $this->categories_model->get_category_with_child($language_id);
        
        $data['category_list'] = $category_list;
        $data['name'] = $name;
        $data['id'] = $id;
        $data['value'] = $value;
        return $this->build('category_dropdown_view', $data);
    }

}