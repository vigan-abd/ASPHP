<?php
namespace ASPHP\Module\Data;

/**
 * @version 1.0
 * @author Vigan
 */

class DbConnection implements IDbConnection
{
	/**
	 * @var \PDO
	 */
	protected $pdo;
	/**
	 * array of format [dsn => '...', username => '...', password => '...']
	 * @var \string[]
	 */
	protected $connectionString;
	protected $state = IDbConnection::StateClosed;

	/**
	 * @param \string[] $connectionString array of format [dsn => '...', username => '...', password => '...']
	 */
	public function __construct($connectionString = null)
	{
		$this->connectionString = $connectionString;
	}

	public function GetConnectionString()
	{
		return $this->connectionString;
	}

	public function SetConnectionString($value)
	{
		$this->connectionString = $value;
	}

	public function GetResource()
	{
		return $this->pdo;
	}

	public function GetState()
	{
		return $this->state;
	}

	public function Open()
	{
		if(empty($this->connectionString) ||
			!key_exists("driver", $this->connectionString) ||
			!key_exists("dsn", $this->connectionString) ||
			!key_exists("username", $this->connectionString) ||
			!key_exists("password", $this->connectionString))
			throw new \Exception("Invalid connection string");
		$this->pdo = new \PDO
		(
			$this->connectionString["dsn"],
			$this->connectionString["username"],
			$this->connectionString["password"],
			[\PDO::ATTR_EMULATE_PREPARES => false, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
		);
		$this->state = IDbConnection::StateOpen;
	}

	public function Close()
	{
		$this->pdo = null;
		$this->state = IDbConnection::StateClosed;
	}

	public function BeginTransaction()
	{
		$this->pdo->beginTransaction();
	}

	public function CommitTransaction()
	{
		if(!$this->pdo->commit())
			throw new \Exception($this->pdo->errorInfo[2], $this->pdo->errorInfo[1]);
	}

	public function RollbackTransaction()
	{
		if(!$this->pdo->rollBack())
			throw new \Exception($this->pdo->errorInfo[2], $this->pdo->errorInfo[1]);
	}

	public function CreateCommand()
	{
		return new DbCommand(null, $this);
	}
}