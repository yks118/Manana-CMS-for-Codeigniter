<?php
namespace App\Controllers\Account;

use App\Controllers\BaseController;

/**
 * Class SignOut
 *
 * @package App\Controllers\Account
 */
class SignOut extends BaseController
{
	public function index()
	{
		session()->remove('account_id');
		return $this->response->redirect('/');
	}
}
