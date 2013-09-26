<?php

namespace Lazyants\WorkflowBundle\Model;

abstract class AbstractCollection implements \Iterator
{
    /**
     * @var AbstractModel[]
     */
    protected $collection;

    public function __construct()
    {
        $this->collection = array();
    }

    /**
     * @param AbstractModel $item
     * @return $this
     * @throws \Exception
     */
    public function add(AbstractModel $item)
    {
        if (!$this->exists($item)) {
            $this->collection[$item->getName()] = $item;
            $this->rewind();
        } else {
            throw new \Exception($item->getName() . ' already present in collection');
        }

        return $this;
    }

    /**
     * @param AbstractModel $item
     * @return $this
     * @throws \Exception
     */
    public function remove(AbstractModel $item)
    {
        if (!$this->exists($item)) {
            throw new \Exception($item->getName() . ' not present in collection');
        } else {
            unset($this->collection[$item->getName()]);
        }

        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->collection[$key]) ? $this->collection[$key] : null;
    }

    /**
     * @param AbstractModel $item
     * @return bool
     */
    public function exists(AbstractModel $item)
    {
        return $this->get($item->getName()) !== null ? true : false;
    }

    /**
     * @return AbstractModel
     */
    public function first()
    {
        foreach ($this->collection as $item) {
            return $item;
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->collection);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->collection);
    }

    /**
     * return void
     */
    public function next()
    {
        next($this->collection);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->collection);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return key($this->collection) !== null;
    }

    /**
     * @return void
     */
    public function rewind()
    {
        reset($this->collection);
    }
}