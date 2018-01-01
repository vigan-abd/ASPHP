<?php
namespace ASPHP\Module\Types\Collection;

/**
 * @requires class \ASPHP\Module\Types\Collection\KeyValuePair
 * @author Vigan
 * @version 1.0
 */
class DictionaryEnumerator
{
    protected $collection;
    protected $keyCollection;
    protected $cursor;
    protected $count;

    public function __construct($collection, $keyCollection)
    {
        $this->collection = $collection;
        $this->keyCollection = $keyCollection;
        $this->count = count($keyCollection);
        $this->Reset();
    }

    public function Count(){ return $this->count; }
    public function Current()
    {
        $hKey = $this->Hash($this->cursor);
        return new KeyValuePair($this->keyCollection[$hKey] ,$this->collection[$hKey]);
    }
    public function MoveNext(){ $this->cursor = next($this->keyCollection); return $this->Valid(); }
    public function Reset(){ $this->cursor = reset($this->keyCollection); }
    public function Valid(){ return isset($this->keyCollection[$this->Hash($this->cursor)]); }

    /**
	 * Exports keyCollection and valueCollection, ['keys'=>keyCollection, 'values'=>collection]
	 */
    public function Export()
    {
        return ['keys'=>$this->keyCollection, 'values'=>$this->collection];
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
?>