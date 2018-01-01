<?php
namespace ASPHP\Module\Data;

/**
 * @version 1.0
 * @author Vigan
 */
interface IDbConnection
{
	const StateOpen = "Open",
	StateClosed = "Closed";
	/**
	 * @return \string[]
	 */
	public function GetConnectionString();

	/**
	 * @param \string[] $value array of format [dsn => '...', username => '...', password => '...']
	 */
	public function SetConnectionString($value);

	/**
	 * Returns internal database resource (e.g. PDO or mysqli)
	 * @return mixed|\PDO|\mysqli
	 */
	public function GetResource();

	/**
	 * @return string
	 */
	public function GetState();

	/**
	 * @return \void
	 */
	public function Open();

	/**
	 * @return \void
	 */
	public function Close();

	/**
	 * @throws \Exception
	 * @return \void
	 */
	public function BeginTransaction();

	/**
	 * @throws \Exception
	 * @return \void
	 */
	public function CommitTransaction();

	/**
	 * @throws \Exception
	 * @return \void
	 */
	public function RollbackTransaction();
	/**
	 * @return IDbCommand
	 */
	function CreateCommand();
}
