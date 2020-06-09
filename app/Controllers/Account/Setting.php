<?php
namespace App\Controllers\Account;

use App\Controllers\BaseController;

/**
 * Class Setting
 *
 * @package App\Controllers\Account
 */
class Setting extends BaseController
{
	public function index()
	{
		$data = [];

		if ($this->request->getMethod() === 'post')
		{
			$mAccount = new \App\Models\Account();

			try
			{
				$mAccount->update(account()->id, [
					'name'  => $this->request->getPost('name')
				]);
				return $this->response->redirect('setting');
			}
			catch (\ReflectionException $e)
			{
				show_javascript('alert(\'Error : DB\');');
			}
		}

		$this
			->html
			->addTitle('Account')
			->addTitle('Setting')
		;
		return $this->_view('account/setting', $data);
	}
}
