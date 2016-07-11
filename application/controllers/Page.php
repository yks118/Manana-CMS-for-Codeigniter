<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * admin_index
	 * 
	 * page list
	 */
	public function admin_index () {
		$limit = 20;
		$data = array();
		
		$data['page'] = $this->input->get('page');
		$data['field'] = $this->input->get('field');
		$data['keyword'] = $this->input->get('keyword');
		$data['site_id'] = $this->model->site['site_id'];
		$data['total'] = $this->page->read_total($data['site_id'],$data['field'],$data['keyword']);
		$data['list'] = $this->page->read_list($data['total'],$limit,$data['page'],$data['site_id'],$data['field'],$data['keyword']);
		$data['pagination'] = $this->model->pagination($data['total'],$limit);
		
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
		$result = $data = $file_ids = $file_data = array();
		
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
			echo js('parent.document.location.href = "'.base_url('/admin/page/update/'.$result['insert_id'].'/').'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
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
		$id = 0;
		$language = '';
		$result = $data = $page_data = array();
		
		$id = $this->input->post('page_id');
		$language = $this->input->post('language');
		$data = delete_prefix($this->model->post_data('page_','page_id'),'page_');
		
		$page_data = $this->page->read_id($id);
		$result = ($page_data['language'] == $language)?$this->page->update_data($data,$id):$this->page->write_data($data);
		
		if ($result['status']) {
			// success
			set_cookie('noti',lang('system_update_success'),0);
			set_cookie('noti_type','success',0);
			echo js('parent.document.location.href = "'.base_url('/admin/page/update/'.$id.'/').'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
	
	/**
	 * admin_deleteAjax
	 * 
	 * page delete
	 */
	public function admin_deleteAjax () {
		$id = 0;
		$result = $file_data = $file_id_data = array();
		
		$id = $this->input->post('id');
		$file_data = $this->file->read_model('page',$id);
		
		foreach ($file_data as $row) {
			$file_id_data[] = $row['id'];
		}
		
		if (count($file_id_data)) {
			$this->file->delete($file_id_data);
		}
		
		$result = $this->page->delete($id);
		
		echo json_encode($result);
	}
}