<?php
namespace ASPHP\Module\Data\Entity;
use \ASPHP\Module\Types\Collection\IEnumerable;
use \ASPHP\Module\Types\Collection\IList;
use \ASPHP\Module\Data\DbConnectionFactory;
use \ASPHP\Module\Data\IDbDataReader;

/**
 * @version 1.0
 * @author Vigan
 */
abstract class DbControl
{

	/**
	 * @var Database
	 */
	protected $database = null;

	/**
	 * @var \array
	 */
	protected $dbmap;

	/**
	 * @var IDbQueryBuilder
	 */
	protected $entityProvider;

	protected function DbMap()
	{
		if($this->dbmap == null)
			$this->dbmap = DbMapper::ExtractMap($this->TEntity);
		return $this->dbmap;
	}

	public function GetDatabase()
	{
		if($this->database == null)
			$this->database = new Database(DbConnectionFactory::CreateDefault());
		return $this->database;
	}

	public function SetDatabase(Database $value)
	{
		$this->database = $value;
	}

	protected function __construct(Database $database = null, IDbQueryBuilder $entityProvider = null)
	{
		$this->database = $database;
		if($entityProvider == null)
			$this->entityProvider = DbQueryBuilderFactory::Create(
				$this->GetDatabase()->GetConnection()->GetConnectionString()
			);
		else
			$this->entityProvider = $entityProvider;
	}
}
?>