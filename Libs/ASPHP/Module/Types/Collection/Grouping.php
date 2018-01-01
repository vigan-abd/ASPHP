<?php
namespace ASPHP\Module\Types\Collection;
use \ASPHP\Module\Types\Linq\LambdaExpression;

/**
 * @requires class \ASPHP\Module\Types\Collection\DictionaryEnumerator
 * @requires class \ASPHP\Module\Types\Collection\KeyValuePair
 * @requires class \ASPHP\Module\Types\Collection\IEnumerable
 * @version 1.0
 * @author Vigan
 */
class Grouping extends IEnumerable implements \ArrayAccess
{
    /**
	 * @var mixed
	 */
    protected $TKey = 'mixed';
    /**
	 * @var array
	 */
    protected $keyCollection;

    public function __construct($TKey = 'mixed', $TVal = 'mixed', $keyCollection = [], $valueCollection = [])
    {
        $this->T = $TVal;
        $this->TKey = $TKey;
        if(!is_array($keyCollection))
            throw new \Exception("Collection must be an array");
        $this->count = count($keyCollection);
        $this->keyCollection = [];
        $this->collection = [];
        $this->lambda = new LambdaExpression();
		$this->keyCollection = $keyCollection;
		foreach($valueCollection as $k => $v)
		{
			$this->collection[$k] = $v;
		}
    }

    /*Iterator*/
    /*Combined*/
    public function current(){ return $this->collection[$this->Hash($this->cursor)]; }
    public function key(){ return $this->cursor; }
    public function next(){ $this->cursor = next($this->keyCollection); return $this->cursor; }
    public function rewind(){ $this->cursor = reset($this->keyCollection); }
    public function valid(){ return isset($this->keyCollection[$this->Hash($this->cursor)]); }

    /*ArrayAccess*/
    function offsetExists($offset)
    {
        return isset($this->collection[$this->Hash($offset)]);
    }

    function offsetGet($offset)
    {
        $hKey = $this->Hash($offset);
        if(!isset($this->collection[$hKey]))
            throw new \Exception('Key doesn\'t exist');
        return $this->collection[$hKey];
    }

    function offsetSet($offset, $value) { }

    function offsetUnset($offset) { }

    /*ILinq*/
    public function ToList(){ return $this->AsEnumerable()->ToList(); }
    public function ToArray(){ return parent::ToArray(); }
    /**
	 * @param \Closure|string $keySelector Func<KeyValuePair, TKey>
	 * @param \Closure|string $elementSelector Func<KeyValuePair, TElement>
	 * @return Dictionary
	 */
    public function ToDictionary($keySelector, $elementSelector){ return $this->GroupBy($keySelector, $elementSelector); }
    public function AsEnumerable()
    {
        $colletion = [];
        foreach ($this as $key => $value)
        {
        	$colletion[] = new KeyValuePair($key, $value);
        }
        return new IEnumerable('mixed', $colletion);
    }


    /*IEnumerable Members*/
    public function getIterator()
    {
		return $this->AsEnumerable();
    }

    /**
	 *
	 * @param \Closure|string $func Func<KeyValuePair, bool>
	 * @return bool
	 */
    public function All($func)
    {
        $func = $this->CheckLambda($func);
        foreach ($this as $key => $value)
        {
            if(!$func(new KeyValuePair($key, $value)))
                return false;
        }
        return true;
    }

    /**
	 *
	 * @param \Closure|string $func Func<KeyValuePair, bool>
	 *
	 * @return bool
	 */
    public function Any($func)
    {
        $func = $this->CheckLambda($func);
        foreach ($this as $key => $value)
        {
            if($func(new KeyValuePair($key, $value)))
                return true;
        }
        return false;
    }

    /**
	 *
	 * @param mixed $item
	 * @param \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer IEqualityComparer<KeyValuePair>
	 *
	 * @return bool
	 */
    public function Contains($item, \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer = null)
    {
        if($comparer == null)
        {
            foreach ($this as $key => $value)
            {
                $kv = new KeyValuePair($key, $value);
                if($kv == $item)
                    return true;
            }
        }
        else
        {
            foreach ($this as $key => $value)
            {
                $kv = new KeyValuePair($key, $value);
                if($comparer->Equals($kv, $item))
                    return true;
            }
        }
        return false;
    }

