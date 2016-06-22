<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Board extends CI_Controller {
	public function __construct () {
		parent::__construct();
		
		$this->load->model(array('board_model'=>'board'));
		$this->load->language('board');
	}
	
	/**
	 * admin_index
	 * 
	 * admin페이지 기본 페이지
	 */
	public function admin_index () {
		$data = array();
		
		$data['total'] = $this->board->read_total();
		$data['total_config'] = $this->board->read_total_config();
		
		$data['list'] = $this->board->read_list_config(20,$data['total_config']);
		
		$this->load->view('admin/board/list',$data);
	}
	
	/**
	 * admin_write
	 * 
	 * 게시판 추가
	 */
	public function admin_write () {
		$data = array();
		
		$data['data'] = array();
		$data['skin_list'] = read_folder_list('./application/views/board/');
		$data['member_grade_list'] = $this->member->read_grade_list();
		$data['action'] = 'write';
		
		$this->load->view('admin/board/write',$data);
	}
	
	/**
	 * admin_writeForm
	 * 
	 * 게시판 추가 post
	 */
	public function admin_writeForm () {
		$insert_id = 0;
		$config_data = $model_auth_data = $order_by = $result = array();
		
		$config_data = $this->model->post_data('config_','config_id');
		$model_auth_data = $this->model->post_data('model_auth_','model_auth_id');
		
		// ci_board_config 추가 데이터
		$order_by = explode('|',$config_data['config_order_by']);
		$config_data['config_order_by'] = $order_by[0];
		$config_data['config_order_by_sort'] = $order_by[1];
		
		// insert ci_board_config
		$result = $this->board->write_config_data($config_data);
		$insert_id = $result['insert_id'];
		
		if ($result['status']) {
			// insert ci_model_auth
			$result = $this->model->write_model_auth($model_auth_data,'board',$result['insert_id'],$this->model->site['id']);
		}
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			echo js('parent.document.location.href = "'.base_url('/admin/board/update/'.$insert_id.'/').'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
	
	/**
	 * admin_update
	 * 
	 * 게시판 수정
	 * 
	 * @param	numberic	$config_id		ci_board_config.id
	 */
	public function admin_update ($config_id) {
		$data = array();
		
		$data['data'] = $this->board->read_config_id($config_id);
		$data['auth'] = $this->model->read_model_auth('board',$config_id);
		$data['skin_list'] = read_folder_list('./application/views/board/');
		$data['member_grade_list'] = $this->member->read_grade_list();
		$data['action'] = 'update';
		
		$this->load->view('admin/board/write',$data);
	}
	
	/**
	 * admin_updateForm
	 * 
	 * 게시판 수정 post
	 */
	public function admin_updateForm () {
		$id = 0;
		$config_data = $model_auth_data = $order_by = $result = array();
		
		$id = $this->input->post('config_id');
		$config_data = $this->model->post_data('config_','config_id');
		$model_auth_data = $this->model->post_data('model_auth_','model_auth_id');
		
		// ci_board_config 추가 데이터
		$order_by = explode('|',$config_data['config_order_by']);
		$config_data['config_order_by'] = $order_by[0];
		$config_data['config_order_by_sort'] = $order_by[1];
		
		// update ci_model_auth
		$result = $this->model->update_model_auth($model_auth_data,'board',$id,$this->model->site['id']);
		
		if ($result['status']) {
			// update ci_board_config
			$result = $this->board->update_config_data($config_data,$id);
		}
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			echo js('parent.document.location.href = "'.base_url('/admin/board/update/'.$id.'/').'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
	
	/**
	 * admin_delete
	 * 
	 * 게시판 삭제
	 * 
	 * @param	numberic	$config_id		ci_board_config.id
	 */
	public function admin_delete ($config_id) {
		$result = array();
		
		// delete ci_model_auth
		$result = $this->model->delete_model_auth('board',$config_id,$this->model->site['id']);
		
		if ($result['status']) {
			// delete ci_board_config
			$result = $this->board->delete_config_id($config_id);
		}
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			echo js('parent.document.location.href = "'.base_url('/admin/board/').'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
}