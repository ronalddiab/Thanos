<?php
###################################################################
##	Last Edit:
##	October 01 2014
##
##	Library:
##	Breadcrumb
##	
##	Developed by:
##	By Pankit Shah
##
##	Modified by:
##	Anand Solanki
##	
##	Comments:
##	To display breadcrumb
##################################################################
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Breadcrumb {

    private $breadcrumbs = array();
    // private $separator = ''; 
    private $start = '<div class="breadcrumb-wrapper">';
    private $end = '</div>';

    public function __construct($params = array()) {
        if (count($params) > 0) {
            $this->initialize($params);
        }
    }

    private function initialize($params = array()) {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->{'_' . $key})) {
                    $this->{'_' . $key} = $val;
                }
            }
        }
    }

    /**
     *  Breadcrumb Method: add
     * Allows to add new breadcrumb with URL
     * @param $title 
     * @param $href - optional, default #
     */
    function add($title, $href = '#') {
        if (!$title OR !$href)
            return; $this->breadcrumbs[] = array('title' => $title, 'href' => $href);
    }

    /**
     *  Breadcrumb Method: output
     * Allows to display breadcrumb
     */
//    function output() {
//        if ($this->breadcrumbs) {
//            $output = $this->start;
//            foreach ($this->breadcrumbs as $key => $crumb) {
//                if ($key) {
//                    //$output .= $this->separator;
//                    $output .= " ".add_image(array('bcarrow.png'))." ";
//                } if (end(array_keys($this->breadcrumbs)) == $key) {
//                    $output .= '<span>' . $crumb['title'] . '</span>';
//                } else {
//                    $output .= '<a href="' . $crumb['href'] . '">' . $crumb['title'] . '</a>';
//                }
//            } return $output . $this->end . PHP_EOL;
//        } return '';
//    }

    function output() {
        
        if ($this->breadcrumbs) {
            $arrayKeys = array_keys($this->breadcrumbs);
            
            $output = $this->start;
            $output.= '<span class="label"><i class="fa fa-hand-o-right" aria-hidden="true"></i></span><ol class="breadcrumb">';

            foreach ($this->breadcrumbs as $key => $crumb) {
                
                if ($key) {
                } if (end($arrayKeys) == $key) {
                    $output .= '<li class="active">' . $crumb['title'] . '</li>';
                } else {
                    $output .= '<li><a href="' . $crumb['href'] . '">' . $crumb['title'] . '</a></li>';
                }
            }
            $output.= '</ol>';
            return $output . $this->end . PHP_EOL;
        }
        return '';
    }

}