    /**
	 *
	 * @param Grouping|array $second
	 * @return void
	 */
    public function Concat($second)
    {
        foreach ($second as $key => $value)
        {
			$hkey= $this->Hash($key);
			$this->keyCollection[$hkey] = $key;
			$this->collection[$hkey][] = $value;
        }
    }

    /**
	 *
	 * @param int $index
	 *
	 * @return mixed
	 */
    public function ElementAt($index)
    {
        $reach = 0;
        foreach ($this as $key => $value)
        {
        	if($reach == $index)
                return new KeyValuePair($key, $value);
            $reach++;
        }
        throw new \Exception('Index out of Range');
    }

    /**
	 *
	 * @param IEnumerable|array $second
	 * @param \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer IEqualityComparer<KeyValuePair>
	 *
	 * @return IEnumerable
	 */
    public function Except($second, \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer = null)
    {
        if(is_array($second))
            $second = new IEnumerable('mixed', $second);
        var_dump($second);
        $exist = false;
        $collection = [];
        if($comparer != null)
        {
            foreach($this as $key => $value)
            {
                $kv = new KeyValuePair($key, $value);
                foreach($second as $key2 => $value2)
                {
                    $kv2 = new KeyValuePair($key2, $value2);
                    if($comparer->Equals($kv2, $kv))
                    {
                        $exist = true;break;
                    }
                }
                if(!$exist)
                    $collection[] = $kv;
                $exist = false;
            }
        }
        else
        {
            foreach($this as $key => $value)
            {
                foreach($second as $key2 => $value2)
                {
                    if($key2 == $key)
                    {
                        $exist = true;break;
                    }
                }
                if(!$exist)
                    $collection[] = new KeyValuePair($key, $value);
                $exist = false;
            }
        }
        return new IEnumerable('\ASPHP\Module\Types\Collection\KeyValuePair', $collection);
    }

    /**
	 * @param \Closure|string $func Func<KeyValuePair, bool>
	 * @return mixed
	 */
    public function Find($func)
    {
        $func = $this->CheckLambda($func);
        foreach ($this as $key => $value)
        {
        	if($func(new KeyValuePair($key, $value)))
                return $value;
        }
        return null;
    }

    /**
	 *
	 * @param \Closure|string $func Func<KeyValuePair, bool>
	 *
	 * @return mixed
	 */
    public function First($func = null)
    {
        if($func == null)
        {
            if($this->count < 1)
                throw new \Exception("Item not found");
            $this->rewind();
            $curr = $this->current();
            return $curr;
        }
        else
        {
            $f = $this->Find($func);
            if($f == null)
                throw new \Exception("Item not found");
            return $f;
        }
    }

    /**
	 *
	 * @return DictionaryEnumerator
	 */
    public function GetEnumerator()
    {
        return $this->getIterator();
    }

    /**
	 *
	 * @param \Closure|string $keySelector Func<KeyValuePair, TKey>
	 * @param \Closure|string $elementSelector Func<KeyValuePair, TElement>
	 * @return Grouping
	 */
    public function GroupBy($keySelector, $elementSelector)
    {
        $keySelector = $this->CheckLambda($keySelector);
        $elementSelector = $this->CheckLambda($elementSelector);
        $gr = new Grouping('mixed', 'mixed');
        foreach ($this as $key => $value)
        {
            $kv = new KeyValuePair($key, $value);
			$k = $keySelector($kv); $v = $elementSelector($kv);
			$hKey = $this->Hash($k);
            $gr->keyCollection[$hKey] = $k;
            $gr->collection[$hKey][] = $v;
        }
        return $gr;
    }

    /**
	 *
	 * @param IEnumerable|array $second IEnumerable<KeyValuePair>
	 * @param \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer
	 *
	 * @return IEnumerable
	 */
    public function Intersect($second, \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer = null)
    {
        if(is_array($second))
            $second = new IEnumerable('mixed', $second);
        var_dump($second);
        $collection = [];
        if($comparer != null)
        {
            foreach($this as $key => $value)
            {
                $kv = new KeyValuePair($key, $value);
                foreach($second as $key2 => $value2)
                {
                    $kv2 = new KeyValuePair($key2, $value2);
                    if($comparer->Equals($kv2, $kv))
                    {
                        $collection[] = new KeyValuePair($key, $value);
                        break;
                    }
                }
            }
        }
        else
        {
            foreach($this as $key => $value)
            {
                foreach($second as $key2 => $value2)
                {
                    if($key2 == $key)
                    {
                        $collection[] = new KeyValuePair($key, $value);
                        break;
                    }
                }
            }
        }
        return new IEnumerable('\ASPHP\Module\Types\Collection\KeyValuePair', $collection);
    }

