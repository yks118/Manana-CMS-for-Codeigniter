<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	public function __construct() {
		parent::__construct();
		
		if (isset($this->member->data['id']) === FALSE) {
			// login 유저가 아니라면 로그인 화면으로..
			set_cookie('noti',lang('member_login_required'),0);
			set_cookie('noti_type','danger',0);
			redirect('/member/login/');
		} else if (isset($this->member->data['id']) && $this->member->check_admin()) {
			// admin 권한이 없다면, 팅겨냄..
			set_cookie('noti',lang('system_auth_danger'),0);
			set_cookie('noti_type','danger',0);
			redirect('/');
		}
	}
	
	public function index () {
		
	}
}