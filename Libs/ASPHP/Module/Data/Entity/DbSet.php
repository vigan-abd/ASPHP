<?php
namespace ASPHP\Module\Data\Entity;
use \ASPHP\Module\Types\Collection\IEnumerable;
use \ASPHP\Module\Types\Collection\IList;
use \ASPHP\Module\Data\DbConnectionFactory;
use \ASPHP\Module\Data\IDbDataReader;

/**
 * @version 1.0
 * @author Vigan
 */
class DbSet extends DbControl
{

	#region Resource Manager
	protected function MapDrToEntity(IDbDataReader $dr)
	{
		$entity = new $this->TEntity();
		$fields = $this->DbMap()["fields"];
		foreach($fields as $k => $v)
		{
			if($k == '*')
				continue;
			else if (!isset($dr[$v["dbname"]]) && empty($dr[$v["dbname"]]))
				continue;
			$type = strtoupper($v['type']);
			if(in_array($type, $this->entityProvider->DateTypes()))
				$entity->{$k} = new \DateTime($dr[$v["dbname"]]);
			else if(in_array($type, $this->entityProvider->BlobTypes()))
				$entity->{$k} = base64_encode($dr[$v["dbname"]]);
			else
				$entity->{$k} = $dr[$v["dbname"]];
		}
		return $entity;
	}

	protected function MapDrToAnonymous(IDbDataReader $dr)
	{
		$entity = new \stdClass();
		foreach($dr as $k => $v)
		{
			$entity->{$k} = $dr[$k];
		}
		return $entity;
	}

	protected function WhereMapper($func)
	{
		$func = trim(preg_replace("/(.)+\=\>/", "", $func));
		$func = $this->entityProvider->MapOperation($func);
		$parts = explode(" ", $func);
		$length = count($parts);
		$where = "";
		for ($i = 0; $i < $length; $i++)
		{
			if(preg_match("/(.)+\-\>(.)+/", $parts[$i]))
				$parts[$i] = $this->DbMap()["fields"][preg_replace("/\s?(.)+\-\>/", "", $parts[$i])]["dbname"];
			$where .= " ".$parts[$i]." ";
		}
		return $where;
	}

	public function GetType()
	{
		return $this->TEntity;
	}

	/**
	 * @param \string $TEntity
	 * @param Database $database
	 */
	public function __construct($TEntity, Database $database = null, $entityProvider = null)
	{
		$this->TEntity = $TEntity;
		parent::__construct($database, $entityProvider);
	}
	#endregion

    public function Add($entity)
	{
		$args = $this->entityProvider->Insert($this->DbMap(), $entity);
		$this->database->AddCmd($args[0], $args[1]);
	}

	public function AddOrUpdate($entity)
	{
		if($this->Contains($entity))
			$this->Update($entity);
		else
			$this->Add($entity);
	}

    public function AddRange($entities)
	{
		foreach($entities as $entity)
			$this->Add($entity);
	}

    public function All($func)
	{
		$where = $this->WhereMapper($func);
		$args = $this->entityProvider->CountWhere($this->DbMap(), $where);
		$count = 0;
		$this->database->Read($args[0], $args[1], function($dr, &$count) { $count = $dr[0]; }, $count);
		$all = 0;
		$args = $this->entityProvider->CountWhere($this->DbMap(), "1=1");
		$this->database->Read($args[0], $args[1], function($dr, &$all) { $all = $dr[0]; }, $all);
		return $all == $count;
	}

    public function Any($func)
	{
		$where = $this->WhereMapper($func);
		$args = $this->entityProvider->CountWhere($this->DbMap(), $where);
		$count = 0;
		$this->database->Read($args[0], $args[1], function($dr, &$count) { $count = $dr[0]; }, $count);
		if($count > 0)
			return true;
		else
			return false;
	}

    public function AsEnumerable()
	{
		$args = $this->entityProvider->SelectAll($this->DbMap());
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		return new IEnumerable($this->TEntity, $collection);
	}

    public function Average($property, $isDistinct = false)
	{
		$args = $this->entityProvider->SelectAvg($this->DbMap(), $property, $isDistinct);
		$avg = -1;
		$this->database->Read($args[0], $args[1], function($dr, &$avg) { $avg = $dr[0]; }, $avg);
		return $avg;
	}

    public function Contains($entity)
	{
		$args = $this->entityProvider->SelectByPKey($this->DbMap(), $entity);
		$count = 0;
		$this->database->Read($args[0], $args[1], function($dr, &$count)
		{
			$count++;
		}, $count);
		return $count > 0;
	}

	public function Count($property = '*')
	{
		$args = $this->entityProvider->Count($this->DbMap(), $property);
		$cnt = -1;
		$this->database->Read($args[0], $args[1], function($dr, &$cnt) { $cnt = $dr[0]; }, $cnt);
		return $cnt;
	}

