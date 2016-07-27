<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Board extends CI_Controller {
	private $board_config = array();
	
	public function __construct () {
		parent::__construct();
		
		$this->load->model(array('board_model'=>'board'));
		$this->load->language('board');
		
		// load common board js
		$this->model->js($this->model->path.'/views/admin/board/js/board.js');
		
		if (isset($this->model->now_menu['name'])) {
			if (!(in_array(0,$this->model->now_menu['grade']) && !isset($this->member->data['id']))) {
				$auth_check = FALSE;
				foreach ($this->model->now_menu['grade'] as $grade_id) {
					if (isset($this->member->data['grade'][$grade_id])) {
						$auth_check = TRUE;
						break;
					}
				}
				
				if (
					$auth_check == FALSE ||
					(
						(isset($this->board->auth[$this->router->method]) && !$this->board->auth[$this->router->method]) ||
						(isset($this->board->auth[$this->router->method.'Form']) && !$this->board->auth[$this->router->method.'Form'])
					)
				) {
					set_cookie('noti',lang('system_auth_danger'),0);
					set_cookie('noti_type','danger',0);
					redirect('/');
				}
			}
			
			$this->model->html['site_title'] = $this->model->now_menu['name'].' :: '.$this->model->html['site_title'];
			$this->board_config = $this->board->read_config_id($this->model->now_menu['model_id']);
		}
	}
	
	/**
	 * index
	 * 
	 * board list
	 */
	public function index () {
		$language = '';
		$limit = $page = 0;
		$data = array();
		
		$limit = $this->board_config['limit'];
		$page = ($this->input->get('page'))?$this->input->get('page'):1;
		$language = $this->config->item('language');
		
		$data['field'] = $this->input->get('field');
		$data['keyword'] = $this->input->get('keyword');
		$data['total'] = $this->board->read_total($this->board_config['board_config_id']);
		$data['list'] = $this->board->read_list($this->model->now_menu['model_id'],$data['total'],$limit,$page,$language,$data['field'],$data['keyword']);
		$data['pagination'] = $this->model->pagination($data['total'],$limit);
		
		$this->load->view('board/'.$this->board_config['skin'].'/list',$data);
	}
	
	/**
	 * write
	 * 
	 * board write
	 */
	public function write () {
		$data = array();
		
		$data['action'] = 'write';
		$data['data']['board_config_id'] = $this->board_config['board_config_id'];
		
		$this->load->view('board/'.$this->board_config['skin'].'/write',$data);
	}
	
	/**
	 * writeForm
	 * 
	 * DB insert
	 */
	public function writeForm () {
		$data = $result = array();
		
		$data = delete_prefix($this->model->post_data('board_','board_id'),'board_');
		$result = $this->board->write_data($data);
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			echo js('parent.document.location.href = "'.base_url('/'.$this->model->now_menu['uri'].'/'.$result['insert_id']).'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
	
	/**
	 * view
	 * 
	 * board view
	 * 
	 * @param	numberic	$id		ci_board.board_id
	 */
	public function view ($id) {
		$data = array();
		
		$data['data'] = $this->board->read_id($id);
		$this->model->html['site_title'] = $data['data']['title'].' - '.$this->model->now_menu['name'].' :: '.$this->model->html['site_title'];
		
		// add reader
		$this->board->reader($id);
		
		$this->load->view('board/'.$this->board_config['skin'].'/view',$data);
	}
	
	/**
	 * update
	 * 
	 * board update
	 * 
	 * @param	numberic	$id		ci_board.board_id
	 */
	public function update ($id) {
		$data = array();
		
		$data['action'] = 'update';
		$data['data'] = $this->board->read_id($id);
		
		$this->load->view('board/'.$this->board_config['skin'].'/write',$data);
	}
	
	/**
	 * updateForm
	 * 
	 * DB update
	 */
	public function updateForm () {
		$data = $result = array();
		
		$data = delete_prefix($this->model->post_data('board_','board_id'),'board_');
		$result = $this->board->update_data($data,$data['board_id'],$data['language']);
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			echo js('parent.document.location.href = "'.base_url('/'.$this->model->now_menu['uri'].'/'.$data['board_id']).'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
	
	/**
	 * delete
	 */
	public function delete ($id) {
		$result = array();
		
		$result = $this->board->delete_data($id);
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			echo js('parent.document.location.href = "'.base_url('/'.$this->model->now_menu['uri'].'/').'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
	
	/**
	 * admin_config
	 * 
	 * board_config admin페이지
	 */
	public function admin_config () {
		$limit = 20;
		$data = array();
		
		$data['site_id'] = $this->model->site['site_id'];
		$data['keyword'] = $this->input->get('keyword');
		$data['page'] = $this->input->get('page');
		$data['total'] = $this->board->read_total();
		$data['total_config'] = $this->board->read_total_config($data['site_id'],$data['keyword']);
		$data['list'] = $this->board->read_list_config($data['total_config'],$limit,$data['page'],$data['site_id'],$this->config->item('language'),$data['keyword']);
		$data['pagination'] = $this->model->pagination($data['total'],$limit);
		
		$this->load->view('admin/board/config_list',$data);
	}
	
	/**
	 * admin_writeConfig
	 * 
	 * 게시판 추가
	 */
	public function admin_writeConfig () {
		$data = array();
		
		$data['data'] = array();
		$data['skin_list'] = read_folder_list('./application/views/board/');
		$data['member_grade_list'] = $this->member->read_site_grade_list();
		$data['member_grade_list'][] = array(
			'id'=>0,
			'site_id'=>0,
			'site_member_grade_id'=>0,
			'name'=>lang('text_guest')
		);
		$data['action'] = 'write';
		
		$this->load->view('admin/board/config_write',$data);
	}
	
	/**
	 * admin_writeConfigForm
	 * 
	 * 게시판 추가 post
	 */
	public function admin_writeConfigForm () {
		$insert_id = 0;
		$config_data = $model_auth_data = $order_by = $result = array();
		
		$config_data = delete_prefix($this->model->post_data('config_','config_id'),'config_');
		$model_auth_data = delete_prefix($this->model->post_data('model_auth_','model_auth_id'),'model_auth_');
		
		// ci_board_config 추가 데이터
		$order_by = explode('|',$config_data['order_by']);
		$config_data['order_by'] = $order_by[0];
		$config_data['order_by_sort'] = $order_by[1];
		
		// insert ci_board_config
		$result = $this->board->write_config($config_data);
		$insert_id = $result['insert_id'];
		
		if ($result['status']) {
			// insert ci_model_auth
			$result = $this->model->write_model_auth($model_auth_data,'board',$result['insert_id'],$this->model->site['site_id']);
		}
		
		if ($result['status']) {
			// success
			set_cookie('noti',$result['message'],0);
			set_cookie('noti_type','success',0);
			echo js('parent.document.location.href = "'.base_url('/admin/board/updateConfig/'.$insert_id.'/').'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
	
	/**
	 * admin_updateConfig
	 * 
	 * 게시판 수정
	 * 
	 * @param	numberic	$config_id		ci_board_config.id
	 */
	public function admin_updateConfig ($config_id) {
		$data = array();
		
		$data['data'] = $this->board->read_config_id($config_id);
		$data['auth'] = $this->model->read_model_auth('board',$config_id);
		$data['skin_list'] = read_folder_list('./application/views/board/');
		$data['member_grade_list'] = $this->member->read_site_grade_list();
		$data['member_grade_list'][] = array(
			'id'=>0,
			'site_id'=>0,
			'site_member_grade_id'=>0,
			'name'=>lang('text_guest')
		);
		$data['action'] = 'update';
		
		$this->load->view('admin/board/config_write',$data);
	}
	
	/**
	 * admin_updateConfigForm
	 * 
	 * 게시판 수정 post
	 */
	public function admin_updateConfigForm () {
		$id = 0;
		$config_data = $model_auth_data = $order_by = $result = $data = array();
		
		$id = $this->input->post('config_board_config_id');
		$config_data = delete_prefix($this->model->post_data('config_','config_id'),'config_');
		$model_auth_data = delete_prefix($this->model->post_data('model_auth_','model_auth_id'),'model_auth_');
		
		// ci_board_config 추가 데이터
		$order_by = explode('|',$config_data['order_by']);
		$config_data['order_by'] = $order_by[0];
		$config_data['order_by_sort'] = $order_by[1];
		
		// update ci_model_auth
		$result = $this->model->update_model_auth($model_auth_data,'board',$id,$this->model->site['id']);
		
		if ($result['status']) {
			$data = $this->board->read_config_id($id,$this->model->site['site_id'],$config_data['language']);
			
			// update ci_board_config
			$result = ($data['language'] == $config_data['language'])?$this->board->update_config($config_data,$id):$this->board->write_config($config_data);
		}
		
		if ($result['status']) {
			// success
			set_cookie('noti',lang('system_update_success'),0);
			set_cookie('noti_type','success',0);
			echo js('parent.document.location.href = "'.base_url('/admin/board/updateConfig/'.$id.'/').'";');
		} else {
			// error
			echo notify($result['message'],'danger',TRUE);
		}
	}
	
	/**
	 * admin_deleteConfigAjax
	 * 
	 * 게시판 삭제
	 */
	public function admin_deleteConfigAjax () {
		$config_id = $this->input->post('id');
		$result = array();
		
		// delete ci_model_auth
		$result = $this->model->delete_model_auth('board',$config_id,$this->model->site['site_id']);
		
		if ($result['status']) {
			// delete ci_board_config
			$result = $this->board->delete_config($config_id);
		}
		
		echo json_encode($result);
	}
}