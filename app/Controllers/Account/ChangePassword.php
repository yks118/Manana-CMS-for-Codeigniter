<?php
namespace App\Controllers\Account;

use App\Controllers\BaseController;

/**
 * Class ChangePassword
 *
 * @package App\Controllers\Account
 */
class ChangePassword extends BaseController
{
	public function index()
	{
		$data = [];

		if ($this->request->getMethod() === 'post')
		{
			$postData = $this->request->getPost();
			if (!isset($postData['password']) || empty($postData['password']))
				show_javascript('alert(\'Required : Password\');');
			elseif (!isset($postData['new_password']) || empty($postData['new_password']))
				show_javascript('alert(\'Required : New Password\');');
			elseif (!account()->checkPassword($postData['password']))
				show_javascript('alert(\'Error : Password\');');
			else
			{
				$mAccount = new \App\Models\Account();
				try
				{
					$mAccount->update(account()->id, [
						'password'  => $postData['new_password']
					]);
					return $this->response->redirect('change-password');
				}
				catch (\ReflectionException $e)
				{
					show_javascript('alert(\'Error : DB\');');
				}
			}
		}

		$this
			->html
			->addTitle('Account')
			->addTitle('Change Password')
		;
		return $this->_view('account/change-password', $data);
	}
}
