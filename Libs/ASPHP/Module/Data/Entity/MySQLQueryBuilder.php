<?php
namespace ASPHP\Module\Data\Entity;

/**
 * MySQLQueryBuilder short summary.
 *
 * MySQLQueryBuilder description.
 *
 * @version 1.0
 * @author Vigan
 */
class MySQLQueryBuilder implements IDbQueryBuilder
{
	function DateTypes() { return ["DATE", "DATETIME", "TIMESTAMP", "TIME", "YEAR"]; }
	function BlobTypes(){ return ["BLOB", "VARBINARY", "BINARY", "TINYBLOB"]; }
	function IntTypes(){ return ['INTEGER', 'INT', 'SMALLINT', 'TINYINT', 'MEDIUMINT', 'BIGINT']; }
	function FloatTypes(){ return ['DECIMAL', 'NUMERIC', 'FLOAT', 'DOUBLE', 'REAL']; }
	function BoolTypes(){ return ["BIT", "BOOL", "BOOLEAN"]; }

	function Count($dbMap, $property)
	{
		$query = "SELECT COUNT(".$dbMap["fields"][$property]["dbname"].") FROM ".$dbMap['table']."";
		return [$query, []];
	}

	function CountWhere($dbMap, $whereClause)
	{
		return ["SELECT COUNT(*) FROM ".$dbMap['table']." WHERE ".$whereClause, []];
	}

	function Delete($dbMap, $entity)
	{
		$query = "DELETE FROM ".$dbMap['table']." WHERE ";
		$values = '';
		$params = [];
		$fields = $dbMap["fields"];
		$pkeys = $dbMap['pKey'];
		$length = count($pkeys);
		for ($i = 0; $i < $length; $i++)
		{
			$values .= $fields[$pkeys[$i]]["dbname"]." = :".$pkeys[$i].($i + 1 != $length ? " AND " : "");
			$type = \PDO::PARAM_STR;
			if($fields[$pkeys[$i]]["type"] == "INT"){ $type = \PDO::PARAM_INT; }
			else if($fields[$pkeys[$i]]["type"] == "BOOL"){ $type = \PDO::PARAM_BOOL; }
			else if($fields[$pkeys[$i]]["type"] == "BLOB"){ $type = \PDO::PARAM_LOB; }
			if(!isset($entity->{$pkeys[$i]})){ $type = \PDO::PARAM_NULL; }
			$params[] = ["param" => $pkeys[$i], "val" => $entity->{$pkeys[$i]}, "type" => $type];
		}
		$query .= $values.";";
		return [$query, $params];
	}

	protected function FormatValue($val, $type)
	{
		if($val == null)
			return null;
		$type = strtoupper($type);
		if(in_array($type, $this->DateTypes()))
			$val = $val->format('Y-m-d h-i-s');
		else if(in_array($type, $this->BlobTypes()))
			$val = base64_decode($val);
		return $val;
	}

	function Insert($dbMap, $entity)
	{
		$query = "INSERT INTO ".$dbMap['table']." (";
		$values = "";
		$params = [];
		foreach($dbMap["fields"] as $k => $v)
		{
			if($v["dbname"] == "*")
				continue;
			$query .= $v["dbname"].', ';
			$values .= ":".$v["dbname"].', ';
			$type = \PDO::PARAM_STR;
			if($v["type"] == "INT"){ $type = \PDO::PARAM_INT; }
			else if($v["type"] == "BOOL"){ $type = \PDO::PARAM_BOOL; }
			else if($v["type"] == "BLOB"){ $type = \PDO::PARAM_LOB; }
			if(!isset($entity->{$k})){ $type = \PDO::PARAM_NULL; }

			$entity->{$k} = $this->FormatValue($entity->{$k}, $v["type"]);
			$params[] = ["param" => $v["dbname"], "val" => $entity->{$k}, "type" => $type];
		}
		$query = rtrim($query, ', ').") VALUES (".rtrim($values, ', ').');';
		return [$query, $params];
	}

	function MapOperation($operation)
	{
		return str_replace(["&&", "!=", "||", "=="], ["AND", "!=", "OR", "="], $operation);
	}

	function OrderBy($dbMap, $property)
	{
		$query = "SELECT * FROM ".$dbMap['table']." ORDER BY ".$dbMap["fields"][$property]["dbname"]." ASC";
		return [$query, []];
	}

