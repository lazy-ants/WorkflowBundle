<?php

namespace Lazyants\WorkflowBundle\Service;

use Lazyants\WorkflowBundle\Model\Task;
use Lazyants\WorkflowBundle\Model\TaskCollection;
use Lazyants\WorkflowBundle\Model\Workflow;
use Lazyants\WorkflowBundle\Model\WorkflowCollection;
use Lazyants\WorkflowBundle\Model\WorkflowStep;

class WorkflowManager
{
    /**
     * @var \LazyAnts\WorkflowBundle\Model\TaskCollection
     */
    protected $taskCollection;

    /**
     * @var \LazyAnts\WorkflowBundle\Model\WorkflowCollection
     */
    protected $workflowCollection;

    /**
     * @param array $tasks
     * @param array $workflows
     * @throws \Exception
     */
    public function __construct(array $tasks, array $workflows)
    {
        $this->taskCollection = new TaskCollection();
        $this->taskCollection->fromArray($tasks);

        $this->workflowCollection = new WorkflowCollection();

        foreach ($workflows as $workflowName => $workflowSteps) {
            $workflow = new Workflow($workflowName);

            $steps = $workflow->getSteps();

            foreach ($workflowSteps as $workflowStepName => $workflowStepData) {
                $task = $this->taskCollection->get($workflowStepData['task']);
                if ($task === null) {
                    throw new \Exception('"' . $workflowStepData['task'] . '" not found');
                }

                $workflowStep = new WorkflowStep($workflowStepName, $task);

                $workflowStep
                    ->setAuto((bool)$workflowStepData['auto'])
                    ->setStart((bool)$workflowStepData['start'])
                    ->setFinish((bool)$workflowStepData['finish']);

                $steps->add($workflowStep);
            }

            foreach ($workflowSteps as $workflowStepName => $workflowStepData) {
                foreach ($workflowStepData['next'] as $nextStepName) {
                    if ($steps->get($nextStepName) !== null) {
                        $steps
                            ->get($workflowStepName)
                            ->getNext()
                            ->add($steps->get($nextStepName));
                    } else {
                        throw new \Exception('Workflow step "' . $nextStepName . '" not found');
                    }
                }
            }

            $workflow->setSteps($steps);

            $this->workflowCollection->add($workflow);
        }
    }

    /**
     * @param string $workflow
     * @param string $step
     * @return \Lazyants\WorkflowBundle\Model\WorkflowStepCollection
     * @throws \Exception
     */
    public function next($workflow, $step)
    {
        $workflow = $this->getWorkflows()->get($workflow);
        if ($workflow === null) {
            throw new \Exception($workflow . " doesn't exists");
        }

        $workflowStep = $workflow->getStep($step);
        if ($workflowStep === null) {
            throw new \Exception($step . " doesn't exists");
        }

        return $workflowStep->getNext();
    }

    /**
     * @param string $workflowName
     * @return WorkflowStep
     * @throws \Exception
     */
    public function getFirstStep($workflowName)
    {
        $workflow = $this->getWorkflows()->get($workflowName);
        if ($workflow === null) {
            throw new \Exception('"' . $workflowName . '" not found');
        }

        return $workflow->getFirstStep();
    }

    /**
     * @param string $workflowName
     * @return WorkflowStep
     * @throws \Exception
     */
    public function getLastStep($workflowName)
    {
        $workflow = $this->getWorkflows()->get($workflowName);
        if ($workflow === null) {
            throw new \Exception('"' . $workflowName . '" not found');
        }

        return $workflow->getLastStep();
    }

    /**
     * @return \LazyAnts\WorkflowBundle\Model\TaskCollection
     */
    public function getTasks()
    {
        return $this->taskCollection;
    }

    /**
     * @return \LazyAnts\WorkflowBundle\Model\WorkflowCollection
     */
    public function getWorkflows()
    {
        return $this->workflowCollection;
    }

    /**
     * @param string $workflowName
     * @return Workflow
     * @throws \Exception
     */
    public function getWorkflow($workflowName)
    {
        $workflow = $this->getWorkflows()->get($workflowName);
        if ($workflow === null) {
            throw new \Exception('"' . $workflowName . '" not found');
        } else {
            return $workflow;
        }
    }
}