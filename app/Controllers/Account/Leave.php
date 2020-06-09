<?php
namespace App\Controllers\Account;

use App\Controllers\BaseController;

/**
 * Class Leave
 *
 * @package App\Controllers\Account
 */
class Leave extends BaseController
{
	public function index()
	{
		$data = [];

		if ($this->request->getMethod() === 'post')
		{
			$password = $this->request->getPost('password');
			if (!isset($password) || empty($password))
				show_javascript('alert(\'Required : Password\');');
			elseif (!account()->checkPassword($password))
				show_javascript('alert(\'Error : Password\');');
			else
			{
				$mAccount = new \App\Models\Account();
				$mAccount->delete(account()->id);
				return $this->response->redirect('sign-out');
			}
		}

		$this
			->html
			->addTitle('Account')
			->addTitle('Leave')
		;
		return $this->_view('account/leave', $data);
	}
}