    public function ElementAt($index)
	{
		$args = $this->entityProvider->SelectLimitOffset($this->DbMap(), 1, $index);
		$entity = null;
		$this->database->Read($args[0], $args[1], function($dr, &$entity)
		{
			$entity = $this->MapDrToEntity($dr);
		}, $entity);
		return $entity;
	}

    /**
     * @param IEnumerable $second
	 * @param \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer
     * @return IEnumerable
     */
    public function Except($second, \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer = null)
	{
		return $this->AsEnumerable()->Except($second, $comparer);
	}

	public function ExceptSQL($TSecond, $secondProp, $srcProp)
	{
		$args = $this->entityProvider->SelectExcept($this->DbMap(), DbMapper::ExtractMap($TSecond), $secondProp, $srcProp);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		return new IEnumerable('mixed', $collection);
	}

    public function Find($keyValues)
	{
		$entity = new $this->TEntity();
		foreach ($keyValues as $k => $v)
			$entity->{$k} = $v;
		$args = $this->entityProvider->SelectByPKey($this->DbMap(), $entity);
		$entity = null;
		$this->database->Read($args[0], $args[1], function($dr, &$entity)
		{
			$entity = $this->MapDrToEntity($dr);
		}, $entity);
		return $entity;
	}

    public function First($predicate = null)
	{
		if($predicate == null)
			return $this->ElementAt(0);

		$where = $this->WhereMapper($predicate);
		$args = $this->entityProvider->SelectAllWhere($this->DbMap(), $where);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		if(count($collection) > 1)
			throw new \Exception("One or more results returned");
		return $collection[0];
	}

    public function FirstOrDefault($predicate = null)
	{
		try
		{
			return $this->First($predicate);
		}
		catch (\Exception $ex)
		{
			return null;
		}
	}

    /**
	 * @param \Closure|string $keySelector Func<TVar,?TKey>
	 * @param \Closure|string $elementSelector Func<TVar,?TElement>
	 * @return \ASPHP\Module\Types\Collection\Grouping
	 */
    public function GroupBy($keySelector, $elementSelector)
	{
		return $this->AsEnumerable()->GroupBy($keySelector, $elementSelector);
	}

    /**
	 * @param IEnumerable $second
	 * @param \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer
	 * @return IEnumerable
	 */
    public function Intersect($second, \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer = null)
	{
		return $this->AsEnumerable()->Intersect($second, $comparer);
	}

