<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {
	public function __construct() {
		parent::__construct();
		
		// load model page
		$this->load->model(array('page_model'=>'page'));
		
		if (isset($this->model->now_menu['name'])) {
			$auth_check = FALSE;
			
			// menu auth
			if (isset($this->member->data['id'])) {
				if ($this->page->is_admin) {
					// 운영자나 페이지 관리자는 통과..
					$auth_check = TRUE;
				} else {
					// 일반 회원..
					foreach ($this->model->now_menu['grade'] as $grade_id) {
						if (isset($this->member->data['grade'][$grade_id])) {
							$auth_check = TRUE;
							break;
						}
					}
				}
			} else {
				if (in_array(0,$this->model->now_menu['grade'])) {
					$auth_check = TRUE;
				}
			}
			
			// page auth
			if (
				$auth_check == FALSE ||
				(
					(isset($this->page->auth[$this->router->method]) && !$this->page->auth[$this->router->method]) ||
					(isset($this->page->auth[$this->router->method.'Form']) && !$this->page->auth[$this->router->method.'Form']) ||
					(isset($this->page->auth[$this->router->method.'Ajax']) && !$this->page->auth[$this->router->method.'Ajax'])
				)
			) {
				set_cookie('noti',lang('system_auth_danger'),0);
				set_cookie('noti_type','danger',0);
				redirect('/');
			}
			
			$this->model->html['site_title'] = $this->model->now_menu['name'].' :: '.$this->model->html['site_title'];
		} else if ($this->uri->segment(1) != 'admin') {
			// 사이트맵에 존재하지 않는 메뉴라면..
			set_cookie('noti',lang('system_connect_danger'),0);
			set_cookie('noti_type','danger',0);
			redirect('/');
		}
	}
	
	/**
	 * index
	 * 
	 * page view
	 */
	public function index () {
		$data = array();
		
		$data['data'] = $this->page->read_id($this->model->now_menu['model_id']);
		
		$this->load->view('page/'.$this->page->skin.'/view',$data);
	}
	
	/**
	 * updateForm
	 * 
	 * page update
	 */
	public function updateForm () {
		$blank = $result = $data = $page_data = array();
		
		$data = delete_prefix($this->model->post_data('page_','page_id'),'page_');
		$page_data = $this->page->read_id($data['page_id'],$this->model->site['site_id'],$data['language']);
		$result = ($page_data['language'] == $data['language'])?$this->page->update_data($data,$data['page_id']):$this->page->write_data($data);
		
		if ($result['status']) {
			// success
			set_cookie('noti',lang('system_update_success'),0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = parent.document.location.href';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * admin_index
	 * 
	 * page list
	 */
	public function admin_index () {
		$data = array();
		
		$data['limit'] = 20;
		$data['page'] = $this->input->get('page');
		$data['field'] = $this->input->get('field');
		$data['keyword'] = $this->input->get('keyword');
		$data['site_id'] = $this->model->site['site_id'];
		$data['total'] = $this->page->read_total($data);
		$data['list'] = $this->page->read_list($data);
		$data['pagination'] = $this->model->pagination($data['total'],$data['limit']);
		
		$this->load->view('admin/page/list',$data);
	}
	
	/**
	 * admin_write
	 * 
	 * page write
	 */
	public function admin_write () {
		$data = array();
		
		$data['action'] = 'write';
		
		$this->load->view('admin/page/write',$data);
	}
	
	/**
	 * admin_writeForm
	 * 
	 * page DB write
	 */
	public function admin_writeForm () {
		$blank = $result = $data = $file_ids = $file_data = array();
		
		$data = delete_prefix($this->model->post_data('page_','page_id'),'page_');
		$file_ids = explode('|',$this->input->post('file_ids'));
		
		$result = $this->page->write_data($data);
		
		foreach ($file_ids as $value) {
			if ($value) {
				$file_data[] = array(
					'id'=>$value,
					'model'=>'page',
					'model_id'=>$result['insert_id']
				);
			}
		}
		
		if (count($file_data)) {
			$this->file->update_data_batch($file_data);
		}
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/admin/page/update/'.$result['insert_id'].'/').'";';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * admin_update
	 * 
	 * page update form
	 * 
	 * @param	numberic	$id		ci_page.id
	 */
	public function admin_update ($id) {
		$data = array();
		
		$data['action'] = 'update';
		$data['data'] = $this->page->read_id($id);
		
		$this->load->view('admin/page/write',$data);
	}
	
	/**
	 * admin_updateForm
	 * 
	 * page update
	 */
	public function admin_updateForm () {
		$blank = $result = $data = $page_data = array();
		
		$data = delete_prefix($this->model->post_data('page_','page_id'),'page_');
		$page_data = $this->page->read_id($data['page_id'],$this->model->site['site_id'],$data['language']);
		$result = ($page_data['language'] == $data['language'])?$this->page->update_data($data,$data['page_id']):$this->page->write_data($data);
		
		if ($result['status']) {
			// success
			set_cookie('noti',lang('system_update_success'),0);
			set_cookie('noti_type','success',0);
			$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/admin/page/update/'.$data['page_id'].'/').'";';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * admin_deleteAjax
	 * 
	 * page delete
	 */
	public function admin_deleteAjax () {
		$id = 0;
		$blank = $result = $file_data = $file_id_data = array();
		
		$id = $this->input->post('id');
		$file_data = $this->file->read_model('page',$id);
		
		foreach ($file_data as $row) {
			$file_id_data[] = $row['id'];
		}
		
		if (count($file_id_data)) {
			$this->file->delete($file_id_data);
		}
		
		$result = $this->page->delete($id);
		
		$blank['data']['json'] = $result;
		$this->load->view('blank',$blank);
	}
}