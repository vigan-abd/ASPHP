<?php
namespace ASPHP\Module\Data\Entity\Schema;
/**
 * TableDefinition short summary.
 *
 * TableDefinition description.
 *
 * @version 1.0
 * @author Vigan
 */
class TableDefinition
{
	/**
	 * @var string
	 */
	public $class;

	/**
	 * @var string
	 */
	public $table;

	/**
	 * @var \ASPHP\Module\Data\Entity\Schema\ColumnDefinition[]
	 */
	public $fields = [];

	/**
	 * @var \ASPHP\Module\Data\Entity\Schema\PKeyDefinition
	 */
	public $pKey;

	/**
	 * @var \ASPHP\Module\Data\Entity\Schema\FKeyDefinition[]
	 */
	public $fKeys = [];

	/**
	 * @var \ASPHP\Module\Data\Entity\Schema\UniqueDefiniton[]
	 */
	public $uniqueConstraints = [];

	/**
	 * @var \ASPHP\Module\Data\Entity\Schema\IndexDefinition[]
	 */
	public $indexes = [];

	/**
	 *
	 * @var SchemaCharset
	 */
	public $charset;
}