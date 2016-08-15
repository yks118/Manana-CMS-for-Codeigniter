<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (isset($data['text'])) {
	$this->model->type = 'text';
	echo $data['text'];
}

if (isset($data['json'])) {
	$this->model->type = 'json';
	echo json_encode($data['json']);
}

if (isset($data['js'])) {
	$this->model->type = 'js';
	echo js($data['js']);
} ?>