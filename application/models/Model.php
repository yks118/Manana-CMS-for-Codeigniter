<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model extends CI_Model {
	public $html = array();
	public $css = array();
	public $js = array();
	public $path = '';
	public $site = array();
	
	public function __construct() {
		parent::__construct();
		
		if ($this->db->table_exists('site')) {
			$this->site = $this->read_site_url(base_url('/'));
		}
		
		// assets path
		$this->path = base_url('/assets/');
		
		// 기본 설정
		$this->_default();
	}
	
	/**
	 * _default
	 * 
	 * 기본설정
	 */
	private function _default () {
		// site setting
		$this->html['site_title'] = 'Manana CMS';
		
		// css setting
		$this->css('//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css');
		$this->css('//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
		$this->css($this->path.'/css/style.less');
		
		// js setting
		$this->js('//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js','header');
		$this->js('//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js','footer');
		$this->js($this->path.'/js/bootstrap-notify/bootstrap-notify.js','footer');
		$this->js($this->path.'/js/common.js','footer');
	}
	
	/**
	 * css
	 * 
	 * css 파일 설정
	 * 
	 * @param	string		$path		css path
	 */
	public function css ($path) {
		$pathinfo = pathinfo($path);
		
		switch ($pathinfo['extension']) {
			case 'less' :
					// $this->css[] = array('type'=>'stylesheet/less','path'=>$path);
					
					$less = str_replace('/assets/views/','/application/views/',$path);
					$less = str_replace(base_url('/'),'./',$less);
					$this->lessc->checkedCompile($less,str_replace('.less','.css',$less));
					
					$this->css[] = array('type'=>'stylesheet','path'=>str_replace('.less','.css',$path));
				break;
			default :
					$this->css[] = array('type'=>'stylesheet','path'=>$path);
				break;
		}
	}
	
	/**
	 * js
	 * 
	 * javascript 파일 설정
	 * 
	 * @param	string		$path		javascript path
	 * @param	string		$position	header / footer
	 */
	public function js ($path,$position = 'footer') {
		$this->js[$position][] = $path;
	}
	
	/**
	 * read_site_url
	 * 
	 * site 테이블을 url로 가져옴
	 * 
	 * @param	string		$url	site url
	 */
	public function read_site_url ($url) {
		$url = preg_replace('/(https?:\/\/)([0-9.]+)(\/?)/i','$2',$url);
		
		// get DB
		$this->db->select('*');
		$this->db->from('site');
		$this->db->like('url',$url,'both');
		$this->db->limit(1);
		return $this->db->get()->row();
	}
	
	/**
	 * post_data
	 * 
	 * post로 넘어오는 data를 리패키징..
	 * 
	 * @param	string		$prefix		post prefix
	 * @param	string		$id			PK
	 */
	public function post_data ($prefix,$id = '') {
		$data = array();
		
		foreach ($this->input->post() as $key => $value) {
			if (strpos($key,$prefix) == 0 && ($id && $key != $id)) {
				$data[$key] = $value;
			}
		}
		
		return $data;
	}
	
	/**
	 * write_data
	 * 
	 * site 생성
	 * 
	 * @param	array	$data
	 */
	public function write_data ($data) {
		$result = array();
		$result['status'] = FALSE;
		
		if (isset($data['site_language']) === FALSE) {
			$data['site_language'] = $this->config->item('language');
		}
		
		if (isset($data['site_member_id']) === FALSE) {
			if ($this->member->read_total() == 1) {
				$data['site_member_id'] = 1;
			} else if (isset($this->member->data['id'])) {
				$data['site_member_id'] = $this->member->data['id'];
			} else {
				$data['message'] = lang('member_login_required');
				return $result;
			}
		}
		
		$site_data = $site_member_grade_data = array();
		foreach ($data as $key => $value) {
			if (strpos($key,'site_member_grade_') !== FALSE && strpos($key,'site_member_grade_') == 0) {
				$site_member_grade_data[str_replace('site_member_grade_','',$key)] = $value;
			} else if (strpos($key,'site_') !== FALSE && strpos($key,'site_') == 0) {
				$site_data[str_replace('site_','',$key)] = $value;
			}
		}
		
		// insert ci_site
		if ($this->db->insert('site',$site_data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('site_write_success');
			$result['insert_id'] = $this->db->insert_id();
			
			// site member grade admin
			$site_member_grade_data[0]['site_id'] = $result['insert_id'];
			$site_member_grade_data[0]['name'] = lang('member_grade_admin');
			$site_member_grade_data[0]['language'] = $this->config->item('language');
			$site_member_grade_data[0]['default'] = 'f';
			
			// site member grade normal
			$site_member_grade_data[1]['site_id'] = $result['insert_id'];
			$site_member_grade_data[1]['name'] = lang('member_grade_normal');
			$site_member_grade_data[1]['language'] = $this->config->item('language');
			$site_member_grade_data[1]['default'] = 't';
			
			// insert ci_site_member_grade
			$this->db->insert_batch('site_member_grade',$site_member_grade_data);
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
	 * site DB 설치
	 * 
	 * @return	string	true / false
	 */
	public function install () {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		// site table
		$fields = array(
			'id'=>array(
				'type'=>'INT',
				'constraint'=>11,
				'unsigned'=>TRUE,
				'auto_increment'=>TRUE
			),
			'url'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'name'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'description'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'keywords'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'author'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'mobile_view'=>array(
				'type'=>'VARCHAR',
				'constraint'=>1
			),
			'robots'=>array(
				'type'=>'VARCHAR',
				'constraint'=>1
			),
			'login'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'language'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'member_id'=>array(
				'type'=>'INT',
				'constraint'=>11
			)
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id',TRUE);
		$return = $this->dbforge->create_table('site');
		
		// site_member_grade table
		$fields = array(
			'id'=>array(
				'type'=>'INT',
				'constraint'=>11,
				'unsigned'=>TRUE,
				'auto_increment'=>TRUE
			),
			'site_id'=>array(
				'type'=>'INT',
				'constraint'=>11,
				'unsigned'=>TRUE
			),
			'site_member_grade_id'=>array(
				'type'=>'INT',
				'constraint'=>11,
				'unsigned'=>TRUE,
				'default'=>0
			),
			'name'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'language'=>array(
				'type'=>'VARCHAR',
				'constraint'=>255
			),
			'default'=>array(
				'type'=>'VARCHAR',
				'constraint'=>1
			)
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id',TRUE);
		$return = $this->dbforge->create_table('site_member_grade');
		
		return $return;
	}
	
	/**
	 * change
	 * 
	 * site DB 변경사항
	 */
	public function change () {
		
	}
	
	/**
	 * uninstall
	 * 
	 * site DB 삭제
	 * 
	 * @return	string	true / false
	 */
	public function uninstall () {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		$return = $this->dbforge->drop_table('site');
		if ($return) {
			$this->dbforge->drop_table('site_member_grade');
		}
		
		return $return;
	}
}