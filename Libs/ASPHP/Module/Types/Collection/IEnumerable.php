<?php
namespace ASPHP\Module\Types\Collection;

/**
 * @requires class \ASPHP\Module\Types\Collection\CollectionBase
 * @version 1.0
 * @author Vigan
 */
class IEnumerable extends CollectionBase
{
    public function __construct($T = 'mixed', $collection = [])
    {
        parent::__construct($T,$collection);
    }

    /*Iterator*/
    public function current(){ return $this->collection[$this->cursor]; }
    public function key(){ return $this->cursor; }
    public function next(){ ++$this->cursor; }
    public function rewind(){ $this->cursor = 0; }
    public function valid(){ return isset($this->collection[$this->cursor]); }
    public function getIterator()
    {
        return new IEnumerable($this->T, $this->collection);
    }


    /*ILinq*/
    public function ToList(){ return new IList($this->T, $this->collection); }
    public function ToArray(){ return $this->collection; }
    public function ToDictionary($keySelector, $elementSelector){ return $this->GroupBy($keySelector, $elementSelector); }
    public function AsEnumerable(){ return new IEnumerable($this->T, $this->collection); }

    /*IEnumerable*/
    /**
     * @param \Closure|string $func Func<TSource, bool>
     * @return bool
     */
    public function All($func)
    {
        $func = $this->CheckLambda($func);
        for ($i = 0; $i < $this->count; $i++)
        {
            if(!$func($this->collection[$i]))
                return false;
        }
        return true;
    }

    /**
     * @param \Closure|string $func Func<TSource, bool>
     * @return bool
     */
    public function Any($func)
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
     * @param \Closure|string $func Func<TSource, float|double|int>
     * @return float|double|int
     */
    public function Average($func = null)
    {
        return ($this->count > 0) ? ($this->Sum($func) / $this->count) : 0;
    }

    /**
     * @param mixed $item
	 * @param \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer
     * @return bool
     */
    public function Contains($item, \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer = null)
    {
        if($comparer == null)
        {
            for ($i = 0; $i < $this->count; $i++)
            {
                if($item == $this->collection[$i])
                    return true;
            }
        }
        else
        {
            for ($i = 0; $i < $this->count; $i++)
            {
                if($comparer->Equals($item, $this->collection[$i]))
                    return true;
            }
        }
        return false;
    }

    /**
     * @param IEnumerable $second
     * @return IEnumerable
     */
    public function Concat($second)
    {
        $collection = $this->collection;
        for ($i = 0; $i < $second->count; $i++)
        {
        	$collection[] = $second->ElementAt($i);
        }
        return new IEnumerable($this->T, $collection);
    }

    /**
     * @param int $index
     * @throws \Exception
     * @return mixed
     */
    public function ElementAt($index)
    {
        if(!isset($this->collection[$index]))
            throw new \Exception("No item found");
        return $this->collection[$index];
    }

    /**
     * @param int $index
     * @return mixed
     */
    public function ElementAtOrDefault($index)
    {
        try
        {
        	return $this->ElementAt($index);
        }
        catch (\Exception $exception)
        {
            return null;
        }
    }

