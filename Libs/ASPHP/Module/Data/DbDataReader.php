<?php
namespace ASPHP\Module\Data;

/**
 * @version 1.0
 * @author Vigan
 */
class DbDataReader implements IDbDataReader
{
	protected $row;
	protected $stm;
	protected $cursor;
	protected $rowCount;

	public function __construct(\PDOStatement $stm)
	{
		$this->stm = $stm;
		$this->cursor = 0;
		$this->rowCount = $this->stm->rowCount();
	}

	function offsetExists($offset) { return isset($this->row[$offset]); }
	function offsetGet($offset) { return $this->row[$offset]; }
	function offsetSet($offset, $value) { }
	function offsetUnset($offset) { }

	public function Read()
	{
		if($this->cursor < $this->rowCount)
		{
			$this->row = $this->stm->fetch(\PDO::FETCH_BOTH);
			$this->cursor++;
			return true;
		}
		else
		{
			$this->stm->closeCursor();
			return false;
		}
	}

	public function NextResult()
	{
		return $this->stm->nextRowset();
	}

    public function current(){ return current($this->row); }
    public function key(){ return key($this->row); }
    public function next()
	{
		next($this->row);
		if(is_integer($this->key()) && $this->valid())
		{
			while(is_integer($this->key()) && $this->valid())
				$this->next();
		}
	}
    public function rewind(){ reset($this->row); }
    public function valid(){ return key($this->row) !== null;}
}