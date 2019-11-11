<?php namespace App\Entities;

use CodeIgniter\Entity;
use DateTime;

/**
 * Class Account
 *
 * @package App\Entities
 *
 * @property int $id
 * @property string $username
 * @property DateTime $reg_timestamp
 * @property DateTime $mod_timestamp
 * @property DateTime $del_timestamp
 */
class Account extends Entity
{
	/**
	 * @var array $casts
	 */
	protected $casts = [
		'id'                            => 'int',
		'username'                      => 'string',
		'password'                      => 'string',
		'reg_timestamp'                 => 'datetime',
		'mod_timestamp'                 => 'datetime',
		'del_timestamp'                 => 'datetime'
	];

	/**
	 * getPassword
	 *
	 * @return  void
	 */
	protected function getPassword(): void
	{}

	/**
	 * checkPassword
	 *
	 * @param   string  $password
	 *
	 * @return  bool
	 */
	public function checkPassword(string $password): bool
	{
		if (!isset($this->attributes['password']) || empty($this->attributes['password']))
			return false;
		return $password === \Config\Services::encrypter()->decrypt(base64_decode($this->attributes['password']));
	}
}
