<?php
namespace App\Entities;

use CodeIgniter\Entity;
use DateTime;

/**
 * Class Account
 *
 * @package App\Entities
 *
 * @property int $id
 * @property string $username
 * @property string $name
 * @property DateTime $last_login_at
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property DateTime $deleted_at
 */
class Account extends Entity
{
	/**
	 * @var array $casts
	 */
	protected $casts = [
		'id'            => '?int',
		'username'      => '?string',
		'password'      => '?string',
		'name'          => '?string',
		'last_login_at' => '?datetime',
		'created_at'    => '?datetime',
		'updated_at'    => '?datetime',
		'deleted_at'    => '?datetime'
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

		$encryption = new \App\Libraries\Encryption();
		return $encryption->checkPassword($password, $this->attributes['password']);
	}
}
