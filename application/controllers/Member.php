<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * login
	 */
	public function login () {
		if (isset($this->member->data['id'])) {
			set_cookie('noti',lang('system_connect_danger'),0);
			set_cookie('noti_type','danger',0);
			redirect('/');
		}
		
		$this->load->view('member/'.$this->member->skin.'/login');
	}
	
	/**
	 * writeLogin
	 */
	public function writeLogin () {
		$data = array();
		$data = $this->model->post_data('member_');
		
		$result = $this->member->login($data['member_username'],$data['member_password']);
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			echo js('parent.document.location.href = "'.base_url('/').'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
}