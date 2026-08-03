<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Nivoslider {
	private $_effect;
	private $_slices;
	private $_boxCols;
	private $_boxRows;
	private $_animSpeed;
	private $_pauseTime;
	private $_startSlide;
	private $_directionNav;
	private $_directionNavHide;
	private $_controlNav;
	private $_controlNavThumbs;
	private $_controlNavThumbsFromRel;
	private $_controlNavThumbsSearch;
	private $_controlNavThumbsReplace;
	private $_keyboardNav;
	private $_pauseOnHover;
	private $_manualAdvance;
	private $_captionOpacity;
	private $_prevText;
	private $_nextText;
	private $_beforeChange;
	private $_afterChange;
	private $_slideshowEnd;
	private $_lastSlide;
	private $_afterLoad;
	private $_theme = 'default';
	private $_ci;
	
	function __construct($theme = '',$params = array()) {
		if (!empty($theme)) {
				$this->_theme = $theme;
		}
		$this->_ci =& get_instance();
		if (!empty($params)) {		
			if (array_key_exists('effect', $params)) {
				$this->_effect = $params['effect'];
			}			
			if (array_key_exists('slices', $params)) {
				$this->_slices = $params['slices'];
			}
			if (array_key_exists('boxCols', $params)) {
				$this->_boxCols = $params['boxCols'];
			}
			if (array_key_exists('boxRows', $params)) {
				$this->_boxRows = $params['boxRows'];
			}
			if (array_key_exists('animSpeed', $params)) {
				$this->_animSpeed = $params['animSpeed'];
			}
			if (array_key_exists('pauseTime', $params)){
				$this->_pauseTime = $params['pauseTime'];
			}
			if (array_key_exists('startSlide', $params)) {
				$this->_startSlide = $params['startSlide'];
			}
			if (array_key_exists('directionNav', $params)) {
				$this->_directionNav = $params['directionNav'];
			}
			if (array_key_exists('directionNavHide', $params)) {
				$this->_directionNavHide = $params['directionNavHide'];
			}
			if (array_key_exists('controlNav', $params)) {
				$this->_controlNav = $params['controlNav'];
			}
			if (array_key_exists('controlNavThumbs', $params)) {
				$this->_controlNavThumbs = $params['controlNavThumbs'];
			}
			if (array_key_exists('controlNavThumbsFromRel', $params)) {
				$this->_controlNavThumbsFromRel = $params['controlNavThumbsFromRel'];
			}
			if (array_key_exists('controlNavThumbsSearch', $params)) {
				$this->_controlNavThumbsSearch = $params['controlNavThumbsSearch'];
			}
			if (array_key_exists('controlNavThumbsReplace', $params)) {
				$this->_controlNavThumbsReplace = $params['controlNavThumbsReplace'];
			}
			if (array_key_exists('keyboardNav', $params)) {
				$this->_keyboardNav = $params['keyboardNav'];
			}
			if (array_key_exists('pauseOnHover', $params)) {
				$this->_pauseOnHover = $params['pauseOnHover'];
			}
			if (array_key_exists('manualAdvance', $params)) {
				$this->_manualAdvance = $params['manualAdvance'];
			}
			if (array_key_exists('captionOpacity', $params)) {
				$this->_captionOpacity = $params['captionOpacity'];
			}
			if (array_key_exists('prevText', $params)) {
				$this->_prevText = $params['prevText'];
			}
			if (array_key_exists('nextText', $params)) {
				$this->_nextText = $params['nextText'];
			}
			if (array_key_exists('beforeChange', $params)) {
				$this->_beforeChange = $params['beforeChange'];
			}
			if (array_key_exists('afterChange', $params)) {
				$this->_afterChange = $params['afterChange'];
			}
			if (array_key_exists('slideshowEnd', $params)) {
				$this->_slideshowEnd = $params['slideshowEnd'];
			}
			if (array_key_exists('lastSlide', $params)) {
				$this->_lastSlide = $params['lastSlide'];
			}
			if (array_key_exists('afterLoad', $params)) {
				$this->_afterLoad = $params['afterLoad'];
			}			
		 }
	}
	
	function addCssJs() {
		$this->_ci->template->prepend_metadata(link_tag(base_url().'application/views/web/layouts/two_columns/css/nivo-slider.css'));
		$this->_ci->template->prepend_metadata(link_tag(base_url().'application/views/web/layouts/two_columns/css/'.$this->_theme.'.css'));
		$this->_ci->template->prepend_metadata(script_tag(base_url().'application/views/web/layouts/two_columns/js/jquery.nivo.slider.pack.js'));		
	}
	
	function addJsNivoslider($paramNivo,$selectorNivo) {
		$output = '';
		if (empty($paramNivo)) {
			$output .= '$("'.$selectorNivo.'").nivoSlider();';
		}else {
			$output .= '$("'.$selectorNivo.'").nivoSlider({';
			foreach ($paramNivo as $clave => $valor) {
				$output .= "$clave:".$valor.",".chr(10);
			}
			$output .= '});';
		}
		$this->_ci->javascript->output($output);
		$this->_ci->javascript->compile();
	}
	
	function printHtml($arrImg,$selectorNivo) {
		$theme = $this->getTheme();
		$tipoSelector = substr($selectorNivo, 0,1);
		$nombreSelector = substr($selectorNivo, 1);
		
		$output = '';
		$output = '<div class ="slider-wrapper theme-'.$theme.'">';
		$output .= '<div class="ribbon"></div>';
		if ($tipoSelector == '#') {
			$output .= '<div id="'.$nombreSelector.'" class="nivoSlider">';
		}else {
			$output .= '<div class="nivoSlider '.$nombreSelector.'">';		
		}
		foreach ($arrImg as $valor) {
			$output .= $valor.chr(10);
		}				
		$output .= '</div>';
		$output .= '</div>';
		return $output;
	}
	
	function outputTodo($imagenes = array(), $paramNivo = array(), $nombreSelector = '#slider-home') {
		$this->addCssJs();
		$this->addJsNivoslider($paramNivo, $nombreSelector);
		return $this->printHtml($imagenes, $nombreSelector);
	}
	
	function getTheme() {
		return $this->_theme;
	}
	
	function setTheme($theme) {
		$this->_theme = $theme;
	}
	
}
