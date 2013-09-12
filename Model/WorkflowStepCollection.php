<?php

namespace Lazyants\WorkflowBundle\Model;

class WorkflowStepCollection extends AbstractCollection
{
    /**
     * @param WorkflowStep $workflowStep
     * @return WorkflowCollection
     * @throws \Exception
     */
    public function add(WorkflowStep $workflowStep)
    {
        if (!$this->exists($workflowStep)) {
            $this->collection[$workflowStep->getName()] = $workflowStep;
            $this->rewind();
        } else {
            throw new \Exception($workflowStep->getName() . ' already present in collection');
        }

        return $this;
    }

    /**
     * @param WorkflowStep $workflowStep
     * @return WorkflowStepCollection
     * @throws \Exception
     */
    public function remove(WorkflowStep $workflowStep)
    {
        if (!$this->exists($workflowStep)) {
            throw new \Exception($workflowStep->getName() . ' not present in collection');
        } else {
            unset($this->collection[$workflowStep->getName()]);
        }

        return $this;
    }

    /**
     * @param string $key
     * @return WorkflowStep
     */
    public function get($key)
    {
        return isset($this->collection[$key]) ? $this->collection[$key] : null;
    }

    /**
     * @param WorkflowStep $workflow
     * @return bool
     */
    public function exists(WorkflowStep $workflowStep)
    {
        return $this->get($workflowStep->getName()) !== null ? true : false;
    }
}