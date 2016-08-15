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
		$blank = $result = array();
		
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
				$blank['data']['js'] = 'parent.document.location.href = parent.document.location.href;';
			} else {
				$blank['data']['js'] = 'parent.'.$action.'('.$result['data']['id'].',"'.$result['data']['name'].'","'.$result['data']['path'].'",'.$result['data']['size'].',"'.$result['data']['is_image'].'");';
				$blank['data']['js'] .= 'parent.notify("'.$result['message'].'","success")';
			}
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * download
	 * 
	 * file download
	 */
	public function download ($id) {
		// load download helper
		$this->load->helper('download');
		
		$blank = $data = array();
		
		$data = $this->file->read_id($id);
		
		if (isset($data['id'])) {
			$data['data'] = file_get_contents($data['path']);
			force_download($data['name'],$data['data']);
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.lang('system_download_danger_empty').'","danger");';
			$this->load->view('blank',$blank);
		}
	}
	
	/**
	 * deleteAjax
	 * 
	 * file delete
	 */
	public function deleteAjax () {
		$id = 0;
		$blank = $result = array();
		
		$id = $this->input->post('id');
		$result = $this->file->delete($id);
		
		$blank['data']['json'] = $result;
		$this->load->view('blank',$blank);
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