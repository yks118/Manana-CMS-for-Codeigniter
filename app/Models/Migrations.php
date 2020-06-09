<?php
namespace App\Models;

/**
 * Class Migrations
 *
 * @package App\Models
 */
class Migrations extends BaseModel
{
	/**
	 * @var string $table
	 */
	protected $table = 'migrations';

	/**
	 * @var string $primaryKey
	 */
	protected $primaryKey = 'id';

	/**
	 * @var array $allowedFields
	 */
	protected $allowedFields = [];

	/**
	 * @var string $returnType
	 */
	protected $returnType = 'App\Entities\Migrations';

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
	protected $createdField = '';

	/**
	 * @var string $updatedField
	 */
	protected $updatedField = '';

	/**
	 * @var string $deletedField
	 */
	protected $deletedField = '';

	/**
	 * @var bool $cache
	 */
	protected $cache = true;

	/**
	 * @var int $cacheTTL
	 */
	protected $cacheTTL = 60;

	/**
	 * @var array $beforeInsert
	 */
	protected $beforeInsert = [];

	/**
	 * @var array $beforeUpdate
	 */
	protected $beforeUpdate = [];
}
