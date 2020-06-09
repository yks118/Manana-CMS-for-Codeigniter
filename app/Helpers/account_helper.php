<?php
use App\Entities\Account as eAccount;

if (!function_usable('account'))
{
	/**
	 * account
	 *
	 * @return  eAccount
	 */
	function account(): eAccount
	{
		return \Config\Services::account();
	}
}

if (!function_usable('account_is_login'))
{
	/**
	 * account_is_login
	 *
	 * @return  bool
	 */
	function account_is_login(): bool
	{
		return intval(account()->id) > 0;
	}
}
