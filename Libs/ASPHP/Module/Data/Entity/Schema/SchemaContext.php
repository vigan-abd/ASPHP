<?php
namespace ASPHP\Module\Data\Entity\Schema;
use \ASPHP\Module\Data\DbConnectionFactory;
use \ASPHP\Module\Data\IDbDataReader;
use \ASPHP\Module\Data\Entity\Database;
use \ASPHP\Module\Data\Entity\DbMapper;
use \ASPHP\Core\Configuration\Config;
use \ASPHP\Core\IO\FileHandler;

/**
 * SchemaContext short summary.
 *
 * SchemaContext description.
 *
 * @version 1.0
 * @author Vigan
 */
class SchemaContext
{
	/**
	 * @var Database
	 */
	protected $database = null;

	/**
	 * @var \array
	 */
	protected $dbmap;

	/**
	 * @var IDbSchemaBuilder
	 */
	protected $entityProvider;

	/**
	 * @var string
	 */
	protected $modelDir;

	/*
	 * @var string
	 */
	protected $namespace;

	/**
	 * @var string
	 */
	protected $dbName;

	/**
	 * @var SchemaCharset
	 */
	protected $dbCharset;

	/**
	 * @return Database
	 */
	public function GetDatabase()
	{
		if($this->database == null)
			$this->database = new Database(DbConnectionFactory::CreateDefault());
		return $this->database;
	}

	/**
	 * @param Database $value
	 */
	public function SetDatabase(Database $value)
	{
		$this->database = $value;
	}

	/**
	 * @return string
	 */
	public function GetDbName()
	{
		if(!empty($this->dbName))
			return $this->dbName;

		$conn = explode(";", $this->database->GetConnection()->GetConnectionString()['dsn']);
		$length = count($conn);
		for ($i = 0; $i < $length; $i++)
		{
			$kv= explode("=", $conn[$i]);
			if(strtolower($kv[0]) == "dbname")
			{
				$this->dbName = $kv[1];
				break;
			}
		}

		return $this->dbName;
	}

	/**
	 * @return string
	 */
	public function SetDbName($value)
	{
		$this->dbName = $value;
	}

	/**
	 * @return SchemaCharset
	 */
	public function GetDbCharset()
	{
		if($this->dbCharset != null)
			return $this->dbCharset;

		$conn = explode(";", $this->database->GetConnection()->GetConnectionString()['dsn']);
		$length = count($conn);
		for ($i = 0; $i < $length; $i++)
		{
			$kv= explode("=", $conn[$i]);
			if(strtolower($kv[0]) == "charset")
			{
				$this->dbCharset = $this->entityProvider->CharsetCollation($kv[1]);
				break;
			}
		}

		return $this->dbCharset;
	}

	public function SetDbCharset(SchemaCharset $value)
	{
		$this->dbCharset = $value;
	}

	public function GetModelDir()
	{
		return $this->modelDir;
	}

	public function SetModelDir($value)
	{
		$this->modelDir = $value;
	}

	public function GetNamespace()
	{
		return $this->namespace;
	}

	public function SetNamespace($value)
	{
		$this->namespace = $value;
	}

	function __construct(Database $database = null, IDbSchemaBuilder $entityProvider = null, $modelDir = "/Model/Entity", $namespace = "\\Model\\Entity")
	{
		$this->database = $database;
		$this->modelDir = $modelDir;
		$this->namespace = $namespace;
		if($entityProvider == null)
			$this->entityProvider = DbSchemaBuilderFactory::Create(
				$this->GetDatabase()->GetConnection()->GetConnectionString()
			);
		else
			$this->entityProvider = $entityProvider;
	}

	/**
	 * @param string $dir
	 * @param array $collections
	 * @return void
	 */
	protected function ListFolderFiles($dir, &$collections)
	{
	    $ffs = scandir($dir);

	    unset($ffs[array_search('.', $ffs, true)]);
	    unset($ffs[array_search('..', $ffs, true)]);

	    // prevent empty ordered elements
	    if (count($ffs) < 1)
	        return;

	    foreach($ffs as $ff)
		{
			if(!is_dir($dir.'/'.$ff))
				$collections[] = $dir.'/'.$ff;
	        else
				$this->listFolderFiles($dir.'/'.$ff, $collections);
	    }
	}

