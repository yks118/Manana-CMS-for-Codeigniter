<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class manana {
	// codeigniter
	private $CI;
	
	/**
	 * post_controller_constructor
	 * 
	 * 컨트롤러가 인스턴스화 된 직후입니다.
	 */
	public function post_controller_constructor () {
		$this->CI =& get_instance();
		
		// 데이터베이스 설정 확인
		if (!$this->CI->db->database && ($this->CI->uri->segment(1) != 'install')) {
			// 설치 페이지로 리다이렉트
			redirect('/install/');
		}
	}
	
	/**
	 * display_override
	 * 
	 * 최종적으로 브라우저에 페이지를 전송할때 사용됩니다.
	 */
	public function display_override () {
		global $OUT;
		$output = $this->CI->output->get_output();
		
		$this->CI->model->config->layout = $output;
		$output = $this->CI->load->view('html',$this->CI->model->config,TRUE);
		
		$OUT->_display($output);
	}
}