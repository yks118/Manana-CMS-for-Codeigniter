<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Board_model extends CI_Model {
	public $menu = array();
	
	public function __construct () {
		parent::__construct();
		
		if ($this->uri->segment(1) == 'admin') {
			$this->menu['board']['name'] = lang('admin_menu_board');
			$this->menu['board']['href'] = base_url('/admin/board/');
			$this->menu['board']['target'] = '_self';
		}
	}
	
	/**
	 * read_total
	 * 
	 * 게시글의 전체 숫자를 리턴
	 * 
	 * @param	numberic	$config_id		ci_board_config.id
	 */
	public function read_total ($config_id = '') {
		$total = 0;
		
		$this->db->group_by('board_id');
		
		if ($config_id) {
			$this->db->where('board_config_id',$config_id);
		}
		
		$total = $this->db->count_all_results('board');
		return $total;
	}
	
	/**
	 * read_total_config
	 * 
	 * 게시판 전체 숫자를 리턴
	 * 
	 * @param	numberic	$site_id		ci_board_config.site_id
	 */
	public function read_total_config ($site_id = 0) {
		$total = 0;
		
		// check site_id
		if (empty($site_id)) {
			$site_id = $this->model->site['id'];
		}
		
		$this->db->where('site_id',$site_id);
		$this->db->group_by('board_config_id');
		$total = $this->db->count_all_results('board_config');
		
		return $total;
	}
	
	/**
	 * read_config_id
	 * 
	 * 게시판 설정을 ci_board_config.id로 검색 후 리턴
	 * 
	 * @param	numberic	$id		ci_board_config.id
	 */
	public function read_config_id ($id) {
		$data = array();
		
		$this->db->select('*');
		$this->db->from('board_config');
		$this->db->where('id',$id);
		$this->db->limit(1);
		$data = $this->db->get()->row_array();
		
		return $data;
	}
	
	/**
	 * read_list_config
	 * 
	 * 게시판 리스트를 리턴
	 * 
	 * @param	numberic	$limit			limit
	 * @param	numberic	$total			board config total
	 * @param	numberic	$site_id		ci_board_config.site_id
	 */
	public function read_list_config ($limit = 20,$total = 0,$site_id = 0) {
		$list = $result = array();
		
		// check site_id
		if (empty($site_id)) {
			$site_id = $this->model->site['id'];
		}
		
		// check total
		if (empty($total)) {
			$total = $this->read_total_config($site_id);
		}
		
		// get DB
		$this->db->select('*');
		$this->db->from('board_config');
		$this->db->where('site_id',$site_id);
		$this->db->group_by('board_config_id');
		$this->db->limit($limit);
		$result = $this->db->get()->result_array();
		
		foreach ($result as $key => $row) {
			$row['number'] = $total - $key;
			
			$list[] = $row;
		}
		
		return $list;
	}
	
	/**
	 * write_config_data
	 * 
	 * 게시판 설정 추가
	 * 
	 * @param	array	$data	ci_board_config row
	 */
	public function write_config_data ($data) {
		$result = $config_data = array();
		$result['status'] = FALSE;
		
		foreach ($data as $key => $value) {
			if (strpos($key,'config_') !== FALSE && strpos($key,'config_') == 0) {
				$config_data[str_replace('config_','',$key)] = $value;
			}
		}
		
		// check site id
		if (!isset($config_data['site_id'])) {
			$config_data['site_id'] = $this->model->site['id'];
		}
		
		// check site language
		if (!isset($config_data['language'])) {
			$config_data['language'] = $this->config->item('language');
		}
		
		// insert ci_board_config
		if ($this->db->insert('board_config',$config_data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_write_success');
			$result['insert_id'] = $this->db->insert_id();
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * update_config_data
	 * 
	 * 게시판 설정 업데이트
	 * 
	 * @param	array		$data	ci_board_config row
	 * @param	numberic	$id		ci_board_config.id
	 */
	public function update_config_data ($data,$id) {
		$site_id = 0;
		$language = '';
		$result = $config_data = array();
		
		$result['status'] = FALSE;
		$site_id = $this->model->site['id'];
		$language = $this->config->item('language');
		
		foreach ($data as $key => $value) {
			if (strpos($key,'config_') !== FALSE && strpos($key,'config_') == 0) {
				$config_data[str_replace('config_','',$key)] = $value;
			}
		}
		
		// update ci_board_config
		$this->db->where('language',$language);
		$this->db->where('site_id',$site_id);
		$this->db->where('id',$id);
		if ($this->db->update('board_config',$config_data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_update_success');
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * delete_config_id
	 * 
	 * 게시판 삭제
	 * 
	 * @param	numberic	$id			ci_board_config.id
	 * @param	numberic	$site_id	ci_site.id
	 */
	public function delete_config_id ($id,$site_id = 0) {
		$total = 0;
		$result = array();
		
		// check site_id
		if (empty($site_id)) {
			$site_id = $this->model->site['id'];
		}
		
		$total = $this->read_total($id);
		
		if (empty($total)) {
			// delete DB
			$this->db->where('id',$id);
			$this->db->where('site_id',$site_id);
			if ($this->db->delete('board_config')) {
				$result['status'] = TRUE;
				$result['message'] = lang('board_config_delete_success');
			} else {
				$result['status'] = FALSE;
				$result['message'] = $this->db->_error_message();
				$result['number'] = $this->db->_error_number();
			}
		} else {
			$result['status'] = FALSE;
			$result['message'] = lang('board_document_not_empty');
		}
		
		return $result;
	}
	
	/**
	 * install
	 * 
	 * board DB 설치
	 * 
	 * @param	string		$flag		true / false
	 * @return	string		$return		true / false
	 */
	public function install ($flag = TRUE) {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		// Board DB check
		if (!$this->db->table_exists('board')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'board_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'board_config_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'parent_id'=>array(
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
						'unsigned'=>TRUE,
						'default'=>0
					),
					'name'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255,
						'null'=>TRUE
					),
					'password'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255,
						'null'=>TRUE
					),
					'write_datetime'=>array(
						'type'=>'DATETIME',
						'default'=>'0000-00-00 00:00:00'
					),
					'update_datetime'=>array(
						'type'=>'DATETIME',
						'default'=>'0000-00-00 00:00:00'
					),
					'last_datetime'=>array(
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
				$return = $this->dbforge->create_table('board');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// Board Config DB check
		if (!$this->db->table_exists('board_config')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'board_config_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'name'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'skin'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'use_secret'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1,
						'default'=>'t'
					),
					'default_secret'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1,
						'default'=>'f'
					),
					'comment_use_secret'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1,
						'default'=>'t'
					),
					'comment_default_secret'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1,
						'default'=>'f'
					),
					'order_by'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'order_by_sort'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'limit'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'default'=>20
					),
					'language'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('board_config');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		return $return;
	}
	
	/**
	 * uninstall
	 * 
	 * board DB 삭제
	 * 
	 * @return	string	true / false
	 */
	public function uninstall () {}
}