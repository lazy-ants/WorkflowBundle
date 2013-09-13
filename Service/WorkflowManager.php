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
     */
    public function __construct(array $tasks, array $workflows)
    {
        $this->taskCollection = new TaskCollection();

        foreach ($tasks as $name => $description) {
            $task = new Task($name, $description);
            $this->taskCollection->add($task);
        }

        $this->workflowCollection = new WorkflowCollection();

        foreach ($workflows as $workflowName => $workflowSteps) {
            $workflow = new Workflow($workflowName);

            foreach ($workflowSteps as $workflowStepName => $workflowStepData) {
                $workflowStep = new WorkflowStep(
                    $workflowStepName,
                    $this->taskCollection->get($workflowStepData['task']),
                    $workflow
                );

                $workflowStep
                    ->setAuto((bool)$workflowStepData['auto'])
                    ->setStart((bool)$workflowStepData['start'])
                    ->setFinish((bool)$workflowStepData['finish'])
                    ->setNext($workflowStepData['next']);

                $workflow->addStep($workflowStep);
            }

            $this->workflowCollection->add($workflow);
        }
    }

    /**
     * @param string $workflow
     * @param string $step
     * @return WorkflowStep[]
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

        $nextSteps = $workflowStep->getNext();
        foreach ($nextSteps as $i => $nextStepName) {
            $nextSteps[$i] = $workflow->getStep($nextStepName);
        }

        return $nextSteps;
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
}