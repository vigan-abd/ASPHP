<?php
namespace ASPHP\Module\Data\Entity\Schema;

/**
 * MySQLSchemaBuilder short summary.
 *
 * MySQLSchemaBuilder description.
 *
 * @version 1.0
 * @author Vigan
 */
class MySQLSchemaBuilder implements IDbSchemaBuilder
{
	/**
	 * @return string[]
	 */
	function ListTables($dbName, \ASPHP\Module\Data\Entity\Database $db)
	{
		$tables = [];
		$db->Read("SHOW TABLES", [], function($dr, &$tables)
		{
			$tables[] = $dr[0];
		}, $tables);

		return $tables;
	}

	/**
	 *
	 * @param string $table
	 * @param string $dbName
	 * @param SchemaCharset $charset
	 * @param \ASPHP\Module\Data\Entity\Database $db
	 *
	 * @return TableDefinition
	 */
	function ExtractTableDef($table, $dbName, SchemaCharset $charset, \ASPHP\Module\Data\Entity\Database $db)
	{
		$tableDef = new TableDefinition();
		$tableDef->table = $table;
		$tableDef->class = ucfirst($table);
		$tableDef->charset = $charset;

		$db->Read("DESCRIBE ".$table, [], function($dr, &$tableDef)
		{
			$fieldDef = new ColumnDefinition();
			$matches = [];
			$fieldDef->type = strtoupper(preg_replace('/\(.+\)/', '', $dr['Type']));
			if($fieldDef->type == "CHAR" || $fieldDef->type == "NCHAR" || $fieldDef->type == "VARCHAR" || $fieldDef->type == "VARBINARY" || $fieldDef->type == "NVARCHAR")
			{
				preg_match_all('/\(.+\)/', $dr['Type'], $matches, PREG_SET_ORDER, 0);
				$length = str_replace(['(', ')'], '', $matches[0][0]);
				$fieldDef->constraints[] = ["key" => "length", "val" => $length];
			}
			if(ctype_upper($dr["Field"][0]) && !ctype_upper($dr["Field"][1]))
				$fieldDef->property = lcfirst($dr['Field']);
			else
				$fieldDef->property = $dr['Field'];

			$fieldDef->field = $dr['Field'];
			if($dr["Null"] == "NO")
				$fieldDef->constraints[] = "NOT NULL";
			if($dr["Key"] == "PRI")
			{
				if($tableDef->pKey == null)
					$tableDef->pKey = new PKeyDefinition();
				$tableDef->pKey->fields[] = $dr['Field'];
				if($dr['Extra'] == "auto_increment")
				{
					$fieldDef->constraints[] = "AI";
					$tableDef->pKey->identity = true;
				}
				if(isset($dr["Default"]) && !empty($dr["Default"]))
					$fieldDef->constraints[] = ["key" => 'Default', "val" => $dr["Default"]];
			}
			$tableDef->fields[] = $fieldDef;
		}, $tableDef);

		$db->Read("SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = :Schema AND TABLE_NAME = :Table AND REFERENCED_COLUMN_NAME is not NULL;", [["param" => ":Schema", "val" => $dbName, "type" => \PDO::PARAM_STR], ["param" => ":Table", "val" => $table, "type" => \PDO::PARAM_STR]], function($dr, &$tableDef)
		{
			$fk = new FKeyDefinition();
			$fk->field = $dr['COLUMN_NAME'];
			$fk->foreignField = $dr['REFERENCED_COLUMN_NAME'];
			$fk->foreignTable = $dr['REFERENCED_TABLE_NAME'];
			$tableDef->fKeys[] = $fk;
		}, $tableDef);

		$db->Read("SHOW INDEX FROM ".$table, [], function($dr, &$tableDef)
		{
			if($dr['Key_name'] == "PRIMARY")
			{

			}
			else if(strstr($dr["Key_name"], "UNIQUE"))
			{
				if(!key_exists($dr["Key_name"], $tableDef->uniqueConstraints))
					$tableDef->uniqueConstraints[$dr["Key_name"]] = new UniqueDefiniton();
				$tableDef->uniqueConstraints[$dr["Key_name"]]->fields[] = $dr['Column_name'];
				$tableDef->uniqueConstraints[$dr["Key_name"]]->name = $dr['Key_name'];
			}
			else
			{
				if(!key_exists($dr["Key_name"], $tableDef->indexes))
					$tableDef->indexes[$dr["Key_name"]] = new IndexDefinition();
				$tableDef->indexes[$dr["Key_name"]]->fields[] = $dr['Column_name'];
				$tableDef->indexes[$dr["Key_name"]]->name = $dr['Key_name'];
			}

		}, $tableDef);

		return $tableDef;
	}

