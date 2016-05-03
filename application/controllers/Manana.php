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
		$message = get_cookie('noti');
		$type = get_cookie('noti_type');
		
		if ($message && $type) {
			delete_cookie('noti');
			delete_cookie('noti_type');
			
			echo notify($message,$type,TRUE);
		}
	}
	
	/**
	 * csrf
	 * 
	 * csrf를 갱신..
	 */
	public function csrf () {
		$js = '
		var csrf = parent.document.getElementsByName("'.$this->security->get_csrf_token_name().'");
		
		for (var i = 0; i < csrf.length; i++) {
			csrf[i].value = "'.$this->security->get_csrf_hash().'";
		}
		';
		
		echo js($js);
	}
}