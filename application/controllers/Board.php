<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Board extends CI_Controller {
	public function __construct () {
		parent::__construct();
		
		$this->load->model(array('board_model'=>'board'));
		
		if (isset($this->model->now_menu['name'])) {
			$auth_check = FALSE;
			
			// menu auth
			if (isset($this->member->data['id'])) {
				// login
				
				if ($this->board->is_admin) {
					// 운영자나 게시판 관리자는 통과..
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
				// guest
				if (in_array(0,$this->model->now_menu['grade'])) {
					$auth_check = TRUE;
				}
			}
			
			// board auth
			if (
				$auth_check == FALSE ||
				(
					(isset($this->board->auth[$this->router->method]) && !$this->board->auth[$this->router->method]) ||
					(isset($this->board->auth[$this->router->method.'Form']) && !$this->board->auth[$this->router->method.'Form']) ||
					(isset($this->board->auth[$this->router->method.'Ajax']) && !$this->board->auth[$this->router->method.'Ajax'])
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
	 * board list
	 */
	public function index () {
		$data = array();
		
		$data['limit'] = $this->board->configure['limit'];
		$data['page'] = ($this->input->get('page'))?$this->input->get('page'):1;
		$data['language'] = $this->config->item('language');
		
		$data['field'] = $this->input->get('field');
		$data['keyword'] = $this->input->get('keyword');
		$data['total'] = $this->board->read_total($this->board->configure['board_config_id']);
		$data['list'] = $this->board->read_list($this->model->now_menu['model_id'],$data);
		$data['pagination'] = $this->model->pagination($data['total'],$data['limit']);
		
		$this->load->view('board/'.$this->board->configure['skin'].'/list',$data);
	}
	
	/**
	 * write
	 * 
	 * board write
	 */
	public function write () {
		$data = array();
		
		$data['action'] = 'write';
		$data['data']['board_config_id'] = $this->board->configure['board_config_id'];
		
		$this->load->view('board/'.$this->board->configure['skin'].'/write',$data);
	}
	
	/**
	 * writeForm
	 * 
	 * DB insert
	 */
	public function writeForm () {
		$blank = $data = $result = array();
		
		if ($this->input->method() == 'post') {
			$data = delete_prefix($this->model->post_data('board_','board_id'),'board_');
			$result = $this->board->write_data($data);
			
			if ($result['status']) {
				// success
				set_cookie('noti',$result['message'],0);
				set_cookie('noti_type','success',0);
				$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/'.$this->model->now_menu['uri'].'/'.$result['insert_id']).'";';
			} else {
				// error
				$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
			}
		} else {
			set_cookie('noti',lang('system_connect_danger'),0);
			set_cookie('noti_type','danger',0);
			$blank['js'] = 'document.location.href = "'.base_url('/').'";';
		}
		
		$this->load->view('blank',$blank);
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
		
		// add reader
		$this->board->reader($id);
		
		$data['data'] = $this->board->read_id($id);
		$this->model->html['site_title'] = $data['data']['title'].' - '.$this->model->now_menu['name'].' :: '.$this->model->html['site_title'];
		
		$this->load->view('board/'.$this->board->configure['skin'].'/view',$data);
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
		
		$this->load->view('board/'.$this->board->configure['skin'].'/write',$data);
	}
	
	/**
	 * updateForm
	 * 
	 * DB update
	 */
	public function updateForm () {
		$blank = $data = $result = array();
		
		if ($this->input->method() == 'post') {
			$data = delete_prefix($this->model->post_data('board_','board_id'),'board_');
			$result = $this->board->update_data($data,$data['board_id'],$data['language']);
			
			if ($result['status']) {
				// success
				set_cookie('noti',$result['message'],0);
				set_cookie('noti_type','success',0);
				$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/'.$this->model->now_menu['uri'].'/'.$data['board_id']).'";';
			} else {
				// error
				$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
			}
		} else {
			set_cookie('noti',lang('system_connect_danger'),0);
			set_cookie('noti_type','danger',0);
			$blank['js'] = 'document.location.href = "'.base_url('/').'";';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * deleteForm
	 * 
	 * @param	numberic	$id		board.board_id
	 */
	public function deleteForm ($id) {
		$blank = $result = array();
		
		if ($this->input->method() == 'post') {
			$result = $this->board->delete_data($id);
			
			if ($result['status']) {
				// success
				set_cookie('noti',$result['message'],0);
				set_cookie('noti_type','success',0);
				$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/'.$this->model->now_menu['uri'].'/').'";';
			} else {
				// error
				$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
			}
		} else {
			set_cookie('noti',lang('system_connect_danger'),0);
			set_cookie('noti_type','danger',0);
			$blank['js'] = 'document.location.href = "'.base_url('/').'";';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * deleteAjax
	 * 
	 * @param	numberic	$id		board.board_id
	 */
	public function deleteAjax ($id) {
		$blank = $response = array();
		
		if ($this->input->method() == 'post') {
			$response = $this->board->delete_data($id);
			$blank['json'] = $response;
		} else {
			set_cookie('noti',lang('system_connect_danger'),0);
			set_cookie('noti_type','danger',0);
			$blank['js'] = 'document.location.href = "'.base_url('/').'";';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * password
	 * 
	 * 비밀번호 확인
	 * 
	 * @param	numberic	$id			board.board_id
	 * @param	string		$action		update / delete
	 */
	public function password ($id,$action = '') {
		$data = array();
		
		$data['action'] = $action;
		$data['data'] = $this->board->read_id($id);
		
		$this->load->view('board/'.$this->board->configure['skin'].'/password',$data);
	}
	
	/**
	 * checkPasswordForm
	 * 
	 * 비밀번호 확인
	 */
	public function checkPasswordForm () {
		$action = $uri = '';
		$blank = $data = $document_data = $session = array();
		
		$data = delete_prefix($this->model->post_data('board_','board_id'),'board_');
		$action = $this->input->post('action');
		
		$document_data = $this->board->read_id($data['board_id']);
		if ($this->board->check_password($data['password'],$document_data['password'])) {
			if (empty($action)) {
				// view
				$uri = base_url('/'.$this->model->now_menu['uri'].'/'.$data['board_id'].'/');
			} else {
				$uri = base_url('/'.$this->model->now_menu['uri'].'/'.$action.'/'.$data['board_id'].'/');
			}
			
			// set session
			if (!in_array($data['board_id'],$this->board->auth_id)) {
				$this->board->auth_id[] = $data['board_id'];
				$this->session->set_userdata('board_auth_id',$this->board->auth_id);
			}
			
			$blank['data']['js'] = 'parent.document.location.href = "'.$uri.'";';
		} else {
			$blank['data']['js'] = 'parent.notify("'.lang('member_miss_match_password').'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	public function reply ($id) {
		$data = $document_data = array();
		
		$document_data = $this->board->read_id($id);
		$data['action'] = 'write';
		
		// 데이터 수동 업데이트
		$data['data']['title'] = '[re] '.$document_data['title'];
		$data['data']['document'] = '<p></p><p>--------------------------------------------------</p>'.$document_data['document'];
		$data['data']['board_config_id'] = $document_data['board_config_id'];
		$data['data']['parent_id'] = $id;
		
		$this->load->view('board/'.$this->board->configure['skin'].'/write',$data);
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
		$blank = $config_data = $model_auth_data = $order_by = $result = array();
		
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
			$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/admin/board/updateConfig/'.$insert_id.'/').'";';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
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
		$blank = $config_data = $model_auth_data = $order_by = $result = $data = array();
		
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
			$blank['data']['js'] = 'parent.document.location.href = "'.base_url('/admin/board/updateConfig/'.$id.'/').'";';
		} else {
			// error
			$blank['data']['js'] = 'parent.notify("'.$result['message'].'","danger");';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * admin_deleteConfigAjax
	 * 
	 * 게시판 삭제
	 */
	public function admin_deleteConfigAjax () {
		$config_id = $this->input->post('id');
		$blank = $result = array();
		
		// delete ci_model_auth
		$result = $this->model->delete_model_auth('board',$config_id,$this->model->site['site_id']);
		
		if ($result['status']) {
			// delete ci_board_config
			$result = $this->board->delete_config($config_id);
		}
		
		$blank['data']['json'] = $result;
		$this->load->view('blank',$blank);
	}
}