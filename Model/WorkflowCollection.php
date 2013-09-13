<?php

namespace Lazyants\WorkflowBundle\Model;

class WorkflowCollection extends AbstractCollection
{
    /**
     * @param string $key
     * @return Workflow
     */
    public function get($key)
    {
        return parent::get($key);
    }
}