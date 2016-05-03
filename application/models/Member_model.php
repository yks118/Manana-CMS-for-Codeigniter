<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_model extends CI_Model {
	public $data = array();
	public $skin = 'basic';
	
	public function __construct() {
		parent::__construct();
		
		// member password library
		$this->load->library('encrypt');
		
		// set member data
		$this->data = $this->read_id($this->session->userdata('member_id'));
	}
	
	/**
	 * _encode
	 * 
	 * 비밀번호 암호화
	 * 
	 * @param	string		$password
	 */
	private function _encode ($password) {
		return $this->encrypt->encode($password);
	}
	
	/**
	 * _decode
	 * 
	 * 비밀번호 복호화
	 * 
	 * @param	string		$password
	 */
	private function _decode ($password) {
		return $this->encrypt->decode($password);
	}
	
	/**
	 * login
	 * 
	 * 로그인 처리
	 * 
	 * @param	string		$username		ci_member.username
	 * @param	string		$password		ci_member.password
	 */
	public function login ($username,$password) {
		$result = $log_data = array();
		$member = $this->read_username($username);
		
		// login_log check
		$this->db->select('*');
		$this->db->from('login_log');
		$this->db->where('write_datetime >=',date('Y-m-d H:i:s',strtotime('-5 minute')));
		$count = $this->db->count_all_results();
		
		// default setting ci_login_log data
		$log_data['member_id'] = 0;
		$log_data['ip'] = $this->input->ip_address();
		$log_data['status'] = 'f';
		$log_data['write_datetime'] = date('Y-m-d H:i:s');
		
		if (isset($member['id'])) {
			$log_data['member_id'] = $member['id'];
			
			if ($password == $this->_decode($member['password']) && $count <= 5) {
				$log_data['status'] = 't';
				
				// set session
				$this->session->set_userdata('member_id',$member['id']);
				
				$result['status'] = TRUE;
				$result['message'] = lang('member_login_success');
			} else {
				$result['status'] = FALSE;
				$result['message'] = lang('member_login_danger');
			}
		} else {
			$result['status'] = FALSE;
			$result['message'] = lang('member_login_danger');
		}
		
		// insert ci_login_log data
		$this->db->insert('login_log',$log_data);
		
		return $result;
	}
	
	/**
	 * read_id
	 * 
	 * id로 ci_member 테이블 검색
	 * 
	 * @param	numberic	$id		ci_member.id
	 */
	public function read_id ($id) {
		$this->db->select('*');
		$this->db->from('member');
		$this->db->where('id',$id);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	
	/**
	 * read_username
	 * 
	 * username로 ci_member 테이블 검색
	 * 
	 * @param	string		$username		ci_member.username
	 */
	public function read_username ($username) {
		$this->db->select('*');
		$this->db->from('member');
		$this->db->where('username',$username);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	
	/**
	 * read_total
	 * 
	 * member table rows
	 */
	public function read_total () {
		return $this->db->count_all('member');
	}
	
	/**
	 * check_admin
	 * 
	 * 운영자인지 체크
	 * 
	 * @param	numberic	$id		ci_member.id
	 */
	public function check_admin ($id = 0) {
		$result = FALSE;
		
		if (isset($this->data['id']) && empty($id)) {
			// $id가 설정되어있지 않은데, 로그인이 되어있다면 로그인 유저의 권한을 체크..
			$id = $this->data['id'];
		}
		
		if ($id) {
			
		}
		
		return $result;
	}
	
	/**
	 * write_data
	 * 
	 * 들어온 데이터를 테이블에 insert
	 * 
	 * @param	array		$data
	 * @return	numberic	$result		ci_member.id
	 */
	public function write_data ($data) {
		$result = array();
		$result['status'] = FALSE;
		
		$data['member_password'] = $this->_encode($data['member_password']);
		if (isset($data['member_write_datetime']) === FALSE) {
			$data['member_write_datetime'] = $data['member_update_datetime'] = date('Y-m-d H:i:s');
		}
		
		$member_data = $member_information_data = array();
		foreach ($data as $key => $value) {
			if (strpos($key,'member_information_') !== FALSE && strpos($key,'member_information_') == 0) {
				$member_information_data[str_replace('member_information_','',$key)] = $value;
			} else if (strpos($key,'member_') !== FALSE && strpos($key,'member_') == 0) {
				$member_data[str_replace('member_','',$key)] = $value;
			}
		}
		
		// insert ci_member
		if ($this->db->insert('member',$member_data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('member_write_success');
			$result['insert_id'] = $this->db->insert_id();
			
			// set ci_member_information data
			$member_information_data['member_id'] = $result['insert_id'];
			
			if (!$this->db->table_exists('site')) {
				// 첫 사이트라면..
				$member_information_data['site_id'] = 1;
				$member_information_data['site_member_grade_id'] = 1;
				$member_information_data['language'] = $this->config->item('language');
			} else {
				// 일반 회원의 가입..
			}
			
			// insert ci_member_information
			$this->db->insert('member_information',$member_information_data);
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * install
	 * 
	 * member DB install
	 * 
	 * @return	string	true / false
	 */
	public function install () {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		// member table
		$fields = array(
			'id'=>array(
				'type'=>'INT',
				'constraint'=>11,
				'unsigned'=>TRUE,
				'auto_increment'=>TRUE
			),
			'username'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'password'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'name'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'email'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'write_datetime'=>array(
				'type'=>'DATETIME',
				'default'=>'0000-00-00 00:00:00'
			),
			'update_datetime'=>array(
				'type'=>'DATETIME',
				'default'=>'0000-00-00 00:00:00'
			),
			'last_login'=>array(
				'type'=>'DATETIME',
				'default'=>'0000-00-00 00:00:00'
			)
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id',TRUE);
		$return = $this->dbforge->create_table('member');
		
		// member information table
		$fields = array(
			'id'=>array(
				'type'=>'INT',
				'constraint'=>11,
				'unsigned'=>TRUE,
				'auto_increment'=>TRUE
			),
			'member_id'=>array(
				'type'=>'INT',
				'constraint'=>11,
				'unsigned'=>TRUE
			),
			'site_id'=>array(
				'type'=>'INT',
				'constraint'=>11,
				'unsigned'=>TRUE
			),
			'site_member_grade_id'=>array(
				'type'=>'INT',
				'constraint'=>11,
				'unsigned'=>TRUE
			),
			'description'=>array(
				'type'=>'TEXT',
				'null'=>TRUE
			),
			'language'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			)
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id',TRUE);
		$this->dbforge->create_table('member_information');
		
		// login log table
		$fields = array(
			'id'=>array(
				'type'=>'INT',
				'constraint'=>11,
				'unsigned'=>TRUE,
				'auto_increment'=>TRUE
			),
			'member_id'=>array(
				'type'=>'INT',
				'constraint'=>11,
				'unsigned'=>TRUE
			),
			'ip'=>array(
				'type'=>'VARCHAR',
				'constraint'=>45
			),
			'status'=>array(
				'type'=>'VARCHAR',
				'constraint'=>1
			),
			'write_datetime'=>array(
				'type'=>'DATETIME',
				'default'=>'0000-00-00 00:00:00'
			)
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id',TRUE);
		$this->dbforge->create_table('login_log');
		
		return $return;
	}
	
	/**
	 * change
	 * 
	 * member DB 변경사항
	 */
	public function change () {
		
	}
	
	/**
	 * uninstall
	 * 
	 * member DB 삭제
	 * 
	 * @return	string	true / false
	 */
	public function uninstall () {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		$return = $this->dbforge->drop_table('member');
		if ($return) {
			$this->dbforge->drop_table('member_information');
		}
		
		return $return;
	}
}