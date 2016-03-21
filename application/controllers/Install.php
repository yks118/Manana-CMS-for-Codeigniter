<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Install extends CI_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	public function index () {
		if (!$this->db->username || !$this->db->password || !$this->db->database) {
			// database 설정
			$this->load->view('install/database');
		} else if (!$this->db->table_exists('site')) {
			// site 설정
			$this->load->view('install/site');
		}
	}
}