<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	public function __construct () {
		parent::__construct();
		
		if (!isset($this->member->data['id'])) {
			// login 유저가 아니라면 로그인 화면으로..
			set_cookie('noti',lang('member_login_required'),0);
			set_cookie('noti_type','danger',0);
			redirect('/member/login/');
		} else if (!$this->member->is_admin) {
			// admin 권한이 없다면, 팅겨냄..
			set_cookie('noti',lang('system_auth_danger'),0);
			set_cookie('noti_type','danger',0);
			redirect('/');
		} else {
			// set admin model
			$this->load->model(array('admin_model'=>'admin'));
		}
	}
	
	/**
	 * index
	 * 
	 * /admin/ 으로 접속하면 DashBoard로 보낸다.
	 */
	public function index () {
		redirect('/admin/dashboard/');
	}
	
	/**
	 * dashboard
	 * 
	 * admin 페이지 기본 화면..
	 */
	public function dashboard () {
		$data = $analytics_data = array();
		
		// set JS
		$this->model->js($this->model->path.'/plugin/flot/jquery.flot.min.js','footer');
		$this->model->js($this->model->path.'/plugin/flot/jquery.flot.resize.js','footer');
		$this->model->js($this->model->path.'/plugin/flot/jquery.flot.categories.min.js','footer');
		$this->model->js($this->model->path.'/plugin/flot.tooltip/jquery.flot.tooltip.min.js','footer');
		$this->model->js($this->model->path.'/js/dashboard.admin.js','footer');
		
		$data = $this->model->analytics_visitor(date('Y-m-d',strtotime('-'.number_format(7 + date('w')).'day')),date('Y-m-d',strtotime('+'.number_format(6 - date('w')).'day')));
		
		foreach ($this as $key => $row) {
			if (method_exists($this->$key,'install')) {
				if ($this->$key->install(FALSE)) {
					$data['model_check'][] = $key;
				}
			}
		}
		
		$data['week'] = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
		
		$this->load->view('admin/dashboard',$data);
	}
	
	/**
	 * site
	 * 
	 * site 관리 화면
	 */
	public function site () {
		$data = array();
		
		$data['data'] = $this->model->read_site_url(base_url('/'));
		$data['editor_list'] = $this->editor->read_list();
		$data['language_list'] = read_folder_list('./application/language/');
		
		$this->load->view('admin/site',$data);
	}
	
	/**
	 * updateSiteForm
	 * 
	 * site 관리 업데이트
	 */
	public function updateSiteForm () {
		$id = 0;
		$blank = $result = $data = $languages = $site_data = array();
		
		$id = $this->input->post('site_id');
		$data = delete_prefix($this->model->post_data('site_','site_id'),'site_');
		$languages = delete_prefix($this->input->post('use_site_language'),'use_');
		
		$site_data = $this->model->read_site_id($id);
		
		$this->model->site_language($languages);
		$result = (isset($site_data['id']) && ($site_data['language'] == $this->config->item('language')))?$this->model->update_data($data,$id):$this->model->write_data($data);
		
		if ($result['status']) {
			// success
			set_cookie('noti',lang('system_update_success'),0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/admin/site/').'";';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * menu
	 * 
	 * 메뉴 관리
	 */
	public function menu () {
		$data = array();
		
		// set nestable plugin
		$this->model->css($this->model->path.'/plugin/nestable/nestable.less');
		$this->model->js($this->model->path.'/plugin/nestable/jquery.nestable.js','footer');
		$this->model->js($this->model->path.'/js/menu.admin.js','footer');
		
		$data['list'] = $this->model->read_menu_list();
		$data['model_list'] = array('board','page','outpage');
		$data['site_member_grade_list'] = $this->member->read_site_grade_list();
		$data['site_member_grade_list'][] = array(
			'id'=>0,
			'site_id'=>0,
			'site_member_grade_id'=>0,
			'name'=>lang('text_guest')
		);
		$data['layout_list'] = read_folder_list('./application/views/layout/');
		
		$this->load->view('admin/menu',$data);
	}
	
	/**
	 * updateMenuForm
	 * 
	 * 메뉴 업데이트
	 */
	public function updateMenuForm () {
		$blank = $result = $data = $menu_data = $grade_data = array();
		
		$data = delete_prefix($this->model->post_data('menu_','menu_id'),'menu_');
		$grade_data = $this->input->post('grade');
		$menu_data = $this->model->read_menu_id($data['site_menu_id']);
		
		$result = (isset($menu_data['language']) && $menu_data['language'] == $data['language'])?$this->model->update_menu($data,$data['site_menu_id']):$this->model->write_menu($data);
		
		if ($result['status']) {
			$this->model->menu_auth($grade_data,$result['insert_id']);
			
			// success
			set_cookie('noti',lang('system_update_success'),0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/admin/menu/').'";';
		} else {
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * readMenuModelIdAjax
	 * 
	 * 메뉴 모델 리스트 리턴
	 */
	public function readMenuModelIdAjax () {
		$limit = 999;
		$model = '';
		$blank = $response = array();
		
		$model = $this->input->post('model');
		
		switch ($model) {
			case 'board' :
					$response['data'] = $this->board->read_list_config(0,$limit);
				break;
			case 'page' :
					$response['data'] = $this->page->read_list (0,$limit);
				break;
		}
		
		if (isset($response['data'])) {
			$response['status'] = TRUE;
			
			if (!count($response['data'])) {
				$response['data'][] = lang('system_not_data');
			}
		} else {
			$response['status'] = FALSE;
			$response['message'] = lang('system_not_data');
		}
		
		$blank['data']['json'] = $response;
		$this->load->view('blank',$blank);
	}
	
	/**
	 * updateMenuIndexAjax
	 * 
	 * 메뉴 순서 변경
	 */
	public function updateMenuIndexAjax () {
		$language = '';
		$blank = $response = $node = array();
		
		$language = $this->input->post('language');
		$node = $this->input->post('node');
		
		foreach ($node[0]['children'] as $lnb_key => $lnb) {
			$response = $this->model->update_menu(array('parent_id'=>0,'index'=>$lnb_key+1),$lnb['id'],$this->model->site['site_id'],$language);
			
			if (isset($lnb['children'])) {
				foreach ($lnb['children'] as $snb_key => $snb) {
					$response = $this->model->update_menu(array('parent_id'=>$lnb['id'],'index'=>$snb_key+1),$snb['id'],$this->model->site['site_id'],$language);
				}
			}
		}
		
		$blank['data']['json'] = $response;
		$this->load->view('blank',$blank);
	}
	
	/**
	 * readMenuId
	 * 
	 * return ci_site_menu row
	 */
	public function readMenuId () {
		$id = 0;
		$blank = $response = array();
		
		$id = $this->input->post('id');
		
		if (empty($id)) {
			$response['status'] = FALSE;
			$response['message'] = lang('system_connect_danger');
		} else {
			$response['data'] = $this->model->read_menu_id($id);
			
			if (isset($response['data']['id'])) {
				$response['status'] = TRUE;
			} else {
				$response['status'] = FALSE;
				$response['message'] = lang('system_not_data');
			}
		}
		
		$blank['data']['json'] = $response;
		$this->load->view('blank',$blank);
	}
	
	/**
	 * updateMenuHomeAjax
	 * 
	 * set menu main
	 */
	public function updateMenuHomeAjax () {
		$id = 0;
		$blank = $response = array();
		
		$id = $this->input->post('id');
		
		$this->db->set('is_main','f');
		$this->db->where('site_id',$this->model->site['site_id']);
		$this->db->update('site_menu');
		
		// update
		$response = $this->model->update_menu(array('is_main'=>'t'),$id);
		
		$blank['data']['json'] = $response;
		$this->load->view('blank',$blank);
	}
	
	/**
	 * analytics
	 */
	public function analytics () {
		$view_id = 0;
		$data = $result = $analytics = $category = array();
		
		$view_id = ($this->input->get('view_id'))?$this->input->get('view_id'):0;
		$data['analytics_data'] = $this->model->read_analytics();
		
		if (isset($data['analytics_data']['view_id']) && !empty($view_id) && $data['analytics_data']['view_id'] != $view_id) {
			$result = $this->model->update_analytics(array('view_id'=>$view_id),$data['analytics_data']['id']);
		} else if (!isset($data['analytics_data']['view_id']) && !empty($view_id)) {
			$result = $this->model->write_analytics(array('view_id'=>$view_id));
		}
		
		if (isset($result['status'])) {
			$data['analytics_data'] = $this->model->read_analytics();
		}
		
		$data['report'] = ($this->input->get('report'))?$this->input->get('report'):'browser';
		$data['start_data'] = ($this->input->get('startData'))?$this->input->get('startData'):date('Y-m-d',strtotime('-1 week'));
		$data['end_data'] = ($this->input->get('endData'))?$this->input->get('endData'):date('Y-m-d');
		
		// load google api libraries
		$this->load->library('google_api');
		
		// set JS
		$this->model->js($this->model->path.'/plugin/flot/jquery.flot.min.js','footer');
		$this->model->js($this->model->path.'/plugin/flot/jquery.flot.resize.js','footer');
		$this->model->js($this->model->path.'/plugin/flot/jquery.flot.pie.min.js','footer');
		$this->model->js($this->model->path.'/plugin/flot.tooltip/jquery.flot.tooltip.min.js','footer');
		$this->model->js($this->model->path.'/js/analytics.admin.js','footer');
		
		switch ($data['report']) {
			case 'browser' :
					$category[] = 'browser';
					$category[] = 'browserVersion';
				break;
			case 'mobileDeviceInfo' :
					$category[] = 'mobileDeviceInfo';
					$category[] = 'mobileDeviceBranding';
				break;
			default :
					$category[] = $data['report'];
				break;
		}
		
		if (isset($data['analytics_data']['id'])) {
			if ($data['report'] == 'visitor') {
				$data['data'] = $this->model->analytics_visitor($data['start_data'],$data['end_data']);
			} else {
				$data['data'] = $this->google_api->analytics($data['analytics_data']['view_id'],$category,$data['start_data'],$data['end_data']);
			}
		} else {
			$data['data'] = array();
		}
		
		$data['reports'] = array(
			'browser'=>'analytics_browser',
			'country'=>'analytics_country',
			'deviceCategory'=>'analytics_device_category',
			'page'=>'analytics_favourite_page',
			'keyword'=>'text_keyword',
			'referral'=>'analytics_referral',
			'mobileDeviceInfo'=>'analytics_mobile_device_model',
			'browserSize'=>'analytics_browser_size',
			'visitor'=>'analytics_visitors'
		);
		
		$this->load->view('admin/analytics',$data);
	}
	
	/**
	 * install
	 * 
	 * 모듈 갱신
	 * 
	 * @param	string		$model		model name
	 */
	public function install ($model) {
		$blank = array();
		
		if ($model != 'model') {
			// model load
			$this->load->model(array($model.'_model'=>$model));
		}
		
		// model install
		$result = $this->$model->install(TRUE);
		
		if ($result) {
			// success
			set_cookie('noti',lang('admin_module_install_success'),0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/admin/dashboard/').'";';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.lang('admin_module_install_error').'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
}