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
        if (!$this->steps->exists($step)) {
            $this->steps->add($step);
        } else {
            throw new \Exception($step->getName() . ' already present in collection of ' . $this->getName());
        }

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
     * @return WorkflowStep[]
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

}