<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {
	public function __construct () {
		parent::__construct();
	}
	
	/**
	 * read_menu_model_list
	 * 
	 * 사이트에 등록된 모델리스트를 리턴
	 * 
	 * @param	numberic	$site_id	ci_site.site_id
	 * @param	string		$language
	 */
	public function read_menu_model_list ($site_id = 0,$language = '') {
		$limit = 999;
		$list = array();
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		// ci_board_config
		$list['board_config'] = $this->board->read_list_config(0,$limit,0,$site_id,$language,array());
		
		// ci_page
		$list['page'] = $this->page->read_list(0,$limit,0,$site_id,$language,array(),array());
		
		return $list;
	}
}