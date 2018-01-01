<?php
namespace ASPHP\Module\Data\Entity\Schema;
use \ASPHP\Core\Configuration\Config;

/**
 * @version 1.0
 * @author Vigan
 */
class DbSchemaBuilderFactory
{
	/**
	 * @param \array
	 * @return IDbSchemaBuilder
	 */
	public static function Create($connectionString)
	{
		$db = null;
		if(in_array($connectionString['driver'], Config::Get()["schemaProviders"]))
			$provider = Config::Get()["schemaProviders"][$connectionString['driver']];
		else
			$provider = Config::Get()["schemaProviders"]['default'];
        $code = '
        $db = new '.$provider.'();';
		eval($code);
		return $db;
	}
}