	function OrderByDesc($dbMap, $property)
	{
		$query = "SELECT * FROM ".$dbMap['table']." ORDER BY ".$dbMap["fields"][$property]["dbname"]." DESC";
		return [$query, []];
	}

	function Select($dbMap, $properties)
	{
		$props = "";
		$length = count($properties);
		for ($i = 0; $i < $length; $i++)
		{
			$props .= $dbMap["fields"][$properties[$i]]["dbname"].', ';
		}
		$query = "SELECT ".rtrim($props, ", ")." FROM ".$dbMap['table'];
		return [$query, []];
	}

	function SelectAll($dbMap)
	{
		return ["SELECT * FROM ".$dbMap['table'], []];
	}

	function SelectAllWhere($dbMap, $whereClause)
	{
		return ["SELECT * FROM ".$dbMap['table']." WHERE ".$whereClause, []];
	}

	function SelectAvg($dbMap, $property, $isDistinct = false)
	{
		$query = "SELECT AVG(".
			($isDistinct ? "DISTINCT(".$dbMap["fields"][$property]["dbname"].")" :
			$dbMap["fields"][$property]["dbname"]).
			") FROM ".$dbMap['table'];
		return [$query, []];
	}

	function SelectByPKey($dbMap, $entity)
	{
		$query = "SELECT * FROM ".$dbMap['table']." WHERE ";
		$pkeys = $dbMap['pKey'];
		$length = count($pkeys);
		$values = "";
		$params = [];
		for ($i = 0; $i < $length; $i++)
		{
			$values .= $dbMap["fields"][$pkeys[$i]]["dbname"]." = :".$pkeys[$i].($i + 1 != $length ? " AND " : "");
			$type = \PDO::PARAM_STR;
			if($dbMap["fields"][$pkeys[$i]]["type"] == "INT"){ $type = \PDO::PARAM_INT; }
			else if($dbMap["fields"][$pkeys[$i]]["type"] == "BOOL"){ $type = \PDO::PARAM_BOOL; }
			else if($dbMap["fields"][$pkeys[$i]]["type"] == "BLOB"){ $type = \PDO::PARAM_LOB; }
			if(!isset($entity->{$pkeys[$i]})){ $type = \PDO::PARAM_NULL; }
			$params[] = ["param" => $pkeys[$i], "val" => $entity->{$pkeys[$i]}, "type" => $type];
		}
		return [$query.$values, $params];
	}

	function SelectExcept($srcDbMap, $secondDbMap, $secondProp, $srcProp)
	{
		$query = "SELECT * FROM ".$srcDbMap['table']." src
			WHERE src.".$srcDbMap["fields"][$srcProp]["dbname"]." NOT IN (
				SELECT dest.".$secondDbMap["fields"][$secondProp]["dbname"]." FROM ".$secondDbMap['table']." dest)";
		return [$query, []];
	}

	function SelectInnerJoin($srcDbMap, $secondDbMap, $secondKey, $srcKey, $resSrcProps, $resSecondProps)
	{
		$props = "";
		$length = count($resSrcProps);
		for ($i = 0; $i < $length; $i++)
			$props .= "src.".$srcDbMap["fields"][$resSrcProps[$i]]["dbname"].', ';

		$length = count($resSecondProps);
		if($length == 0)
			$props = rtrim($props, ", ");

		for ($i = 0; $i < $length; $i++)
			$props .= "dest.".$secondDbMap["fields"][$resSecondProps[$i]]["dbname"].', ';
		$props = rtrim($props, ", ");

		$query = "SELECT ".$props." FROM ".
			$srcDbMap['table']." src INNER JOIN ".$secondDbMap['table']." dest ON
			src.".$srcDbMap["fields"][$srcKey]["dbname"]." = dest.".$secondDbMap["fields"][$secondKey]["dbname"];
		return [$query, []];
	}

	function SelectIntersect($srcDbMap, $secondDbMap, $secondProp, $srcProp)
	{
		$query = "SELECT * FROM ".$srcDbMap['table']." src
			WHERE src.".$srcDbMap["fields"][$srcProp]["dbname"]." IN (
				SELECT dest.".$secondDbMap["fields"][$secondProp]["dbname"]." FROM ".$secondDbMap['table']." dest)";
		return [$query, []];
	}

	function SelectLimitOffset($dbMap, $limit, $offset)
	{
		$query = "SELECT * FROM ".$dbMap['table']." LIMIT {$limit} OFFSET {$offset}";
		return [$query, []];
	}

