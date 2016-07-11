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
			if ($entry != '.' && $entry != '..' && is_dir($path.$entry)) {
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
 * read_prefix_db
 * 
 * array로 된 배열을 필요한것만 가공해서 리턴 (DB에서 사용)
 * 
 * @param	array	$data
 * @param	array	$prefix
 */
function read_prefix_db ($data,$prefix) {
	$row = array();
	
	if (isset($data[$prefix.'_id'])) {
		foreach ($data as $key => $value) {
			if ($key && (strpos($key,$prefix) !== FALSE && strpos($key,$prefix) == 0)) {
				$row[preg_replace('/^[^_]+_(.+)/i','$1',$key)] = $value;
			}
		}
	} else {
		foreach ($data as $key => $value) {
			if ($key && !(strpos($key,$prefix) !== FALSE && strpos($key,$prefix) == 0)) {
				$row[preg_replace('/^[^_]+_(.+)/i','$1',$key)] = $value;
			}
		}
	}
	
	return $row;
}

/**
 * write_prefix_db
 * 
 * array로 된 배열을 prefix를 붙인 string으로 리턴 (DB에서 사용)
 * 
 * @param	array	$data
 * @param	array	$prefix
 */
function write_prefix_db ($data,$prefix) {
	$string = '';
	$prefixes = array();
	
	if (is_array($prefix)) {
		$prefixes = $prefix;
	} else {
		$prefixes[] = $prefix;
	}
	
	foreach ($data as $field) {
		foreach ($prefixes as $prefix) {
			$string .= ','.$prefix.'.'.$field.' AS '.$prefix.'_'.$field;
		}
	}
	
	return mb_substr($string,1);
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

/**
 * language
 * 
 * @param	string		$language
 * @param	numberic	$length
 */
function language ($language,$length = 2) {
	$lang = '';
	
	if ($length == 2) {
		$lang = substr($language,0,2);
	} else if ($length == 4) {
		
	}
	
	return $lang;
}

/**
 * language_data
 * 
 * data의 배열에서 language의 row를 리턴
 * 
 * @param	string		$language
 * @param	array		$data
 */
function language_data ($language,$result) {
	$data = array();
	
	foreach ($result as $key => $row) {
		if (empty($key)) {
			$data = $row;
		}
		
		if ($language == $row['language']) {
			$data = $row;
			break;
		}
	}
	
	return $data;
}

/**
 * html_path
 * 
 * html path로 리턴
 * 
 * @param	string		$path
 */
function html_path ($path) {
	$html_path = '';
	
	$html_path = base_url(substr($path,1));
	
	return $html_path;
}