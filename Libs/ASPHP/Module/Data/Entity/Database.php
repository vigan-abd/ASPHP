<?php
namespace ASPHP\Module\Data\Entity;
use \ASPHP\Module\Data\IDbConnection;

/**
 * @version 1.0
 * @author Vigan
 */
class Database
{
	/**
	 * @var IDbConnection
	 */
	protected $dbConnection;

	protected $queue = [];

	/**
	 * @param \string $query
	 * @param \array $params array(["param" => ..., "val" => ..., "type" => ...], ...)
	 */
	public function AddCmd($query, $params = [])
	{
		$this->queue[] = ["query" => $query, "params" => $params];
	}

	public function __construct(IDbConnection $connection)
	{
		$this->dbConnection = $connection;
		if($this->dbConnection->GetState() == IDbConnection::StateClosed)
			$this->dbConnection->Open();
	}

	/**
	 * @return IDbConnection
	 */
	public function GetConnection() { return $this->dbConnection; }

	/**
	 * @param \string $query
	 * @param \array $params array(["param" => ..., "val" => ..., "type" => ...], ...)
	 * @return \integer
	 */
	protected function Write($query, $params = [])
	{
		$cmd = $this->dbConnection->CreateCommand();
		$cmd->SetCommandText($query);
		$cmd->AddParameters($params);
		return $cmd->ExecuteNonQuery();
	}

	/**
	 * @param \string $query
	 * @param \array $params array(["param" => ..., "val" => ..., "type" => ...], ...)
	 * @param \Closure $mapper Func<IDataReader, ref Collection>
	 * @param mixed $collection
	 */
	public function Read($query, $params, \Closure $mapper, &$collection)
	{
		$cmd = $this->dbConnection->CreateCommand();
		$cmd->SetCommandText($query);
		$cmd->AddParameters($params);
		$dr = $cmd->ExecuteReader();
		while($dr->Read())
		{
			$mapper($dr, $collection);
		}
	}

	public function SaveChanges()
	{
		$length = count($this->queue);
		for ($i = 0; $i < $length; $i++)
		{
			$this->Write($this->queue[$i]["query"], $this->queue[$i]["params"]);
		}
		$this->queue = [];
	}
}