<?php
namespace App\Controllers\Account;

use App\Controllers\BaseController;

/**
 * Class SignUp
 *
 * @package App\Controllers\Account
 */
class SignUp extends BaseController
{
	public function index()
	{
		$data = [];

		if ($this->request->getMethod() === 'post')
		{
			$mAccount = new \App\Models\Account();

			$postData = $this->request->getPost();
			if (!isset($postData['username']) || empty($postData['username']))
				show_javascript('alert(\'Required : Username\');');
			elseif (!isset($postData['password']) || empty($postData['password']))
				show_javascript('alert(\'Required : Password\');');
			elseif ($mAccount->where('username', $postData['username'])->countAllResults() > 0)
				show_javascript('alert(\'Not Unique : Username\');');
			else
			{
				$eAccount = new \App\Entities\Account($postData);
				try
				{
					$mAccount->insert($eAccount);
					return $this->response->redirect('sign-in');
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
			->addTitle('Sign Up')
		;
		return $this->_view('account/sign-up', $data);
	}
}
