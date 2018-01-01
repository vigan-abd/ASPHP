<?php
namespace ASPHP\Module\Data\Entity;
use \ASPHP\Module\Data\IDbConnection;
use \ASPHP\Module\Data\DbConnection;
use \ASPHP\Module\Data\DbConnectionFactory;

/**
 * @version 1.0
 * @author Vigan
 */
abstract class DbContext
{
	/**
	 * @var Database
	 */
	protected $database;
	protected $transactSupported = true;
	/**
	 * @var IDbQueryBuilder
	 */
	protected $entityProvider;

	/**
	 * @param \string[] $connectionString array of format [dsn => '...', username => '...', password => '...']
	 * @param IDbConnection $connection
	 */
	public function __construct($connectionString = null, IDbConnection $connection = null, IDbQueryBuilder $entityProvider = null)
	{
		if($connection == null)
		{
			if($connectionString == null)
				$dbConnection = DbConnectionFactory::CreateDefault();
			else
				$dbConnection = new DbConnection($connectionString);
		}
		else
		{
			$dbConnection = $connection;
			if($connectionString != null)
				$dbConnection->SetConnectionString($connectionString);
		}
		$this->database = new Database($dbConnection);
		try
		{
			$this->database->GetConnection()->BeginTransaction();
		}
		catch(\Exception $ex) { $this->transactSupported = false; }

		if($entityProvider == null)
			$this->entityProvider = DbQueryBuilderFactory::Create(
				$this->database->GetConnection()->GetConnectionString()
			);
		else
			$this->entityProvider = $entityProvider;
	}

	public function SaveChanges()
	{
		$this->database->SaveChanges();
		if($this->transactSupported)
			$this->database->GetConnection()->CommitTransaction();
	}

	/**
	 * @param \string $entityType
	 * @return DbSet
	 */
	public function Set($entityType)
	{
		return $this->{$entityType};
	}

	public function DisableKeyConstraints()
	{
		$query = $this->entityProvider->DisableKeyConstraints();
		$this->database->AddCmd($query[0], $query[1]);
	}

	public function EnableKeyConstraints()
	{
		$query = $this->entityProvider->EnableKeyConstraints();
		$this->database->AddCmd($query[0], $query[1]);
	}
}