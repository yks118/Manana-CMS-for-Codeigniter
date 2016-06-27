<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Loader extends CI_Loader {
	public function __construct () {
		parent::__construct();
	}
	
	public function view ($view, $vars = array(), $return = FALSE) {
		$vars['path'] = base_url('/assets/views/'.preg_replace('/(.+)\/.+$/i','$1',$view));
		
		return $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
}