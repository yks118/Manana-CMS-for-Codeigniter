<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page_model extends CI_Model {
	public $menu = array();
	
	public function __construct() {
		parent::__construct();
		
		if ($this->uri->segment(1) == 'admin') {
			$this->menu['page']['name'] = lang('admin_menu_page');
			$this->menu['page']['href'] = base_url('/admin/page/');
			$this->menu['page']['target'] = '_self';
		}
	}
	
	/**
	 * read_total
	 * 
	 * 사이트에 등록된 페이지의 총 수
	 * 
	 * @param	numberic	$site_id	ci_site.id
	 * @param	array		$field
	 * @param	array		$keyword
	 */
	public function read_total ($site_id = 0,$field = array(),$keyword = array()) {
		$total = 0;
		$fields = $keywords = array();
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		if (is_array($field)) {
			$fields = $field;
		} else {
			$fields[] = $field;
		}
		
		if (is_array($keyword)) {
			$keywords = $keyword;
		} else {
			$keywords[] = $keyword;
		}
		
		foreach ($fields as $key => $value) {
			if (isset($fields[$key])) {
				$this->db->like($fields[$key],$keywords[$key],'both');
			}
		}
		
		$this->db->where('site_id',$site_id);
		$this->db->group_by('page_id');
		$total = $this->db->get('page')->num_rows();
		
		return $total;
	}
	
	/**
	 * read_list
	 * 
	 * ci_page의 리스트를 리턴
	 * 
	 * @param	numberic	$total
	 * @param	numberic	$limit
	 * @param	numberic	$page
	 * @param	numberic	$site_id		ci_site.id
	 * @param	array		$field
	 * @param	array		$keyword
	 */
	public function read_list ($total,$limit = 20,$page = 1,$site_id = 0,$field = array(),$keyword = array()) {
		$offset = 0;
		$query = '';
		$list = $result = $fields = $keywords = array();
		
		if (empty($page)) {
			$page = 1;
		}
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		if (is_array($field)) {
			$fields = $field;
		} else {
			$fields[] = $field;
		}
		
		if (is_array($keyword)) {
			$keywords = $keyword;
		} else {
			$keywords[] = $keyword;
		}
		
		$offset = ($page - 1) * $limit;
		
		$this->db->select(write_prefix_db($this->db->list_fields('page'),array('p','pj')));
		$this->db->from('page p');
		$this->db->join('page pj','p.page_id = pj.page_id AND pj.language = "'.$this->config->item('language').'"','LEFT');
		
		foreach ($fields as $key => $value) {
			if (isset($fields[$key])) {
				$this->db->like('pj.'.$fields[$key],$keywords[$key],'both');
			}
		}
		
		$this->db->where('p.site_id',$site_id);
		$this->db->group_by('p.page_id');
		$this->db->order_by('p.page_id','DESC');
		$this->db->offset($offset);
		$this->db->limit($limit);
		$result = $this->db->get()->result_array();
		
		foreach ($result as $key => $row) {
			$row = read_prefix_db($row,'pj');
			$row['number'] = $total - ($limit * ($page - 1)) - $key;
			
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
		$data = array();
		
		if (empty($site_id)) {
			$site_id = $this->model->site['site_id'];
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
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
				$this->update_data(array('page_id'=>$result['insert_id'],'update_datetime'=>$data['update_datetime']),$result['insert_id']);
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
	 * @param	numberic	$id			ci_page.id
	 */
	public function update_data ($data,$id) {
		$result = array();
		
		if (!isset($data['update_datetime'])) {
			$data['update_datetime'] = date('Y-m-d H:i:s');
		}
		
		$this->db->where('id',$id);
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