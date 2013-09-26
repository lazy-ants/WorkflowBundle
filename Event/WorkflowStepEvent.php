<?php

namespace Lazyants\WorkflowBundle\Event;

use Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface;
use Lazyants\WorkflowBundle\Model\WorkflowStep;
use Symfony\Component\EventDispatcher\Event;

class WorkflowStepEvent extends Event
{
    /**
     * @var string
     */
    protected $workflow;

    /**
     * @var \Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface
     */
    protected $object;

    /**
     * @var \Lazyants\WorkflowBundle\Model\WorkflowStep
     */
    protected $workflowStep;

    /**
     * @param string $workflow
     * @param WorkflowedObjectInterface $object
     * @param WorkflowStep $workflowStep
     */
    public function __construct($workflow, WorkflowedObjectInterface $object, WorkflowStep $workflowStep)
    {
        $this->workflow = $workflow;
        $this->object = $object;
        $this->workflowStep = $workflowStep;
    }

    /**
     * @return string
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * @return \Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return \Lazyants\WorkflowBundle\Model\WorkflowStep
     */
    public function getWorkflowStep()
    {
        return $this->workflowStep;
    }
}