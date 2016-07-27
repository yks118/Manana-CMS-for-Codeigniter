<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model extends CI_Model {
	public $html = array();
	public $css = array();
	public $js = array();
	public $path = '';
	public $site = array();
	public $layout = '';
	public $menu = array();
	public $now_menu = array();
	public $color = array('#f44336','#03a9f4','#8bc34a','#ffeb3b','#009688','#4661ee','#ec5657','#1bcdd1','#8faabb','#b08beb','#3ea0dd','#f5a52a','#23bfaa','#faa586','#eb8cc6');
	
	public function __construct () {
		parent::__construct();
		
		if ($this->db->table_exists('site')) {
			$this->site = $this->read_site_url(base_url('/'));
		}
		
		// assets path
		$this->path = base_url('/assets/');
		
		// 기본 설정
		$this->_default();
		
		// 메뉴 설정
		$this->menu = $this->_menu($this->uri->segment(1));
	}
	
	/**
	 * _default
	 * 
	 * 기본설정
	 */
	private function _default () {
		// site setting
		$this->html['site_title'] = (isset($this->site['id']))?$this->site['name']:'Manana CMS';
		
		// css setting
		$this->css('//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css');
		$this->css('//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
		$this->css('//maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css');
		$this->css('//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css');
		$this->css($this->path.'/css/style.less');
		
		// js setting
		$this->js('//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js','header');
		$this->js('//code.jquery.com/ui/1.12.0/jquery-ui.min.js','footer');
		$this->js('//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js','footer');
		$this->js($this->path.'/js/bootstrap-notify/bootstrap-notify.js','footer');
		$this->js($this->path.'/js/autosize/autosize.js','footer');
		$this->js($this->path.'/js/jquery.cookie.js','footer');
		$this->js($this->path.'/js/common.js','footer');
	}
	
	/**
	 * _menu
	 * 
	 * 메뉴 설정
	 * 
	 * @param	string		$segment		uri first segment
	 * @param	numberic	$parent_id		ci_site_menu.parent_id
	 */
	private function _menu ($segment = '',$parent_id = 0) {
		$menu_data = $menu_list = array();
		
		if ($segment == 'admin') {
			// dashboard
			$menu_data['dashboard']['name'] = lang('admin_menu_dashboard');
			$menu_data['dashboard']['href'] = base_url('/admin/dashboard/');
			$menu_data['dashboard']['target'] = '_self';
			
			$menu_data['site']['name'] = lang('admin_menu_site');
			$menu_data['site']['href'] = base_url('/admin/site/');
			$menu_data['site']['target'] = '_self';
			
			$menu_data['menu']['name'] = lang('admin_menu_menu');
			$menu_data['menu']['href'] = base_url('/admin/menu/');
			$menu_data['menu']['target'] = '_self';
			
			$menu_data['analytics']['name'] = lang('admin_menu_analytics');
			$menu_data['analytics']['href'] = base_url('/admin/analytics/');
			$menu_data['analytics']['target'] = '_self';
		} else {
			$menu_list = $this->read_menu_list($parent_id);
			
			foreach ($menu_list as $row) {
				$menu_data[$row['uri']]['name'] = $row['name'];
				$menu_data[$row['uri']]['href'] = ($row['model'] == 'outpage')?$row['href']:base_url('/'.$row['uri'].'/');
				$menu_data[$row['uri']]['target'] = $row['target'];
				$menu_data[$row['uri']]['class'] = array();
				
				$menu_data[$row['uri']]['children'] = $this->_menu($segment,$row['site_menu_id']);
				
				foreach ($menu_data[$row['uri']]['children'] as $children) {
					if (in_array('active',$children['class'])) {
						$menu_data[$row['uri']]['class'][] = 'active';
						break;
					}
				}
				
				if (($segment == $row['uri']) || (empty($segment) && $row['is_main'] == 't')) {
					$this->now_menu = $row;
					$this->layout = $row['layout'];
					$menu_data[$row['uri']]['class'][] = 'active';
				}
			}
		}
		
		return $menu_data;
	}
	
	/**
	 * _read_menu_auth
	 * 
	 * ci_site_menu_auth에 명시된 권한들 리턴
	 * 
	 * @param	numberic	$menu_id	ci_site_menu_auth.site_menu_id
	 */
	private function _read_menu_auth ($menu_id) {
		$list = $result = array();
		
		$this->db->select('*');
		$this->db->from('site_menu_auth');
		$this->db->where('site_menu_id',$menu_id);
		$this->db->where('status','t');
		$this->db->order_by('id','ASC');
		$result = $this->db->get()->result_array();
		
		foreach ($result as $row) {
			$list[] = $row['site_member_grade_id'];
		}
		
		return $list;
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
	
	/**
	 * pagination
	 * 
	 * 페이지 설정
	 */
	public function pagination ($total,$per_page = 20) {
		// load library pagination
		$this->load->library('pagination');
		
		$pagination = '';
		$config = array();
		
		$config['base_url'] = base_url('/'.$_SERVER['REQUEST_URI']);
		$config['total_rows'] = $total;
		$config['per_page'] = $per_page;
		$config['use_page_numbers'] = TRUE;
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'page';
		
		$config['total_num_link'] = 10;
		
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['cur_tag_open'] = '<li class="active"><a>';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['prev_link'] = '&lsaquo;';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_link'] = '&rsaquo;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['first_link'] = '&laquo;';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = '&raquo;';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$this->pagination->initialize($config);
		
		$pagination = $this->pagination->create_links();
		
		return $pagination;
	}
	
	/**
	 * site_language
	 * 
	 * site_language update
	 * 
	 * @param	array		$data		languages
	 * @param	numberic	$site_id	ci_site.id
	 */
	public function site_language ($data,$site_id = 0) {
		$result = $language_data = $languages = $insert_data = array();
		
		if (empty($site_id)) {
			$site_id = $this->site['site_id'];
		}
		
		// get Site Language
		$this->db->select('*');
		$this->db->from('site_language');
		$this->db->where('site_id',$site_id);
		$language_data = $this->db->get()->result_array();
		
		foreach ($language_data as $row) {
			$languages[] = $row['language'];
			
			if (!in_array($row['language'],$data)) {
				$this->db->where('id',$row['language']);
				$this->db->delete('site_language');
			}
		}
		
		foreach ($data as $language) {
			if (!in_array($language,$languages)) {
				$insert_data[] = array(
					'site_id'=>$site_id,
					'language'=>$language
				);
			}
		}
		
		if (count($insert_data)) {
			if ($this->db->insert_batch('site_language',$insert_data) > 0) {
				$result['status'] = TRUE;
				$result['message'] = lang('system_update_success');
			} else {
				$result['status'] = FALSE;
				$result['message'] = $this->db->_error_message();
				$result['number'] = $this->db->_error_number();
			}
		}
		
		return $result;
	}
	
	/**
	 * menu_auth
	 * 
	 * ci_site_menu_auth update
	 * 
	 * @param	array		$data
	 * @param	numberic	$menu_id
	 */
	public function menu_auth ($data,$menu_id) {
		$result = $auth_data = $insert_data = array();
		
		// update
		$this->db->set('status','f');
		$this->db->where('site_menu_id',$menu_id);
		$this->db->update('site_menu_auth');
		
		$this->db->select('*');
		$this->db->from('site_menu_auth');
		$this->db->where('site_menu_id',$menu_id);
		$result = $this->db->get()->result_array();
		
		foreach ($result as $row) {
			$auth_data[] = $row['site_member_grade_id'];
			
			if (in_array($row['site_member_grade_id'],$data)) {
				// update
				$this->db->set('status','t');
				$this->db->where('id',$row['id']);
				$this->db->update('site_menu_auth');
			}
		}
		
		foreach ($data as $value) {
			if (!in_array($value,$auth_data)) {
				// insert
				$insert_data[] = array(
					'site_menu_id'=>$menu_id,
					'site_member_grade_id'=>$value,
					'status'=>'t'
				);
			}
		}
		
		if (count($insert_data)) {
			// insert_batch
			$this->db->insert_batch('site_menu_auth',$insert_data);
		}
	}
	
	/**
	 * analytics_visitor
	 * 
	 * google analytics의 방문자 통계
	 * 
	 * @param	date	$start		YYYY-mm-dd
	 * @param	date	$end		YYYY-mm-dd
	 */
	public function analytics_visitor ($start,$end) {
		$new = $returning = 0;
		$now = $today = $tmp = '';
		$data = $result = $analytics_data = $visitor_data = $analytics_visitor_data = array();
		
		// load google api libraries
		$this->load->library('google_api');
		
		$analytics_data = $this->read_analytics();
		
		$this->db->select('*');
		$this->db->from('site_visitor');
		$this->db->where('date >=',$start);
		$this->db->where('date <=',$end);
		$this->db->order_by('date','ASC');
		$result = $this->db->get()->result_array();
		
		foreach ($result as $row) {
			$visitor_data[preg_replace('/-/i','',$row['date'])] = $row;
		}
		
		$today = date('Ymd');
		$now = $start = preg_replace('/-/i','',$start);
		$end = preg_replace('/-/i','',$end);
		$data['visitor'] = array();
		
		while ($now <= $end) {
			$new = $returning = 0;
			
			if (isset($analytics_data['id']) && $now <= $today) {
				if (isset($visitor_data[$now])) {
					$new = $visitor_data[$now]['new'];
					$returning = $visitor_data[$now]['returning'];
				} else if ($today != $now) {
					$tmp = preg_replace('/([0-9]{4})([0-9]{2})([0-9]{2})/i','$1-$2-$3',$now);
					$analytics_visitor_data = $this->google_api->analytics($analytics_data['view_id'],array('userType'),$tmp,$tmp);
					
					if (isset($analytics_visitor_data['userType'])) {
						if ($analytics_visitor_data['userType'][0]['dimensions'][0] == 'New Visitor') {
							$new = $analytics_visitor_data['userType'][0]['data'];
							$returning = (isset($analytics_visitor_data['userType'][1]))?$analytics_visitor_data['userType'][1]['data']:0;
						} else {
							$new = (isset($analytics_visitor_data['userType'][1]))?$analytics_visitor_data['userType'][1]['data']:0;
							$returning = $analytics_visitor_data['userType'][0]['data'];
						}
					}
					
					$this->db->set('site_analytics_id',$analytics_data['id']);
					$this->db->set('date',$tmp);
					$this->db->set('new',$new);
					$this->db->set('returning',$returning);
					$this->db->insert('site_visitor');
				} else {
					// today
					$tmp = preg_replace('/([0-9]{4})([0-9]{2})([0-9]{2})/i','$1-$2-$3',$now);
					$analytics_visitor_data = $this->cache->file->get('visitor_today');
					
					if (isset($analytics_visitor_data['date']) && $analytics_visitor_data['date'] == $now) {
						$new = $analytics_visitor_data['new'];
						$returning = $analytics_visitor_data['returning'];
					} else {
						$this->cache->file->delete('visitor_today');
						$analytics_visitor_data = $this->google_api->analytics($analytics_data['view_id'],array('userType'),$tmp,$tmp);
						
						if (isset($analytics_visitor_data['userType'])) {
							if ($analytics_visitor_data['userType'][0]['dimensions'][0] == 'New Visitor') {
								$new = $analytics_visitor_data['userType'][0]['data'];
								$returning = (isset($analytics_visitor_data['userType'][1]))?$analytics_visitor_data['userType'][1]['data']:0;
							} else {
								$new = (isset($analytics_visitor_data['userType'][1]))?$analytics_visitor_data['userType'][1]['data']:0;
								$returning = $analytics_visitor_data['userType'][0]['data'];
							}
						}
						
						$this->cache->file->save('visitor_today',array('site_analytics_id'=>$analytics_data['id'],'date'=>$now,'new'=>$new,'returning'=>$returning));
					}
				}
			}
			
			$data['visitor'][$now] = array('site_analytics_id'=>$analytics_data['id'],'date'=>$now,'new'=>$new,'returning'=>$returning);
			$now = date('Ymd',strtotime('+1 day',strtotime($now.'000000')));
		}
		
		return $data;
	}
	
	/**
	 * read_site_url
	 * 
	 * site 테이블을 url로 가져옴
	 * 
	 * @param	string		$url	site url
	 */
	public function read_site_url ($url) {
		$language = $this->config->item('language');
		$data = $site_member_grade_row = array();
		$url = preg_replace('/(https?:\/\/)([0-9.]+)(\/?)/i','$2',$url);
		
		// get DB
		$this->db->select(write_prefix_db($this->db->list_fields('site'),array('s','sj')));
		$this->db->from('site s');
		$this->db->join('site sj','s.site_id = sj.site_id AND sj.language = "'.$language.'"','LEFT');
		$this->db->like('s.url',$url,'both');
		$this->db->group_by('s.site_id');
		$this->db->limit(1);
		$data = read_prefix_db($this->db->get()->row_array(),'sj');
		
		// get site admin member grade id
		$this->db->select('*');
		$this->db->from('site_member_grade');
		$this->db->where('site_id',$data['site_id']);
		$this->db->order_by('id','ASC');
		$this->db->limit(1);
		$site_member_grade_row = $this->db->get()->row_array();
		
		$data['admin_grade_id'] = $site_member_grade_row['id'];
		
		// get favicon
		$data['favicon'] = $this->file->read_model('site_favicon',$data['site_id']);
		
		if (isset($data['favicon'][0])) {
			$data['favicon'] = $data['favicon'][0];
		}
		
		// get use language
		$data['use_language'] = $this->read_site_language($data['site_id']);
		
		return $data;
	}
	
	/**
	 * read_site_id
	 * 
	 * ci_site
	 * 
	 * @param	numberic	$id		ci_site.id
	 */
	public function read_site_id ($id) {
		$data = array();
		
		$this->db->select('*');
		$this->db->from('site');
		$this->db->where('id',$id);
		$this->db->limit(1);
		$data = $this->db->get()->row_array();
		
		return $data;
	}
	
	/**
	 * post_data
	 * 
	 * post로 넘어오는 data를 리패키징..
	 * 
	 * @param	string		$prefix		post prefix
	 * @param	string		$id			PK
	 */
	public function post_data ($prefix,$id = '') {
		$data = array();
		
		foreach ($this->input->post() as $key => $value) {
			if ($key && (strpos($key,$prefix) !== FALSE && strpos($key,$prefix) == 0) && ($key != $id)) {
				$data[$key] = $value;
			}
		}
		
		return $data;
	}
	
	/**
	 * read_total
	 * 
	 * site table rows
	 */
	public function read_total () {
		return $this->db->count_all('site');
	}
	
	/**
	 * read_model_auth
	 * 
	 * site model auth
	 * 
	 * @param	string		$model		model name
	 * @param	numberic	$model_id	model pk
	 * @param	numberic	$site_id	site id
	 */
	public function read_model_auth ($model,$model_id,$site_id = 0) {
		$data = $result = array();
		
		// check ci_site.id
		if (empty($site_id)) {
			$site_id = $this->site['site_id'];
		}
		
		$this->db->select('*');
		$this->db->from('model_auth');
		$this->db->where('site_id',$site_id);
		$this->db->where('model',$model);
		$this->db->where('model_id',$model_id);
		$result = $this->db->get()->result_array();
		
		foreach ($result as $row) {
			if ($row['status'] == 't') {
				$data[$row['action']][] = $row['site_member_grade_id'];
			}
		}
		
		return $data;
	}
	
	/**
	 * read_site_language
	 * 
	 * ci_site_language
	 * 
	 * @param	numberic	$site_id	ci_site.id
	 */
	public function read_site_language ($site_id = 0) {
		$result = $languages = array();
		
		if (empty($site_id)) {
			$site_id = $this->site['site_id'];
		}
		
		$this->db->select('*');
		$this->db->from('site_language');
		$this->db->where('site_id',$site_id);
		$result = $this->db->get()->result_array();
		
		foreach ($result as $row) {
			$languages[] = $row['language'];
		}
		
		return $languages;
	}
	
	/**
	 * read_menu_id
	 * 
	 * ci_site_menu의 row를 리턴
	 * 
	 * @param	numberic	$menu_id	ci_site_menu.site_menu_id
	 */
	public function read_menu_id ($id,$site_id = 0,$language = '') {
		$data = array();
		
		if (empty($site_id)) {
			$site_id = $this->site['site_id'];
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		$this->db->select(write_prefix_db($this->db->list_fields('site_menu'),array('m','mj')));
		$this->db->from('site_menu m');
		$this->db->join('site_menu mj','m.site_menu_id = mj.site_menu_id AND mj.language = "'.$language.'"','LEFT');
		$this->db->where('m.site_id',$site_id);
		$this->db->where('m.site_menu_id',$id);
		$this->db->group_by('m.site_menu_id');
		$this->db->limit(1);
		$data = read_prefix_db($this->db->get()->row_array(),'mj');
		
		$data['grade'] = $this->_read_menu_auth($id);
		
		return $data;
	}
	
	/**
	 * read_menu_list
	 * 
	 * 사이트 메뉴 리스트 리턴
	 * 
	 * @param	numberic	$parent_id		ci_site_menu.parent_id
	 * @param	numberic	$site_id		ci_site.site_id
	 * @param	string		$language
	 */
	public function read_menu_list ($parent_id = 0,$site_id = 0,$language = '') {
		$list = $result = array();
		
		if (empty($site_id)) {
			$site_id = $this->site['site_id'];
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		$this->db->select(write_prefix_db($this->db->list_fields('site_menu'),array('m','mj')));
		$this->db->from('site_menu m');
		$this->db->join('site_menu mj','m.site_menu_id = mj.site_menu_id AND mj.language = "'.$language.'"','LEFT');
		$this->db->where('m.site_id',$site_id);
		$this->db->where('m.parent_id',$parent_id);
		$this->db->group_by('m.site_menu_id');
		$this->db->order_by('m.index','ASC');
		$result = $this->db->get()->result_array();
		
		foreach ($result as $key => $row) {
			$row = read_prefix_db($row,'mj');
			$row['grade'] = $this->_read_menu_auth($row['site_menu_id']);
			$row['children'] = $this->read_menu_list($row['site_menu_id'],$site_id,$language);
			
			$list[] = $row;
		}
		
		return $list;
	}
	
	/**
	 * read_analytics
	 * 
	 * get google analytics info
	 * 
	 * @param	numberic	$site_id		ci_site.site_id
	 */
	public function read_analytics ($site_id = 0) {
		$data = array();
		
		if (empty($site_id)) {
			$site_id = $this->site['site_id'];
		}
		
		$this->db->select('*');
		$this->db->from('site_analytics');
		$this->db->where('site_id',$site_id);
		$this->db->limit(1);
		$data = $this->db->get()->row_array();
		
		return $data;
	}
	
	/**
	 * write_data
	 * 
	 * site 생성
	 * 
	 * @param	array	$data
	 */
	public function write_data ($data) {
		$result = $site_member_grade_data = array();
		$result['status'] = FALSE;
		
		if (!isset($data['language'])) {
			$data['language'] = $this->config->item('language');
		}
		
		if (!isset($data['member_id'])) {
			if ($this->member->read_total() == 1) {
				$data['member_id'] = 1;
			} else if (isset($this->member->data['id'])) {
				$data['member_id'] = $this->member->data['id'];
			} else {
				$data['message'] = lang('member_login_required');
				return $result;
			}
		}
		
		if (!isset($data['default_language'])) {
			$data['default_language'] = $data['language'];
		}
		
		// insert ci_site
		if ($this->db->insert('site',$data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('site_write_success');
			$result['insert_id'] = $this->db->insert_id();
			
			if (!isset($data['site_id'])) {
				// site member grade admin
				$site_member_grade_data[0]['site_id'] = $result['insert_id'];
				$site_member_grade_data[0]['name'] = lang('member_grade_admin');
				$site_member_grade_data[0]['language'] = $this->config->item('language');
				$site_member_grade_data[0]['default'] = 'f';
				
				// site member grade normal
				$site_member_grade_data[1]['site_id'] = $result['insert_id'];
				$site_member_grade_data[1]['name'] = lang('member_grade_normal');
				$site_member_grade_data[1]['language'] = $this->config->item('language');
				$site_member_grade_data[1]['default'] = 't';
				
				// insert ci_site_member_grade
				$this->db->insert_batch('site_member_grade',$site_member_grade_data);
				
				// update ci_site_member_grade
				$this->db->set('site_member_grade_id','id');
				$this->db->where('site_id',$result['insert_id']);
				$this->db->update('site_member_grade');
				
				// update ci_site.site_id
				$this->update_data(array('site_id'=>$result['insert_id']),$result['insert_id']);
				
				// update ci_site_language
				$this->site_language(array($data['site_default_language']),$result['insert_id']);
			}
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * write_model_auth
	 * 
	 * 각 모델별 권한 설정
	 * 
	 * @param	array		$data		ci_model_auth row
	 * @param	string		$model		model name
	 * @param	numberic	$model_id	model pk
	 */
	public function write_model_auth ($data,$model,$model_id,$site_id = '') {
		$result = $model_auth_data = $insert = array();
		
		// check ci_site.id
		if (empty($site_id)) {
			$site_id = $this->site['site_id'];
		}
		
		foreach ($data as $key => $row) {
			foreach ($row as $value) {
				$model_auth_data[] = array(
					'site_id'=>$site_id,
					'site_member_grade_id'=>$value,
					'model'=>$model,
					'model_id'=>$model_id,
					'action'=>$key,
					'status'=>'t'
				);
			}
		}
		
		// insert ci_model_auth
		$insert = $this->db->insert_batch('model_auth',$model_auth_data);
		
		// error check
		if ($insert > 0) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_write_success');
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * write_menu
	 * 
	 * ci_site_menu insert
	 * 
	 * @param	array	$data
	 */
	public function write_menu ($data) {
		$total = 0;
		$result = array();
		
		if (!isset($data['site_id'])) {
			$data['site_id'] = $this->model->site['site_id'];
		}
		
		if (!isset($data['parent_id'])) {
			$data['parent_id'] = 0;
		}
		
		if (!isset($data['language'])) {
			$data['language'] = $this->config->item('language');
		}
		
		// total
		$this->db->where('parent_id',$data['parent_id']);
		$this->db->where('site_id',$data['site_id']);
		$this->db->group_by('site_menu_id');
		$total = $this->db->get('site_menu')->num_rows();
		
		$data['index'] = 1 + $total;
		
		if ($this->db->insert('site_menu',$data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_write_success');
			$result['insert_id'] = $this->db->insert_id();
			
			if (!isset($data['site_menu_id']) || empty($data['site_menu_id'])) {
				$this->db->set('site_menu_id',$result['insert_id']);
				$this->db->where('id',$result['insert_id']);
				$this->db->update('site_menu');
			}
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * write_analytics
	 * 
	 * insert google analytics view_id
	 * 
	 * @param	array	$data
	 */
	public function write_analytics ($data) {
		$result = array();
		
		if (!isset($data['site_id'])) {
			$data['site_id'] = $this->site['site_id'];
		}
		
		if ($this->db->insert('site_analytics',$data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_write_success');
			$result['insert_id'] = $this->db->insert_id();
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * update_model_auth
	 * 
	 * 각 모델별 권한 설정을 수정
	 * 
	 * @param	array		$data		ci_model_auth row
	 * @param	string		$model		model name
	 * @param	numberic	$model_id	model pk
	 */
	public function update_model_auth ($data,$model,$model_id,$site_id = '') {
		$action = '';
		$result = $model_auth_result = $model_auth_data = $insert_data = $update_data = array();
		
		$result['status'] = TRUE;
		
		// check ci_site.id
		if (empty($site_id)) {
			$site_id = $this->site['site_id'];
		}
		
		foreach ($data as $key => $row) {
			if (strpos($key,'model_auth_') !== FALSE && strpos($key,'model_auth_') == 0) {
				$action = str_replace('model_auth_','',$key);
				
				// update
				$this->db->set('status','f');
				$this->db->where('site_id',$site_id);
				$this->db->where('model',$model);
				$this->db->where('model_id',$model_id);
				$this->db->where('action',$action);
				if ($this->db->update('model_auth')) {
					$result['status'] = TRUE;
					
					// get DB
					$this->db->select('*');
					$this->db->from('model_auth');
					$this->db->where('site_id',$site_id);
					$this->db->where('model',$model);
					$this->db->where('model_id',$model_id);
					$this->db->where('action',$action);
					$model_auth_result = $this->db->get()->result_array();
					
					foreach ($model_auth_result as $model_auth_key => $model_auth_row) {
						$model_auth_data[$model_auth_row['site_member_grade_id']] = $model_auth_row['id'];
					}
					
					foreach ($row as $value) {
						if (isset($model_auth_data[$value])) {
							// update
							$update_data[] = array(
								'id'=>$model_auth_data[$value],
								'status'=>'t'
							);
						} else {
							// insert
							$insert_data[] = array(
								'site_id'=>$site_id,
								'site_member_grade_id'=>$value,
								'model'=>$model,
								'model_id'=>$model_id,
								'action'=>$action,
								'status'=>'t'
							);
						}
					}
					
					// insert_batch
					if (count($insert_data)) {
						$this->db->insert_batch('model_auth');
					}
					
					// update_batch
					if (count($update_data)) {
						$this->db->update_batch('model_auth',$update_data,'id');
					}
				} else {
					$result['status'] = FALSE;
					$result['message'] = $this->db->_error_message();
					$result['number'] = $this->db->_error_number();
					
					break;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * update_data
	 * 
	 * ci_site 업데이트
	 * 
	 * @param	array		$data
	 * @param	numberic	$id			ci_site.id
	 */
	public function update_data ($data,$id) {
		$result = array();
		
		if (!isset($data['language'])) {
			$data['language'] = $this->config->item('language');
		}
		
		$this->db->where('id',$id);
		if ($this->db->update('site',$data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_update_success');
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * update_menu
	 * 
	 * ci_site_menu update
	 * 
	 * @param	array		$data
	 * @param	numberic	$id			ci_site_menu.site_menu_id
	 * @param	numberic	$site_id	ci_site.site_id
	 * @param	string		$language
	 */
	public function update_menu ($data,$id,$site_id = 0,$language = '') {
		$result = array();
		
		if (empty($site_id)) {
			$site_id = $this->site['site_id'];
		}
		
		if (empty($language)) {
			$language = $this->config->item('language');
		}
		
		$this->db->where('site_menu_id',$id);
		$this->db->where('site_id',$site_id);
		$this->db->where('language',$language);
		if ($this->db->update('site_menu',$data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_update_success');
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * update_analytics
	 * 
	 * update google analytics view_id
	 * 
	 * @param	array		$data
	 * @param	numberic	$id			ci_site_analytics.id
	 * @param	numberic	$site_id	ci_site.site_id
	 */
	public function update_analytics ($data,$id,$site_id = 0) {
		$result = array();
		
		if (empty($site_id)) {
			$site_id = $this->site['site_id'];
		}
		
		$this->db->where('id',$id);
		$this->db->where('site_id',$site_id);
		if ($this->db->update('site_analytics',$data)) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_update_success');
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * delete_model_auth
	 * 
	 * 모델 권한 설정 삭제
	 * 
	 * @param	string		$model		model name
	 * @param	numberic	$model_id	model pk
	 * @param	numberic	$site_id	ci_site.id
	 */
	public function delete_model_auth ($model,$model_id,$site_id = 0) {
		$result = array();
		
		// check site_id
		if (empty($site_id)) {
			$site_id = $this->site['site_id'];
		}
		
		// delete DB
		$this->db->where('site_id',$site_id);
		$this->db->where('model',$model);
		$this->db->where('model_id',$model_id);
		if ($this->db->delete('model_auth')) {
			$result['status'] = TRUE;
			$result['message'] = lang('system_delete_success');
		} else {
			$result['status'] = FALSE;
			$result['message'] = $this->db->_error_message();
			$result['number'] = $this->db->_error_number();
		}
		
		return $result;
	}
	
	/**
	 * install
	 * 
	 * site DB 설치
	 * 
	 * @param	string		$flag		true / false
	 * @return	string		$return		true / false
	 */
	public function install ($flag = TRUE) {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		// site table
		if (!$this->db->table_exists('site')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'url'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'name'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'description'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'keywords'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'author'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'mobile_view'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1
					),
					'robots'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1
					),
					'login'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'language'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'member_id'=>array(
						'type'=>'INT',
						'constraint'=>11
					),
					'default_editor'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255,
						'default'=>'ckeditor'
					),
					'default_language'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('site');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// site_member_grade table
		if (!$this->db->table_exists('site_member_grade')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'site_member_grade_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'default'=>0
					),
					'name'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'language'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'default'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('site_member_grade');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// site_language table
		if (!$this->db->table_exists('site_language')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'language'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('site_language');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// model_admin table
		if (!$this->db->table_exists('model_admin')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'model'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'model_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'member_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('model_admin');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// model_auth table
		if (!$this->db->table_exists('model_auth')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'site_member_grade_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'model'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'model_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'action'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'status'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('model_auth');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// site_menu table
		if (!$this->db->table_exists('site_menu')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'site_menu_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'parent_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'default'=>0
					),
					'name'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'uri'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'model'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'model_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'href'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'layout'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255,
						'default'=>'basic'
					),
					'target'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					),
					'index'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'is_main'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1,
						'default'=>'f'
					),
					'language'=>array(
						'type'=>'VARCHAR',
						'constraint'=>255
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('site_menu');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// site_menu_auth table
		if (!$this->db->table_exists('site_menu_auth')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'site_menu_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'site_member_grade_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'status'=>array(
						'type'=>'VARCHAR',
						'constraint'=>1,
						'default'=>'t'
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('site_menu_auth');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		// site_analytics
		if (!$this->db->table_exists('site_analytics')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'site_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'view_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('site_analytics');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		if (!$this->db->table_exists('site_visitor')) {
			if ($flag) {
				$fields = array(
					'id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE,
						'auto_increment'=>TRUE
					),
					'site_analytics_id'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'date'=>array(
						'type'=>'DATE'
					),
					'new'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					),
					'returning'=>array(
						'type'=>'INT',
						'constraint'=>11,
						'unsigned'=>TRUE
					)
				);
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id',TRUE);
				$return = $this->dbforge->create_table('site_visitor');
			} else if (!$return) {
				$return = TRUE;
			}
		}
		
		return $return;
	}
	
	/**
	 * uninstall
	 * 
	 * site DB 삭제
	 * 
	 * @return	string	true / false
	 */
	public function uninstall () {
		$return = FALSE;
		
		// dbforge load
		$this->load->dbforge();
		
		$return = $this->dbforge->drop_table('site');
		if ($return) {
			$this->dbforge->drop_table('site_member_grade');
			$this->dbforge->drop_table('site_language');
			$this->dbforge->drop_table('model_admin');
			$this->dbforge->drop_table('model_auth');
			$this->dbforge->drop_table('site_menu');
			$this->dbforge->drop_table('site_menu_auth');
			$this->dbforge->drop_table('site_analytics');
			$this->dbforge->drop_table('site_visitor');
		}
		
		return $return;
	}
}