<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_model extends CI_Model {
	private $select_field = 'm.*, m.description AS memo, mi.description AS mi_description, mij.description AS mij_description, mi.language AS mi_language, mij.language AS mij_language';
	
	public $data = array();
	public $skin = 'basic';
	public $menu = array();
	
	public function __construct() {
		parent::__construct();
		
		// member password library
		$this->load->library('encrypt');
		
		// set member data
		if ($this->session->userdata('member_id')) {
			$this->data = $this->read_data('id',$this->session->userdata('member_id'));
		}
		
		if ($this->uri->segment(1) == 'admin') {
			$this->menu['member']['name'] = lang('admin_menu_member');
			$this->menu['member']['href'] = base_url('/admin/member/');
			$this->menu['member']['target'] = '_self';
			
			$this->menu['member']['children']['grade']['name'] = lang('admin_menu_member_grade');
			$this->menu['member']['children']['grade']['href'] = base_url('/admin/member/grade/');
			$this->menu['member']['children']['grade']['target'] = '_self';
		}
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
	 * _read_grade
	 * 
	 * 회원등급 리턴
	 * 
	 * @param	numberic	$id			ci_member.id
	 * @param	numberic	$site_id	ci_site.id
	 */
	private function _read_grade ($id,$site_id = 0) {
		$data = $result = array();
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		$this->db->select('smg.id, smg.name');
		$this->db->from('member_grade mg');
		$this->db->join('site_member_grade smg','mg.site_member_grade_id = smg.id','LEFT');
		$this->db->where('mg.member_id',$id);
		$this->db->where('mg.site_id',$site_id);
		$result = $this->db->get()->result_array();
		
		foreach ($result as $row) {
			$data[$row['id']] = $row['name'];
		}
		
		return $data;
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
		$login_key = '';
		$result = $log_data = $member = array();
		
		$login_key = $this->model->site['login'];
		$member = $this->read_data($login_key,$username);
		
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
				
				// update last login
				$this->db->set('last_login',date('Y-m-d H:i:s'));
				$this->db->where($login_key,$username);
				$this->db->update('member');
				
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
	 * logout
	 * 
	 * 로그아웃 처리
	 */
	public function logout () {
		$result = array();
		
		if (isset($this->data['id'])) {
			$this->session->unset_userdata('member_id');
			
			$result['status'] = TRUE;
			$result['message'] = lang('member_logout_success');
		} else {
			$result['status'] = FALSE;
			$result['message'] = lang('member_logout_danger_not_login');
		}
		
		return $result;
	}
	
	/**
	 * read_data
	 * 
	 * ci_member 테이블을 지정된 필드로 검색해서 리턴
	 * 
	 * @param	string		$field
	 * @param	string		$value
	 * @param	numberic	$site_id		ci_site.id
	 * @param	string		$language
	 */
	public function read_data ($field,$value,$site_id = 0,$language = '') {
		$data = array();
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		// get member info
		$this->db->select($this->select_field);
		$this->db->from('member m');
		$this->db->join('member_information mi','m.id = mi.member_id','LEFT');
		$this->db->join('member_information mij','m.id = mij.member_id AND mij.language = "'.$language.'"','LEFT');
		$this->db->where('mi.site_id',$site_id);
		$this->db->where('m.'.$field,$value);
		$this->db->limit(1);
		$data = $this->db->get()->row_array();
		
		if (isset($data['mij_description'])) {
			$data['language'] = $data['mij_language'];
			$data['description'] = $data['mij_description'];
		} else {
			$data['language'] = $data['mi_language'];
			$data['description'] = $data['mi_description'];
		}
		unset($data['mi_language'],$data['mij_language'],$data['mi_description'],$data['mij_description']);
		
		// get member grade
		$data['grade'] = $this->_read_grade($data['id'],$site_id);
		
		// get profile photo
		$data['profile_photo'] = $this->file->read_model('member_profile_photo',$data['id']);
		
		return $data;
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
	 * read_list
	 * 
	 * member table list
	 * 
	 * @param	array	$data
	 */
	public function read_list ($data = array()) {
		$list = $result = array();
		
		if (!isset($data['site_id'])) {
			$data['site_id'] = $this->model->site['site_id'];
		}
		
		if (!isset($data['limit'])) {
			$data['limit'] = 20;
		}
		
		if (!isset($data['offset'])) {
			$data['offset'] = 0;
		}
		
		if (!isset($data['total'])) {
			$data['total'] = $this->read_total();
		}
		
		// get list
		$this->db->select($this->select_field);
		$this->db->from('member m');
		$this->db->join('member_information mi','m.id = mi.member_id','LEFT');
		$this->db->join('member_information mij','m.id = mij.member_id','LEFT');
		$this->db->where('mi.site_id',$data['site_id']);
		$this->db->group_by('m.id');
		
		if (isset($data['order_by']) && isset($data['order_by_sort'])) {
			$this->db->order_by('mi.'.$data['order_by'],$data['order_by_sort']);
		} else {
			$this->db->order_by('mi.member_id','DESC');
		}
		
		if (isset($data['limit'])) {
			$this->db->limit($data['limit']);
		}
		
		if (isset($data['offset'])) {
			$this->db->offset($data['offset']);
		}
		
		$result = $this->db->get()->result_array();
		
		foreach ($result as $key => $row) {
			if (isset($row['mij_description'])) {
				$data['language'] = $row['mij_language'];
				$data['description'] = $row['mij_description'];
			} else {
				$data['language'] = $row['mi_language'];
				$data['description'] = $row['mi_description'];
			}
			unset($row['mi_language'],$row['mij_language'],$row['mi_description'],$row['mij_description']);
			
			$row['number'] = $data['total'] - $key;
			
			$list[] = $row;
		}
		
		return $list;
	}
	
	/**
	 * read_site_grade_list
	 * 
	 * 사이트의 회원등급 리스트를 리턴
	 * 
	 * @param	numberic	$site_id		ci_site.id
	 * @param	string		$language
	 */
	public function read_site_grade_list ($site_id = 0,$language = '') {
		$list = array();
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		// get DB
		$this->db->select(write_prefix_db($this->db->list_fields('site_member_grade'),array('s','sj')));
		$this->db->from('site_member_grade s');
		$this->db->join('site_member_grade sj','s.site_member_grade_id = sj.site_member_grade_id AND sj.language = "'.$language.'"','LEFT');
		$this->db->where('s.site_id',$site_id);
		$this->db->group_by('s.site_member_grade_id');
		$this->db->order_by('s.site_member_grade_id','ASC');
		$this->db->order_by('s.id','ASC');
		$query = $this->db->get();
		
		foreach ($query->result_array() as $row) {
			$list[] = read_prefix_db($row,'sj');
		}
		
		return $list;
	}
	
	/**
	 * read_site_grade_id
	 * 
	 * 사이트의 회원등급 정보를 리턴
	 * 
	 * @param	numberic	$site_grade_id		ci_site_member_grade.site_member_grade_id
	 * @param	numberic	$site_id			ci_site.id
	 * @param	string		$language
	 */
	public function read_site_grade_id ($site_grade_id,$site_id = 0,$language = '') {
		$data = array();
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		$this->db->select(write_prefix_db($this->db->list_fields('site_member_grade'),array('s','sj')));
		$this->db->from('site_member_grade s');
		$this->db->join('site_member_grade sj','s.site_member_grade_id = sj.site_member_grade_id AND sj.language = "'.$language.'"','LEFT');
		$this->db->where('s.site_member_grade_id',$site_grade_id);
		$this->db->where('s.site_id',$site_id);
		$this->db->limit(1);
		$data = read_prefix_db($this->db->get()->row_array(),'sj');
		
		return $data;
	}
	
	/**
	 * read_grade_total
	 * 
	 * 사이트에 해당 등급의 멤버 숫자를 리턴
	 * 
	 * @param	numberic	$site_grade_id		ci_site_member_grade.site_member_grade_id
	 */
	public function read_grade_total ($site_grade_id) {
		$total = 0;
		
		$this->db->where('site_member_grade_id',$site_grade_id);
		$total = $this->db->count_all_results('member_grade');
		
		return $total;
	}
	
	/**
	 * read_login_log_total
	 * 
	 * ci_login_log
	 * 
	 * @param	numberic	$id			ci_member_id
	 * @param	array		$keyword
	 */
	public function read_login_log_total ($id,$keyword = array()) {
		$total = 0;
		$keywords = array();
		
		if (is_array($keyword)) {
			$keywords = $keyword;
		} else {
			$keywords[] = $keyword;
		}
		
		foreach ($keywords as $ip) {
			if ($ip) {
				$this->db->where('ip',$ip);
			}
		}
		
		$this->db->where('member_id',$id);
		$total = $this->db->count_all_results('login_log');
		
		return $total;
	}
	
	/**
	 * read_login_log_list
	 * 
	 * ci_login_log
	 * 
	 * @param	array	$data
	 */
	public function read_login_log_list ($data = array()) {
		$list = $result = $keywords = array();
		
		if (!isset($data['member_id'])) {
			$data['member_id'] = $this->data['id'];
		}
		
		if (!isset($data['limit'])) {
			$data['limit'] = 20;
		}
		
		if (!isset($data['offset'])) {
			$data['offset'] = 0;
		}
		
		if (!isset($data['total'])) {
			$data['total'] = $this->read_login_log_total($data['member_id']);
		}
		
		if (is_array($data['keyword'])) {
			$keywords = $data['keyword'];
		} else {
			$keywords[] = $data['keyword'];
		}
		
		// get list
		$this->db->select('*');
		$this->db->from('login_log');
		$this->db->where('member_id',$data['member_id']);
		
		foreach ($keywords as $ip) {
			if ($ip) {
				$this->db->where('ip',$ip);
			}
		}
		
		$this->db->order_by('write_datetime','DESC');
		
		if (isset($data['limit'])) {
			$this->db->limit($data['limit']);
		}
		
		if (isset($data['offset'])) {
			$this->db->offset($data['offset']);
		}
		
		$result = $this->db->get()->result_array();
		
		foreach ($result as $key => $row) {
			$row['number'] = $data['total'] - $key;
			
			$list[] = $row;
		}
		
		return $list;
	}
	
	/**
	 * check_admin
	 * 
	 * 운영자인지 체크
	 * 
	 * @param	numberic	$id			ci_member.id
	 * @param	numberic	$site_id	ci_site.id
	 */
	public function check_admin ($id = 0,$site_id = 0) {
		$result = FALSE;
		
		if (isset($this->data['id']) && empty($id)) {
			// $id가 설정되어있지 않은데, 로그인이 되어있다면 로그인 유저의 권한을 체크..
			$id = $this->data['id'];
		}
		
		// check site id
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		if ($id) {
			// get admin ci_site_member_grade.id
			$this->db->select('*');
			$this->db->from('site_member_grade');
			$this->db->where('site_id',$site_id);
			$this->db->order_by('id','ASC');
			$this->db->limit(1);
			$grade_data = $this->db->get()->row_array();
			
			// get member data
			$this->db->select('*');
			$this->db->from('member_grade');
			$this->db->where('member_id',$id);
			$this->db->where('site_id',$site_id);
			$this->db->limit(1);
			$member_data = $this->db->get()->row_array();
			
			if ($grade_data['id'] == $member_data['site_member_grade_id']) {
				$result = TRUE;
			}
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
		
		$member_data = $member_information_data = $member_grade_data = array();
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
				$member_information_data['language'] = $this->config->item('language');
				
				$member_grade_data['site_id'] = 1;
				$member_grade_data['member_id'] = $result['insert_id'];
				$member_grade_data['site_member_grade_id'] = 1;
			} else {
				// 일반 회원의 가입..
			}
			
			// insert ci_member_information
			$this->db->insert('member_information',$member_information_data);
			
			// insert ci_member_grade
			$this->db->insert('member_grade',$member_grade_data);
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * write_information
	 * 
	 * 회원정보 추가
	 * 
	 * @param	array		$data
	 * @param	numberic	$id			ci_member.id
	 * @param	numberic	$site_id	ci_site.id
	 * @param	string		$language
	 */
	function write_information ($data,$id,$site_id = 0,$language = '') {
		$result = array();
		
		if (!isset($data['member_id'])) {
			$data['member_id'] = $id;
		}
		
		if (!isset($data['site_id'])) {
			$data['site_id'] = (empty($site_id))?$this->model->site['site_id']:$site_id;
		}
		
		if (!isset($data['language'])) {
			$data['language'] = (empty($language))?$this->model->site['site_id']:$language;
		}
		
		if ($this->db->insert('member_information',$data)) {
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
	 * write_site_grade
	 * 
	 * 사이트 등급 추가
	 * 
	 * @param	array		$data
	 */
	public function write_site_grade ($data) {
		$result = array();
		
		if (!isset($data['site_id'])) {
			$data['site_id'] = $this->model->site['site_id'];
		}
		
		if (!isset($data['site_member_grade_id'])) {
			$data['site_member_grade_id'] = 0;
		}
		
		if (!isset($data['language'])) {
			$data['language'] = $this->config->item('language');
		}
		
		if (!isset($data['default'])) {
			$data['default'] = 'f';
		}
		
		if ($this->db->insert('site_member_grade',$data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_write_success');
			$result['insert_id'] = $this->db->insert_id();
			
			if (empty($data['site_member_grade_id'])) {
				$this->db->set('site_member_grade_id',$result['insert_id']);
				$this->db->where('id',$result['insert_id']);
				$this->db->update('site_member_grade');
			}
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * update_grade
	 * 
	 * 등급 업데이트
	 * 
	 * @param	array		$data
	 * @param	numberic	$id			ci_member.id
	 * @param	numberic	$site_id	ci_site.id
	 */
	public function update_grade ($data,$id,$site_id = 0) {
		$result = $grade_data = $insert_data = array();
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		// get grade DB
		$this->db->select('*');
		$this->db->from('member_grade');
		$this->db->where('member_id',$id);
		$this->db->where('site_id',$site_id);
		$result = $this->db->get()->result_array();
		
		foreach ($result as $row) {
			if (!in_array($row['site_member_grade_id'],$data)) {
				// delete
				$this->db->where('id',$row['id']);
				$this->db->delete('member_grade');
			}
			
			$grade_data[] = $row['site_member_grade_id'];
		}
		
		foreach ($data as $value) {
			if (!in_array($value,$grade_data)) {
				// insert
				$insert_data[] = array(
					'member_id'=>$id,
					'site_id'=>$site_id,
					'site_member_grade_id'=>$value
				);
			}
		}
		
		if (count($insert_data)) {
			$this->db->insert_batch('member_grade',$insert_data);
		}
		
		$result = array();
		$result['status'] = TRUE;
		
		return $result;
	}
	
	/**
	 * update_information
	 * 
	 * ci_member_information 수정
	 * 
	 * @param	array		$data
	 * @param	numberic	$id			ci_member.id
	 * @param	numberic	$site_id	ci_site.id
	 * @param	string		$language
	 */
	public function update_information ($data,$id,$site_id = 0,$language = '') {
		$result = array();
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		$this->db->where('member_id',$id);
		$this->db->where('site_id',$site_id);
		$this->db->where('language',$language);
		if ($this->db->update('member_information',$data)) {
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
	 * update_data
	 * 
	 * ci_member 업데이트
	 * 
	 * @param	array		$data
	 * @param	numberic	$id					ci_member.id
	 * @param	string		$now_password		ci_member.password
	 */
	public function update_data ($data,$id,$now_password = '') {
		$result = $member_data = array();
		
		// encode password
		if (isset($data['password'])) {
			if (empty($data['password'])) {
				unset($data['password']);
			} else {
				$data['password'] = $this->_encode($data['password']);
			}
		}
		
		if (!isset($data['update_datetime'])) {
			$data['update_datetime'] = date('Y-m-d H:i:s');
		}
		
		if (empty($now_password) && $this->check_admin()) {
			// admin
			$this->db->where('id',$id);
			if ($this->db->update('member',$data)) {
				$result['status'] = TRUE;
				$result['message'] = lang('system_update_success');
			} else {
				$result['status'] = FALSE;
				$result['message'] = $this->db->_error_message();
				$result['number'] = $this->db->_error_number();
			}
		} else {
			// member
			$member_data = $this->read_data('id',$id);
			
			if ($now_password == $this->_decode($member_data['password'])) {
				$this->db->where('id',$id);
				if ($this->db->update('member',$data)) {
					$result['status'] = TRUE;
					$result['message'] = lang('system_update_success');
				} else {
					$result['status'] = FALSE;
					$result['message'] = $this->db->_error_message();
					$result['number'] = $this->db->_error_number();
				}
			} else {
				$result['status'] = FALSE;
				$result['message'] = lang('member_inconsistency_password');
			}
		}
		
		return $result;
	}
	
	/**
	 * update_site_grade
	 * 
	 * ci_site_member_grade 업데이트
	 * 
	 * @param	array		$data
	 * @param	numberic	$site_member_grade_id		ci_site_member_grade.site_member_grade_id
	 */
	public function update_site_grade ($data,$site_member_grade_id) {
		$result = array();
		
		if (!isset($data['site_id'])) {
			$data['site_id'] = $this->model->site['site_id'];
		}
		
		if (!isset($data['language'])) {
			$data['language'] = $this->config->item('language');
		}
		
		if (isset($data['default']) && $data['default'] == 't') {
			$this->db->set('default','f');
			$this->db->where('site_id',$data['site_id']);
			$this->db->update('site_member_grade');
			
			$this->db->set('default','t');
			$this->db->where('site_member_grade_id',$data['site_member_grade_id']);
			$this->db->update('site_member_grade');
		}
		
		$this->db->where('site_member_grade_id',$site_member_grade_id);
		$this->db->where('language',$data['language']);
		if ($this->db->update('site_member_grade',$data)) {
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
	 * delete_site_grade
	 * 
	 * ci_site_member_grade 삭제
	 * 
	 * @param	numberic	$id		ci_site_member_grade.site_member_grade_id
	 */
	public function delete_site_grade ($site_grade_id) {
		$total = 0;
		$result = $data = array();
		
		$total = $this->member->read_grade_total($site_grade_id);
		$data = $this->member->read_site_grade_id($site_grade_id);
		
		if ($data['default'] == 't') {
			$result['status'] = FALSE;
			$result['message'] = lang('member_grade_delete_danger_default');
		} else if (empty($total)) {
			$result['status'] = FALSE;
			$result['message'] = lang('member_grade_delete_danger_empty');
		} else {
			$this->db->where('site_member_grade_id',$data['site_member_grade_id']);
			if ($this->db->delete('site_member_grade')) {
				$result['status'] = TRUE;
				$result['message'] = lang('member_grade_delete_success');
			} else {
				$result['status'] = FALSE;
				$result['message'] = $this->db->_error_message();
				$result['number'] = $this->db->_error_number();
			}
		}
		
		return $result;
	}
	
	/**
	 * install
	 * 
	 * member DB install
	 * 
	 * @param	string		$flag		true / false
	 * @return	string		$return		true / false
	 */
	public function install ($flag = TRUE) {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		// member table
		if (!$this->db->table_exists('member')) {
			if ($flag) {
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
					),
					'description'=>array(
						'type'=>'TEXT',
						'null'=>TRUE
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('member');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// member grade table
		if (!$this->db->table_exists('member_grade')) {
			if ($flag) {
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
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$this->dbforge->create_table('member_grade');
			} else if (!$return) {
				$return = FALSE;
			}
		}
		
		// member information table
		if (!$this->db->table_exists('member_information')) {
			if ($flag) {
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
			} else if (!$return) {
				$return = FALSE;
			}
		}
		
		// login log table
		if (!$this->db->table_exists('login_log')) {
			if ($flag) {
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
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		return $return;
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
			$this->dbforge->drop_table('member_grade');
			$this->dbforge->drop_table('member_information');
			$this->dbforge->drop_table('login_log');
		}
		
		return $return;
	}
}