	/**
	 * @param string $file
	 * @return ClassDefinition
	 */
	protected function ExtractClassFromFile($file)
	{
		$def = new ClassDefinition();

		$file = file_get_contents($file);
		preg_match_all('/namespace.+;/', $file, $matches, PREG_SET_ORDER, 0);
		$namespace = str_replace(["namespace", "\t", " ", "\n", "\r", ";"], "", $matches[0][0]);
		$def->namespace = trim($namespace, "\\");

		unset($matches);
		preg_match_all('/class.+/', $file, $matches, PREG_SET_ORDER, 0);
		$class = preg_replace('/extends.+/', '', $matches[0][0]);
		$class = str_replace(["class", "\t", " ", "\n", "\r", ";"], "", $class);
		$def->class = $class;

		$def->filename = "{$class}.php";

		return $def;
	}

	/**
	 * @param TableDefinition $table
	 * @param string $namespace
	 * @return ClassDefinition
	 */
	protected function ConstructClassFromTableDef(TableDefinition $table)
	{
		$def = new ClassDefinition();
		$namespace = trim($this->GetNamespace(), '\\');
		$code = "";
		$code .= "<?php
namespace {$namespace};

/**
 * This class is generated from database via \\ASPHP\\Module\\Data\\Entity\\Schema\\SchemaContext
 * @dbTable {$table->table}";
		foreach($table->uniqueConstraints as $v)
		{
			$length = count($v->fields);
			$code .= "
 * @dbUnique {$v->fields[0]}";
			for($i = 1; $i < $length; $i++)
				$code.= ",{$v->fields[$i]}";
		}

		foreach($table->indexes as $k => $v)
		{
			$length = count($v->fields);
			$code .= "
 * @dbIndex {$k} => {$v->fields[0]}";
			for($i = 1; $i < $length; $i++)
				$code.= ",{$v->fields[$i]}";
		}
		$code .= "
 * @version 1.0
 * @author SchemaContextGenerated
 */
class {$table->class} extends \\ASPHP\\Core\\Types\\ModelBase
{";
		foreach($table->fields as $field)
		{
			$code.="
	/**
	 * @dbField {$field->field}";

			if($table->pKey != null and in_array($field->field, $table->pKey->fields))
				$code.="
	 * @dbPKey";

			$code.="
	 * @dbType {$field->type}";

			foreach($table->fKeys as $fkey)
			{
				if($field->field == $fkey->field)
				{
					$code.="
	 * @dbFKey {$fkey->foreignTable} => {$fkey->foreignField}";
					break;
				}
			}

			foreach($field->constraints as $k => $v)
			{
				if(is_integer($k))
				{
					switch($v)
					{
						case 'NOT NULL': $code .= "
	 * @required";
							break;
						case "AI": $code .= "
	 * @dbAInc";
							break;
					}
				}
				else
				{
					if($v['key'] == 'length')
						$code .= "
	 * @maxlength {$v['val']}";
					else
						$code .= "
	 * @{$v['key']} {$v['val']}";
				}
			}

			$code.="
	 * @var ".$this->entityProvider->GetClassTypeFromSQLType($field->type)."
	 */
	public \${$field->property};
";
		}
		$code .= "
}
?>";
		$def->namespace = $namespace;
		$def->class = $table->class;
		$def->filename = "{$def->class}.php";
		$def->code = $code;
		return $def;
	}

	/**
	 * @param SchemaDefinition $schema
	 * @return ClassDefinition[]
	 */
	public function ConstructClassesFromSchema(SchemaDefinition $schema)
	{
		$classes = [];
		$defs = $schema->GetTables();
		foreach($defs as $def)
			$classes[] = $this->ConstructClassFromTableDef($def);
		return $classes;
	}

	/**
	 * @param ClassDefinition[] $classes
	 * @return ClassDefinition
	 */
	public function ConstructDbContext($classes)
	{
		$namespace = trim($this->GetNamespace(), "\\");
		$classname = ucfirst($this->GetDbName())."Context";
		$code = "<?php
namespace {$namespace};
use \ASPHP\Module\Data\IDbConnection;
use \ASPHP\Module\Data\Entity\DbSet;
use \ASPHP\Module\Data\Entity\DbContext;

/**
 * This class is generated from database via \\ASPHP\\Module\\Data\\Entity\\Schema\\SchemaContext
 * @version 1.0
 * @author SchemaContextGenerated
 */
class {$classname} extends DbContext
{";
		foreach($classes as $class)
		{
			$code .= "
	protected \$".lcfirst($class->class).";";
		}

		$code .= "

	/**
	 * @param \string[] \$connectionString array of format [dsn => '...', username => '...', password => '...']
	 * @param IDbConnection \$connection
	 */
	public function __construct(\$connectionString = null, IDbConnection \$connection = null)
	{
		parent::__construct(\$connectionString, \$connection);";
		foreach($classes as $class)
		{
			$code .= "
		\$this->".lcfirst($class->class)." = new DbSet('\\\\".str_replace("\\","\\\\", $class->namespace)."\\\\{$class->class}', \$this->database, \$this->entityProvider);";
		}

		$code .= "
	}
}
?>";

		$def = new ClassDefinition();
		$def->class = $classname;
		$def->namespace = $namespace;
		$def->filename = "{$classname}.php";
		$def->code = $code;

		return $def;
	}

