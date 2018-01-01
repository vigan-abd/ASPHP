<?php
namespace ASPHP\Module\Data\Entity\Schema;

/**
 * IDbSchemaBuilder short summary.
 *
 * IDbSchemaBuilder description.
 *
 * @version 1.0
 * @author Vigan
 */
interface IDbSchemaBuilder
{
	/**
	 * @return string[]
	 */
	public function ListTables($dbName, \ASPHP\Module\Data\Entity\Database $db);

	/**
	 * @param string $table
	 * @param string $dbName
	 * @param SchemaCharset $charset
	 * @return \ASPHP\Module\Data\Entity\Schema\TableDefinition
	 */
	public function ExtractTableDef($table, $dbName, SchemaCharset $charset, \ASPHP\Module\Data\Entity\Database $db);

	/**
	 * @param TableDefinition $table
	 * @return string
	 */
	public function GenerateSQLFromDef(\ASPHP\Module\Data\Entity\Schema\TableDefinition $table);

	/**
	 * @param string $type
	 */
	public function GetClassTypeFromSQLType($type);

	/**
	 * @param string $name
	 * @throws \Exception
	 * @return SchemaCharset
	 */
	public function CharsetCollation($name);
}