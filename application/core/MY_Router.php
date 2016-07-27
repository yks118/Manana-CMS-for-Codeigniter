<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Router extends CI_Router {
	public function __construct () {
		parent::__construct();
		
		$uris = $admin_method = array();
		$uris = explode('/',trim(preg_replace('/([^\?]+).*/i','$1',$_SERVER['REQUEST_URI']),'/'));
		
		$admin_method[] = 'dashboard';
		$admin_method[] = 'install';
		$admin_method[] = 'site';
		$admin_method[] = 'updateSiteForm';
		$admin_method[] = 'menu';
		$admin_method[] = 'updateMenuForm';
		$admin_method[] = 'readMenuModelIdAjax';
		$admin_method[] = 'updateMenuIndexAjax';
		$admin_method[] = 'readMenuId';
		$admin_method[] = 'updateMenuHomeAjax';
		$admin_method[] = 'analytics';
		
		if ($uris[0] == 'admin' && isset($uris[1]) && !in_array($uris[1],$admin_method)) {
			$this->class = $uris[1];
			$this->method = (isset($uris[2]))?'admin_'.$uris[2]:'admin_index';
			
			$this->uri->rsegments = array_slice($this->uri->rsegments,1);
			$this->uri->rsegments[1] = $this->method;
		} else if ($uris[0] != 'admin') {
			// DB connection settings
			include_once APPPATH.'config/database.php';
			
			if (empty($db[$active_group]['dsn'])) {
				if ($db[$active_group]['dbdriver'] == 'mysqli') {
					$db[$active_group]['dsn'] = 'mysql:host='.$db[$active_group]['hostname'].';dbname='.$db[$active_group]['database'].';charset=utf8';
				}
			}
			
			$pdo = new PDO($db[$active_group]['dsn'],$db[$active_group]['username'],$db[$active_group]['password']);
			
			$query = $pdo->prepare('SELECT * FROM '.$db[$active_group]['dbprefix'].'site WHERE url = "'.$_SERVER['HTTP_HOST'].'" LIMIT 1');
			$query->execute();
			$site_data = $query->fetch();
			
			if (isset($site_data['site_id'])) {
				if (empty($uris[0])) {
					$query = $pdo->prepare('SELECT * FROM '.$db[$active_group]['dbprefix'].'site_menu WHERE site_id = '.$site_data['site_id'].' AND is_main = "t" LIMIT 1');
					$query->execute();
					$menu_data = $query->fetch();
				} else {
					$query = $pdo->prepare('SELECT * FROM '.$db[$active_group]['dbprefix'].'site_menu WHERE site_id = '.$site_data['site_id'].' AND uri = "'.$uris[0].'" LIMIT 1');
					$query->execute();
					$menu_data = $query->fetch();
				}
				
				if (isset($menu_data['id'])) {
					$this->class = $menu_data['model'];
					
					if (isset($uris[1]) && !preg_match('/^[^0-9]+$/i',$uris[1])) {
						$this->method = 'view';
						$this->uri->rsegments = array($menu_data['model'],'view',$uris[1]);
					}
				}
			}
			
			$pdo = NULL;
		}
	}
}