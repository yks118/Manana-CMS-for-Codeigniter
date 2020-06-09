<?php
namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Entities\Account as eAccount;

/**
 * Class SignIn
 *
 * @package App\Controllers\Account
 */
class SignIn extends BaseController
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
			else
			{
				/** @var eAccount $eAccount */
				$eAccount = $mAccount
					->where('username', $postData['username'])
					->find()[0]??null
				;

				if (!isset($eAccount))
					show_javascript('alert(\'Error : Sign In\');');
				elseif (!$eAccount->checkPassword($postData['password']))
					show_javascript('alert(\'Error : Sign In\');');
				else
				{
					// update account.last_login_at
					$mAccount
						->updateBatch(
							[
								[
									'id'            => $eAccount->id,
									'last_login_at' => date('Y-m-d H:i:s')
								]
							],
							'id'
						)
					;

					session()->set('account_id', $eAccount->id);
					return $this->response->redirect('/');
				}
			}
		}

		$this
			->html
			->addTitle('Account')
			->addTitle('Sign In')
		;
		return $this->_view('account/sign-in', $data);
	}
}
