<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model extends CI_Model {
	public $config;
	public $css = array();
	public $js = array();
	public $path;
	
	public function __construct() {
		parent::__construct();
		
		// config 리셋
		$this->config = new stdClass();
		
		// 언어 설정
		$this->_lang();
		
		// assets path
		$this->path = base_url('/assets/');
		
		// 기본 설정
		$this->_default();
	}
	
	/**
	 * _default
	 * 
	 * 기본설정
	 */
	private function _default () {
		// site setting
		$this->config->site_title = 'Manana CMS';
		
		// css setting
		$this->css('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
		$this->css($this->path.'/css/style.less');
		
		// js setting
		$this->js('https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js','header');
		$this->js('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js','footer');
	}
	
	/**
	 * _lang
	 * 
	 * 사이트 html용 언어를 설정
	 */
	private function _lang () {
		$this->config->site_lang = 'en-US';
		$languages = array('ko-KR','ko','ja-JP','ja');
		$match = array();
		
		preg_match_all('/([^;]+);([^,]+),?/i',$_SERVER['HTTP_ACCEPT_LANGUAGE'],$match);
		
		if (isset($match[1][0])) {
			// ko-KR,ko;q=0.8,en-US;q=0.5,en;q=0.3
			foreach ($match[1] as $value) {
				$http_accept_language = explode(',',$value);
				
				if (in_array($http_accept_language[0],$languages)) {
					$this->config->site_lang = $http_accept_language[0];
					break;
				}
			}
		} else {
			// ko-KR
			if (in_array($_SERVER['HTTP_ACCEPT_LANGUAGE'],$languages)) {
				$this->config->site_lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
			}
		}
		
		switch ($this->config->site_lang) {
			case 'ko' :
			case 'ko-KR' :
					$this->config->site_language = 'korean';
					$this->config->site_language_nation = 'korea';
				break;
			case 'ja' :
			case 'ja-JP' :
					$this->config->site_language = 'japanese';
					$this->config->site_language_nation = 'japan';
				break;
			default :
					$this->config->site_language = 'english';
					$this->config->site_language_nation = 'USA';
				break;
		}
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
}