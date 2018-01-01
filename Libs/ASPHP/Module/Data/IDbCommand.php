<?php
namespace ASPHP\Module\Data;

/**
 * IDbCommand short summary.
 *
 * IDbCommand description.
 *
 * @version 1.0
 * @author Vigan
 */
interface IDbCommand
{
	/**
	 * @return \string
	 */
	public function GetCommandText();

	/**
	 * @param \string $value
	 */
	public function SetCommandText($value);

	/**
	 * @return \string
	 */
	public function GetCommandType();

	/**
	 * @param \string $value
	 */
	public function SetCommandType($value);

	/**
	 * @return IDbConnection
	 */
	public function GetConnection();

	/**
	 * @param IDbConnection $value
	 */
	public function SetConnection(IDbConnection $value);

	/**
	 * @param \int|\string $parameter string if named parameters
	 * @param mixed $value
	 * @param mixed $data_type
	 */
	public function AddParameter($parameter, $value, $data_type = \PDO::PARAM_STR);

	/**
	 * @param \array $params ["param" => ..., "val" => ..., "type" => ...]
	 */
	public function AddParameters($params);

	/**
	 * @return \array
	 */
	public function GetParameters();

	/**
	 * @param \array $parameter ["param" => ..., "val" => ..., "type" => ...]
	 */
	public function DeleteParameter($parameter);

	/**
	 * Clears internal parameters of the statement
	 */
	public function ClearParameters();

	/**
	 * @throws \Exception
	 * @return IDbDataReader
	 */
	public function ExecuteReader();

	/**
	 * @throws \Exception
	 * @return \int
	 */
	public function ExecuteNonQuery();

	/**
	 * @throws \Exception
	 * @return mixed
	 */
	public function ExecuteScalar();
}
