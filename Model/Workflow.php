<?php

namespace Lazyants\WorkflowBundle\Model;

/**
 * Class Workflow
 * @package LazyantsWorkflowBundle
 */
class Workflow
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var WorkflowStep[]
     */
    protected $steps;

    public function __construct($name)
    {
        $this->name = $name;
        $this->steps = new WorkflowStepCollection();
    }

    /**
     * @param string $name
     * @return Workflow
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param WorkflowStep $step
     * @return Workflow
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