<?php namespace App\Models;

use CodeIgniter\Model;

/**
 * Class Account
 *
 * @package App\Models
 */
class Account extends Model
{
	/**
	 * @var string $table
	 */
	protected $table = 'account';

	/**
	 * @var string $primaryKey
	 */
	protected $primaryKey = 'id';

	/**
	 * @var array $allowedFields
	 */
	protected $allowedFields = [
		'username', 'password'
	];

	/**
	 * @var string $returnType
	 */
	protected $returnType = 'App\Entities\Account';

	/**
	 * @var string $dateFormat
	 */
	protected $dateFormat = 'datetime';

	/**
	 * @var bool $useTimestamps
	 */
	protected $useTimestamps = true;

	/**
	 * @var string $createdField
	 */
	protected $createdField = 'reg_timestamp';

	/**
	 * @var string $updatedField
	 */
	protected $updatedField = 'mod_timestamp';

	/**
	 * @var string $deletedField
	 */
	protected $deletedField = 'del_timestamp';

	/**
	 * @var array $beforeInsert
	 */
	protected $beforeInsert = [
		'hashPassword'
	];

	/**
	 * @var array $beforeUpdate
	 */
	protected $beforeUpdate = [
		'hashPassword'
	];

	/**
	 * hashPassword
	 *
	 * @param   array   $data
	 *
	 * @return  array
	 */
	protected function hashPassword(array $data): array
	{
		if (isset($data['data']['password']))
			$data['data']['password'] = base64_encode(\Config\Services::encrypter()->encrypt($data['data']['password']));
		return $data;
	}
}
