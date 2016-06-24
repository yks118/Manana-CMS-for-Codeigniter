<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class manana_hook {
	// codeigniter
	private $CI;
	private $site_lang;
	
	/**
	 * pre_system
	 * 
	 * 시스템 작동초기입니다.
	 */
	public function pre_system () {
		
	}
	
	/**
	 * pre_controller
	 * 
	 * 컨트롤러가 호출되기 직전입니다.
	 */
	public function pre_controller () {
		// 언어 설정
		$this->_language($GLOBALS['CFG']->config['language']);
	}
	
	/**
	 * post_controller_constructor
	 * 
	 * 컨트롤러가 인스턴스화 된 직후입니다.
	 */
	public function post_controller_constructor () {
		$this->CI =& get_instance();
		
		// 데이터베이스 설정 확인
		if ((!$this->CI->db->database || !$this->CI->db->table_exists('member')) && $this->CI->uri->segment(1) != 'install') {
			// 설치 페이지로 리다이렉트
			redirect('/install/');
		} else if ($this->CI->uri->segment(1) == 'admin') {
			// check site admin
			if ($this->CI->member->check_admin()) {
				// set admin layout
				$this->CI->model->layout = 'admin';
				
				// set admin menu
				$this->_read_admin_menu();
				
				if (isset($this->CI->model->menu[$this->CI->uri->segment(2)]['name'])) {
					$this->CI->model->html['site_title'] = $this->CI->model->menu[$this->CI->uri->segment(2)]['name'].' :: '.$this->CI->model->html['site_title'];
				}
			} else {
				set_cookie('noti',lang('system_auth_danger'),0);
				set_cookie('noti_type','danger',0);
				redirect('/');
			}
		}
	}
	
	/**
	 * _read_admin_menu
	 * 
	 * set admin menu
	 */
	private function _read_admin_menu () {
		$list = read_file_list('./application/models/',array('php'));
		
		foreach ($list as $key => $value) {
			$class = strtolower(str_replace('.php','',$value));
			$model = strtolower(str_replace(array('_model.php','.php'),'',$value));
			
			$this->CI->load->model(array($class=>$model));
			
			if (isset($this->CI->$model->menu) && $model != 'model') {
				$this->CI->model->menu[$model] = $this->CI->$model->menu[$model];
			}
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
		
		// json check
		$json = json_decode($output);
		if (json_last_error() === JSON_ERROR_NONE) {
			// json
			
			// set csrf
			$json[$this->CI->security->get_csrf_token_name()] = $this->CI->security->get_csrf_hash();
			
			// encode json
			$output = json_encode($json);
		} else {
			// html
			$this->CI->model->html['site_lang'] = $this->site_lang;
			
			if (empty($this->CI->model->layout)) {
				$this->CI->model->html['layout'] = $output;
			} else {
				$data = array();
				$data['page'] = $output;
				$data['path'] = base_url('/assets/views/layout/'.$this->CI->model->layout);
				$this->CI->model->html['layout'] = $this->CI->load->view('layout/'.$this->CI->model->layout.'/layout',$data,TRUE);
			}
			
			$output = $this->CI->load->view('html',$this->CI->model->html,TRUE);
		}
		
		$OUT->_display($output);
	}
	
	/**
	 * _language
	 * 
	 * 사이트 언어 설정
	 * 
	 * @param	string	$config_language	korean / japanese / english
	 */
	private function _language ($config_language) {
		$language = 'english';
		$cookie_prefix = $GLOBALS['CFG']->config['cookie_prefix'];
		
		if (isset($_COOKIE[$cookie_prefix.'language'])) {
			// 언어 설정이 존재
			$language = $_COOKIE[$cookie_prefix.'language'];
			
			switch ($language) {
				case 'korean' : $this->site_lang = 'ko-KR'; break;
				case 'japanese' : $this->site_lang = 'ja-JP'; break;
				default : $this->site_lang = 'en-US'; break;
			}
		} else {
			// 언어 설정이 존재하지 않음
			$languages = array('ko-KR','ko','ja-JP','ja');
			
			preg_match_all('/([^;]+);([^,]+),?/i',$_SERVER['HTTP_ACCEPT_LANGUAGE'],$match);
			
			if (isset($match[1][0])) {
				// ko-KR,ko;q=0.8,en-US;q=0.5,en;q=0.3
				foreach ($match[1] as $value) {
					$http_accept_language = explode(',',$value);
					
					if (in_array($http_accept_language[0],$languages)) {
						$this->site_lang = $http_accept_language[0];
						break;
					}
				}
			} else {
				// ko-KR
				if (in_array($_SERVER['HTTP_ACCEPT_LANGUAGE'],$languages)) {
					$this->site_lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
				}
			}
			
			switch ($this->site_lang) {
				case 'ko' :
				case 'ko-KR' :
						$language = 'korean';
					break;
				case 'ja' :
				case 'ja-JP' :
						$language = 'japanese';
					break;
				default :
						$language = 'english';
					break;
			}
		}
		
		$GLOBALS['CFG']->config['language'] = $language;
	}
}