	function SelectMax($dbMap, $property)
	{
		$query = "SELECT MAX(".$dbMap["fields"][$property]["dbname"].") FROM ".$dbMap['table'];
		return [$query, []];
	}

	function SelectMin($dbMap, $property)
	{
		$query = "SELECT MIN(".$dbMap["fields"][$property]["dbname"].") FROM ".$dbMap['table'];
		return [$query, []];
	}

	function SelectSum($dbMap, $property, $isDistinct = false)
	{
		$query = "SELECT SUM(".
			($isDistinct ? "DISTINCT(".$dbMap["fields"][$property]["dbname"].")" :
			$dbMap["fields"][$property]["dbname"]).
			") FROM ".$dbMap['table'];
		return [$query, []];
	}

	function SelectUnion($srcDbMap, $secondDbMap, $secondProps, $srcProps)
	{
		$srcprops = "";
		$length = count($srcProps);
		for ($i = 0; $i < $length; $i++)
			$srcprops .= "src.".$srcDbMap["fields"][$srcProps[$i]]["dbname"].', ';
		$srcprops = rtrim($srcprops, ", ");

		$secprops = "";
		$length = count($secondProps);
		for ($i = 0; $i < $length; $i++)
			$secprops .= "dest.".$secondDbMap["fields"][$secondProps[$i]]["dbname"].', ';
		$secprops = rtrim($secprops, ", ");

		$query = "(SELECT ".$srcprops." FROM ".$srcDbMap['table']." src) UNION
				(SELECT ".$secprops." FROM ".$secondDbMap['table']." dest)";
		return [$query, []];
	}

	function SelectWhere($dbMap, $properties, $whereClause)
	{
		$props = "";
		$length = count($properties);
		for ($i = 0; $i < $length; $i++)
		{
			$props .= $dbMap["fields"][$properties[$i]]["dbname"].', ';
		}
		$query = "SELECT ".rtrim($props, ", ")." FROM ".$dbMap['table']." WHERE ".$whereClause;
		return [$query, []];
	}

	function Update($dbMap, $entity)
	{
		$query = "UPDATE ".$dbMap['table']." SET ";
		$values = '';
		$params = [];
		$fields = $dbMap["fields"];
		foreach($fields as $k => $v)
		{
			if($v["dbname"] == "*")
				continue;
			$query .= $v["dbname"]." = :".$v["dbname"].', ';
			$type = \PDO::PARAM_STR;
			if($v["type"] == "INT"){ $type = \PDO::PARAM_INT; }
			else if($v["type"] == "BOOL"){ $type = \PDO::PARAM_BOOL; }
			else if($v["type"] == "BLOB"){ $type = \PDO::PARAM_LOB; }

			if(!isset($entity->{$k})){ $type = \PDO::PARAM_NULL; }
			$entity->{$k} = $this->FormatValue($entity->{$k}, $v["type"]);
			$params[] = ["param" => $v["dbname"], "val" => $entity->{$k}, "type" => $type];
		}
		$query = rtrim($query, ', ')." WHERE ";
		$pkeys = $dbMap['pKey'];
		$length = count($pkeys);
		for ($i = 0; $i < $length; $i++)
		{
			$values .= $fields[$pkeys[$i]]["dbname"]." = :".$pkeys[$i].($i + 1 != $length ? " AND " : "");
			$type = \PDO::PARAM_STR;
			if($fields[$pkeys[$i]]["type"] == "INT"){ $type = \PDO::PARAM_INT; }
			else if($fields[$pkeys[$i]]["type"] == "BOOL"){ $type = \PDO::PARAM_BOOL; }
			else if($fields[$pkeys[$i]]["type"] == "BLOB"){ $type = \PDO::PARAM_LOB; }
			if(!isset($entity->{$pkeys[$i]})){ $type = \PDO::PARAM_NULL; }

			$entity->{$pkeys[$i]} = $this->FormatValue($entity->{$pkeys[$i]}, $fields[$pkeys[$i]]["type"]);
			$params[] = ["param" => $pkeys[$i], "val" => $entity->{$pkeys[$i]}, "type" => $type];
		}
		$query .= $values.";";
		return [$query, $params];
	}

	function DisableKeyConstraints()
	{
		return ["SET FOREIGN_KEY_CHECKS = 0", []];
	}

	function EnableKeyConstraints()
	{
		return ["SET FOREIGN_KEY_CHECKS = 1", []];
	}
}