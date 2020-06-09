<?php
namespace App\Libraries;

/**
 * Class Encryption
 *
 * @package App\Libraries
 */
class Encryption
{
	/**
	 * @var string $key
	 */
	private $key = 'cms.manana.kr';

	/**
	 * encode
	 *
	 * @param   string  $password
	 * @param   string  $key
	 *
	 * @return  string
	 */
	public function encode(string $password, string $key = null): string
	{
		return bin2hex(openssl_encrypt(
			$password,
			'aes256',
			empty($key)?$this->key:$key,
			OPENSSL_RAW_DATA,
			'0123456789abcdef'
		));
	}

	/**
	 * encodePassword
	 *
	 * @param   string  $password
	 * @param   string  $key
	 *
	 * @return  string
	 */
	public function encodePassword(string $password, string $key = null): string
	{
		return $this
			->encode(
				password_hash($password, PASSWORD_DEFAULT),
				$key
			)
		;
	}

	/**
	 * decode
	 *
	 * @param   string  $password
	 * @param   string  $key
	 *
	 * @return  string
	 */
	public function decode(string $password, string $key = null): string
	{
		return openssl_decrypt(
			hex2bin($password),
			'aes256',
			empty($key)?$this->key:$key,
			OPENSSL_RAW_DATA,
			'0123456789abcdef'
		);
	}

	/**
	 * checkPassword
	 *
	 * @param   string  $password
	 * @param   string  $check_password
	 * @param   string  $key
	 *
	 * @return  bool
	 */
	public function checkPassword (string $password, string $check_password, string $key = null): bool
	{
		return password_verify($password, $this->decode($check_password, $key));
	}
}
