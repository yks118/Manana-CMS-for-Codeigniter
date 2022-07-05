<?php namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

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
	 * @var string $replicationGroup
	 */
	protected $replicationGroup = '';

	/**
	 * @var object|resource $replicationConnID
	 */
	protected $replicationConnID;

    public function __construct(?ConnectionInterface &$db = null, ?ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        if (is_cli())
        {
            $this->cache = false;
            $this->cacheRefresh = true;
            $this->cacheTTL = 0;
        }
    }

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
	 * getReplicationConnID
	 *
	 * @return object
	 */
	private function getReplicationConnID(): object
	{
		if (is_object($this->replicationConnID))
			return $this->replicationConnID;

		$db = db_connect($this->replicationGroup);
		$db->initialize();
		return $this->replicationConnID = $db->connID;
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
     * @param   bool                $master
	 *
	 * @return  array|object|null   The resulting row of data, or null.
	 */
	public function find($id = null, bool $master = false)
	{
        if ($this->cache)
        {
            $cacheKey = $this->getCacheKey('id_' . $id);

            if ($this->cacheRefresh === false)
            {
                $result = cache()->get($cacheKey);
                if (is_null($result) === false)
                {
                    $this->resetQuery();
                    return $result;
                }
            }
        }

        if ($master === true)
        {
            $connID = $this->db->connID;
            $this->db->connID = $this->getReplicationConnID();
            $result = parent::find($id);
            $this->db->connID = $connID;
        }
        else
        {
            $result = parent::find($id);
        }

		if ($this->cache && isset($cacheKey))
			cache()->save($cacheKey, $result, $this->cacheTTL);
		return $result;
	}

	/**
	 * findColumn
	 *
	 * @param   string  $columnName
     * @param   bool    $master
	 *
	 * @return  array|null  The resulting row of data, or null if no data found.
	 * @throws  \CodeIgniter\Database\Exceptions\DataException
	 */
	public function findColumn(string $columnName, bool $master = false)
	{
        if ($this->cache)
        {
            $cacheKey = $this->getCacheKey('columnName_' . $columnName);

            if ($this->cacheRefresh === false)
            {
                $result = cache()->get($cacheKey);
                if (is_null($result) === false)
                {
                    $this->resetQuery();
                    return $result;
                }
            }
        }

        if ($master === true)
        {
            $connID = $this->db->connID;
            $this->db->connID = $this->getReplicationConnID();
            $result = parent::findColumn($columnName);
            $this->db->connID = $connID;
        }
        else
        {
            $result = parent::findColumn($columnName);
        }

		if ($this->cache && isset($cacheKey))
			cache()->save($cacheKey, $result, $this->cacheTTL);
		return $result;
	}

	/**
	 * findAll
	 *
	 * @param   int     $limit
	 * @param   int     $offset
     * @param   bool    $master
	 *
	 * @return  array|null
	 */
	public function findAll(int $limit = 0, int $offset = 0, bool $master = false)
	{
        if ($this->cache)
        {
            $cacheKey = $this->getCacheKey('limit_' . $limit . '_offset_' . $offset);

            if ($this->cacheRefresh === false)
            {
                $result = cache()->get($cacheKey);
                if (is_null($result) === false)
                {
                    $this->resetQuery();
                    return $result;
                }
            }
        }

        if ($master === true)
        {
            $connID = $this->db->connID;
            $this->db->connID = $this->getReplicationConnID();
            $result = parent::findAll($limit, $offset);
            $this->db->connID = $connID;
        }
        else
        {
            $result = parent::findAll($limit, $offset);
        }

		if ($this->cache && isset($cacheKey))
			cache()->save($cacheKey, $result, $this->cacheTTL);
		return $result;
	}

	/**
	 * insert
	 *
	 * @param   array|object    $data
	 * @param   boolean         $returnID   Whether insert ID should be returned or not.
	 *
	 * @return  int|string|boolean
	 * @throws  \ReflectionException
	 */
	public function insert($data = null, bool $returnID = true)
	{
		if (empty($this->replicationGroup))
			return parent::insert($data, $returnID);

		$connID = $this->db->connID;
		$this->db->connID = $this->getReplicationConnID();
		$result = parent::insert($data, $returnID);
		$this->db->connID = $connID;
		return $result;
	}

	/**
	 * insertBatch
	 *
	 * @param   array   $set        An associative array of insert values
	 * @param   boolean $escape     Whether to escape values and identifiers
	 * @param   int     $batchSize
	 * @param   boolean $testing
	 *
	 * @return  int|boolean Number of rows inserted or FALSE on failure
	 */
	public function insertBatch(array $set = null, bool $escape = null, int $batchSize = 100, bool $testing = false)
	{
		if (empty($this->replicationGroup))
			return parent::insertBatch($set, $escape, $batchSize, $testing);

		$connID = $this->db->connID;
		$this->db->connID = $this->getReplicationConnID();
		$result = parent::insertBatch($set, $escape, $batchSize, $testing);
		$this->db->connID = $connID;
		return $result;
	}

	/**
	 * update
	 *
	 * @param   int|array|string    $id
	 * @param   array|object        $data
	 *
	 * @return  boolean
	 * @throws  \ReflectionException
	 */
	public function update($id = null, $data = null): bool
	{
		if (empty($this->replicationGroup))
			return parent::update($id, $data);

		$connID = $this->db->connID;
		$this->db->connID = $this->getReplicationConnID();
		$result = parent::update($id, $data);
		$this->db->connID = $connID;
		return $result;
	}

	/**
	 * updateBatch
	 *
	 * @param   array   $set        An associative array of update values
	 * @param   string  $index      The where key
	 * @param   int     $batchSize  The size of the batch to run
	 * @param   boolean $returnSQL  True means SQL is returned, false will execute the query
	 *
	 * @return  mixed   Number of rows affected or FALSE on failure
	 * @throws  \CodeIgniter\Database\Exceptions\DatabaseException
	 */
	public function updateBatch(array $set = null, string $index = null, int $batchSize = 100, bool $returnSQL = false)
	{
		if (empty($this->replicationGroup))
			return parent::updateBatch($set, $index, $batchSize, $returnSQL);

		$connID = $this->db->connID;
		$this->db->connID = $this->getReplicationConnID();
		$result = parent::updateBatch($set, $index, $batchSize, $returnSQL);
		$this->db->connID = $connID;
		return $result;
	}

	/**
	 * delete
	 *
	 * @param   int|array|null  $id     The rows primary key(s)
	 * @param   boolean         $purge  Allows overriding the soft deletes setting.
	 *
	 * @return  mixed
	 * @throws  \CodeIgniter\Database\Exceptions\DatabaseException
	 */
	public function delete($id = null, bool $purge = false)
	{
		if (empty($this->replicationGroup))
			return parent::delete($id, $purge);

		$connID = $this->db->connID;
		$this->db->connID = $this->getReplicationConnID();
		$result = parent::delete($id, $purge);
		$this->db->connID = $connID;
		return $result;
	}

	/**
	 * purgeDeleted
	 *
	 * @return  bool|mixed
	 */
	public function purgeDeleted()
	{
		if (empty($this->replicationGroup))
			return parent::purgeDeleted();

		$connID = $this->db->connID;
		$this->db->connID = $this->getReplicationConnID();
		$result = parent::purgeDeleted();
		$this->db->connID = $connID;
		return $result;
	}

	/**
	 * replace
	 *
	 * @param   mixed   $data
	 * @param   boolean $returnSQL
	 *
	 * @return  mixed
	 */
	public function replace($data = null, bool $returnSQL = false)
	{
		if (empty($this->replicationGroup))
			return parent::replace($data, $returnSQL);

		$connID = $this->db->connID;
		$this->db->connID = $this->getReplicationConnID();
		$result = parent::replace($data, $returnSQL);
		$this->db->connID = $connID;
		return $result;
	}

	/**
	 * paginate
	 *
	 * @param ?int      $perPage
	 * @param string    $group
	 * @param ?int      $page
	 * @param int       $segment
	 *
	 * @return ?array
	 */
	public function paginate(int $perPage = null, string $group = 'default', int $page = null, int $segment = 0): ?array
	{
		$config = new \Config\Pager();
		$config->templates['theme'] = \Config\Services::html()->getThemeName() . '/layout/pagination';
		$pager = \Config\Services::pager($config, null, false);

		if ($segment)
			$pager->setSegment($segment);

		$page = $page >= 1 ? $page : $pager->getCurrentPage($group);

		$total = $this->countAllResults(false);

		$this->pager = $pager->store($group, $page, $perPage, $total, $segment);
		$perPage     = $this->pager->getPerPage($group);
		$offset      = ($page - 1) * $perPage;

		return $this->findAll($perPage, $offset);
	}
}
