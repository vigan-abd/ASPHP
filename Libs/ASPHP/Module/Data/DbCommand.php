<?php
namespace ASPHP\Module\Data;

/**
 * @version 1.0
 * @author Vigan
 */
class DbCommand implements IDbCommand
{
	const StoredProcedure = "StoredProcedure",
	TableDirect = "TableDirect",
	Text = "Text";

	/**
	 * @var IDbConnection
	 */
	protected $dbConnection;

	/**
	 * @var \string
	 */
	protected $commandType;

	/**
	 * @var \string
	 */
	protected $cmdText;

	/**
	 * @var \array
	 */
	protected $params;

	/**
	 * @var \PDOStatement
	 */
	protected $stm;

	/**
	 * @param \string $cmdText
	 * @param DbConnection $connection
	 */
	public function __construct($cmdText = null , DbConnection $connection = null)
	{
		$this->cmdText = $cmdText;
		$this->dbConnection = $connection;
		$this->commandType = static::Text;
	}

	public function GetCommandText()
	{
		return $this->cmdText;
	}

	public function SetCommandText($value)
	{
		$this->cmdText = $value;
	}

	public function GetCommandType()
	{
		return $this->cmdText;
	}

	/**
	 * @param \string $value
	 */
	public function SetCommandType($value)
	{
		if($value != DbCommand::Text && $value != DbCommand::TableDirect && $value != DbCommand::StoredProcedure)
			throw new \Exception("Command type not supported");
		$this->commandType = $value;
	}

	public function GetConnection()
	{
		return $this->dbConnection;
	}

	public function SetConnection(IDbConnection $value)
	{
		$this->dbConnection = $value;
	}

	/**
	 * @param \int|\string $parameter string if named parameters
	 * @param mixed $value
	 * @param mixed $data_type
	 */
	public function AddParameter($parameter, $value, $data_type = \PDO::PARAM_STR)
	{
		$this->params[] = ["param" => $parameter, "val" => $value, "type" => $data_type];
	}

	/**
	 * @param \array $params
	 */
	public function AddParameters($params)
	{
		foreach ($params as $param)
			$this->params[] = $param;
	}

	/**
	 * @return \array
	 */
	public function GetParameters()
	{
		return $this->params;
	}

	/**
	 * @param mixed $parameter
	 */
	public function DeleteParameter($parameter)
	{
		foreach($this->params as $i => $param)
			if($i == $parameter || $param["param"] == $parameter)
			{ unset($this->params[$i]); break; }
	}

	public function ClearParameters()
	{
		unset($this->params);
		$this->params = [];
	}

	protected function PrepareStatment()
	{
		$query = $this->cmdText;
		switch($this->commandType)
		{
			case static::TableDirect: $query = "SELECT * FROM {$this->cmdText};"; break;
		}
		$length = count($this->params);
		$this->stm = $this->dbConnection->GetResource()->prepare($query);
		for ($i = 0; $i < $length; $i++)
			$this->stm->bindParam($this->params[$i]["param"], $this->params[$i]["val"], $this->params[$i]["type"]);
	}

	public function ExecuteReader()
	{
		$this->PrepareStatment();
		if(!$this->stm->execute())
			throw new \Exception($this->stm->errorInfo[2], $this->stm->errorInfo[1]);
		return new DbDataReader($this->stm);
	}

	public function ExecuteNonQuery()
	{
		$this->PrepareStatment();
		if(!$this->stm->execute())
			throw new \Exception($this->stm->errorInfo[2], $this->stm->errorInfo[1]);
		$affectedRows = $this->stm->rowCount();
		$this->stm->closeCursor();
		return $affectedRows;
	}

	public function ExecuteScalar()
	{
		$this->PrepareStatment();
		if(!$this->stm->execute())
			throw new \Exception($this->stm->errorInfo[2], $this->stm->errorInfo[1]);
		$result = $this->stm->fetch(\PDO::FETCH_NUM)[0];
		$this->stm->closeCursor();
		return $result;
	}
}
?>