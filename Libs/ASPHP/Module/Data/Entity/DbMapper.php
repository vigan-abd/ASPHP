<?php
namespace ASPHP\Module\Data\Entity;
use \ASPHP\Core\Attribute\AttributeReader;

/**
 * DbMapper short summary.
 *
 * DbMapper description.
 *
 * @version 1.0
 * @author Vigan
 */
class DbMapper
{
	public static function ExtractMap($TEntity)
	{
		$map = [];
		$map["table"] = pathinfo($TEntity, PATHINFO_BASENAME);
		$map["class"] = $TEntity;
		$map["fields"] = [];
		$map["pKey"] = [];
		$map["fKey"] = [];
		$map["unique"] = [];
		$map["index"] = [];
		$uniqueIdx = -1;
		$indexIdx = -1;
		$fKeyIdx = -1;
		$reader = new AttributeReader($TEntity);
		$attr = $reader->GetClassAttributes();
		$length = count($attr);

		// Read class attributes
		for ($i = 0; $i < $length; $i++)
			switch ($attr[$i]['key'])
			{
				case 'dbTable': $map["table"] = $attr[$i]['val']; break;
				case 'dbUnique': $uniqueIdx++; $map["unique"][$uniqueIdx] = $attr[$i]['val']; break;
				case 'dbIndex': $indexIdx++; $parts = explode('=>', $attr[$i]['val']);
					$map["index"][$indexIdx]["name"] = $parts[0];
					$map["index"][$indexIdx]["fields"] = $parts[1]; break;
			}

		// Read propery attributes
		$attr = $reader->ReadProperties();
		$length = count($attr);
		$map["fields"]['*']["dbname"] = '*';
		for ($i = 0; $i < $length; $i++)
		{
			$fkeyExist = false;
			$map["fields"][$attr[$i]->name]["type"] = "-1";
			$map["fields"][$attr[$i]->name]["length"] = "-1";
			$map["fields"][$attr[$i]->name]["dbname"] = $attr[$i]->name;
			$map["fields"][$attr[$i]->name]["constraints"] = [];
			$propAttr = $reader->GetPropertyAttributes($attr[$i]->name);
			$length2 = count($propAttr);
			for ($j = 0; $j < $length2; $j++)
			{
				switch ($propAttr[$j]["key"])
				{
					case 'required': $map["fields"][$attr[$i]->name]["constraints"][] = "NOT NULL"; break;
					case 'dbPKey': $map["pKey"][] = $attr[$i]->name; break;
					case 'dbFKey':
						$fKeyIdx++; $fkeyExist = true;
						$parts = explode("=>", $propAttr[$j]["val"]);
						$map["fKey"][$fKeyIdx]["table"] = trim($parts[0]);
						$map["fKey"][$fKeyIdx]["tblFField"] = trim($parts[1]);
						$map["fKey"][$fKeyIdx]["field"] = $map["fields"][$attr[$i]->name]["dbname"]; break;
					case 'dbField':
						$map["fields"][$attr[$i]->name]["dbname"] = $propAttr[$j]["val"];
						$map["invfields"][$propAttr[$j]["val"]] = $attr[$i]->name;
						break;
					case 'dbLength': $map["fields"][$attr[$i]->name]["length"] = $propAttr[$j]["val"]; break;
					case 'maxlength': if($map["fields"][$attr[$i]->name]["length"] == "-1")
							$map["fields"][$attr[$i]->name]["length"] = $propAttr[$j]["val"];
						break;
					case 'dbAInc': $map["fields"][$attr[$i]->name]["constraints"][] = "AI"; break;
					case 'dbDefault': $map["fields"][$attr[$i]->name]["constraints"][] = ['key' => "Default", 'val' => $propAttr[$j]["val"]]; break;
					case 'dbUnique': $map["fields"][$attr[$i]->name]["constraints"][] = ['key' => "Default", 'val' => $propAttr[$j]["val"]];  break;
					case 'var':
						if($map["fields"][$attr[$i]->name]["type"] == "-1")
							$propAttr[$j]["val"] = trim($propAttr[$j]["val"], '\\');
							switch($propAttr[$j]["val"])
							{
								case 'float':
								case 'double':
								case 'decimal':
								case 'number':
									$map["fields"][$attr[$i]->name]["type"] = 'FLOAT';
									break;
								case 'int':
								case 'integer':
									$map["fields"][$attr[$i]->name]["type"] = "INT";
									break;
								case 'bool':
								case 'boolean':
									$map["fields"][$attr[$i]->name]["type"] = "BOOL";
									break;
								case 'string':
									$map["fields"][$attr[$i]->name]["type"] = "VARCHAR";
									break;
								default:
									break;
							}
						break;
					case 'dbType': $map["fields"][$attr[$i]->name]["type"] = $propAttr[$j]["val"]; break;
				}
			}
			if(!empty($map["fKey"][$fKeyIdx]["field"]) && $fkeyExist)
				$map["fKey"][$fKeyIdx]["field"] = $map["fields"][$attr[$i]->name]["dbname"];
			if($map["fields"][$attr[$i]->name]["length"] == "-1")
			{
				$t = strtoupper($map["fields"][$attr[$i]->name]["type"]);
				if($t == "VARCHAR" || $t == "NVARCHAR" || $t == "CHAR")
					$map["fields"][$attr[$i]->name]["length"] = "(50)";
				else if($t == "DOUBLE" || $t == "DECIMAL" || $t == "REAL" || $t == "FLOAT")
					$map["fields"][$attr[$i]->name]["length"] = "(10,2)";
			}
		}

		return $map;
	}
}