<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system'][] = array(
	'class'=>'manana_hook',
	'function'=>'pre_system',
	'filename'=>'manana_hook.php',
	'filepath'=>'hooks'
);

$hook['pre_controller'][] = array(
	'class'=>'manana_hook',
	'function'=>'pre_controller',
	'filename'=>'manana_hook.php',
	'filepath'=>'hooks'
);

$hook['post_controller_constructor'][] = array(
	'class'=>'manana_hook',
	'function'=>'post_controller_constructor',
	'filename'=>'manana_hook.php',
	'filepath'=>'hooks'
);

$hook['display_override'][] = array(
	'class'=>'manana_hook',
	'function'=>'display_override',
	'filename'=>'manana_hook.php',
	'filepath'=>'hooks'
);
