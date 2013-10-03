<?php

namespace Lazyants\WorkflowBundle\Event;

use Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface;
use Lazyants\WorkflowBundle\Model\WorkflowStep;
use Symfony\Component\EventDispatcher\Event;

class StepReachedEvent extends Event
{
    /**
     * @var \Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface
     */
    protected $object;

    /**
     * @var \Lazyants\WorkflowBundle\Model\WorkflowStep
     */
    protected $workflowStep;

    /**
     * @param WorkflowStep $workflowStep
     * @param WorkflowedObjectInterface $object
     */
    public function __construct(WorkflowStep $workflowStep, WorkflowedObjectInterface $object)
    {
        $this->object = $object;
        $this->workflowStep = $workflowStep;
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