	/**
	 * @param ClassDefinition[] $classes
	 * @return ClassDefinition
	 */
	public function ConstructSchemaAutoloader($classes)
	{
		$def = new ClassDefinition();
		$def->class = "";
		$def->namespace = "";
		$date = new \DateTime();
		$def->filename = "autoload_SchemaContext-".$date->format('Y_m_d-h_i_s').".php";
		$code = "<?php";
		foreach($classes as $class)
		{
			$code .= "
require_once __DIR__.'/{$class->filename}';";
		}
		$code .= "
?>";
		$def->code = $code;
		return $def;
	}

	/**
	 * @param SchemaDefinition $schema
	 * @return string[]
	 */
	public function ConstructTablesFromSchema(SchemaDefinition $schema)
	{
		$tables = [];
		$defs = $schema->GetTables();
		foreach($defs as $def)
		{
			$tables[] = $this->entityProvider->GenerateSQLFromDef($def);
		}
		return $tables;
	}

	/**
	 * @return SchemaDefinition
	 */
	public function ConstructSchemaFromSQL()
	{
		$tables = $this->entityProvider->ListTables($this->GetDbName(), $this->database);
		$tableDefs = [];
		$length = count($tables);
		for ($i = 0; $i < $length; $i++)
			$tableDefs[] = $this->entityProvider->ExtractTableDef($tables[$i], $this->GetDbName(), $this->GetDbCharset(), $this->GetDatabase());
		$schema = new SchemaDefinition($this->GetDbName(), [], $this->GetDbCharset());
		$schema->SetTables($tableDefs);
		return $schema;
	}

	/**
	 * @param array $exclude
	 * @return SchemaDefinition
	 */
	public function ConstructSchemaFromClasses($exclude = [])
	{
		$files = [];
		$classes = [];
		$maps = [];
		$this->listFolderFiles(Config::Get()["environment"]["directory"]["~"].$this->modelDir, $files);
		foreach($files as $file)
			$classes[] = $this->ExtractClassFromFile($file);

		foreach($classes as $class)
		{
			if(in_array('\\'.$class->namespace.'\\'.$class->class, $exclude))
				continue;
			$maps[] = DbMapper::ExtractMap('\\'.$class->namespace.'\\'.$class->class);
		}
		$schema = new SchemaDefinition($this->GetDbName(), $maps, $this->GetDbCharset());
		return $schema;
	}

	public function ScaffoldDatabase(SchemaSeeder $seed = null)
	{
		$schema = $this->ConstructSchemaFromClasses(["\\Model\\Entity\\Context"]);
		$tables = $this->ConstructTablesFromSchema($schema);

		$length = count($tables);
		for ($i = 0; $i < $length; $i++)
		{
			$cmds = explode(";", $tables[$i]);
			$length2 = count($cmds);
			for ($j = 0; $j < $length2; $j++)
			{
				if(!empty($cmds[$j]))
					$this->GetDatabase()->AddCmd($cmds[$j]);
			}
		}

		$this->GetDatabase()->SaveChanges();
		if($seed != null)
			$seed->Seed();

	}

	public function ScaffoldClasses()
	{
		$schema = $this->ConstructSchemaFromSQL();
		$classes = $this->ConstructClassesFromSchema($schema);
		$context = $this->ConstructDbContext($classes);
		$classes[] = $context;
		$autoload = $this->ConstructSchemaAutoloader($classes);
		$classes[] = $autoload;

		$dir = trim(Config::Get()["environment"]["directory"]["~"].$this->modelDir, "/");
		if(!file_exists($dir))
			mkdir($dir);
		foreach($classes as $class)
		{
			FileHandler::WriteFile($dir."/".$class->filename, "w", $class->code);
		}
	}
}