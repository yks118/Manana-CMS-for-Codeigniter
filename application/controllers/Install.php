<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Install extends CI_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	public function index () {
		if (!$this->db->username || !$this->db->password || !$this->db->database) {
			// database 설정
			$this->load->view('install/database');
		} else if (!$this->db->table_exists('member')) {
			// member 설정
			$this->load->view('install/member');
		} else if (isset($this->model->site['id']) === FALSE) {
			$site_total = ($this->db->table_exists('site'))?$this->model->read_total():0;
			
			if (
				empty($site_total) ||
				(
					$site_total && isset($this->member->data['id'])
				)
			) {
				// site 설정
				$this->load->view('install/site');
			} else {
				// 첫 사이트 생성이 아니라면, 로그인이 필요..
				set_cookie('noti',lang('member_login_required'),0);
				set_cookie('noti_type','danger',0);
				redirect('/member/login/');
			}
		} else {
			// install complete
			$this->load->view('install/complete');
		}
	}
	
	/**
	 * writeMember
	 * 
	 * member table install
	 * 
	 * @param	array	$_POST
	 */
	public function writeMember () {
		if (!$this->db->table_exists('member')) {
			// member DB install
			$this->member->install();
		}
		
		$data = $this->model->post_data('member_','member_id');
		$result = $this->member->write_data($data);
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			echo js('parent.document.location.href = "'.base_url('/install/').'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
	
	/**
	 * writeSite
	 * 
	 * site table install
	 * 
	 * @param	array	$_POST
	 */
	public function writeSite () {
		if (!$this->db->table_exists('site')) {
			// site DB install
			$this->model->install();
		}
		
		$data = $this->model->post_data('site_','site_id');
		$result = $this->model->write_data($data);
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			echo js('parent.document.location.href = "'.base_url('/install/').'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
}