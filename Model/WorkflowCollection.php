<?php

namespace Lazyants\WorkflowBundle\Model;

class WorkflowCollection extends AbstractCollection
{
    /**
     * @param Workflow $workflow
     * @return WorkflowCollection
     * @throws \Exception
     */
    public function add(Workflow $workflow)
    {
        if (!$this->exists($workflow)) {
            $this->collection[$workflow->getName()] = $workflow;
            $this->rewind();
        } else {
            throw new \Exception($workflow->getName() . ' already present in collection');
        }

        return $this;
    }

    /**
     * @param Workflow $workflow
     * @return WorkflowCollection
     * @throws \Exception
     */
    public function remove(Workflow $workflow)
    {
        if (!$this->exists($workflow)) {
            throw new \Exception($workflow->getName() . ' not present in collection');
        } else {
            unset($this->collection[$workflow->getName()]);
        }

        return $this;
    }

    /**
     * @param string $key
     * @return Workflow
     */
    public function get($key)
    {
        return isset($this->collection[$key]) ? $this->collection[$key] : null;
    }

    /**
     * @param Workflow $workflow
     * @return bool
     */
    public function exists(Workflow $workflow)
    {
        return $this->get($workflow->getName()) !== null ? true : false;
    }
}