    /**
	 *
	 * @param IEnumerable|array $inner IEnumerable<TInner>
	 * @param \Closure|string $outerKeySelector Func<KeyValuePair, TKey>
	 * @param \Closure|string $innerKeySelector Func<TInner, TKey>
	 * @param \Closure|string $resultSelector Func<KeyValuePair, IEnumerable<TInner>, TResult>
	 *
	 * @return IEnumerable
	 */
    public function Join($inner, $outerKeySelector, $innerKeySelector, $resultSelector)
    {
        if(is_array($inner))
            $inner = new IEnumerable('mixed', $inner);
        $length = count($inner);
        $result = [];
        $outerKeySelector = $this->CheckLambda($outerKeySelector);
        $innerKeySelector = $this->CheckLambda($innerKeySelector);
        $resultSelector = $this->CheckLambda($resultSelector);
        foreach ($this as $key => $value)
        {
            $kv = new KeyValuePair($key, $value);
        	for ($j = 0; $j < $length; $j++)
            {
                if($outerKeySelector($kv) == $innerKeySelector($inner->ElementAt($j)))
                    $result[] = $resultSelector($kv, $inner->ElementAt($j));
            }
        }
        return new IEnumerable('mixed', $result);
    }

    /**
	 *
	 * @param \Closure|string $func Func<KeyValuePair, bool>
	 *
	 * @return mixed
	 */
    public function Last($func = null)
    {
        if($func == null)
        {
            return $this->ElementAt($this->count-1);
        }
        else
        {
            $l = $this->Where($func);
            return $l->Last();
        }
    }

    /**
	 *
	 * @param \Closure|string $func Func<KeyValuePair, TSelector>
	 *
	 * @return mixed
	 */
    public function Max($func = null)
    {
        $max = $this->First();
        if($func == null)
        {
            foreach ($this as $value)
            {
                if($value > $max)
                    $max = $value;
            }
        }
        else
        {
            $func = $this->CheckLambda($func);
            foreach ($this as $key => $value)
            {
                if($func(new KeyValuePair($key, $value)) > $max)
                    $max = $this->$value;
            }
        }
        return $max;
    }

    /**
	 *
	 * @param \Closure|string $func Func<KeyValuePair, TSelector>
	 *
	 * @return mixed
	 */
    public function Min($func = null)
    {
        $min = $this->First();
        if($func == null)
        {
            foreach ($this as $value)
            {
                if($value < $min)
                    $min = $value;
            }
        }
        else
        {
            $func = $this->CheckLambda($func);
            foreach ($this as $key => $value)
            {
                if($func(new KeyValuePair($key, $value)) < $min)
                    $min = $this->$value;
            }
        }
        return $min;
    }

    /**
	 *
	 * @param \Closure|string $keySelector Func<KeyValuePair, TSelector>
	 * @param \ASPHP\Module\Types\Comparer\Comparer $comparer
	 *
	 * @return IEnumerable
	 */
    public function OrderBy($keySelector = null, \ASPHP\Module\Types\Comparer\Comparer $comparer = null)
    {
        $enumerator = $this->AsEnumerable();
        $collection = ($keySelector != null ?
            $this->SelectorInsertionSort($enumerator->collection, $keySelector, $comparer) :
            $this->InsertionSort($this->$enumerator->collection, $comparer));
        return new IEnumerable('\ASPHP\Module\Types\Collection\KeyValuePair', $collection);
    }

    /**
	 *
	 * @param \Closure|string $keySelector Func<KeyValuePair, TSelector>
	 * @param \ASPHP\Module\Types\Comparer\Comparer $comparer
	 *
	 * @return IEnumerable
	 */
    public function OrderByDescending($keySelector = null, \ASPHP\Module\Types\Comparer\Comparer $comparer = null)
    {
        $enumerator = $this->AsEnumerable();
        $collection = ($keySelector != null ?
            $this->SelectorInsertionSort($enumerator->collection, $keySelector, $comparer) :
            $this->InsertionSort($this->$enumerator->collection, $comparer));
        return new IEnumerable('\ASPHP\Module\Types\Collection\KeyValuePair', array_reverse($collection));
    }

