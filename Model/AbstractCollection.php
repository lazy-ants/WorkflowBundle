<?php

namespace Lazyants\WorkflowBundle\Model;

abstract class AbstractCollection implements \Iterator
{
    /**
     * @var array
     */
    protected $collection;

    public function __construct()
    {
        $this->collection = array();
    }

    abstract public function get($key);

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