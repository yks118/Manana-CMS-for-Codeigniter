<?php namespace App\Controllers\Samples;

/**
 * Class Api
 *
 * @package App\Controllers\Samples
 */
class Api extends BaseController
{
	public function index()
	{
		$data = [
			'status'    => true,
			'data'      => [
				'param1'    => 'value1',
				'param2'    => 'value2',
				'param3'    => 'value3'
			]
		];

		// $mAccount = new \App\Models\Account();
		/** @var \App\Entities\Account $eAccount */
		/*
		$eAccount = $mAccount->find(1);
		if (!isset($eAccount))
		{
			// Return HTML Response Code : Not Found Data
			return $this->response->setStatusCode(412, 'Not Data.');
		}
		*/

		// set page cache : 1 seconds
		// $this->cachePage(1);
		return $this->_view('', $data);
	}
}
