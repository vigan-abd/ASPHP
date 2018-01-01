<?php
namespace ASPHP\Module\Data\Entity\Schema;

/**
 * SchemaDefinition short summary.
 *
 * SchemaDefinition description.
 *
 * @version 1.0
 * @author Vigan
 */
class SchemaDefinition
{
	protected $tables = [];
	protected $dbName = "";

	/**
	 * @param string $dbName
	 * @param array $maps
	 * @param SchemaCharset $charset
	 */
	public function __construct($dbName, $maps, $charset)
	{
		$this->dbName = $dbName;
		foreach($maps as $map)
		{
			$tableDef = new TableDefinition();
			$tableDef->class = $map['class'];
			$tableDef->table = $map['table'];
			$tableDef->charset = $charset;

			foreach($map['fields'] as $k => $v)
			{
				if(trim($k) == '*')
					continue;
				$fieldDef = new ColumnDefinition();
				$fieldDef->property = $k;;
				$fieldDef->field = $v['dbname'];
				$fieldDef->type = $v['type'];
				if($v['length'] > 0)
					$fieldDef->constraints = array_merge([['key' => 'length', 'val' => $v['length']]], $v['constraints']);
				else
					$fieldDef->constraints = $v['constraints'];
				$tableDef->fields[] = $fieldDef;
			}

			$tableDef->pKey = new PKeyDefinition();
			$tableDef->pKey->fields = $map['pKey'];

			foreach($map['fKey'] as $fkey)
			{
				$fkeyDef = new FKeyDefinition();
				$fkeyDef->field = $fkey['field'];
				$fkeyDef->foreignField = $fkey['tblFField'];
				$fkeyDef->foreignTable = $fkey['table'];
				$tableDef->fKeys[] = $fkeyDef;
			}

			foreach($map['index'] as $index)
			{
				$indexDef = new IndexDefinition();
				$indexDef->fields = explode(',', trim(trim($index['fields']),','));
				$indexDef->name = $index['name'];
				$tableDef->indexes[$indexDef->name] = $indexDef;
			}

			foreach($map['unique'] as $unique)
			{
				$uniqueDef = new UniqueDefiniton();
				$uniqueDef->fields = explode(',', trim(trim($unique),','));
				$tableDef->uniqueConstraints[] = $uniqueDef;
			}

			//Prevent Foreign Key bugs
			if(!empty($tableDef->fKeys))
				$this->tables[] = $tableDef;
			else
				array_unshift($this->tables, $tableDef);
		}
	}

	public function GetTables()
	{
		return $this->tables;
	}

	public function SetTables($value)
	{
		$this->tables = $value;
	}


	public function GetDbName()
	{
		return $this->dbName;
	}
}