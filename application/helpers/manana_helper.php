<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * print_r2
 * 
 * print_r을 보기좋게 변경
 * 
 * @param	array	$var
 */
function print_r2 ($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}

/**
 * js
 * 
 * javascript를 출력함
 * 
 * @param	string		$text
 */
function js ($text) {
	return '<script type="text/javascript" charset="UTF-8">'.$text.'</script>';
}

/**
 * notify
 * 
 * js 알림을 셋팅
 * 
 * @param	string		$message		메세지
 * @param	string		$type			success / warning / danger
 * @param	string		$parent			true / false
 */
function notify ($message,$type = 'success',$parent = TRUE) {
	if ($parent) {
		return js('parent.notify("'.$message.'","'.$type.'")');
	} else {
		return js('notify("'.$message.'","'.$type.'")');
	}
}
