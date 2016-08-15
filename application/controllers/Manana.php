<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manana extends CI_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * notify
	 * 
	 * cookie에 저장되어있는 notify를 표시..
	 */
	public function notify () {
		$message = $type = '';
		$blank = array();
		
		$message = get_cookie('noti');
		$type = get_cookie('noti_type');
		
		if ($message && $type) {
			delete_cookie('noti');
			delete_cookie('noti_type');
			
			$blank['data']['js'] = 'parent.notify("'.$message.'","'.$type.'");';
			$this->load->view('blank',$blank);
		}
	}
	
	/**
	 * csrf
	 * 
	 * csrf를 갱신..
	 */
	public function csrf () {
		$blank = array();
		
		$blank['data']['js'] = '
		var csrf = parent.document.getElementsByName("'.$this->security->get_csrf_token_name().'");
		
		for (var i = 0; i < csrf.length; i++) {
			csrf[i].value = "'.$this->security->get_csrf_hash().'";
		}
		
		parent.csrf["'.$this->security->get_csrf_token_name().'"] = "'.$this->security->get_csrf_hash().'";
		';
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * robots
	 * 
	 * robots.txt
	 */
	public function robots () {
		$blank = array();
		
		header('Content-Type: text/plain');
		
		if ($this->model->site['robots'] == 't') {
			$blank['data']['text'] = 'User-agent: *
Disallow: ';
		} else {
			$blank['data']['text'] = 'User-agent: *
Disallow: /';
		}
		
		$this->load->view('blank',$blank);
	}
	
	/**
	 * language
	 * 
	 * 언어 설정 리턴
	 */
	public function language () {
		$blank = $data = array();
		
		$data['text'] = $this->input->post('text');
		$data['file'] = $this->input->post('file');
		$data['language'] = $this->input->post('language');
		
		if ($data['file'] != 'common') {
			$this->lang->load($data['file'],$data['language']);
		}
		
		$data['lang'] = $this->lang->line($data['text']);
		
		$blank['data']['json'] = $data;
		$this->load->view('blank',$blank);
	}
}