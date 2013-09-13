<?php

namespace Lazyants\WorkflowBundle\Model;

/**
 * Class Workflow
 * @package LazyantsWorkflowBundle
 */
class Workflow extends AbstractModel
{
    /**
     * @var WorkflowStepCollection
     */
    protected $steps;

    /**
     * @var WorkflowStep
     */
    protected $firstStep;

    /**
     * @var WorkflowStep
     */
    protected $lastStep;

    public function __construct($name)
    {
        $this
            ->setName($name)
            ->setSteps(new WorkflowStepCollection());
    }

    /**
     * @param WorkflowStep $step
     * @return Workflow
     * @throws \Exception
     */
    public function addStep(WorkflowStep $step)
    {
        $this
            ->getSteps()
            ->add($step);

        return $this;
    }

    /**
     * @param WorkflowStepCollection $steps
     * @return $this
     */
    public function setSteps(WorkflowStepCollection $steps)
    {
        $this->steps = $steps;

        return $this;
    }

    /**
     * @return WorkflowStepCollection
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @param string $name
     * @return WorkflowStep
     * @throws \Exception
     */
    public function getStep($name)
    {
        if ($this->steps->get($name) !== null) {
            return $this->steps->get($name);
        } else {
            throw new \Exception('Step "' . $name . '" not exists');
        }
    }

    /**
     * @param \Lazyants\WorkflowBundle\Model\WorkflowStep $firstStep
     * @return $this
     */
    public function setFirstStep(WorkflowStep $firstStep)
    {
        $this->firstStep = $firstStep;

        return $this;
    }

    /**
     * @return \Lazyants\WorkflowBundle\Model\WorkflowStep
     */
    public function getFirstStep()
    {
        return $this->firstStep;
    }

    /**
     * @param \Lazyants\WorkflowBundle\Model\WorkflowStep $lastStep
     * @return $this
     */
    public function setLastStep(WorkflowStep $lastStep)
    {
        $this->lastStep = $lastStep;

        return $this;
    }

    /**
     * @return \Lazyants\WorkflowBundle\Model\WorkflowStep
     */
    public function getLastStep()
    {
        return $this->lastStep;
    }
}