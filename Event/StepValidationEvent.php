<?php

namespace Lazyants\WorkflowBundle\Event;

use Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface;
use Lazyants\WorkflowBundle\Model\WorkflowStep;
use Symfony\Component\EventDispatcher\Event;

class StepValidationEvent extends Event
{
    /**
     * @var WorkflowStep
     */
    private $step;

    /**
     * @var WorkflowedObjectInterface
     */
    private $object;

    /**
     * @var array
     */
    private $violations = array();

    /**
     * @param WorkflowStep $step
     * @param WorkflowedObjectInterface $object
     */
    public function __construct(WorkflowStep $step, WorkflowedObjectInterface $object)
    {
        $this->step = $step;
        $this->object = $object;
    }

    /**
     * Returns current
     *
     * @return WorkflowStep
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Return worflowed object
     *
     * @return WorkflowedObjectInterface
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Returns violation list
     *
     * @return array
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * @param string $message
     */
    public function addViolation($message)
    {
        $this->violations[] = $message;
    }
}
