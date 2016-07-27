<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Board_model extends CI_Model {
	public $menu = array();
	public $auth = array();
	
	public function __construct () {
		parent::__construct();
		
		if ($this->uri->segment(1) == 'admin') {
			$this->menu['board']['name'] = lang('admin_menu_board');
			$this->menu['board']['href'] = base_url('/admin/board/config');
			$this->menu['board']['target'] = '_self';
		} else if (isset($this->model->now_menu['model']) && $this->model->now_menu['model'] == 'board') {
			$id = ($this->uri->segment(3))?$this->uri->segment(3):$this->uri->segment(2);
			$this->auth = $this->_auth($this->model->now_menu['model_id'],$id);
		}
	}
	
	/**
	 * _auth
	 * 
	 * 각 권한을 설정해서 넘김..
	 * 
	 * @param	numberic	$config_id		ci_board_config.board_config_id
	 */
	private function _auth ($config_id,$id = 0) {
		$member_id = 0;
		$data = $model_auth = $document_data = array();
		
		if (isset($this->member->data['id'])) {
			$member_id = $this->member->data['id'];
		}
		
		$model_auth = $this->model->read_model_auth('board',$config_id);
		
		foreach ($model_auth as $action => $row) {
			$data[$action] = (in_array($member_id,$row))?TRUE:FALSE;
		}
		
		if ($id) {
			$document_data = $this->read_id($id);
			
			if ($document_data['member_id'] == $member_id) {
				$data['update'] = TRUE;
				$data['delete'] = TRUE;
			}
		}
		
		return $data;
	}
	
	/**
	 * reader
	 * 
	 * board reader check & insert
	 * 
	 * @param	numberic	$board_id		ci_board.board_id
	 */
	public function reader ($board_id) {
		$today = '';
		$data = array();
		
		$today = date('Y-m-d');
		
		if (isset($this->member->data['id'])) {
			// login
			$this->db->where('member_id',$this->member->data['id']);
		} else {
			// guest
			$this->db->where('ip',$this->input->ip_address());
		}
		
		$this->db->where('datetime >=',$today.' 00:00:00');
		$this->db->where('datetime <=',$today.' 23:59:59');
		
		if ($this->db->get('board_reader')->num_rows() == 0) {
			if (isset($this->member->data['id'])) {
				// login
				$this->db->set('member_id',$this->member->data['id']);
			} else {
				// guest
				$this->db->set('ip',$this->input->ip_address());
			}
			
			$this->db->set('datetime',date('Y-m-d H:i:s'));
			$this->db->set('board_id',$board_id);
			$this->db->insert('board_reader');
		}
	}
	
	/**
	 * read_total
	 * 
	 * 게시글의 전체 숫자를 리턴
	 * 
	 * @param	numberic	$config_id		ci_board_config.id
	 */
	public function read_total ($config_id = 0) {
		$total = 0;
		
		$this->db->group_by('board_id');
		
		if ($config_id) {
			$this->db->where('board_config_id',$config_id);
		}
		
		$total = $this->db->get('board')->num_rows();
		return $total;
	}
	
	/**
	 * read_total_config
	 * 
	 * 게시판 전체 숫자를 리턴
	 * 
	 * @param	numberic	$site_id		ci_board_config.site_id
	 * @param	array		$keyword
	 */
	public function read_total_config ($site_id = 0,$keyword = array()) {
		$total = 0;
		$keywords = array();
		
		if (is_array($keyword)) {
			$keywords = $keyword;
		} else {
			$keywords[] = $keyword;
		}
		
		// check site_id
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		foreach ($keywords as $key => $value) {
			if (isset($keywords[$key])) {
				$this->db->like('name',$keywords[$key],'both');
			}
		}
		
		$this->db->where('site_id',$site_id);
		$this->db->group_by('board_config_id');
		$total = $this->db->get('board_config')->num_rows();
		
		return $total;
	}
	
	/**
	 * read_id
	 * 
	 * get ci_board row
	 * 
	 * @param	numberic	$id			ci_board.board_id
	 * @param	string		$language
	 */
	public function read_id ($id,$language = '') {
		$data = $result = $tmp = array();
		
		$this->db->select(write_prefix_db($this->db->list_fields('board'),array('b','bj')).', m.name AS member_name, COUNT(mr.board_id) AS hit');
		$this->db->from('board b');
		$this->db->join('board bj','b.board_id = bj.board_id AND bj.language = "'.$language.'"','LEFT');
		$this->db->join('member m','b.member_id = m.id','LEFT');
		$this->db->join('board_reader mr','b.board_id = mr.board_id','LEFT');
		$this->db->where('b.board_id',$id);
		$this->db->group_by('b.board_id');
		$this->db->limit(1);
		$result = $tmp = $this->db->get()->row_array();
		
		unset($tmp['member_name']);
		$data = read_prefix_db($tmp,'bj');
		
		if (isset($data['member_id']) && !isset($data['name'])) {
			$data['name'] = $result['member_name'];
		}
		
		return $data;
	}
	
	/**
	 * read_config_id
	 * 
	 * 게시판 설정을 ci_board_config.id로 검색 후 리턴
	 * 
	 * @param	numberic	$id			ci_board_config.board_config_id
	 * @param	numberic	$site_id	ci_site.id
	 * @param	string		$language
	 */
	public function read_config_id ($id,$site_id = 0,$language = '') {
		$data = array();
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		$this->db->select(write_prefix_db($this->db->list_fields('board_config'),array('c','cj')));
		$this->db->from('board_config c');
		$this->db->join('board_config cj','c.board_config_id = cj.board_config_id AND cj.language = "'.$language.'"','LEFT');
		$this->db->where('c.board_config_id',$id);
		$this->db->where('c.site_id',$site_id);
		$this->db->group_by('c.board_config_id');
		$this->db->limit(1);
		$data = read_prefix_db($this->db->get()->row_array(),'cj');
		
		return $data;
	}
	
	/**
	 * read_list
	 * 
	 * 게시판의 게시글 리스트를 리턴
	 * 
	 * @param	numberic	$config_id		ci_board_config.board_config_id
	 * @param	numberic	$total			board total
	 * @param	numberic	$limit			limit
	 * @param	numberic	$page
	 * @param	numberic	$site_id		ci_board_config.site_id
	 * @param	string		$language
	 * @param	array		$field
	 * @param	array		$keyword
	 */
	public function read_list ($config_id,$total = 0,$limit = 20,$page = 0,$language = '',$field = array(),$keyword = array()) {
		$offset = 0;
		$list = $result = $tmp = $fields = $keywords = array();
		
		if (empty($page)) {
			$page = 1;
		}
		
		if (empty($total)) {
			$total = $this->read_total($config_id);
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		if (is_array($field)) {
			$fields = $field;
		} else {
			$fields[] = $field;
		}
		
		if (is_array($keyword)) {
			$keywords = $keyword;
		} else {
			$keywords[] = $keyword;
		}
		
		$offset = ($page - 1) * $limit;
		
		// get DB
		$this->db->select(write_prefix_db($this->db->list_fields('board'),array('b','bj')).', m.name AS member_name, COUNT(*) AS hit');
		$this->db->from('board b');
		$this->db->join('board bj','b.board_config_id = bj.board_config_id AND bj.language = "'.$language.'"','LEFT');
		$this->db->join('member m','b.member_id = m.id','LEFT');
		$this->db->join('board_reader mr','b.board_id = mr.board_id','LEFT');
		
		foreach ($fields as $key => $value) {
			if (isset($fields[$key])) {
				switch ($fields[$key]) {
					case 'name' :
							$this->db->like('bj.name',$keywords[$key],'both');
							$this->db->or_like('m.name',$keywords[$key],'both');
						break;
					default :
							$this->db->like('bj.'.$fields[$key],$keywords[$key],'both');
						break;
				}
			}
		}
		
		$this->db->group_by('b.board_id');
		$this->db->order_by('b.id','DESC');
		$this->db->offset($offset);
		$this->db->limit($limit);
		$result = $this->db->get()->result_array();
		
		foreach ($result as $key => $row) {
			$tmp = array();
			$tmp['name'] = $row['member_name'];
			$tmp['hit'] = $row['hit'];
			
			$row = read_prefix_db($row,'bj');
			$row['number'] = $total - $key;
			$row['hit'] = $tmp['hit'];
			
			if (isset($tmp['name'])) {
				$row['name'] = $tmp['name'];
			}
			
			$list[] = $row;
		}
		
		return $list;
	}
	
	/**
	 * read_list_config
	 * 
	 * 게시판 리스트를 리턴
	 * 
	 * @param	numberic	$total			board config total
	 * @param	numberic	$limit			limit
	 * @param	numberic	$page
	 * @param	numberic	$site_id		ci_board_config.site_id
	 * @param	string		$language
	 * @param	array		$keyword
	 */
	public function read_list_config ($total = 0,$limit = 20,$page = 0,$site_id = 0,$language = '',$keyword = array()) {
		$offset = 0;
		$list = $result = $keywords = array();
		
		if (empty($page)) {
			$page = 1;
		}
		
		// check site_id
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		// check total
		if (empty($total)) {
			$total = $this->read_total_config($site_id);
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		if (is_array($keyword)) {
			$keywords = $keyword;
		} else {
			$keywords[] = $keyword;
		}
		
		$offset = ($page - 1) * $limit;
		
		// get DB
		$this->db->select(write_prefix_db($this->db->list_fields('board_config'),array('bc','bcj')));
		$this->db->from('board_config bc');
		$this->db->join('board_config bcj','bc.board_config_id = bcj.board_config_id AND bcj.language = "'.$language.'"','LEFT');
		$this->db->where('bc.site_id',$site_id);
		
		foreach ($keywords as $key => $value) {
			if (isset($keywords[$key])) {
				$this->db->like('bcj.name',$keywords[$key],'both');
			}
		}
		
		$this->db->group_by('bc.board_config_id');
		$this->db->order_by('bc.id','DESC');
		$this->db->offset($offset);
		$this->db->limit($limit);
		$result = $this->db->get()->result_array();
		
		foreach ($result as $key => $row) {
			$row = read_prefix_db($row,'bcj');
			$row['number'] = $total - $key;
			
			$list[] = $row;
		}
		
		return $list;
	}
	
	/**
	 * write_data
	 * 
	 * insert board
	 * 
	 * @param	array	$data
	 */
	public function write_data ($data) {
		$result = array();
		
		if (!isset($data['board_config_id'])) {
			$data['board_config_id'] = $this->model->now_menu['model_id'];
		}
		
		if (!isset($data['member_id']) && isset($this->member->data['id'])) {
			$data['member_id'] = $this->member->data['id'];
		}
		
		if (!isset($data['write_datetime'])) {
			$data['write_datetime'] = date('Y-m-d H:i:s');
		}
		
		if (!isset($data['update_datetime'])) {
			$data['update_datetime'] = $data['write_datetime'];
		}
		
		if (!isset($data['last_datetime'])) {
			$data['last_datetime'] = $data['write_datetime'];
		}
		
		if (!isset($data['ip'])) {
			$data['ip'] = $this->input->ip_address();
		}
		
		if (!isset($data['language'])) {
			$data['language'] = $this->config->item('language');
		}
		
		// insert ci_board_config
		if ($this->db->insert('board',$data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_write_success');
			$result['insert_id'] = $this->db->insert_id();
			
			if (!isset($data['board_id']) || empty($data['board_id'])) {
				$this->db->set('board_id',$result['insert_id']);
				$this->db->where('id',$result['insert_id']);
				$this->db->update('board');
			}
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * write_config
	 * 
	 * 게시판 설정 추가
	 * 
	 * @param	array	$data	ci_board_config row
	 */
	public function write_config ($data) {
		$result = array();
		$result['status'] = FALSE;
		
		// check site id
		if (!isset($data['site_id'])) {
			$data['site_id'] = $this->model->site['site_id'];
		}
		
		// check site language
		if (!isset($data['language'])) {
			$data['language'] = $this->config->item('language');
		}
		
		// insert ci_board_config
		if ($this->db->insert('board_config',$data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_write_success');
			$result['insert_id'] = $this->db->insert_id();
			
			if (!isset($data['board_config_id']) || empty($data['board_config_id'])) {
				$this->db->set('board_config_id',$result['insert_id']);
				$this->db->where('id',$result['insert_id']);
				$this->db->update('board_config');
			}
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * update_data
	 * 
	 * board update
	 * 
	 * @param	array		$data
	 * @param	numberic	$id			ci_board.board_id
	 * @param	string		$language
	 */
	public function update_data ($data,$id,$language = '') {
		$result = array();
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		if (isset($data['update_datetime'])) {
			$data['update_datetime'] = date('Y-m-d H:i:s');
		}
		
		if (isset($data['last_datetime'])) {
			$data['last_datetime'] = $data['update_datetime'];
		}
		
		$this->db->where('language',$language);
		$this->db->where('board_id',$id);
		if ($this->db->update('board',$data)) {
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
	 * update_config
	 * 
	 * 게시판 설정 업데이트
	 * 
	 * @param	array		$data		ci_board_config row
	 * @param	numberic	$id			ci_board_config.board_config_id
	 * @param	numberic	$site_id	ci_site.site_id
	 * @param	string		$language
	 */
	public function update_config ($data,$id,$site_id = 0,$language = '') {
		$site_id = 0;
		$language = '';
		$result = array();
		
		$result['status'] = FALSE;
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		// update ci_board_config
		$this->db->where('language',$language);
		$this->db->where('site_id',$site_id);
		$this->db->where('board_config_id',$id);
		if ($this->db->update('board_config',$data)) {
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
	 * delete_data
	 * 
	 * DB delete
	 * 
	 * @param	numberic	$id		ci_board.board_id
	 */
	public function delete_data ($id) {
		$result = array();
		
		$this->db->where('board_id',$id);
		if ($this->db->delete('board')) {
			$result['status'] = TRUE;
			$result['message'] = lang('board_config_delete_success');
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * delete_config
	 * 
	 * 게시판 삭제
	 * 
	 * @param	numberic	$id			ci_board_config.board_config_id
	 * @param	numberic	$site_id	ci_site.id
	 */
	public function delete_config ($id,$site_id = 0) {
		$total = 0;
		$result = array();
		
		// check site_id
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		$total = $this->read_total($id);
		
		if (empty($total)) {
			// delete DB
			$this->db->where('board_config_id',$id);
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
						'unsigned'=>TRUE,
						'default'=>0
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
		
		// board_reader table
		if (!$this->db->table_exists('board_reader')) {
			if ($flag) {
				$fields = array(
					'board_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'datetime'=>array(
						'type'=>'DATETIME',
						'default'=>'0000-00-00 00:00:00'
					),
					'ip'=>array(
						'type'=>'VARCHAR',
						'constraint'=>45
					),
					'member_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					)
				);
				$this->dbforge->add_field($fields);
				$return = $this->dbforge->create_table('board_reader');
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
	public function uninstall () {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		$return = $this->dbforge->drop_table('board');
		if ($return) {
			$this->dbforge->drop_table('board_config');
			$this->dbforge->drop_table('board_reader');
		}
		
		return $return;
	}
}