	public function IntersectSQL($TSecond, $secondProp, $srcProp)
	{
		$args = $this->entityProvider->SelectIntersect($this->DbMap(), DbMapper::ExtractMap($TSecond), $secondProp, $srcProp);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToAnonymous($dr);
		}, $collection);
		return new IEnumerable('mixed', $collection);
	}

    /**
	 * @param IEnumerable $inner IEnumerable<TInner>
	 * @param \Closure|string $outerKeySelector Func<TOuter,?TKey>
	 * @param \Closure|string $innerKeySelector Func<TInner,?TKey>
	 * @param \Closure|string $resultSelector Func<TOuter,?IEnumerable<TInner>,?TResult>
	 **/
    public function Join($inner, $outerKeySelector, $innerKeySelector, $resultSelector)
	{
		return $this->AsEnumerable()->Join($inner, $outerKeySelector, $innerKeySelector, $resultSelector);
	}

	/**
	 * @param string $TSecond
	 * @param string $secondKey
	 * @param string $srcKey
	 * @param array $resSrcProps
	 * @param array $resSecondProps
	 */
	public function JoinSQL($TSecond, $secondKey, $srcKey, $resSrcProps, $resSecondProps)
	{
		$args = $this->entityProvider->SelectInnerJoin($this->DbMap(), DbMapper::ExtractMap($TSecond), $secondKey, $srcKey, $resSrcProps, $resSecondProps);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToAnonymous($dr);
		}, $collection);
		return new IEnumerable('mixed', $collection);
	}

    public function Last($predicate = null)
	{
		if($predicate == null)
		{
			$last = $this->Count() - 1;
			if($last < 0)
				throw new \Exception("One or more results returned");
			return $this->ElementAt($last);
		}

		$where = $this->WhereMapper($predicate);
		$args = $this->entityProvider->SelectAllWhere($this->DbMap(), $where);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		$last = count($collection) - 1;
		if($last < 0)
			throw new \Exception("One or more results returned");
		return $collection[$last];
	}

    public function LastOrDefault($predicate = null)
	{
		try
		{
			return $this->Last($predicate);
		}
		catch (\Exception $ex)
		{
			return null;
		}
	}

    public function Max($property)
	{
		$args = $this->entityProvider->SelectMax($this->DbMap(), $property);
		$max = -1;
		$this->database->Read($args[0], $args[1], function($dr, &$max) { $max = $dr[0]; }, $max);
		return $max;
	}

    public function Min($property)
	{
		$args = $this->entityProvider->SelectMin($this->DbMap(), $property);
		$min = -1;
		$this->database->Read($args[0], $args[1], function($dr, &$min) { $min = $dr[0]; }, $min);
		return $min;
	}

    public function OrderBy($property)
	{
		$args = $this->entityProvider->OrderBy($this->DbMap(), $property);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		return new IEnumerable($this->TEntity, $collection);
	}

    public function OrderByDescending($property)
	{
		$args = $this->entityProvider->OrderByDesc($this->DbMap(), $property);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		return new IEnumerable($this->TEntity, $collection);
	}

    public function Remove($entity)
	{
		$args = $this->entityProvider->Delete($this->DbMap(), $entity);
		$this->database->AddCmd($args[0], $args[1]);
	}

    public function Select($properties = ['*'])
	{
		$args = $this->entityProvider->Select($this->DbMap(), $properties);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		return new IEnumerable('mixed', $collection);
	}

	public function SelectMany($collectionSelector, $resultSelector = null)
	{
		return $this->AsEnumerable()->SelectMany($collectionSelector, $resultSelector);
	}

	public function Single($func = null)
	{
		$where = "1=1";
		if($func != null)
		{
			$where = $this->WhereMapper($func);
		}
		$count = 0;
		$args = $this->entityProvider->CountWhere($this->DbMap(), $where);
		$this->database->Read($args[0], $args[1], function($dr, &$count) { $count = $dr[0]; }, $count);
		if($count != 1)
			throw new \Exception("Sequence contains more than one element");
		return $this->First($func);
	}

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
		$args = $this->entityProvider->SelectLimitOffset($this->DbMap(), PHP_INT_MAX, $count);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		return new IEnumerable($this->TEntity, $collection);
	}

    public function SkipWhile($func)
	{
		return $this->AsEnumerable()->SkipWhile($func);
	}

    public function SkipWhile2($func)
	{
		return $this->AsEnumerable()->SkipWhile2($func);
	}

    public function SqlQuery($query, $params = [], $isRead = true)
	{
		if($isRead)
		{
			$collection = [];
			$this->database->Read($query, $params, function($dr, &$collection)
			{
				$collection[] = $this->MapDrToEntity($dr);
			}, $collection);
			return new IEnumerable('mixed', $collection);
		}
		else
		{
			$this->database->AddCmd($query, $params);
			return null;
		}
	}

    public function Sum($property, $isDistinct = false)
	{
		$args = $this->entityProvider->SelectSum($this->DbMap(), $property, $isDistinct);
		$sum = -1;
		$this->database->Read($args[0], $args[1], function($dr, &$sum) { $sum = $dr[0]; }, $sum);
		return $sum;
	}

    public function Take($count)
	{
		$args = $this->entityProvider->SelectLimitOffset($this->DbMap(), $count, 0);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		return new IEnumerable($this->TEntity, $collection);
	}

    /**
	 * @param \Closure|string $func Func<TSource,?bool>
	 * @return IEnumerable
	 */
    public function TakeWhile($func)
    {
		return $this->AsEnumerable()->TakeWhile($func);
    }

    /**
	 * @param \Closure|string $func Func<TSource, int, ?bool>
	 * @return IEnumerable
	 */
    public function TakeWhile2($func)
    {
		return $this->AsEnumerable()->TakeWhile2($func);
    }
    public function ToArray()
	{
		$args = $this->entityProvider->SelectAll($this->DbMap());
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		return $collection;
	}

    public function ToDictionary($keySelector, $elementSelector)
	{
		return $this->AsEnumerable()->ToDictionary($keySelector, $elementSelector);
	}
    public function ToList()
	{
		$args = $this->entityProvider->SelectAll($this->DbMap());
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		return new IList($this->TEntity, $collection);
	}

    public function Union($second, \ASPHP\Module\Types\Comparer\IEqualityComparer $comparer = null)
	{
		return $this->AsEnumerable()->Union($second, $comparer);
	}

	public function UnionSQL($TSecond, $secondProps = ['*'], $srcProps = ['*'])
	{
		$args = $this->entityProvider->SelectUnion($this->DbMap(), DbMapper::ExtractMap($TSecond), $secondProps, $srcProps);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToAnonymous($dr);
		}, $collection);
		return new IEnumerable('mixed', $collection);
	}

	public function Update($entity)
	{
		$args = $this->entityProvider->Update($this->DbMap(), $entity);
		$this->database->AddCmd($args[0], $args[1]);
	}

    public function Where($predicate)
	{
		$where = $this->WhereMapper($predicate);
		$args = $this->entityProvider->SelectAllWhere($this->DbMap(), $where);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		return new IEnumerable($this->TEntity, $collection);
	}

    public function WhereRaw($where)
	{
		$args = $this->entityProvider->SelectAllWhere($this->DbMap(), $where);
		$collection = [];
		$this->database->Read($args[0], $args[1], function($dr, &$collection)
		{
			$collection[] = $this->MapDrToEntity($dr);
		}, $collection);
		return new IEnumerable($this->TEntity, $collection);
	}

}