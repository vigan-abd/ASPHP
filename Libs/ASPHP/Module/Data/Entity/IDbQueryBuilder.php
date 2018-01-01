<?php
namespace ASPHP\Module\Data\Entity;

/**
 * IDbQueryBuilder is used by DbSet to map sql queries to user functions.
 * Each method should return the following format:
 * [ "query string", [["param" => ..., "val" => ..., "type" => ...], ["param2" => ...], ...] ]
 * @version 1.0
 * @author Vigan
 */
interface IDbQueryBuilder
{
	/**
	 * @return array
	 */
	function DateTypes();

	/**
	 * @return array
	 */
	function BlobTypes();

	/**
	 * @return array
	 */
	function IntTypes();

	/**
	 * @return array
	 */
	function FloatTypes();

	/**
	 * @return array
	 */
	function BoolTypes();
	/**
	 * The Select COUNT(property) SQL Query generated from funtion.
	 * The properties should be named based on class, not database!
	 * @param array $dbMap
	 * @param string $property
	 * @return array
	 */
	function Count($dbMap, $property);

	/**
	 * SELECT COUNT(*) FROM .. WHERE. The where clause should use database field names instead of entity fields.
	 * @param mixed $dbMap
	 * @param string $whereClause
	 * @return array
	 */
	function CountWhere($dbMap, $whereClause);

	/**
	 * The Delete SQL Query generated from funtion.
	 * @param array $dbMap
	 * @param mixed $entity
	 * @return array
	 */
	function Delete($dbMap, $entity);

	/**
	 * The Insert SQL Query generated from funtion.
	 * @param array $dbMap
	 * @param mixed $entity
	 * @return array
	 */
	function Insert($dbMap, $entity);

	/**
	 * Maps php conditional operations to database conditional operations.
	 * e.g. && => AND, || => OR
	 * @param string $operation
	 * @return string
	 */
	function MapOperation($operation);

	/**
	 * @param array $dbMap
	 * @param string $property Use entity field names
	 * @return array
	 */
	function OrderBy($dbMap, $property);

	/**
	 * @param array $dbMap
	 * @param string $property Use entity field names
	 * @return array
	 */
	function OrderByDesc($dbMap, $property);

	/**
	 * The Select SQL Query generated from funtion.
	 * The properties should be named based on class, not database!
	 * @param array $dbMap
	 * @param array $properties ["objProp1", "objProp2", ...]
	 * @return array
	 */
	function Select($dbMap, $properties);

	/**
	 * The Select * SQL Query generated from funtion
	 * @param array $dbMap
	 * @return array
	 */
	function SelectAll($dbMap);

	/**
	 * SELECT * FROM .. WHERE. The where clause should use database field names instead of entity fields.
	 * @param mixed $dbMap
	 * @param string $whereClause
	 * @return array
	 */
	function SelectAllWhere($dbMap, $whereClause);

	/**
	 * The Select AVG(property) SQL Query generated from funtion.
	 * The properties should be named based on class, not database!
	 * @param array $dbMap
	 * @param string $property
	 * @param bool $isDistinct
	 * @return array
	 */
	function SelectAvg($dbMap, $property, $isDistinct = false);

	/**
	 * The query that selects all fields from db based on where clause by comparing each primary key
	 * @param array $dbMap
	 * @param mixed $entity
	 * @return array
	 */
	function SelectByPKey($dbMap, $entity);

	/**
	 * The except query based on comparision of two fields from each entity.
	 * The properties should be named based on class, not database!
	 * @param array $srcDbMap
	 * @param array $secondDbMap
	 * @param string $secondProp
	 * @param string $srcProp
	 * @return array
	 */
	function SelectExcept($srcDbMap, $secondDbMap, $secondProp, $srcProp);

	/**
	 * The SQL Inner join statement.
	 * The properties should be named based on class, not database!
	 * @param array $srcDbMap
	 * @param array $secondDbMap
	 * @param string $secondKey field that should be used in join from second entity
	 * @param string $srcKey field that should be used in join from first entity
	 * @param array $resSrcProps fields that should be selected from first entity
	 * @param array $resSecondProps fields that should be selected from second entity
	 * @return array
	 */
	function SelectInnerJoin($srcDbMap, $secondDbMap, $secondKey, $srcKey, $resSrcProps, $resSecondProps);

	/**
	 * The intersect query based on comparision of two fields from each entity.
	 * The properties should be named based on class, not database!
	 * @param array $srcDbMap
	 * @param array $secondDbMap
	 * @param string $secondProp
	 * @param string $srcProp
	 * @return array
	 */
	function SelectIntersect($srcDbMap, $secondDbMap, $secondProp, $srcProp);

	/**
	 * @param array $dbMap
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	function SelectLimitOffset($dbMap, $limit, $offset);

	/**
	 * The Select MAX(property) SQL Query generated from funtion.
	 * The properties should be named based on class, not database!
	 * @param array $dbMap
	 * @param string $property
	 * @return array
	 */
	function SelectMax($dbMap, $property);

	/**
	 * The Select MIN(property) SQL Query generated from funtion.
	 * The properties should be named based on class, not database!
	 * @param array $dbMap
	 * @param string $property
	 * @return array
	 */
	function SelectMin($dbMap, $property);

	/**
	 * The Select SUM(property) SQL Query generated from funtion.
	 * The properties should be named based on class, not database!
	 * @param array $dbMap
	 * @param string $property
	 * @param bool $isDistinct
	 * @return array
	 */
	function SelectSum($dbMap, $property, $isDistinct = false);

	/**
	 * The union query based on selection of the fields from each entity.
	 * The properties should be named based on class, not database!
	 * @param array $srcDbMap
	 * @param array $secondDbMap
	 * @param array $secondProps
	 * @param array $srcProps
	 * @return array
	 */
	function SelectUnion($srcDbMap, $secondDbMap, $secondProps, $srcProps);

	/**
	 * SELECT props FROM .. WHERE.
	 * @param mixed $dbMap
	 * @param string $whereClause Use database field names
	 * @param array $properties Use entity field names
	 * @return array
	 */
	function SelectWhere($dbMap, $properties, $whereClause);

	/**
	 * The Update SQL Query generated from funtion.
	 * @param array $dbMap
	 * @param mixed $entity
	 * @return array
	 */
	function Update($dbMap, $entity);

	/**
	 * @return array
	 */
	function DisableKeyConstraints();

	/**
	 * @return array
	 */
	function EnableKeyConstraints();
}