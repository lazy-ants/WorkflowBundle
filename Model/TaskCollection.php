<?php

namespace Lazyants\WorkflowBundle\Model;

class TaskCollection extends AbstractCollection
{
    /**
     * @param string $key
     * @return Task
     */
    public function get($key)
    {
        return parent::get($key);
    }
}