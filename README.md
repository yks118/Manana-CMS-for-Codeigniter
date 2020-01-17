# Manana CMS For CodeIgniter
Use CI4.

## This Not CMS
But Easy Create Web Service.

## Theme Samples
Basic Theme : app/Libraries/Theme/Basic.php

## Rest API Sample
URL 1. : {Your Domain}/samples/api.json  
URL 2. : {Your Domain}/samples/api.xml  
URL 3. : {Your Domain}/samples/api.csv

Routes : app/Config/Routes.php  
Controller : app/Controllers/Samples/Api.php

## Form Sample
URL : {Your Domain}/samples/form

Routes : app/Config/Routes.php  
Controller : app/Controllers/Samples/Form.php  
Entity : app/Entities/Account.php  
Model : app/Models/Account.php  
View : app/Views/basic/samples/form.php

## DB Query Cache Sample
### Model Full Cache
```php
class Account extends BaseModel
{
	/**
	 * @var bool $cache Default false
	 */
	protected $cache = true;

	/**
	 * @var int $cacheTTL Default 60
	 */
	protected $cacheTTL = 60;

	/**
	 * @var string $cachePrefix Default DB_Cache_
	 */
	protected $cachePrefix = 'DB_Cache_';
}
```  

### Query Select Cache
```php
$mAccount = new \App\Models\Account();
$eAccount = $mAccount
	// Cache refresh 2nd Parameter true
	->setCacheTTL(60)
	// Use find, findAll, findColumn
	->find(1)
;
```

## DB Replication
### Setting Master Group app/Config/Database.php
```php
class Database extends \CodeIgniter\Database\Config
{
	/**
	 * The default database connection.
	 *
	 * @var array
	 */
	public $default = [
		'DSN'      => '',
		'hostname' => 'localhost',
		'username' => '',
		'password' => '',
		'database' => '',
		'DBDriver' => 'MySQLi',
		'DBPrefix' => '',
		'pConnect' => false,
		'DBDebug'  => (ENVIRONMENT !== 'production'),
		'cacheOn'  => false,
		'cacheDir' => '',
		'charset'  => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre'  => '',
		'encrypt'  => false,
		'compress' => false,
		'strictOn' => false,
		'failover' => [],
		'port'     => 3306,
	];

	/**
	 * @var array $master Insert or Update or Delete
	 */
	public $master = [
		'DSN'      => '',
		'hostname' => 'localhost',
		'username' => '',
		'password' => '',
		'database' => '',
		'DBDriver' => 'MySQLi',
		'DBPrefix' => '',
		'pConnect' => false,
		'DBDebug'  => (ENVIRONMENT !== 'production'),
		'cacheOn'  => false,
		'cacheDir' => '',
		'charset'  => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre'  => '',
		'encrypt'  => false,
		'compress' => false,
		'strictOn' => false,
		'failover' => [],
		'port'     => 3306,
	];
}
```

### Setting replicationGroup app/Models/BaseModel.php
```php
class BaseModel extends Model
{
	/**
	 * @var string $replicationGroup
	 */
	protected $replicationGroup = 'master';
}
```
