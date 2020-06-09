<?php namespace App\Controllers\Samples;

/**
 * Class Form
 *
 * @package App\Controllers\Samples
 */
class Form extends BaseController
{
	public function index()
	{
		$data = [];

		// $get = session()->get('key');

		if ($this->request->getMethod() === 'post')
		{
			$postData = $this->request->getPost();
			if (isset($postData['username'], $postData['password']))
			{
				$mAccount = new \App\Models\Account();
				/** @var \App\Entities\Account $eAccount */
				$eAccount = $mAccount->where('username', $postData['username'])->limit(1)->find()[0]??null;
				if (!isset($eAccount))
				{
					// not found username
				}
				elseif (!$eAccount->checkPassword($postData['password']))
				{
					// not match password
				}
				else
				{
					// success login
					// session()->set('key', 'value');
					return $this->response->redirect('/');
				}
			}
		}

		$this
			->html
			->addTitle('Samples')
			->addTitle('Form')
		;
		return $this->_view('samples/form');
	}
}
