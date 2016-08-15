<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller {
	public function __construct() {
		parent::__construct();
		
		// load member language
		$this->load->language('member');
		
		// load common member js
		$this->model->js($this->model->path.'/views/admin/member/js/member.js');
		
		if (empty($this->model->layout)) {
			$this->model->layout = $this->model->site['default_layout'];
		}
		
		if ($this->uri->segment(1) == 'member') {
			$this->model->html['site_title'] = lang('member').' :: '.$this->model->html['site_title'];
			
			$this->model->menu['member']['class'] = array('active','hide');
			$this->model->menu['member']['name'] = lang('member');
			$this->model->menu['member']['href'] = base_url('/member/');
			$this->model->menu['member']['target'] = '_self';
			
			$this->model->menu['member']['children']['information']['class'] = ($this->uri->segment(2) == 'information' || $this->uri->segment(2) == 'update')?array('active'):array();
			$this->model->menu['member']['children']['information']['name'] = lang('member_information');
			$this->model->menu['member']['children']['information']['href'] = base_url('/member/information/'.$this->member->data['username'].'/');
			$this->model->menu['member']['children']['information']['target'] = '_self';
			
			$this->model->menu['member']['children']['loginLog']['class'] = ($this->uri->segment(2) == 'loginLog')?array('active'):array();
			$this->model->menu['member']['children']['loginLog']['name'] = lang('member_login_log');
			$this->model->menu['member']['children']['loginLog']['href'] = base_url('/member/loginLog/');
			$this->model->menu['member']['children']['loginLog']['target'] = '_self';
		}
	}
	
	/**
	 * information
	 * 
	 * member information
	 * 
	 * @param	string		$username		ci_member.username
	 */
	public function information ($username) {
		$data = array();
		
		$data['username'] = $username;
		$data['data'] = $this->member->read_data('username',$username);
		
		$this->model->html['site_title'] = sprintf(lang('member_information_data'),$data['data']['name']).' - '.$this->model->html['site_title'];
		$this->load->view('member/'.$this->member->skin.'/information',$data);
	}
	
	/**
	 * loginLog
	 * 
	 * login log
	 */
	public function loginLog () {
		$data = array();
		
		$data['limit'] = 20;
		$data['member_id'] = $this->member->data['id'];
		$data['keyword'] = $this->input->get('keyword');
		$data['total'] = $this->member->read_login_log_total($data['member_id'],$data['keyword']);
		$data['list'] = $this->member->read_login_log_list($data);
		$data['pagination'] = $this->model->pagination($data['total'],$data['limit']);
		
		$this->model->html['site_title'] = lang('member_login_log').' - '.$this->model->html['site_title'];
		$this->load->view('member/'.$this->member->skin.'/loginLog',$data);
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
		$blank = $data = $this->model->post_data('member_');
		
		$result = $this->member->login($data['member_username'],$data['member_password']);
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = parent.document.location.href;';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * logout
	 */
	public function logout () {
		$blank = $result = array();
		
		$result = $this->member->logout();
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = parent.document.location.href;';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * update
	 * 
	 * 회원정보 수정
	 */
	public function update () {
		$data = array();
		
		$data['action'] = 'update';
		$data['data'] = $this->member->read_data('id',$this->member->data['id']);
		
		$this->load->view('member/'.$this->member->skin.'/write',$data);
	}
	
	/**
	 * updateForm
	 * 
	 * 회원정보 수정
	 */
	public function updateForm () {
		$id = $delete_profile_photo = 0;
		$now_password = '';
		$data = $result = $blank = $information_data = array();
		
		$id = $this->input->post('member_id');
		$data = delete_prefix($this->model->post_data('member_','member_id'),'member_');
		$information_data = delete_prefix($this->model->post_data('information_','information_id'),'information_');
		$now_password = $this->input->post('now_password');
		$delete_profile_photo = ($this->input->post('delete_profile_photo'))?$this->input->post('delete_profile_photo'):0;
		$result = $this->member->update_data($data,$id,$now_password);
		
		if ($result['status']) {
			$this->member->update_information($information_data,$id);
			
			if (!empty($delete_profile_photo)) {
				$this->file->delete($delete_profile_photo);
			}
			$this->file->upload('member_profile_photo',$id);
			
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/member/information/'.$data['username'].'/').'";';
		} else {
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * admin_index
	 */
	public function admin_index () {
		$limit = 20;
		$data = array();
		
		$data['total'] = $this->member->read_total();
		$data['list'] = $this->member->read_list();
		$data['pagination'] = $this->model->pagination($data['total'],$limit);
		
		$this->load->view('admin/member/list',$data);
	}
	
	/**
	 * admin_update
	 * 
	 * @param	numberic	$id		ci_member.id
	 */
	public function admin_update ($id) {
		$data = array();
		
		$data['action'] = 'update';
		$data['data'] = $this->member->read_data('id',$id);
		$data['site_member_grade_list'] = $this->member->read_site_grade_list();
		
		$this->load->view('admin/member/write',$data);
	}
	
	/**
	 * admin_updateForm
	 */
	public function admin_updateForm () {
		$id = 0;
		$language = $this->config->item('language');
		$blank = $result = $member_post_data = $member_data = $member_information_data = $grade_data = $data = array();
		
		$id = $this->input->post('member_id');
		$member_post_data = $this->model->post_data('member_','member_id');
		$grade_data = $this->input->post('grade');
		
		foreach ($member_post_data as $key => $value) {
			if (strpos($key,'member_information_') !== FALSE && strpos($key,'member_information_') == 0) {
				$member_information_data[str_replace('member_information_','',$key)] = $value;
			} else if (strpos($key,'member_') !== FALSE && strpos($key,'member_') == 0) {
				$member_data[str_replace('member_','',$key)] = $value;
			}
		}
		
		$result = $this->member->update_grade($grade_data,$id);
		
		if ($result['status']) {
			$data = $this->member->read_data('id',$id,$this->model->site['site_id'],$language);
			
			$result = ($data['language'] == $language)?$this->member->update_information($member_information_data,$id,$language):$this->member->write_information($member_information_data,$id,$this->model->site['site_id'],$language);
			$result = $this->member->update_data($member_data,$id);
		}
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/admin/member/update/'.$id.'/').'";';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * admin_grade
	 */
	public function admin_grade () {
		$data = array();
		
		$data['list'] = $this->member->read_site_grade_list();
		
		$this->load->view('admin/member/grade',$data);
	}
	
	/**
	 * admin_writeGradeForm
	 */
	public function admin_writeGradeForm () {
		$blank = $result = $data = array();
		
		$data['name'] = $this->input->post('grade_name');
		
		$result = $this->member->write_site_grade($data);
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/admin/member/grade/').'";';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * admin_updateGradeForm
	 */
	public function admin_updateGradeForm () {
		$id = 0;
		$blank = $result = $data = $site_member_grade_data = array();
		
		$id = $this->input->post('grade_site_member_grade_id');
		$data = delete_prefix($this->model->post_data('grade_','grade_id'),'grade_');
		
		$site_member_grade_data = $this->member->read_site_grade_id($id,$this->model->site['site_id'],$data['language']);
		$result = ($site_member_grade_data['language'] == $data['language'])?$this->member->update_site_grade($data,$id):$this->member->write_site_grade($data);
		
		if ($result['status']) {
			// success
			set_cookie('noti',lang('system_update_success'),0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/admin/member/grade/').'";';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * admin_deleteGradeForm
	 */
	public function admin_deleteGradeForm () {
		$id = 0;
		$blank = $result = array();
		
		$id = $this->input->post('grade_id');
		$result = $this->member->delete_site_grade($id);
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/admin/member/grade/').'";';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
}