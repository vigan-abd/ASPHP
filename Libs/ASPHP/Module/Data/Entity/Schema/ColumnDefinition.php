<?php
namespace ASPHP\Module\Data\Entity\Schema;

/**
 * ColumnDefinition short summary.
 *
 * ColumnDefinition description.
 *
 * @version 1.0
 * @author Vigan
 */
class ColumnDefinition
{
	/**
	 * Name of the column in the class
	 * @var mixed
	 */
	public $property;

	/**
	 * Name of the column on the database
	 * @var mixed
	 */
	public $field;

	/**
	 * @var string
	 */
	public $type;

	public $constraints = [];
}