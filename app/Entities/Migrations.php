<?php
namespace App\Entities;

use CodeIgniter\Entity;
use DateTime;

/**
 * Class Migrations
 *
 * @package App\Entities
 *
 * @property int $id
 * @property string $version
 * @property string $class
 * @property string $group
 * @property string $namespace
 * @property DateTime $time
 * @property int $batch
 */
class Migrations extends Entity
{
	/**
	 * @var array $casts
	 */
	protected $casts = [
		'id'        => 'int',
		'version'   => 'string',
		'class'     => 'string',
		'group'     => 'string',
		'namespace' => 'string',
		'time'      => 'datetime',
		'batch'     => 'int'
	];
}
