<?php
namespace ASPHP\Module\Types\Collection;
use \ASPHP\Module\Types\Linq\ILinq;
use \ASPHP\Module\Types\Linq\LambdaExpression;

/**
 * @requires interface \ASPHP\Module\Types\Linq\ILinq
 * @requires class \ASPHP\Module\Types\Linq\LambdaExpression
 * @version 1.0
 * @author Vigan
 */
abstract class CollectionBase implements \Iterator, ILinq, \Countable
{
    /**
     * @var array
     */
    protected $collection;
    /**
     * @var mixed
     */
    protected $cursor;
    /**
     * @var int
     */
    protected $count;
    /**
     * @var string
     */
    protected $T = 'mixed';
    /**
	 * @var \ASPHP\Module\Types\Linq\LambdaExpression
     */
    protected $lambda;

    /**
     * @param string $T
     * @param array $collection
     * @throws \Exception
     */
    public function __construct($T = 'mixed', $collection = [])
    {
        $this->lambda = new LambdaExpression();
        if(!is_array($collection))
            throw new \Exception("Collection must be an array");
        $this->collection = $collection;
        $this->count = count($collection);
        $this->cursor = 0;
        $this->T = $T;
    }

    /*Iterator*/
    abstract public function current();
    abstract public function key();
    abstract public function next();
    abstract public function rewind();
    abstract public function valid();

    /*ILinq*/
    abstract public function ToList();
    abstract public function ToArray();

    /**
     * @param \Closure|string $keySelector Func<TSource, TKey>
     * @param \Closure|string $elementSelector Func<TSource, TElement>
     * @return Dictionary
     */
    abstract public function ToDictionary($keySelector, $elementSelector);
    abstract public function AsEnumerable();

    public function Count(){ return $this->count; }

    /**
     * @param mixed $item
     * @param string $type
     * @throws \Exception
     */
    protected function TypeOf($item, $type)
    {
        $valid = true;
        switch ($type)
        {
            case 'integer':
            case 'int': $valid = is_int($item); break;
            case 'bool': $valid = is_bool($item); break;
            case 'decimal':
            case 'double':
            case 'float': $valid = is_float($item); break;
            case 'array': $valid = is_array($item); break;
            case 'mixed': $valid = true; break;
            case 'string': $valid = is_string($item); break;
            case 'object': $valid = is_object($item); break;
            default: $valid = is_a($item, $type); break;
        }
        if(!$valid)
            throw new \Exception("Item must be instance of {$type}");
    }

    /**
     * @param mixed $item
     * @return string
     */
    protected function GetType($item)
    {
        $type = gettype($item);
        if($type == 'object' || $type == 'unknown type')
            $type = get_class($item);
        return $type;
    }

    /**
     * @param \Closure|string $func
     * @return \Closure
     */
    protected function CheckLambda($func)
    {
        if($this->lambda->DetectLambda($func))
        {
            $this->lambda->ExtractLambda($func);
            $func = $this->lambda->Build();
        }
        return $func;
    }

    /**
     * @param array $collection
	 * @param \ASPHP\Module\Types\Comparer\Comparer $comparer
     * @return array
     */
    protected function InsertionSort($collection, \ASPHP\Module\Types\Comparer\Comparer $comparer = null)
	{
		$length = count($collection);
        $func = \ASPHP\Module\Types\Comparer\Comparer::DefaultCompare();
		for ($i = 0; $i < $length - 1; $i++)
		{
			$index = $i + 1;
			while ($index > 0)
			{
				$bigger = ($comparer == null ?
					$func($collection[$index - 1], $collection[$index]) :
					$comparer->Compare($collection[$index - 1], $collection[$index]));
				if($bigger > 0)
				{
					$temp = $collection[$index - 1];
					$collection[$index - 1] = $collection[$index];
					$collection[$index] = $temp;
				}
				$index--;
			}
		}
		return $collection;
	}

    /**
     * @param array $collection
     * @param \Closure|string $keySelector
	 * @param \ASPHP\Module\Types\Comparer\Comparer $comparer
     * @return array
     */
    protected function SelectorInsertionSort($collection, $keySelector, \ASPHP\Module\Types\Comparer\Comparer $comparer = null)
	{
        $keySelector = $this->CheckLambda($keySelector);
		$length = count($collection);
        $func = \ASPHP\Module\Types\Comparer\Comparer::DefaultCompare();
		for ($i = 0; $i < $length - 1; $i++)
		{
			$index = $i + 1;
			while ($index > 0)
			{
				$bigger = ($comparer == null ?
					$func(
                        $keySelector($collection[$index - 1]),
                        $keySelector($collection[$index])
                        ) :
					$comparer->Compare(
                        $keySelector($collection[$index - 1]),
                        $keySelector($collection[$index])
                        )
                    );
				if($bigger > 0)
				{
					$temp = $collection[$index - 1];
					$collection[$index - 1] = $collection[$index];
					$collection[$index] = $temp;
				}
				$index--;
			}
		}
		return $collection;
	}

    /**
	 * Hash calculator
	 * @param mixed $item
	 * @return mixed
	 */
    protected function Hash($item)
    {
        if(is_object($item))
            return spl_object_hash($item);
        else
            return $item;
    }
}