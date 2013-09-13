<?php

namespace Lazyants\WorkflowBundle\Model;

class WorkflowStepCollection extends AbstractCollection
{
    /**
     * @param string $key
     * @return WorkflowStep
     */
    public function get($key)
    {
        return parent::get($key);
    }
}