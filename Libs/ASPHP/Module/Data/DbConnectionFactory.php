<?php
namespace ASPHP\Module\Data;
use \ASPHP\Core\Configuration\Config;

/**
 * @version 1.0
 * @author Vigan
 */
class DbConnectionFactory
{
	/**
	 * @param \array $connectionString
	 * @param \string $provider
	 * @return IDbConnection
	 */
	public static function Create($connectionString, $provider = "\ASPHP\Module\Data\DbConnection")
	{
		$db = null;
        $code = '
        $db = new '.$provider.'($connectionString);';
		eval($code);
		return $db;
	}

	/**
	 * @return DbConnection
	 */
	public static function CreateDefault()
	{
		return new DbConnection(Config::Get()["connectionStrings"]["default"]);
	}
}