	/**
	 *
	 * @param TableDefinition $table
	 *
	 * @return string
	 */
	function GenerateSQLFromDef(TableDefinition $table)
	{
		$statement = "-- This class is generated from database via \ASPHP\Module\Data\Entity\Schema\SchemaContext
CREATE TABLE {$table->table}
(";
		foreach($table->fields as $field)
		{
			$statement .= "
	{$field->field} {$field->type}";
			foreach($field->constraints as $constraint)
			{
				if(is_array($constraint))
				{
					if($constraint['key'] == 'length')
						$statement .= "({$constraint['val']})";
				}
				else
				{
					if($constraint == "AI")
						$statement .= " AUTO_INCREMENT";
					else
						$statement .= " {$constraint}";
				}
			}
			$statement .= ",";
		}

		foreach($table->uniqueConstraints as $v)
		{
			$statement .= "
	UNIQUE ({$v->fields[0]}";
			$length = count($v->fields);
			for ($i = 1; $i < $length; $i++)
				$statement .= ",{$v->fields[$i]}";
			$statement .= "),";
		}

		if($table->pKey != null)
		{
			$statement .= "
	PRIMARY KEY ({$table->pKey->fields[0]}";
			$length = count($table->pKey->fields);
			for($i = 1; $i < $length; $i++)
				$statement .= ",{$table->pKey->fields[$i]}";
			$statement.= "),";
		}

		foreach($table->fKeys as $v)
		{
			$statement .= "
	FOREIGN KEY({$v->field}) REFERENCES {$v->foreignTable}({$v->foreignField}) ON UPDATE CASCADE ON DELETE CASCADE,";
		}

		$statement = rtrim($statement, ",")."
)
ENGINE INNODB CHARACTER SET {$table->charset->charset} COLLATE {$table->charset->collation};";

		foreach($table->indexes as $v)
		{
			$statement .= "
CREATE INDEX {$v->name} ON {$table->table} ({$v->fields[0]}";
			$length = count($v->fields);
			for ($i = 1; $i < $length; $i++)
				$statement .= ",{$v->fields[$i]}";
			$statement .= ");";
		}
		return $statement;
	}

	/**
	 * @param string $type
	 */
	function GetClassTypeFromSQLType($type)
	{
		$type = strtoupper($type);
		switch($type)
		{

			case 'INTEGER':
			case 'INT':
			case 'SMALLINT':
			case 'TINYINT':
			case 'MEDIUMINT':
			case 'BIGINT':
				return 'int';
			case 'DECIMAL':
			case 'NUMERIC':
			case 'FLOAT':
			case 'DOUBLE':
			case 'REAL':
				return 'float';
			case 'BIT':
			case 'BOOLEAN':
			case 'BOOL':
				return 'bool';
			case 'CHAR':
			case 'NCHAR':
			case 'VARCHAR':
			case 'NVARCHAR':
			case 'TEXT':
			case 'BINARY':
			case 'VARBINARY':
			case 'BLOB':
			case 'TINYBLOB':
				return 'string';
			case 'YEAR':
			case 'TIMESTAMP':
			case 'TIME':
			case 'DATETIME':
			case 'DATE':
				return "\\DateTime";
			default:
				return 'mixed';
		}
	}

	public function CharsetCollation($name)
	{
		$charset = new SchemaCharset();
		$name = strtolower($name);
		$charset->charset = $name;
		switch($name)
		{
			case 'big5': $charset->collation = 'big5_chinese_ci'; break;
			case 'dec8': $charset->collation = 'dec8_swedish_ci'; break;
			case 'cp850': $charset->collation = 'cp850_general_ci'; break;
			case 'hp8': $charset->collation = 'hp8_english_ci'; break;
			case 'koi8r': $charset->collation = 'koi8r_general_ci'; break;
			case 'latin1': $charset->collation = 'latin1_swedish_ci'; break;
			case 'latin2': $charset->collation = 'latin2_general_ci'; break;
			case 'swe7': $charset->collation = 'swe7_swedish_ci'; break;
			case 'ascii': $charset->collation = 'ascii_general_ci'; break;
			case 'ujis': $charset->collation = 'ujis_japanese_ci'; break;
			case 'sjis': $charset->collation = 'sjis_japanese_ci'; break;
			case 'hebrew': $charset->collation = 'hebrew_general_ci'; break;
			case 'tis620': $charset->collation = 'tis620_thai_ci'; break;
			case 'euckr': $charset->collation = 'euckr_korean_ci'; break;
			case 'koi8u': $charset->collation = 'koi8u_general_ci'; break;
			case 'gb2312': $charset->collation = 'gb2312_chinese_ci'; break;
			case 'greek': $charset->collation = 'greek_general_ci'; break;
			case 'cp1250': $charset->collation = 'cp1250_general_ci'; break;
			case 'gbk': $charset->collation = 'gbk_chinese_ci'; break;
			case 'latin5': $charset->collation = 'latin5_turkish_ci'; break;
			case 'armscii8': $charset->collation = 'armscii8_general_ci'; break;
			case 'utf8': $charset->collation = 'utf8_general_ci'; break;
			case 'ucs2': $charset->collation = 'ucs2_general_ci'; break;
			case 'cp866': $charset->collation = 'cp866_general_ci'; break;
			case 'keybcs2': $charset->collation = 'keybcs2_general_ci'; break;
			case 'macce': $charset->collation = 'macce_general_ci'; break;
			case 'macroman': $charset->collation = 'macroman_general_ci'; break;
			case 'cp852': $charset->collation = 'cp852_general_ci'; break;
			case 'latin7': $charset->collation = 'latin7_general_ci'; break;
			case 'utf8mb4': $charset->collation = 'utf8mb4_general_ci'; break;
			case 'cp1251': $charset->collation = 'cp1251_general_ci'; break;
			case 'utf16': $charset->collation = 'utf16_general_ci'; break;
			case 'utf16le': $charset->collation = 'utf16le_general_ci'; break;
			case 'cp1256': $charset->collation = 'cp1256_general_ci'; break;
			case 'cp1257': $charset->collation = 'cp1257_general_ci'; break;
			case 'utf32': $charset->collation = 'utf32_general_ci'; break;
			case 'binary': $charset->collation = 'binary'; break;
			case 'geostd8': $charset->collation = 'geostd8_general_ci'; break;
			case 'cp932': $charset->collation = 'cp932_japanese_ci'; break;
			case 'eucjpms': $charset->collation = 'eucjpms_japanese_ci'; break;
			case 'gb18030': $charset->collation = 'gb18030_chinese_ci'; break;
			default: throw new \Exception("Charset not supported");
		}

		return $charset;
	}
}