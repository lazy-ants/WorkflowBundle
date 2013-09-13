<?php

namespace Lazyants\WorkflowBundle\Model;

/**
 * Class Workflow
 * @package LazyantsWorkflowBundle
 */
class Workflow extends AbstractModel
{
    /**
     * @var WorkflowStep[]
     */
    protected $steps;

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
     * @return WorkflowStep
     */
    public function getFirstStep()
    {
        /** @var $step \Lazyants\WorkflowBundle\Model\WorkflowStep */
        foreach ($this->getSteps() as $step) {
            if ($step->isStart()) {
                return $step;
            }
        }

        return null;
    }

    /**
     * @return WorkflowStep
     */
    public function getLastStep()
    {
        /** @var $step \Lazyants\WorkflowBundle\Model\WorkflowStep */
        foreach ($this->getSteps() as $step) {
            if ($step->isFinish()) {
                return $step;
            }
        }

        return null;
    }
}