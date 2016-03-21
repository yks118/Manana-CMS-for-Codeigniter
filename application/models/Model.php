<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model extends CI_Model {
	public $html;
	public $css = array();
	public $js = array();
	public $path;
	
	public function __construct() {
		parent::__construct();
		
		// html 리셋
		$this->html = new stdClass();
		
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
		$this->html->site_title = 'Manana CMS';
		
		// css setting
		$this->css('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
		$this->css($this->path.'/css/style.less');
		
		// js setting
		$this->js('https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js','header');
		$this->js('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js','footer');
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