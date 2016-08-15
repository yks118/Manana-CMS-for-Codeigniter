<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File_model extends CI_Model {
	private $upload_folder = './upload';
	private $folder_permissions = 0755;
	
	public $menu = array();
	
	public function __construct() {
		parent::__construct();
		
		if ($this->uri->segment(1) == 'admin') {
			$this->menu['file']['name'] = lang('admin_menu_file');
			$this->menu['file']['href'] = base_url('/admin/file/');
			$this->menu['file']['target'] = '_self';
		}
	}
	
	/**
	 * read_model
	 * 
	 * ci_file 테이블을 model과 model_id로 검색합니다.
	 * 
	 * @param	string		$model			ci_file.model
	 * @param	numberic	$model_id		ci_file.model_id
	 */
	public function read_model ($model,$model_id = 0) {
		$data = array();
		
		$this->db->select('*');
		$this->db->from('file');
		$this->db->where('model',$model);
		$this->db->where('model_id',$model_id);
		$this->db->order_by('id','ASC');
		$data = $this->db->get()->result_array();
		
		return $data;
	}
	
	/**
	 * read_id
	 * 
	 * ci_file 테이블을 id로 검색
	 * 
	 * @param	numberic	$id		ci_file.id
	 */
	public function read_id ($id) {
		$data = array();
		
		$this->db->select('*');
		$this->db->from('file');
		$this->db->where('id',$id);
		$this->db->limit(1);
		$data = $this->db->get()->row_array();
		
		return $data;
	}
	
	/**
	 * read_total
	 * 
	 * 총 갯수
	 * 
	 * @param	string		$keyword
	 */
	public function read_total ($keyword = '') {
		$total = 0;
		
		if ($keyword) {
			$this->db->like('name',$keyword,'both');
		}
		
		$total = $this->db->count_all_results('file');
		
		return $total;
	}
	
	/**
	 * read_list
	 * 
	 * ci_file 테이블의 list
	 * 
	 * @param	numberic	$total
	 * @param	numberic	$limit
	 * @param	numberic	$page
	 * @param	string		$keyword
	 */
	public function read_list ($total,$limit = 20,$page = 1,$keyword = '') {
		$offset = 0;
		$data = $result = array();
		
		if (empty($page)) {
			$page = 1;
		}
		
		$offset = ($page - 1) * $limit;
		
		$this->db->select('*');
		$this->db->from('file');
		$this->db->like('name',$keyword,'both');
		$this->db->order_by('id','DESC');
		$this->db->offset($offset);
		$this->db->limit($limit);
		$result = $this->db->get()->result_array();
		
		foreach ($result as $key => $row) {
			$row['number'] = $total - ($limit * ($page - 1)) - $key;
			
			$data[] = $row;
		}
		
		return $data;
	}
	
	/**
	 * update_data
	 * 
	 * ci_file update
	 * 
	 * @param	array		$data
	 * @param	numberic	$id
	 */
	public function update_data ($data,$id) {
		$result = array();
		
		$this->db->where('id',$id);
		if ($this->db->update('file',$data)) {
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
	 * update_data_batch
	 * 
	 * ci_file updates
	 * 
	 * @param	array	$data
	 */
	public function update_data_batch ($data) {
		$update = 0;
		$result = array();
		
		$update = $this->db->update_batch('file',$data,'id');
		if ($update > 0) {
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
	 * upload
	 * 
	 * file upload
	 * 
	 * @param	string		$model
	 * @param	numberic	$model_id
	 * @param	array		$config
	 */
	public function upload ($model,$model_id = 0,$config = array()) {
		$path = '';
		$result = $data = array();
		
		$result['status'] = TRUE;
		
		// config setting
		if (!isset($config['upload_path'])) {
			$config['upload_path'] = $this->upload_folder.'/'.$model.'/';
		}
		
		if (!isset($config['allowed_types'])) {
			$config['allowed_types'] = '*';
		}
		
		if (!isset($config['overwrite'])) {
			$config['overwrite'] = FALSE;
		}
		
		if (!isset($config['max_size'])) {
			$config['max_size'] = 10 * 1024;	// MB
		}
		
		if (!isset($config['max_width'])) {
			$config['max_width'] = 0;
		}
		
		if (!isset($config['max_height'])) {
			$config['max_height'] = 0;
		}
		
		if (!isset($config['max_filename'])) {
			$config['max_filename'] = 255;
		}
		
		if (!isset($config['encrypt_name'])) {
			$config['encrypt_name'] = TRUE;
		}
		
		if (!isset($config['remove_spaces'])) {
			$config['remove_spaces'] = TRUE;
		}
		
		$this->load->library('upload',$config);
		
		// check folder
		if (!is_dir($config['upload_path'])) {
			if (!mkdir($config['upload_path'],$this->folder_permissions,TRUE)) {
				$result['status'] = FALSE;
				$result['message'] = lang('system_mkdir_danger');
			}
		}
		
		if ($result['status']) {
			if ($this->upload->do_upload('file')) {
				$data = $this->upload->data();
				$path = str_replace($_SERVER['DOCUMENT_ROOT'],'.',$data['full_path']);
				
				// insert
				$this->db->set('model',$model);
				$this->db->set('model_id',$model_id);
				$this->db->set('name',$data['orig_name']);
				$this->db->set('type',$data['file_type']);
				$this->db->set('path',$path);
				$this->db->set('size',$data['file_size']);
				$this->db->set('is_image',$data['is_image']);
				
				if ($this->db->insert('file')) {
					$result['status'] = TRUE;
					$result['message'] = lang('system_file_upload_success');
					$result['insert_id'] = $this->db->insert_id();
					
					// set data
					$result['data']['id'] = $result['insert_id'];
					$result['data']['name'] = $data['orig_name'];
					$result['data']['path'] = $path;
					$result['data']['size'] = $data['file_size'];
					$result['data']['is_image'] = $data['is_image'];
				} else {
					$result['status'] = FALSE;
					$result['message'] = $this->db->_error_message();
					$result['number'] = $this->db->_error_number();
				}
			} else {
				$result['status'] = FALSE;
				$result['message'] = $this->upload->display_errors();
			}
		}
		
		return $result;
	}
	
	/**
	 * delete
	 * 
	 * 파일 삭제
	 * 
	 * @param	array	$id		ci_file.id
	 */
	public function delete ($id) {
		$result = $ids = $data = array();
		
		// array check
		if (is_array($id)) {
			$ids = $id;
		} else {
			$ids[] = $id;
		}
		
		foreach ($ids as $value) {
			$data = array();
			
			$this->db->select('*');
			$this->db->from('file');
			$this->db->where('id',$value);
			$this->db->limit(1);
			$data = $this->db->get()->row_array();
			
			if (isset($data['id'])) {
				if (unlink($data['path'])) {
					$this->db->where('id',$value);
					if ($this->db->delete('file')) {
						$result['status'] = TRUE;
						$result['message'] = lang('system_file_delete_success');
					} else {
						$result['status'] = FALSE;
						$result['message'] = $this->db->_error_message();
						$result['number'] = $this->db->_error_number();
						break;
					}
				} else {
					$result['status'] = FALSE;
					$result['message'] = lang('system_file_delete_danger');
					break;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * install
	 * 
	 * file DB install
	 * 
	 * @param	string		$flag		true / false
	 * @return	string		$return		true / false
	 */
	public function install ($flag = TRUE) {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		// page table
		if (!$this->db->table_exists('file')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'model'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'model_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'default'=>0
					),
					'name'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'type'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'path'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'size'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'is_image'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('file');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// create upload folder
		if (!is_dir($this->upload_folder)) {
			if ($flag) {
				$return = mkdir($this->upload_folder,$this->folder_permissions,TRUE);
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		return $return;
	}
	
	/**
	 * uninstall
	 * 
	 * file DB 삭제
	 * 
	 * @return	string	true / false
	 */
	public function uninstall () {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		$return = $this->dbforge->drop_table('file');
		if ($return) {
			rmdir($this->upload_folder);
		}
		
		return $return;
	}
}