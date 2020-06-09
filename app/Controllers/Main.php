<?php
namespace App\Controllers;

/**
 * Class Main
 *
 * @package App\Controllers
 */
class Main extends BaseController
{
	public function index()
	{
		$data = [];

		return $this->_view('main', $data, [
			'cache' => 60
		]);
	}
}