    /**
	 *
	 * @param \Closure|string $selector Func<KeyValuePair, TSelector>
	 *
	 * @return IEnumerable
	 */
    public function Select($selector)
    {
        return $this->AsEnumerable()->Select($selector);
    }

    /**
	 *
	 * @param \Closure|string $collectionSelector Func<KeyValuePair, IEnumerable<TCollection>>
	 * @param \Closure|string $resultSelector Func<KeyValuePair[i], KeyValuePair[i]->TCollection[j], IEnumerable<TResult>>
	 *
	 * @return IEnumerable
	 */
    public function SelectMany($collectionSelector, $resultSelector = null)
    {
        return $this->AsEnumerable($collectionSelector, $resultSelector);
    }

    /**
	 *
	 * @param \Closure|string $func
	 *
	 * @return mixed
	 */
    public function Single($func = null)
    {
        if($func == null)
        {
            if($this->count != 1)
                throw new \Exception("Sequence contains more than one element");
            return $this->First();
        }
        else
        {
            $func = $this->CheckLambda($func);
            $collection = [];
            foreach ($this as $key => $value)
            {
                $kv = new KeyValuePair($key, $value);
            	if($func($kv))
                    $collection[] = $kv;
            }
            if(count($collection) != 1)
                throw new \Exception("Sequence contains more than one element");
            return $collection[0];
        }
    }

    /**
	 *
	 * @param  $count
	 *
	 * @return IEnumerable
	 */
    public function Skip($count)
    {
        $collection = [];
        $i = 0;
        foreach ($this as $key => $value)
        {
            if($i != $count)
            {
                $i++; continue;
            }
        	$collection[] = new KeyValuePair($key, $value);
        }
        return new IEnumerable('\ASPHP\Module\Types\Collection\KeyValuePair', $collection);
    }

    /**
	 *
	 * @param \Closure|string $func Func<KeyValuePair, bool>
	 *
	 * @return IEnumerable
	 */
    public function SkipWhile($func)
    {
        return $this->AsEnumerable()->SkipWhile($func);
    }

    /**
	 *
	 * @param \Closure|string $func Func<KeyValuePair, int, bool>
	 *
	 * @return IEnumerable
	 */
    public function SkipWhile2($func)
    {
        return $this->AsEnumerable()->SkipWhile2($func);
    }

    /**
	 *
	 * @param \Closure|string $func Func<KeyValuePair, float|double|int>
	 *
	 * @return float|double|int
	 */
    public function Sum($func = null)
    {
        if($this->count == 0)
            return 0;
        $sum = 0.0;
        if($func == null)
        {
            foreach ($this as $value)
            {
            	$sum += $value;
            }
        }
        else
        {
            $func = $this->CheckLambda($func);
            foreach ($this as $key => $value)
            {
            	$sum += $func(new KeyValuePair($key, $value));
            }
        }
        return $sum;
    }

    public function Reverse()
    {
        $this->keyCollection = array_reverse($this->keyCollection);
        $this->collection = array_reverse($this->collection);
    }

    /**
	 *
	 * @param int $count
	 *
	 * @return IEnumerable
	 */
    public function Take($count)
    {
        $collection = [];
        $i = 0;
        foreach ($this as $key => $value)
        {
            if($i == $count)
                break;
        	$collection[] = new KeyValuePair($key, $value);
            $i++;
        }
        return new IEnumerable('\ASPHP\Module\Types\Collection\KeyValuePair', $collection);
    }

    /**
	 *
	 * @param \Closure|string $func Func<KeyValuePair, bool>
	 *
	 * @return IEnumerable
	 */
    public function TakeWhile($func)
    {
        return $this->AsEnumerable()->TakeWhile($func);
    }

    /**
	 *
	 * @param \Closure|string $func Func<KeyValuePair, bool>
	 *
	 * @return IEnumerable
	 */
    public function TakeWhile2($func)
    {
        return $this->AsEnumerable()->TakeWhile2($func);
    }

    /**
	 *
	 * @param IEnumerable $second
	 * @param \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer
	 *
	 * @return IEnumerable
	 */
    public function Union($second, \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer = null)
    {
        return $this->AsEnumerable()->Union($second, $comparer);
    }

    /**
	 * @param \Closure|string $func Func<KeyValuePair, bool>
	 * @return IEnumerable
	 */
    public function Where($func)
    {
        return $this->AsEnumerable()->Where($func);
    }
}
?>