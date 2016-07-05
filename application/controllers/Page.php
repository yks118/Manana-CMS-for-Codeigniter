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
		$data = array();
		
		$data['total'] = $this->page->read_total();
		$data['list'] = $this->page->read_list();
		
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
		$data = array();
		
		$data = $this->model->post_data('page_','page_id');
		
		print_r2($data);
	}
}