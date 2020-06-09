<?php namespace App\Models;

/**
 * Class Account
 *
 * @package App\Models
 */
class Account extends BaseModel
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
		'username', 'password', 'name', 'last_login_at'
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
	protected $createdField = 'created_at';

	/**
	 * @var string $updatedField
	 */
	protected $updatedField = 'updated_at';

	/**
	 * @var string $deletedField
	 */
	protected $deletedField = 'deleted_at';

	/**
	 * @var bool $cache
	 */
	protected $cache = true;

	/**
	 * @var int $cacheTTL
	 */
	protected $cacheTTL = 1;

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
		{
			$encryption = new \App\Libraries\Encryption();
			$data['data']['password'] = $encryption->encodePassword($data['data']['password']);
		}
		return $data;
	}
}
