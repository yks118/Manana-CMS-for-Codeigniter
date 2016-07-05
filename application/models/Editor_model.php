<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Editor_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * _ckeditor
	 * 
	 * ckeditor javascript setting
	 * 
	 * @param	array		$ids
	 * @param	string		$language
	 */
	private function _ckeditor ($ids,$language,$inline) {
		$javascript = '';
		
		// set javascript
		if ($inline) {
			foreach ($ids as $id) {
				$javascript .= '
					CKEDITOR.disableAutoInline = true;
					CKEDITOR.inline("'.$id.'",{
						toolbar: [
							["Styles","Format","Font","FontSize"],
							["Bold","Italic","Underline","StrikeThrough","-","Undo","Redo","-","Cut","Copy","Paste","Find","Replace","-","Outdent","Indent","-","Print"],
							["NumberedList","BulletedList","-","JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock"],
							["Image","Table","-","Link","TextColor","BGColor","Source"]
						],
						language: "'.$language.'"
					});
				';
			}
		} else {
			foreach ($ids as $id) {
				$javascript .= '
					CKEDITOR.replace("'.$id.'",{
						toolbar: [
							["Styles","Format","Font","FontSize"],
							["Bold","Italic","Underline","StrikeThrough","-","Undo","Redo","-","Cut","Copy","Paste","Find","Replace","-","Outdent","Indent","-","Print"],
							["NumberedList","BulletedList","-","JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock"],
							["Image","Table","-","Link","TextColor","BGColor","Source"]
						],
						language: "'.$language.'"
					});
				';
			}
		}
		
		$javascript .= '
			/**
			 * write_editor_html
			 * 
			 * insert html
			 * 
			 * @param	{string}	id
			 * @param	{string}	html
			 */
			function write_editor_html (id,html) {
				CKEDITOR.instances[id].insertHtml(html);
			}
		';
		
		return $javascript;
	}
	
	/**
	 * read_list
	 * 
	 * return editor list
	 */
	public function read_list () {
		$list = array();
		
		$list = read_folder_list('./assets/editor/');
		
		return $list;
	}
	
	/**
	 * write_js
	 * 
	 * js 설정 로드
	 * 
	 * @param	array|string	$id
	 * @param	string			$inline			true / false
	 * @param	string			$cdn			use cdn true / false
	 * @param	string			$editor			use editor name
	 */
	public function write_js ($id,$inline = FALSE,$cdn = TRUE,$editor = '') {
		$language = $javascript = $js = '';
		$ids = array();
		
		$language = language($this->config->item('language'),2);
		
		// set ids array
		if (is_array($id)) {
			$ids = $id;
		} else {
			$ids[] = $id;
		}
		
		if (empty($editor)) {
			$editor = $this->model->site['default_editor'];
		}
		
		switch ($editor) {
			case 'ckeditor' :
					// set js
					if ($cdn) {
						$javascript = '//cdn.ckeditor.com/4.5.9/standard/ckeditor.js';
					} else {
						$javascript = $this->model->path.'/editor/ckeditor/ckeditor.js';
					}
					
					$js = $this->_ckeditor($ids,$language,$inline);
				break;
		}
		
		$this->model->js($javascript,'header');
		
		return $js;
	}
}