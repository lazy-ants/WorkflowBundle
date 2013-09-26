<?php

namespace Lazyants\WorkflowBundle\Service;

use Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface;
use Lazyants\WorkflowBundle\Model\WorkflowStep;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Lazyants\WorkflowBundle\Event\WorkflowStepEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WorkflowProcessor implements ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerAwareInterface
     */
    private $container;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param string $workflowName
     * @param WorkflowedObjectInterface $object
     * @param bool $auto If steps with auto=true should be processed
     */
    public function start($workflowName, WorkflowedObjectInterface $object, $auto = true)
    {
        $step = $this->getWorkflow($workflowName)->getFirstStep();
        $object->setWorkflowStep($step->getName());

        if ($auto) {
            $this->reachNext($workflowName, $object);
        }
    }

    /**
     * @param string $workflowName
     * @param WorkflowedObjectInterface $object
     * @param string $nextStepName If there is no auto=true or there are many then one next step
     * @param bool $auto If steps with auto=true should be processed
     */
    public function reachNext($workflowName, WorkflowedObjectInterface $object, $nextStepName = '', $auto = true)
    {
        $workflow = $this->getWorkflow($workflowName);
        $currentStep = $workflow->getStep($object->getWorkflowStep());

        if ($nextStepName !== '') {
            $nextStep = $currentStep->getNext()->get($nextStepName);

            $object->setWorkflowStep($nextStep->getName());
            $currentStep = $nextStep;

            $this->stepReachedEvent($workflowName, $object, $currentStep);
        }

        if ($auto && $currentStep->isAuto() && $currentStep->getNext()->count() === 1) {
            $object->setWorkflowStep($currentStep->getNext()->first()->getName());

            $this->stepReachedEvent($workflowName, $object, $currentStep);

            $this->reachNext($workflowName, $object);
        }
    }

    /**
     * @param string $workflowName
     * @param WorkflowedObjectInterface $object
     * @return \Lazyants\WorkflowBundle\Model\WorkflowStepCollection
     */
    public function nextSteps($workflowName, WorkflowedObjectInterface $object)
    {
        $workflow = $this->getWorkflow($workflowName);

        return $workflow->getStep($object->getWorkflowStep())->getNext();
    }

    /**
     * @param string $workflowName
     * @param WorkflowedObjectInterface $object
     * @param WorkflowStep $step
     */
    protected function stepReachedEvent($workflowName, WorkflowedObjectInterface $object, WorkflowStep $step)
    {
        $eventName = sprintf('%s.%s.reached', $workflowName, $step->getName());
        $this->dispatcher->dispatch($eventName, new WorkflowStepEvent($workflowName, $object, $step));
    }

    /**
     * @param string $workflowName
     * @return \Lazyants\WorkflowBundle\Model\Workflow
     */
    protected function getWorkflow($workflowName)
    {
        return $this->container->get('lazyants.workflow_manager')->getWorkflow($workflowName);
    }
}