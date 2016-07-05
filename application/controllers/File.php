<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File extends CI_Controller {
	public function __construct () {
		parent::__construct();
	}
	
	/**
	 * upload
	 * 
	 * file upload
	 */
	public function upload () {
		$model_id = 0;
		$model = $action = '';
		$result = array();
		
		$model = $this->input->post('model');
		$model_id = $this->input->post('model_id');
		$action = $this->input->post('action');
		
		if (empty($action)) {
			$action = 'file_upload';
		}
		
		$result = $this->file->upload($model,$model_id);
		
		if ($result['status']) {
			if ($action == 'refresh') {
				// success
				set_cookie('noti',$result['message'],0);
				set_cookie('noti_type','success',0);
				echo js('parent.document.location.href = parent.document.location.href;');
			} else {
				echo js('parent.'.$action.'('.$result['data']['id'].',"'.$result['data']['name'].'","'.$result['data']['path'].'",'.$result['data']['size'].',"'.$result['data']['is_image'].'");');
				echo notify($result['message'],'success',TRUE);
			}
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
	
	/**
	 * download
	 * 
	 * file download
	 */
	public function download ($id) {
		// load download helper
		$this->load->helper('download');
		
		$data = array();
		
		$data = $this->file->read_id($id);
		
		if (isset($data['id'])) {
			$data['data'] = file_get_contents($data['path']);
			force_download($data['name'],$data['data']);
		} else {
			// error
			echo notify(lang('system_download_danger_empty'),'danger',TRUE);
		}
	}
	
	/**
	 * deleteAjax
	 * 
	 * file delete
	 */
	public function deleteAjax () {
		$id = 0;
		$result = array();
		
		$id = $this->input->post('id');
		$result = $this->file->delete($id);
		
		echo json_encode($result);
	}
	
	/**
	 * admin_index
	 * 
	 * admin file list
	 */
	public function admin_index () {
		$limit = 20;
		$data = array();
		
		$data['keyword'] = $this->input->get('keyword');
		$data['page'] = $this->input->get('page');
		$data['total'] = $this->file->read_total($data['keyword']);
		$data['list'] = $this->file->read_list($data['total'],$limit,$data['page'],$data['keyword']);
		$data['pagination'] = $this->model->pagination($data['total'],$limit);
		
		$this->load->view('admin/file/list',$data);
	}
}