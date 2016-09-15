<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page_model extends CI_Model {
	public $is_admin = FALSE;
	public $skin = 'basic';
	public $menu = array();
	public $auth = array();
	
	public function __construct() {
		parent::__construct();
		
		if ($this->uri->segment(1) == 'admin') {
			// load common page js
			$this->model->js($this->model->path.'/views/admin/page/js/page.js');
			
			$this->menu['page']['name'] = lang('admin_menu_page');
			$this->menu['page']['href'] = base_url('/admin/page/');
			$this->menu['page']['target'] = '_self';
		} else if (isset($this->model->now_menu['model']) && $this->model->now_menu['model'] == 'page') {
			if ($this->member->is_admin) {
				$this->is_admin = TRUE;
			}
			
			$this->auth = $this->_auth($this->model->now_menu['model_id']);
		}
	}
	
	/**
	 * _auth
	 * 
	 * 페이지의 권한 설정..
	 * 
	 * @param	numberic	$id		page.page_id
	 */
	private function _auth ($id) {
		$data = array();
		
		$data['view'] = TRUE;
		$data['update'] = FALSE;
		
		if ($this->is_admin) {
			foreach ($data as $key => $value) {
				$data[$key] = TRUE;
			}
		}
		
		// 페이지는 index가 view
		$data['index'] = $data['view'];
		
		return $data;
	}
	
	/**
	 * read_total
	 * 
	 * 사이트에 등록된 페이지의 총 수
	 * 
	 * @param	array		$data
	 */
	public function read_total ($data) {
		$total = 0;
		$fields = $keywords = array();
		
		if (!isset($data['site_id'])) {
			$data['site_id'] = $this->model->site['site_id'];
		}
		
		if (isset($data['field'])) {
			if (is_array($data['field'])) {
				$fields = $data['field'];
			} else {
				$fields[] = $data['field'];
			}
		}
		
		if (isset($data['keyword'])) {
			if (is_array($data['keyword'])) {
				$keywords = $data['keyword'];
			} else {
				$keywords[] = $data['keyword'];
			}
		}
		
		
		foreach ($fields as $key => $value) {
			if (isset($fields[$key])) {
				$this->db->like($fields[$key],$keywords[$key],'both');
			}
		}
		
		$this->db->where('site_id',$data['site_id']);
		$this->db->group_by('page_id');
		$total = $this->db->get('page')->num_rows();
		
		return $total;
	}
	
	/**
	 * read_list
	 * 
	 * ci_page의 리스트를 리턴
	 * 
	 * @param	array		$data
	 */
	public function read_list ($data) {
		$offset = 0;
		$query = '';
		$list = $result = $fields = $keywords = array();
		
		if (!isset($data['page'])) {
			$data['page'] = 1;
		}
		
		if (!isset($data['site_id'])) {
			$data['site_id'] = $this->model->site['site_id'];
		}
		
		if (!isset($data['limit'])) {
			$data['limit'] = 20;
		}
		
		if (!isset($data['language'])) {
			$data['language'] = $this->config->item('language');
		}
		
		if (isset($data['field'])) {
			if (is_array($data['field'])) {
				$fields = $data['field'];
			} else {
				$fields[] = $data['field'];
			}
		}
		
		if (isset($data['keyword'])) {
			if (is_array($data['keyword'])) {
				$keywords = $data['keyword'];
			} else {
				$keywords[] = $data['keyword'];
			}
		}
		
		if (!isset($data['total'])) {
			$data['total'] = $this->read_total($data['site_id'],$fields,$keywords);
		}
		
		$offset = ($data['page'] - 1) * $data['limit'];
		
		$this->db->select(write_prefix_db($this->db->list_fields('page'),array('p','pj')));
		$this->db->from('page p');
		$this->db->join('page pj','p.page_id = pj.page_id AND pj.language = "'.$data['language'].'"','LEFT');
		
		foreach ($fields as $key => $value) {
			$this->db->like('pj.'.$fields[$key],$keywords[$key],'both');
		}
		
		$this->db->where('p.site_id',$data['site_id']);
		$this->db->group_by('p.page_id');
		$this->db->order_by('p.page_id','DESC');
		$this->db->offset($offset);
		$this->db->limit($data['limit']);
		$result = $this->db->get()->result_array();
		
		foreach ($result as $key => $row) {
			$row = read_prefix_db($row,'pj');
			$row['number'] = $data['total'] - ($data['limit'] * ($data['page'] - 1)) - $key;
			
			$list[] = $row;
		}
		
		return $list;
	}
	
	/**
	 * read_id
	 * 
	 * ci_page의 데이터를 리턴
	 * 
	 * @param	numberic	$id			ci_page.page_id
	 * @param	numberic	$site_id	ci_site.site_id
	 * @param	string		$language
	 */
	public function read_id ($id,$site_id = 0,$language = '') {
		$data = $cache = array();
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		$cache = $this->model->read_cache('read_page_id_'.$id.'_'.$language);
		
		if (empty($cache)) {
			// ci_page
			$this->db->select(write_prefix_db($this->db->list_fields('page'),array('p','pj')));
			$this->db->from('page p');
			$this->db->join('page pj','p.page_id = pj.page_id AND pj.language = "'.$language.'"','LEFT');
			$this->db->where('p.page_id',$id);
			$this->db->where('p.site_id',$site_id);
			$data = read_prefix_db($this->db->get()->row_array(),'pj');
			
			// ci_file
			$data['files'] = array();
			$data['files'] = $this->file->read_model('page',$id);
			
			$this->model->write_cache('read_page_id_'.$id.'_'.$language,$data);
		} else {
			$data = $cache;
		}
		
		return $data;
	}
	
	/**
	 * write_data
	 * 
	 * page write
	 * 
	 * @param	array	$data
	 */
	public function write_data ($data) {
		$result = array();
		
		if (!isset($data['site_id'])) {
			$data['site_id'] = $this->model->site['site_id'];
		}
		
		if (!isset($data['member_id'])) {
			$data['member_id'] = $this->member->data['id'];
		}
		
		if (!isset($data['write_datetime'])) {
			$data['write_datetime'] = $data['update_datetime'] = date('Y-m-d H:i:s');
		}
		
		if (!isset($data['ip'])) {
			$data['ip'] = $this->input->ip_address();
		}
		
		if (!isset($data['language'])) {
			$data['language'] = $this->config->item('language');
		}
		
		if ($this->db->insert('page',$data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_write_success');
			$result['insert_id'] = $this->db->insert_id();
			
			if (!isset($data['page_id'])) {
				$this->db->set('page_id',$result['insert_id']);
				$this->db->where('id',$result['insert_id']);
				$this->db->update('page');
			}
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * update_data
	 * 
	 * page update
	 * 
	 * @param	array		$data
	 * @param	numberic	$id			ci_page.page_id
	 * @param	numberic	$site_id	ci_site.site_id
	 * @param	string		$language
	 */
	public function update_data ($data,$id,$site_id = 0,$language = '') {
		$result = array();
		
		if (!isset($data['update_datetime'])) {
			$data['update_datetime'] = date('Y-m-d H:i:s');
		}
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		$this->db->where('page_id',$id);
		$this->db->where('site_id',$site_id);
		$this->db->where('language',$language);
		if ($this->db->update('page',$data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_update_success');
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * delete
	 * 
	 * page delete
	 * 
	 * @param	numberic	$id		ci_page.id
	 */
	public function delete ($id) {
		$result = $ids = $data = array();
		
		if (is_array($id)) {
			$ids = $id;
		} else {
			$ids[] = $id;
		}
		
		foreach ($ids as $value) {
			$data = array();
			$data = $this->read_id($value);
			
			$this->db->where('page_id',$data['page_id']);
			if ($this->db->delete('page')) {
				$result['status'] = TRUE;
				$result['message'] = lang('system_delete_success');
			} else {
				$result['status'] = FALSE;
				$result['message'] = $this->db->_error_message();
				$result['number'] = $this->db->_error_number();
				break;
			}
		}
		
		return $result;
	}
	
	/**
	 * install
	 * 
	 * page DB install
	 * 
	 * @param	string		$flag		true / false
	 * @return	string		$return		true / false
	 */
	public function install ($flag = TRUE) {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		// page table
		if (!$this->db->table_exists('page')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'page_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'title'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'document'=>array(
						'type'=>'TEXT'
					),
					'member_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'write_datetime'=>array(
						'type'=>'DATETIME',
						'default'=>'0000-00-00 00:00:00'
					),
					'update_datetime'=>array(
						'type'=>'DATETIME',
						'default'=>'0000-00-00 00:00:00'
					),
					'ip'=>array(
						'type'=>'VARCHAR',
						'constraint'=>45
					),
					'language'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('page');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		return $return;
	}
	
	/**
	 * uninstall
	 * 
	 * page DB 삭제
	 * 
	 * @return	string	true / false
	 */
	public function uninstall () {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		$return = $this->dbforge->drop_table('page');
		
		return $return;
	}
}