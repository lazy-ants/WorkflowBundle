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
    protected $reachedWorkflowStep;

    /**
     * @var \Lazyants\WorkflowBundle\Model\WorkflowStep
     */
    protected $leavedWorkflowStep = null;

    /**
     * @var bool
     */
    protected $flush;

    /**
     * @param WorkflowStep $reachedWorkflowStep
     * @param WorkflowStep $leavedWorkflowStep
     * @param WorkflowedObjectInterface $object
     * @param bool $flush
     */
    public function __construct(
        WorkflowStep $reachedWorkflowStep,
        WorkflowStep $leavedWorkflowStep = null,
        WorkflowedObjectInterface $object,
        $flush
    ) {
        $this->reachedWorkflowStep = $reachedWorkflowStep;
        $this->leavedWorkflowStep = $leavedWorkflowStep;
        $this->object = $object;
        $this->flush = (bool)$flush;
    }

    /**
     * @return \Lazyants\WorkflowBundle\Model\WorkflowStep
     */
    public function getReachedWorkflowStep()
    {
        return $this->reachedWorkflowStep;
    }

    /**
     * @return \Lazyants\WorkflowBundle\Model\WorkflowStep
     */
    public function getLeavedWorkflowStep()
    {
        return $this->leavedWorkflowStep;
    }

    /**
     * @return \Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return bool
     */
    public function isFlushEnabled()
    {
        return $this->flush;
    }
}