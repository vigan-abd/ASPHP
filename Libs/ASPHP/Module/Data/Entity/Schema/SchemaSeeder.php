<?php
namespace ASPHP\Module\Data\Entity\Schema;


/**
 * SchemaSeeder short summary.
 *
 * SchemaSeeder description.
 *
 * @version 1.0
 * @author Vigan
 */
class SchemaSeeder
{
	/**
	 * @var \ASPHP\Module\Data\Entity\DbContext
	 */
	protected $dbContext;

	/**
	 * @var SeedCollection[]
	 */
	protected $seedCollections;

	/**
	 * @param \ASPHP\Module\Data\Entity\DbContext $dbContext
	 * @param SeedCollection[] $seedCollections
	 */
	public function __construct(\ASPHP\Module\Data\Entity\DbContext $dbContext, $seedCollections)
	{
		$this->dbContext = $dbContext;
		$this->seedCollections = $seedCollections;
	}

	public function Seed()
	{
		$this->dbContext->DisableKeyConstraints();
		foreach($this->seedCollections as $collection)
		{
			$set = $this->dbContext->Set($collection->GetProperty());
			$items = $collection->GetCollection();
			foreach($items as $item)
				$set->Add($item);
		}

		$this->dbContext->EnableKeyConstraints();
		$this->dbContext->SaveChanges();
	}
}