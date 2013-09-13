<?php

namespace Lazyants\WorkflowBundle\Model;

/**
 * Class WorkflowStep
 * @package LazyantsWorkflowBundle
 */
class WorkflowStep extends AbstractModel
{
    /**
     * @var Task
     */
    protected $task;

    /**
     * @var boolean
     */
    protected $auto = false;

    /**
     * @var WorkflowStepCollection
     */
    protected $next;

    /**
     * @var string
     */
    protected $role;

    /**
     * @param string $name
     * @param Task $task
     */
    public function __construct($name, Task $task)
    {
        $this
            ->setName($name)
            ->setTask($task)
            ->setNext(new WorkflowStepCollection());
    }

    /**
     * @param \Lazyants\WorkflowBundle\Model\Task $task
     * @return WorkflowStep
     */
    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return \Lazyants\WorkflowBundle\Model\Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param boolean $auto
     * @return WorkflowStep
     */
    public function setAuto($auto)
    {
        $this->auto = $auto;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAuto()
    {
        return $this->auto;
    }

    /**
     * @param WorkflowStepCollection $next
     * @return $this
     */
    public function setNext(WorkflowStepCollection $next)
    {
        $this->next = $next;

        return $this;
    }

    /**
     * @return WorkflowStepCollection
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param string $role
     * @return WorkflowStep
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
}