    /**
     * @param IEnumerable $second
	 * @param \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer
     * @return IEnumerable
     */
    public function Except($second, \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer = null)
    {
        if(is_array($second))
            $second = new IEnumerable('mixed', $second);
        $length = count($second);
        $exist = false;
        $collection = [];
        if($comparer != null)
        {
            for ($i = 0; $i < $this->count; $i++)
            {
                for ($j = 0; $j < $length; $j++)
                {
                    if($comparer->Equals($second->ElementAt($j), $this->collection[$i]))
                    {
                        $exist = true;break;
                    }
                }
                if(!$exist)
                    $collection[] = $this->collection[$i];
                $exist = false;
            }
        }
        else
        {
            for ($i = 0; $i < $this->count; $i++)
            {
                for ($j = 0; $j < $length; $j++)
                {
                    if($second->ElementAt($j) == $this->collection[$i])
                    {
                        $exist = true;break;
                    }
                }
                if(!$exist)
                    $collection[] = $this->collection[$i];
                $exist = false;
            }
        }
        return new IEnumerable($this->T, $collection);
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
     * @throws \Exception
     * @return mixed
     */
    public function First($func = null)
    {
        if($func == null)
        {
            if(!isset($this->collection[0]))
                throw new \Exception("Item not found");
            return $this->collection[0];
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
     * @param \Closure|string $func Func<TVar, bool>
     * @return mixed
     */
    public function FirstOrDefault($func = null)
    {
        try
        {
        	return $this->First($func);
        }
        catch (\Exception $exception)
        {
            return null;
        }
    }

    public function GetEnumerator()
    {
        return new IEnumerable($this->T, $this->collection);
    }

    /**
     * @param \Closure|string $keySelector Func<TVar,?TKey>
     * @param \Closure|string $elementSelector Func<TVar,?TElement>
     * @return Grouping
     */
    public function GroupBy($keySelector, $elementSelector)
    {
        $keySelector = $this->CheckLambda($keySelector);
        $elementSelector = $this->CheckLambda($elementSelector);
        $TKey = $this->GetType($keySelector($this->collection[0]));
        $TVal = $this->GetType($elementSelector($this->collection[0]));
		$kCol = []; $vCol = [];
        for ($i = 0; $i < $this->count; $i++)
        {
			$k = $keySelector($this->collection[$i]);
			$v = $elementSelector($this->collection[$i]);
			$hKey = $this->Hash($k);
			$kCol[$hKey] = $k;
			$vCol[$hKey][] = $v;
        }
        return new Grouping($TKey, $TVal, $kCol, $vCol);
    }

    /**
     * @param IEnumerable $second
	 * @param \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer
     * @return IEnumerable
     */
    public function Intersect($second, \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer = null)
    {
        if(is_array($second))
            $second = new IEnumerable('mixed', $second);
        $length = count($second);
        $collection = [];
        if($comparer != null)
        {
            for ($i = 0; $i < $this->count; $i++)
            {
                for ($j = 0; $j < $length; $j++)
                {
                    if($comparer->Equals($second->ElementAt($j), $this->collection[$i]))
                    {
                        $collection[] = $this->collection[$i];
                        break;
                    }
                }
            }
        }
        else
        {
            for ($i = 0; $i < $this->count; $i++)
            {
                for ($j = 0; $j < $length; $j++)
                {
                    if($second->ElementAt($j) == $this->collection[$i])
                    {
                        $collection[] = $this->collection[$i];
                        break;
                    }
                }
            }
        }

        return new IEnumerable($this->T, $collection);
    }

    /**
     * @param IEnumerable $inner IEnumerable<TInner>
     * @param \Closure|string $outerKeySelector Func<TOuter,?TKey>
     * @param \Closure|string $innerKeySelector Func<TInner,?TKey>
     * @param \Closure|string $resultSelector Func<TOuter,?IEnumerable<TInner>,?TResult>
     **/
    public function Join($inner, $outerKeySelector, $innerKeySelector, $resultSelector)
    {
        if(is_array($inner))
            $inner = new IEnumerable('mixed', $inner);
        $length = count($inner);
        $result = [];
        $outerKeySelector = $this->CheckLambda($outerKeySelector);
        $innerKeySelector = $this->CheckLambda($innerKeySelector);
        $resultSelector = $this->CheckLambda($resultSelector);
        for ($i = 0; $i < $this->count; $i++)
        {
        	for ($j = 0; $j < $length; $j++)
            {
                if($outerKeySelector($this->collection[$i]) == $innerKeySelector($inner->ElementAt($j)))
                    $result[] = $resultSelector($this->collection[$i], $inner->ElementAt($j));
            }
        }
        return new IEnumerable('mixed', $result);
    }

    /**
     * @param \Closure|string $func Func<TVar, bool>
     * @throws \Exception
     * @return mixed
     */
    public function Last($func = null)
    {
        if($func == null)
        {
            if(!isset($this->collection[$this->count-1]))
                throw new \Exception("No item found");
            return $this->collection[$this->count-1];
        }
        else
        {
            $l = $this->Where($func);
            return $l->Last();
        }
    }

    /**
     * @param \Closure|string $func Func<TVar, bool>
     * @return mixed
     */
    public function LastOrDefault($func = null)
    {
        try
        {
            return $this->Last($func);
        }
        catch (\Exception $ex)
        {
            return null;;
        }
    }

    /**
     * @param \Closure|string $func Func<TSource, TSelector>
     * @return mixed
     */
    public function Max($func = null)
    {
        $max = $this->collection[0];
        if($func == null)
        {
            for ($i = 1; $i < $this->count; $i++)
            {
                if($this->collection[$i] > $max)
                    $max = $this->collection[$i];
            }
        }
        else
        {
            $func = $this->CheckLambda($func);
            for ($i = 1; $i < $this->count; $i++)
            {
                if($func($this->collection[$i]) > $max)
                    $max = $this->collection[$i];
            }
        }
        return $max;
    }

    /**
     * @param \Closure|string $func Func<TSource, TSelector>
     * @return mixed
     */
    public function Min($func = null)
    {
        $min = $this->collection[0];
        if($func == null)
        {
            for ($i = 1; $i < $this->count; $i++)
            {
                if($this->collection[$i] < $min)
                    $min = $this->collection[$i];
            }
        }
        else
        {
            $func = $this->CheckLambda($func);
            for ($i = 1; $i < $this->count; $i++)
            {
                if($func($this->collection[$i]) < $min)
                    $min = $this->collection[$i];
            }
        }
        return $min;
    }

    /**
     * @param \Closure|string $keySelector Func<TSource, TSelector>
	 * @param \ASPHP\Module\Types\Comparer\Comparer $comparer
     * @return IEnumerable
     */
    public function OrderBy($keySelector = null, \ASPHP\Module\Types\Comparer\Comparer $comparer = null)
    {
        $collection = ($keySelector != null ?
            $this->SelectorInsertionSort($this->collection, $keySelector, $comparer) :
            $this->InsertionSort($this->collection, $comparer));
        return new IEnumerable($this->T, $collection);
    }

    /**
     * @param \Closure|string $keySelector Func<TSource, TSelector>
	 * @param \ASPHP\Module\Types\Comparer\Comparer $comparer
     * @return IEnumerable
     */
    public function OrderByDescending($keySelector = null, \ASPHP\Module\Types\Comparer\Comparer $comparer = null)
    {
        $collection = ($keySelector != null ?
            $this->SelectorInsertionSort($this->collection, $keySelector, $comparer) :
            $this->InsertionSort($this->collection, $comparer));
        return new IEnumerable($this->T, array_reverse($collection));
    }

    /**
     * @param \Closure|string $selector Func<TSource, TSelector>
     * @return IEnumerable
     */
    public function Select($selector)
    {
        $selector = $this->CheckLambda($selector);
        $collection = [];
        for ($i = 0; $i < $this->count; $i++)
        {
        	$collection[] = $selector($this->collection[$i]);
        }
        return new IEnumerable($this->T, $collection);
    }

    /**
     * @param \Closure|string $collectionSelector Func<TSource,?IEnumerable<TCollection>>
     * @param \Closure|string $resultSelector Func<TSource[i],?TSource[i]->TCollection[j],?IEnumerable<TResult>>
     * @return IEnumerable
     */
    public function SelectMany($collectionSelector, $resultSelector = null)
    {
        $collection = [];
        if($resultSelector == null)
        {
            $collectionSelector = $this->CheckLambda($collectionSelector);
            for ($i = 0; $i < $this->count; $i++)
            {
            	$innerCollection = $collectionSelector($this->collection[$i]);
                foreach ($innerCollection as $value)
                {
                	$collection[] = $value;
                }
            }
        }
        else
        {
            $collectionSelector = $this->CheckLambda($collectionSelector);
            $resultSelector = $this->CheckLambda($resultSelector);
            for ($i = 0; $i < $this->count; $i++)
            {
            	$innerCollection = $collectionSelector($this->collection[$i]);
                foreach ($innerCollection as $value)
                {
                	$collection[] = $resultSelector($this->collection[$i], $value);
                }
            }
        }
        return new IEnumerable('mixed', $collection);
    }

    /**
     * @param \Closure|string $func
     * @throws \Exception
     * @return mixed
     */
    public function Single($func = null)
    {
        if($func == null)
        {
            if($this->count != 1)
                throw new \Exception("Sequence contains more than one element");
            return $this->collection[0];
        }
        else
        {
            $func = $this->CheckLambda($func);
            $collection = [];
            for ($i = 0; $i < $this->count; $i++)
            {
            	if($func($this->collection[$i]))
                    $collection[] = $this->collection[$i];
            }
            if(count($collection) != 1)
                throw new \Exception("Sequence contains more than one element");
            return $collection[0];
        }
    }

    /**
     * @param \Closure|string $func
     * @throws \Exception
     * @return mixed
     */
    public function SingleOrDefault($func = null)
    {
        try
        {
            return $this->Single($func);
        }
        catch (\Exception $ex)
        {
            return null;
        }
    }

    public function Skip($count)
    {
        $collection = [];
        for ($i = $count; $i < $this->count; $i++)
        {
        	$collection[] = $this->collection[$i];
        }
        return new IEnumerable($this->T, $collection);
    }

    /**
     * @param \Closure|string $func Func<TSource,?bool>
     * @return IEnumerable
     */
    public function SkipWhile($func)
    {
        $func = $this->CheckLambda($func);
        $collection = [];
        $k = 0;
        for ($i = 0; $i < $this->count; $i++)
        {
            if($func($this->collection[$i]))
                $k++;
            else
                break;
        }
        for ($i = $k; $i < $this->count; $i++)
        {
        	$collection[] = $this->collection[$i];
        }
        return new IEnumerable($this->T, $collection);
    }

    /**
     * @param \Closure|string $func Func<TSource, int,?bool>
     * @return IEnumerable
     */
    public function SkipWhile2($func)
    {
        $func = $this->CheckLambda($func);
        $collection = [];
        $k = 0;
        for ($i = 0; $i < $this->count; $i++)
        {
            if($func($this->collection[$i], $i))
                $k++;
            else
                break;
        }
        for ($i = $k; $i < $this->count; $i++)
        {
        	$collection[] = $this->collection[$i];
        }
        return new IEnumerable($this->T, $collection);
    }

    /**
     * @param \Closure|string $func Func<TSource, float|double|int>
     * @return float|double|int
     */
    public function Sum($func = null)
    {
        if($this->count == 0)
            return 0;
        $sum = 0.0;
        if($func == null)
        {
            for ($i = 0; $i < $this->count; $i++)
            {
            	$sum += $this->collection[$i];
            }
        }
        else
        {
            $func = $this->CheckLambda($func);
            for ($i = 0; $i < $this->count; $i++)
            {
            	$sum += $func($this->collection[$i]);
            }
        }
        return $sum;
    }

    public function Reverse()
    {
        $this->collection = array_reverse($this->collection);
    }

    /**
     * @param int $count
     * @return IEnumerable
     */
    public function Take($count)
    {
        $collection = [];
        for ($i = 0; $i < $count; $i++)
        {
        	$collection[] = $this->collection[$i];
        }
        return new IEnumerable($this->T, $collection);
    }

    /**
     * @param \Closure|string $func Func<TSource,?bool>
     * @return IEnumerable
     */
    public function TakeWhile($func)
    {
        $func = $this->CheckLambda($func);
        $collection = [];
        for ($i = 0; $i < $this->count; $i++)
        {
            if(!$func($this->collection[$i]))
                break;
            else
                $collection[] = $this->collection[$i];
        }
        return new IEnumerable($this->T, $collection);
    }

    /**
     * @param \Closure|string $func Func<TSource,?bool>
     * @return IEnumerable
     */
    public function TakeWhile2($func)
    {
        $func = $this->CheckLambda($func);
        $collection = [];
        for ($i = 0; $i < $this->count; $i++)
        {
            if(!$func($this->collection[$i], $i))
                break;
            else
                $collection[] = $this->collection[$i];
        }
        return new IEnumerable($this->T, $collection);
    }

    /**
     * @param IEnumerable $second
	 * @param \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer
     * @return IEnumerable
     */
    public function Union($second, \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer = null)
    {
        $collection = $this->collection;
        if(is_array($second))
            $second = new IEnumerable('mixed', $second);
        $length = count($second);
        $add = true;
        if($comparer == null)
        {
            for ($i = 0; $i < $length; $i++)
            {
                $add = true;
                for ($j = 0; $j < $this->count; $j++)
                {
                	if($second->ElementAt($i) == $collection[$j])
                    {
                        $add = false;
                        break;
                    }
                }
                if($add)
                    $collection[] = $second->ElementAt($i);
            }
        }
        else
        {
            for ($i = 0; $i < $length; $i++)
            {
                $add = true;
                for ($j = 0; $j < $this->count; $j++)
                {
                	if($comparer->Equals($second->ElementAt($i), $collection[$j]))
                    {
                        $add = false;
                        break;
                    }
                }
                if($add)
                    $collection[] = $second->ElementAt($i);
            }
        }
        return new IEnumerable($this->T, $collection);
    }

    /**
     * @param \Closure|string $func Func<TVar, bool>
     * @return IEnumerable
     */
    public function Where($func)
    {
        $collection = [];
        $func = $this->CheckLambda($func);
        for ($i = 0; $i < $this->count; $i++)
        {
        	if($func($this->collection[$i]))
                $collection[] = $this->collection[$i];
        }
        return new IEnumerable($this->T, $collection);
    }
}
?>