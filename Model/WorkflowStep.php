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
    protected $start = false;

    /**
     * @var boolean
     */
    protected $auto = false;

    /**
     * @var array
     */
    protected $next;

    /**
     * @var string
     */
    protected $role;

    /**
     * @var Workflow
     */
    protected $workflow;

    /**
     * @var boolean
     */
    protected $finish = false;

    /**
     * @param $name
     * @param Task $task
     * @param Workflow $workflow
     */
    public function __construct($name, Task $task, Workflow $workflow)
    {
        $this
            ->setName($name)
            ->setTask($task)
            ->setWorkflow($workflow);
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
     * @param array $next
     * @return WorkflowStep
     */
    public function setNext(array $next)
    {
        $this->next = $next;

        return $this;
    }

    /**
     * @return array
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

    /**
     * @param boolean $start
     * @return WorkflowStep
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isStart()
    {
        return $this->start;
    }

    /**
     * @param boolean $finish
     * @return WorkflowStep
     */
    public function setFinish($finish)
    {
        $this->finish = $finish;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getFinish()
    {
        return $this->finish;
    }

    /**
     * @param \Lazyants\WorkflowBundle\Model\Workflow $workflow
     * @return WorkflowStep
     */
    public function setWorkflow($workflow)
    {
        $this->workflow = $workflow;

        return $this;
    }

    /**
     * @return \Lazyants\WorkflowBundle\Model\Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }
}