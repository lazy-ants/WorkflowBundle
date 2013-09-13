<?php

namespace Lazyants\WorkflowBundle\Service;

use Lazyants\WorkflowBundle\Model\TaskCollection;
use Lazyants\WorkflowBundle\Model\Workflow;
use Lazyants\WorkflowBundle\Model\WorkflowCollection;
use Lazyants\WorkflowBundle\Model\WorkflowStep;

class WorkflowManager
{
    const TASK = 'Task';
    const WORKFLOW = 'Workflow';
    const WORKFLOW_STEP = 'Workflow step';

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

        foreach ($workflows as $workflowName => $workflowData) {
            $workflow = new Workflow($workflowName);
            $workflow->setDescription($workflowData['description']);

            $steps = $workflow->getSteps();

            foreach ($workflowData['steps'] as $workflowStepName => $workflowStepData) {
                $task = $this->taskCollection->get($workflowStepData['task']);
                if ($task === null) {
                    $this->exceptionNotFound($workflowStepData['task'], WorkflowManager::TASK);
                }

                $workflowStep = new WorkflowStep($workflowStepName, $task);

                $workflowStep
                    ->setAuto((bool)$workflowStepData['auto'])
                    ->setDescription($workflowStepData['description']);

                $steps->add($workflowStep);
            }

            foreach ($workflowData['steps'] as $workflowStepName => $workflowStepData) {
                foreach ($workflowStepData['next'] as $nextStepName) {
                    if ($steps->get($nextStepName) !== null) {
                        $steps
                            ->get($workflowStepName)
                            ->getNext()
                            ->add($steps->get($nextStepName));
                    } else {
                        $this->exceptionNotFound($nextStepName, WorkflowManager::WORKFLOW_STEP);
                    }
                }
            }

            $firstStep = $steps->get($workflowData['first_step']);
            if ($firstStep === null) {
                $this->exceptionNotFound($workflowData['first_step'], WorkflowManager::WORKFLOW_STEP);
            } else {
                $workflow->setFirstStep($firstStep);
            }

            $lastStep = $steps->get($workflowData['last_step']);
            if ($lastStep === null) {
                $this->exceptionNotFound($workflowData['last_step'], WorkflowManager::WORKFLOW_STEP);
            } else {
                $workflow->setLastStep($lastStep);
            }

            $workflow->setSteps($steps);

            $this->workflowCollection->add($workflow);
        }
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
            $this->exceptionNotFound($workflowName, WorkflowManager::WORKFLOW);
        }

        return $workflow;
    }

    /**
     * @param string $itemName
     * @param string $type
     * @throws \Exception
     */
    protected function exceptionNotFound($itemName, $type = null)
    {
        throw new \Exception(trim(sprintf('%s" %s" not found', $type, $itemName)));
    }
}