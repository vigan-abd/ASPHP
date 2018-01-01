<?php
namespace ASPHP\Module\Data\Entity\Schema;

/**
 * SeedCollection short summary.
 *
 * SeedCollection description.
 *
 * @version 1.0
 * @author Vigan
 */
class SeedCollection
{
	/**
	 * @var mixed
	 */
	protected $collection;

	/**
	 * @var string
	 */
	protected $TEntity;

	/**
	 * @var string
	 */
	protected $property;

	/**
	 * @param string $TEntity
	 * @param string $property name of the property on the database context
	 * @param mixed $collection
	 */
	public function __construct($TEntity, $property, $collection)
	{
		$this->TEntity = $TEntity;
		$this->property = $property;

        if(!is_array($collection))
            throw new \Exception("Collection must be an array");
		list(,$var) = each($collection);
		$type = gettype($var);
		if($type == "object")
			$type = get_class($var);

		if($type != trim($this->TEntity, "\\"))
            throw new \Exception("Collection must be instance of {$this->TEntity} array");

		$this->collection = $collection;
	}

	public function GetType()
	{
		return $this->TEntity;
	}

	public function GetProperty()
	{
		return $this->property;
	}

	public function GetCollection()
	{
		return $this->collection;
	}

}