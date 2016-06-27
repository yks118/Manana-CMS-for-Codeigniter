<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model extends CI_Model {
	public $html = array();
	public $css = array();
	public $js = array();
	public $path = '';
	public $site = array();
	public $layout = '';
	public $menu = array();
	
	public function __construct () {
		parent::__construct();
		
		if ($this->db->table_exists('site')) {
			$this->site = $this->read_site_url(base_url('/'));
		}
		
		// assets path
		$this->path = base_url('/assets/');
		
		// 기본 설정
		$this->_default();
		
		// 메뉴 설정
		$this->menu = $this->_menu($this->uri->segment(1));
	}
	
	/**
	 * _default
	 * 
	 * 기본설정
	 */
	private function _default () {
		// site setting
		$this->html['site_title'] = (isset($this->site['id']))?$this->site['name']:'Manana CMS';
		
		// css setting
		$this->css('//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css');
		$this->css('//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
		$this->css('//maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css');
		$this->css($this->path.'/css/style.less');
		
		// js setting
		$this->js('//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js','header');
		$this->js('//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js','footer');
		$this->js($this->path.'/js/bootstrap-notify/bootstrap-notify.js','footer');
		$this->js($this->path.'/js/autosize/autosize.js','footer');
		$this->js($this->path.'/js/common.js','footer');
	}
	
	/**
	 * _menu
	 * 
	 * 메뉴 설정
	 * 
	 * @param	string		$segment		uri first segment
	 */
	private function _menu ($segment = '') {
		$menu_data = array();
		
		if ($segment == 'admin') {
			// dashboard
			$menu_data['dashboard']['name'] = lang('admin_menu_dashboard');
			$menu_data['dashboard']['href'] = base_url('/admin/dashboard/');
			$menu_data['dashboard']['target'] = '_self';
		} else {
			
			if ($segment && isset($menu_data[$segment]['name'])) {
				$this->html['site_title'] = $menu_data[$segment]['name'].' :: '.$this->html['site_title'];
			}
		}
		
		return $menu_data;
	}
	
	/**
	 * css
	 * 
	 * css 파일 설정
	 * 
	 * @param	string		$path		css path
	 */
	public function css ($path) {
		$pathinfo = pathinfo($path);
		
		switch ($pathinfo['extension']) {
			case 'less' :
					// $this->css[] = array('type'=>'stylesheet/less','path'=>$path);
					
					$less = str_replace('/assets/views/','/application/views/',$path);
					$less = str_replace(base_url('/'),'./',$less);
					$this->lessc->checkedCompile($less,str_replace('.less','.css',$less));
					
					$this->css[] = array('type'=>'stylesheet','path'=>str_replace('.less','.css',$path));
				break;
			default :
					$this->css[] = array('type'=>'stylesheet','path'=>$path);
				break;
		}
	}
	
	/**
	 * js
	 * 
	 * javascript 파일 설정
	 * 
	 * @param	string		$path		javascript path
	 * @param	string		$position	header / footer
	 */
	public function js ($path,$position = 'footer') {
		$this->js[$position][] = $path;
	}
	
	/**
	 * read_site_url
	 * 
	 * site 테이블을 url로 가져옴
	 * 
	 * @param	string		$url	site url
	 */
	public function read_site_url ($url) {
		$data = $site_member_grade_row = array();
		$url = preg_replace('/(https?:\/\/)([0-9.]+)(\/?)/i','$2',$url);
		
		// get DB
		$this->db->select('*');
		$this->db->from('site');
		$this->db->like('url',$url,'both');
		$this->db->limit(1);
		$data = $this->db->get()->row_array();
		
		// get site admin member grade id
		$this->db->select('*');
		$this->db->from('site_member_grade');
		$this->db->where('site_id',$data['id']);
		$this->db->order_by('id','ASC');
		$this->db->limit(1);
		$site_member_grade_row = $this->db->get()->row_array();
		
		$data['admin_grade_id'] = $site_member_grade_row['id'];
		
		return $data;
	}
	
	/**
	 * post_data
	 * 
	 * post로 넘어오는 data를 리패키징..
	 * 
	 * @param	string		$prefix		post prefix
	 * @param	string		$id			PK
	 */
	public function post_data ($prefix,$id = '') {
		$data = array();
		
		foreach ($this->input->post() as $key => $value) {
			if ($key && (strpos($key,$prefix) !== FALSE && strpos($key,$prefix) == 0) && ($key != $id)) {
				$data[$key] = $value;
			}
		}
		
		return $data;
	}
	
	/**
	 * read_total
	 * 
	 * site table rows
	 */
	public function read_total () {
		return $this->db->count_all('site');
	}
	
	/**
	 * read_model_auth
	 * 
	 * site model auth
	 * 
	 * @param	string		$model		model name
	 * @param	numberic	$model_id	model pk
	 * @param	numberic	$site_id	site id
	 */
	public function read_model_auth ($model,$model_id,$site_id = 0) {
		$data = $result = array();
		
		// check ci_site.id
		if (empty($site_id)) {
			$site_id = $this->model->site['id'];
		}
		
		$this->db->select('*');
		$this->db->from('model_auth');
		$this->db->where('site_id',$site_id);
		$this->db->where('model',$model);
		$this->db->where('model_id',$model_id);
		$result = $this->db->get()->result_array();
		
		foreach ($result as $row) {
			if ($row['status'] == 't') {
				$data[$row['action']][] = $row['site_member_grade_id'];
			}
		}
		
		return $data;
	}
	
	/**
	 * write_data
	 * 
	 * site 생성
	 * 
	 * @param	array	$data
	 */
	public function write_data ($data) {
		$result = array();
		$result['status'] = FALSE;
		
		if (isset($data['site_language']) === FALSE) {
			$data['site_language'] = $this->config->item('language');
		}
		
		if (isset($data['site_member_id']) === FALSE) {
			if ($this->member->read_total() == 1) {
				$data['site_member_id'] = 1;
			} else if (isset($this->member->data['id'])) {
				$data['site_member_id'] = $this->member->data['id'];
			} else {
				$data['message'] = lang('member_login_required');
				return $result;
			}
		}
		
		$site_data = $site_member_grade_data = array();
		foreach ($data as $key => $value) {
			if (strpos($key,'site_member_grade_') !== FALSE && strpos($key,'site_member_grade_') == 0) {
				$site_member_grade_data[str_replace('site_member_grade_','',$key)] = $value;
			} else if (strpos($key,'site_') !== FALSE && strpos($key,'site_') == 0) {
				$site_data[str_replace('site_','',$key)] = $value;
			}
		}
		
		// insert ci_site
		if ($this->db->insert('site',$site_data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('site_write_success');
			$result['insert_id'] = $this->db->insert_id();
			
			// site member grade admin
			$site_member_grade_data[0]['site_id'] = $result['insert_id'];
			$site_member_grade_data[0]['name'] = lang('member_grade_admin');
			$site_member_grade_data[0]['language'] = $this->config->item('language');
			$site_member_grade_data[0]['default'] = 'f';
			
			// site member grade normal
			$site_member_grade_data[1]['site_id'] = $result['insert_id'];
			$site_member_grade_data[1]['name'] = lang('member_grade_normal');
			$site_member_grade_data[1]['language'] = $this->config->item('language');
			$site_member_grade_data[1]['default'] = 't';
			
			// insert ci_site_member_grade
			$this->db->insert_batch('site_member_grade',$site_member_grade_data);
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * write_model_auth
	 * 
	 * 각 모델별 권한 설정
	 * 
	 * @param	array		$data		ci_model_auth row
	 * @param	string		$model		model name
	 * @param	numberic	$model_id	model pk
	 */
	public function write_model_auth ($data,$model,$model_id,$site_id = '') {
		$result = $model_auth_data = $insert = array();
		
		// check ci_site.id
		if (empty($site_id)) {
			$site_id = $this->model->site['id'];
		}
		
		foreach ($data as $key => $row) {
			if (strpos($key,'model_auth_') !== FALSE && strpos($key,'model_auth_') == 0) {
				foreach ($row as $value) {
					$model_auth_data[] = array(
						'site_id'=>$site_id,
						'site_member_grade_id'=>$value,
						'model'=>$model,
						'model_id'=>$model_id,
						'action'=>str_replace('model_auth_','',$key),
						'status'=>'t'
					);
				}
			}
		}
		
		// insert ci_model_auth
		$insert = $this->db->insert_batch('model_auth',$model_auth_data);
		
		// error check
		if ($insert > 0) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_write_success');
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * update_model_auth
	 * 
	 * 각 모델별 권한 설정을 수정
	 * 
	 * @param	array		$data		ci_model_auth row
	 * @param	string		$model		model name
	 * @param	numberic	$model_id	model pk
	 */
	public function update_model_auth ($data,$model,$model_id,$site_id = '') {
		$action = '';
		$result = $model_auth_result = $model_auth_data = $insert_data = $update_data = array();
		
		$result['status'] = TRUE;
		
		// check ci_site.id
		if (empty($site_id)) {
			$site_id = $this->model->site['id'];
		}
		
		foreach ($data as $key => $row) {
			if (strpos($key,'model_auth_') !== FALSE && strpos($key,'model_auth_') == 0) {
				$action = str_replace('model_auth_','',$key);
				
				// update
				$this->db->set('status','f');
				$this->db->where('site_id',$site_id);
				$this->db->where('model',$model);
				$this->db->where('model_id',$model_id);
				$this->db->where('action',$action);
				if ($this->db->update('model_auth')) {
					$result['status'] = TRUE;
					
					// get DB
					$this->db->select('*');
					$this->db->from('model_auth');
					$this->db->where('site_id',$site_id);
					$this->db->where('model',$model);
					$this->db->where('model_id',$model_id);
					$this->db->where('action',$action);
					$model_auth_result = $this->db->get()->result_array();
					
					foreach ($model_auth_result as $model_auth_key => $model_auth_row) {
						$model_auth_data[$model_auth_row['site_member_grade_id']] = $model_auth_row['id'];
					}
					
					foreach ($row as $value) {
						if (isset($model_auth_data[$value])) {
							// update
							$update_data[] = array(
								'id'=>$model_auth_data[$value],
								'status'=>'t'
							);
						} else {
							// insert
							$insert_data[] = array(
								'site_id'=>$site_id,
								'site_member_grade_id'=>$value,
								'model'=>$model,
								'model_id'=>$model_id,
								'action'=>$action,
								'status'=>'t'
							);
						}
					}
					
					// insert_batch
					if (count($insert_data)) {
						$this->db->insert_batch('model_auth');
					}
					
					// update_batch
					if (count($update_data)) {
						$this->db->update_batch('model_auth',$update_data,'id');
					}
				} else {
					$result['status'] = FALSE;
					$result['message'] = $this->db->_error_message();
					$result['number'] = $this->db->_error_number();
					
					break;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * delete_model_auth
	 * 
	 * 모델 권한 설정 삭제
	 * 
	 * @param	string		$model		model name
	 * @param	numberic	$model_id	model pk
	 * @param	numberic	$site_id	ci_site.id
	 */
	public function delete_model_auth ($model,$model_id,$site_id = 0) {
		$result = array();
		
		// check site_id
		if (empty($site_id)) {
			$site_id = $this->model->site['id'];
		}
		
		// delete DB
		$this->db->where('site_id',$site_id);
		$this->db->where('model',$model);
		$this->db->where('model_id',$model_id);
		if ($this->db->delete('model_auth')) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_delete_success');
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * install
	 * 
	 * site DB 설치
	 * 
	 * @param	string		$flag		true / false
	 * @return	string		$return		true / false
	 */
	public function install ($flag = TRUE) {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		// site table
		if (!$this->db->table_exists('site')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'url'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'name'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'description'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'keywords'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'author'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'mobile_view'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1
					),
					'robots'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1
					),
					'login'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'language'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'member_id'=>array(
						'type'=>'INT',
						'constraint'=>11
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('site');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// site_member_grade table
		if (!$this->db->table_exists('site_member_grade')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'site_member_grade_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'default'=>0
					),
					'name'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'language'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'default'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('site_member_grade');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// model_admin table
		if (!$this->db->table_exists('model_admin')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'model'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'model_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'member_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('model_admin');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// model_auth table
		if (!$this->db->table_exists('model_auth')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'site_member_grade_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'model'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'model_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'action'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'status'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('model_auth');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		return $return;
	}
	
	/**
	 * uninstall
	 * 
	 * site DB 삭제
	 * 
	 * @return	string	true / false
	 */
	public function uninstall () {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		$return = $this->dbforge->drop_table('site');
		if ($return) {
			$this->dbforge->drop_table('site_member_grade');
			$this->dbforge->drop_table('site_auth');
		}
		
		return $return;
	}
}