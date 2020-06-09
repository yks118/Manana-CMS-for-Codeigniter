<?php
namespace App\Controllers;

/**
 * Class DBMigration
 *
 * @package App\Controllers
 */
class DBMigration extends BaseController
{
	public function index()
	{
		$data = [];

		$migrate = \Config\Services::migrations();
		$migrate->latest();

		$mMigrations = new \App\Models\Migrations();
		$data['list'] = $mMigrations
			->orderBy('id', 'DESC')
			->findAll(5, 0)
		;

		return $this->_view('db-migrate', $data);
	}
}
