<?php
namespace ASPHP\Module\Data\Entity;
use \ASPHP\Core\Configuration\Config;

/**
 * @version 1.0
 * @author Vigan
 */
class DbQueryBuilderFactory
{
	/**
	 * @param \array
	 * @return IDbQueryBuilder
	 */
	public static function Create($connectionString)
	{
		$db = null;
		if(in_array($connectionString['driver'], Config::Get()["entityProviders"]))
			$provider = Config::Get()["entityProviders"][$connectionString['driver']];
		else
			$provider = Config::Get()["entityProviders"]['default'];
        $code = '
        $db = new '.$provider.'();';
		eval($code);
		return $db;
	}
}