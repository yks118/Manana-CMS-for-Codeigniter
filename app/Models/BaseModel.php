<?php namespace App\Models;

use CodeIgniter\Model;

/**
 * Class BaseModel
 *
 * @package App\Models
 */
class BaseModel extends Model
{
	/**
	 * @var bool $cache
	 */
	protected $cache = false;

	/**
	 * @var bool $cacheRefresh
	 */
	protected $cacheRefresh = false;

	/**
	 * @var int $cacheTTL
	 */
	protected $cacheTTL = 60;

	/**
	 * @var string $cachePrefix
	 */
	protected $cachePrefix = 'DB_Cache_';

	/**
	 * getCacheKey
	 *
	 * @param   string  $suffix
	 *
	 * @return  string
	 */
	private function getCacheKey(string $suffix): string
	{
		$cacheKey = '';
		$cacheKey .= $this->cachePrefix;
		$cacheKey .= $this->table;
		$cacheKey .= '_';
		$cacheKey .= md5($this->builder()->getCompiledSelect(false));
		$cacheKey .= '_';
		$cacheKey .= $suffix;
		return $cacheKey;
	}

	/**
	 * setCacheTTL
	 *
	 * @param   int     $ttl
	 * @param   bool    $refresh
	 *
	 * @return  BaseModel
	 */
	public function setCacheTTL(int $ttl = null, bool $refresh = false): BaseModel
	{
		if (is_null($ttl))
			$ttl = $this->cacheTTL;

		$this->cache = $ttl > 0;
		$this->cacheRefresh = $refresh;
		$this->cacheTTL = $ttl;
		return $this;
	}

	/**
	 * find
	 *
	 * @param   mixed|array|null    $id One primary key or an array of primary keys
	 *
	 * @return  array|object|null   The resulting row of data, or null.
	 */
	public function find($id = null)
	{
		if ($this->cache)
		{
			$cacheKey = $this->getCacheKey('id_' . $id);

			if ($this->cacheRefresh === false)
			{
				$result = cache()->get($cacheKey);
				if (is_null($result) === false)
					return $result;
			}
		}

		$result = parent::find($id);
		if ($this->cache && isset($cacheKey))
			cache()->save($cacheKey, $result, $this->cacheTTL);

		return $result;
	}

	/**
	 * findColumn
	 *
	 * @param   string  $columnName
	 *
	 * @return  array|null  The resulting row of data, or null if no data found.
	 * @throws  \CodeIgniter\Database\Exceptions\DataException
	 */
	public function findColumn(string $columnName)
	{
		if ($this->cache)
		{
			$cacheKey = $this->getCacheKey('columnName_' . $columnName);

			if ($this->cacheRefresh === false)
			{
				$result = cache()->get($cacheKey);
				if (is_null($result) === false)
					return $result;
			}
		}

		$result = parent::findColumn($columnName);
		if ($this->cache && isset($cacheKey))
			cache()->save($cacheKey, $result, $this->cacheTTL);

		return $result;
	}

	/**
	 * findAll
	 *
	 * @param   int     $limit
	 * @param   int     $offset
	 *
	 * @return  array|null
	 */
	public function findAll(int $limit = 0, int $offset = 0)
	{
		if ($this->cache)
		{
			$cacheKey = $this->getCacheKey('limit_' . $limit . '_offset_' . $offset);

			if ($this->cacheRefresh === false)
			{
				$result = cache()->get($cacheKey);
				if (is_null($result) === false)
					return $result;
			}
		}

		$result = parent::findAll($limit, $offset);
		if ($this->cache && isset($cacheKey))
			cache()->save($cacheKey, $result, $this->cacheTTL);

		return $result;
	}
}
