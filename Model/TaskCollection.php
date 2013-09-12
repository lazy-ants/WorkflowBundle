<?php

namespace Lazyants\WorkflowBundle\Model;

class TaskCollection extends AbstractCollection
{
    /**
     * @param Task $task
     * @return TaskCollection
     * @throws \Exception
     */
    public function add(Task $task)
    {
        if (!$this->exists($task)) {
            $this->collection[$task->getName()] = $task;
            $this->rewind();
        } else {
            throw new \Exception($task->getName() . ' already present in collection');
        }

        return $this;
    }

    /**
     * @param Task $task
     * @return TaskCollection
     * @throws \Exception
     */
    public function remove(Task $task)
    {
        if (!$this->exists($task)) {
            throw new \Exception($task->getName() . ' not present in collection');
        } else {
            unset($this->collection[$task->getName()]);
        }

        return $this;
    }

    /**
     * @param string $key
     * @return Task
     */
    public function get($key)
    {
        return isset($this->collection[$key]) ? $this->collection[$key] : null;
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function exists(Task $task)
    {
        return $this->get($task->getName()) !== null ? true : false;
    }
}