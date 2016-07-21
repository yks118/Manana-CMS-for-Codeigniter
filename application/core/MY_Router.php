<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Router extends CI_Router {
	public function __construct () {
		parent::__construct();
		
		$uris = $admin_method = array();
		$uris = explode('/',trim(preg_replace('/([^\?]+).*/i','$1',$_SERVER['REQUEST_URI']),'/'));
		
		$admin_method[] = 'dashboard';
		$admin_method[] = 'install';
		$admin_method[] = 'site';
		$admin_method[] = 'updateSiteForm';
		$admin_method[] = 'menu';
		$admin_method[] = 'updateMenuForm';
		$admin_method[] = 'readMenuModelIdAjax';
		$admin_method[] = 'updateMenuIndexAjax';
		$admin_method[] = 'readMenuId';
		$admin_method[] = 'updateMenuHomeAjax';
		
		if ($uris[0] == 'admin' && isset($uris[1]) && !in_array($uris[1],$admin_method)) {
			$this->class = $uris[1];
			$this->method = (isset($uris[2]))?'admin_'.$uris[2]:'admin_index';
			
			$this->uri->rsegments = array_slice($this->uri->rsegments,1);
			$this->uri->rsegments[1] = $this->method;
		}
	}
}