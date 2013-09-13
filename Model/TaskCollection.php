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

    /**
     * @param array $tasks
     */
    public function fromArray(array $tasks)
    {
        foreach ($tasks as $name => $description) {
            $task = new Task($name, $description);
            $this->add($task);
        }
    }
}