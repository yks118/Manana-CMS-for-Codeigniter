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
		return js('parent.notify("'.$message.'","'.$type.'");');
	} else {
		return js('notify("'.$message.'","'.$type.'");');
	}
}

/**
 * read_folder_list
 * 
 * 폴더안의 폴더 리스트를 리턴
 * 
 * @param	string		$path		folder path
 */
function read_folder_list ($path) {
	$list = array();
	
	if ($handle = opendir($path)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != '.' && $entry != '..') {
				$list[] = $entry;
			}
		}
		
		closedir($handle);
	}
	
	return $list;
}

/**
 * read_file_list
 * 
 * 폴더 안의 파일의 리스트를 리턴
 * 
 * @param	string		$path			folder path
 * @param	array		$extension		extension list
 */
function read_file_list ($path,$extension) {
	$list = array();
	
	if ($handle = opendir($path)) {
		while (false !== ($file = readdir($handle))) {
			$pathinfo = pathinfo($path.$file);
			
			if ($file != '.' && $file != '..' && in_array($pathinfo['extension'],$extension) === TRUE) {
				$list[] = $file;
			}
		}
		
		closedir($handle);
	}
	
	return $list;
}

/**
 * write_prefix
 * 
 * 배열의 key에 prefix를 추가
 * 
 * @param	array		$data
 * @param	string		$prefix
 */
function write_prefix ($data,$prefix) {
	$result = array();
	
	foreach ($data as $key => $row) {
		$result[$prefix.$key] = $row;
	}
	
	return $result;
}

/**
 * delete_prefix
 * 
 * 배열의 key에 존재하는 prefix를 제거
 * 
 * @param	array		$data
 * @param	string		$prefix
 */
function delete_prefix ($data,$prefix) {
	$result = array();
	
	foreach ($data as $key => $row) {
		$result[preg_replace('/^'.$prefix.'(.+)/i','$1',$key)] = $row;
	}
	
	return $result;
}

/**
 * datetime
 * 
 * 시간 표시
 * 
 * @param	datatime	$datetime
 */
function datetime ($datetime) {
	if (strtotime($datetime) >= strtotime('-1 day')) {
		$datetime = date('H:i',strtotime($datetime));
	} else {
		$datetime = date('Y-m-d',strtotime($datetime));
	}
	
	return $datetime;
}
