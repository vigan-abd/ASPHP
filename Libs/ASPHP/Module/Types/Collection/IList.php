<?php
namespace ASPHP\Module\Types\Collection;

/**
 * @requires class \ASPHP\Module\Types\Collection\IEnumerable
 * @version 1.0
 * @author Vigan
 */
class IList extends IEnumerable implements \ArrayAccess
{
    public function __construct($T = 'mixed', $collection = [])
    {
        parent::__construct($T, $collection);
    }

    /*Iterator*/
    public function current(){ return $this->collection[$this->cursor]; }
    public function key(){ return $this->cursor; }
    public function next(){ ++$this->cursor; }
    public function rewind(){ $this->cursor = 0; }
    public function valid(){ return isset($this->collection[$this->cursor]); }

    /*ArrayAccess*/
    function offsetExists($offset)
    {
        return isset($this->collection[$offset]);
    }

    function offsetGet($offset)
    {
        if(!isset($this->collection[$offset]))
            throw new \Exception('Index out of range');
        return $this->collection[$offset];
    }

    function offsetSet($offset, $value)
    {
        if($offset > $this->count)
            throw new \Exception('Index out of range');
        else if($offset == $this->count)
            $this->Add($value);
        else
            $this->collection[$offset] = $value;
    }

    function offsetUnset($offset)
    {
        $this->RemoveAt($offset);
    }

    /*List*/
    /**
	 * @param mixed $item
	 */
    public function Add($item)
    {
        $this->TypeOf($item, $this->T);
        $this->collection[$this->count] = $item;
        $this->count++;
    }

    /**
	 * @param array $items
	 */
    public function AddRange($items)
    {
        foreach ($items as $item)
        {
        	$this->Add($item);
        }
    }

    public function Clear()
    {
        $this->RemoveAll();
    }

    /**
	 * @param \Closure|string $func Func<TVar, bool>
	 * @return bool
	 */
    public function Exists($func)
    {
        $func = $this->CheckLambda($func);
        for ($i = 0; $i < $this->count; $i++)
        {
        	if($func($this->collection[$i]))
                return true;
        }
        return false;
    }

    /**
	 * @param \Closure|string $func Func<TVar, bool>
	 * @return mixed
	 */
    public function Find($func)
    {
        $func = $this->CheckLambda($func);
        for ($i = 0; $i < $this->count; $i++)
        {
        	if($func($this->collection[$i]))
                return $this->collection[$i];
        }
        return null;
    }

    /**
	 * @param \Closure|string $func Func<TVar, bool>
	 * @return IEnumerable
	 */
    public function FindAll($func)
    {
        return $this->Where($func);
    }

    /**
	 * @param int $index
	 * @param int $count
	 * @return IEnumerable
	 */
    public function GetRange($index, $count)
    {
        $collection = [];
        for ($i = $index; $i < $index + $count; $i++)
            $collection[] = $this->collection[$i];
        return new IEnumerable($this->T, $collection);
    }

    /**
	 * Search for the first index of element starting at specific position (index) until specific iterations (count = -1 => all)
	 * @param mixed $item
	 * @param int $index
	 * @param int $count
	 * @return int
	 */
    public function IndexOf($item, $index = 0, $count = -1)
    {
        if($count < 0 || $count > $this->count)
            $count = $this->count;
        for ($i = $index; $i < $count; $i++)
        {
        	if($this->collection[$i] == $item)
                return $i;
        }
        return -1;
    }

    public function Insert($index, $item)
    {
        for ($i = $this->count; $i > $index; $i--)
        {
            if($i == 0)
                break;
            $this->collection[$i] = $this->collection[$i-1];
        }
        $this->collection[$index] = $item;
        $this->count++;
    }

    public function InsertRange($index, $collection)
    {
        $colLength = count($collection);
        $newLength = $this->count + $colLength - 1;
        $j = $this->count - 1;
        for ($i = $newLength; $i > ($index + $colLength - 1); $i--)
        {
        	$this->collection[$i] = $this->collection[$j]; $j--;
        }
        for ($i = 0; $i < $colLength; $i++)
        {
        	$this->collection[$index] = $collection[$i];
            $index++;
            $this->count++;
        }




    }

    /**
	 * Search for the last index of element starting at specific position (index) until specific iterations (count = -1 => all)
	 * @param mixed $item
	 * @param int $index
	 * @param int $count
	 * @return int
	 */
    public function LastIndexOf($item, $index = 0, $count = -1)
    {
        $k = -1;
        if($count < 0 || $count > $this->count)
            $count = $this->count;
        for ($i = $index; $i < $count; $i++)
        {
        	if($this->collection[$i] == $item)
                $k = $i;
        }
        return $k;
    }

    /**
	 * Removes the first occurrence of a specific object from the collection
	 * @param mixed $item
	 */
    public function Remove($item)
    {
        for ($i = 0; $i < $this->count; $i++)
        {
        	if($this->collection[$i] === $item)
            {
                $this->RemoveAt($i);
                break;
            }
        }
    }

    public function RemoveAll()
    {
        $this->collection = [];
        $this->count = 0;
    }

    /**
	 * @param int $index
	 */
    public function RemoveAt($index)
    {
        $this->RemoveRange($index, 1);
    }

    /**
	 * @param int $index
	 * @param int $count
	 * @throws \Exception
	 */
    public function RemoveRange($index, $count)
    {
        if($index + $count > $this->count)
            throw new \Exception("Index out of range");
        $this->count -= $count;
        for ($j = $index; $j < $this->count; $j++)
        {
            $this->collection[$j] = $this->collection[$j + $count];
        }
        for ($i = $this->count; $i < $this->count + $count; $i++)
        {
        	unset($this->collection[$i]);
        }
    }

    /**
	 * @param \ASPHP\Module\Types\Comparer\Comparer $comparer
	 * @param int $index
	 * @return IList
	 */
    public function Sort(\ASPHP\Module\Types\Comparer\Comparer $comparer = null, $index = 0)
    {
        if($index > 0)
        {
            $collection = [];
            for ($i = $index; $i < $this->count; $i++)
            {
            	$collection[] = $this->collection[$i];
            }
        }
        else
        {
            $collection = $this->collection;
        }
        $collection = $this->InsertionSort($collection, $comparer);
        return new IList($this->T, $collection);
    }

    /**
	 * @param \Closure|string $func Func<TSource, bool>
	 * @return bool
	 */
    public function TrueForAll($func)
    {
        $func = $this->CheckLambda($func);
        for ($i = 0; $i < $this->count; $i++)
        {
            if(!$func($this->collection[$i]))
                return false;
        }
        return true;
    }
}
?>