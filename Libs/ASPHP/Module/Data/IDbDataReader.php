<?php
namespace ASPHP\Module\Data;

/**
 * IDbDataReader short summary.
 *
 * IDbDataReader description.
 *
 * @version 1.0
 * @author Vigan
 */
interface IDbDataReader extends \ArrayAccess, \Iterator
{
	/**
	 * @return boolean
	 */
	public function Read();

	/**
	 * @return mixed
	 */
	public function NextResult();
}
