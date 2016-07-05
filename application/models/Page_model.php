<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page_model extends CI_Model {
	public $menu = array();
	
	public function __construct() {
		parent::__construct();
		
		if ($this->uri->segment(1) == 'admin') {
			$this->menu['page']['name'] = lang('admin_menu_page');
			$this->menu['page']['href'] = base_url('/admin/page/');
			$this->menu['page']['target'] = '_self';
		}
	}
	
	/**
	 * read_total
	 * 
	 * 사이트에 등록된 페이지의 총 수
	 * 
	 * @param	numberic	$site_id	ci_site.id
	 */
	public function read_total ($site_id = 0) {
		$total = 0;
		
		if (empty($site_id)) {
			$site_id = $this->model->site['id'];
		}
		
		$this->db->where('site_id',$site_id);
		$total = $this->db->count_all_results('page');
		
		return $total;
	}
	
	/**
	 * read_list
	 * 
	 * ci_page의 리스트를 리턴
	 */
	public function read_list () {
		$list = array();
		
		$this->db->select('*');
		$this->db->from('page');
		$list = $this->db->get()->result_array();
		
		return $list;
	}
	
	/**
	 * install
	 * 
	 * page DB install
	 * 
	 * @param	string		$flag		true / false
	 * @return	string		$return		true / false
	 */
	public function install ($flag = TRUE) {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		// page table
		if (!$this->db->table_exists('page')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'page_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'title'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'document'=>array(
						'type'=>'TEXT'
					),
					'member_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'write_datetime'=>array(
						'type'=>'DATETIME',
						'default'=>'0000-00-00 00:00:00'
					),
					'update_datetime'=>array(
						'type'=>'DATETIME',
						'default'=>'0000-00-00 00:00:00'
					),
					'ip'=>array(
						'type'=>'VARCHAR',
						'constraint'=>45
					),
					'language'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('page');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		return $return;
	}
	
	/**
	 * uninstall
	 * 
	 * page DB 삭제
	 * 
	 * @return	string	true / false
	 */
	public function uninstall () {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		$return = $this->dbforge->drop_table('page');
		
		return $return;
	}
}