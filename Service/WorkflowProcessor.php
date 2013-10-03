<?php

namespace Lazyants\WorkflowBundle\Service;

use Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface;
use Lazyants\WorkflowBundle\Event\StepValidationEvent;
use Lazyants\WorkflowBundle\Model\WorkflowStep;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Lazyants\WorkflowBundle\Event\StepReachedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContext;

class WorkflowProcessor
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerAwareInterface
     */
    protected $container;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var \Lazyants\WorkflowBundle\Model\Workflow
     */
    protected $workflow;

    /**
     * @param string $workflowName
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, $workflowName)
    {
        $this->container = $container;
        $this->workflow = $this->getWorkflow($workflowName);
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param SecurityContext $securityContext
     */
    public function setSecurityContext(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param WorkflowedObjectInterface $object
     * @param bool $auto If steps with auto=true should be processed
     */
    public function start(WorkflowedObjectInterface $object, $auto = true)
    {
        $step = $this->workflow->getFirstStep();

        $this->changeCurrentStep($object, $step);

        if ($auto) {
            $this->reachNext($object);
        }
    }

    /**
     * @param WorkflowedObjectInterface $object
     * @param string $nextStepName If there is no auto=true or there are many then one next step
     * @param bool $auto If steps with auto=true should be processed
     */
    public function reachNext(WorkflowedObjectInterface $object, $nextStepName = '', $auto = true)
    {
        $currentStep = $this->workflow->getStep($object->getWorkflowStep());

        if ($nextStepName !== '') {
            $currentStep = $currentStep->getNext()->get($nextStepName);

            $this->changeCurrentStep($object, $currentStep);
        }

        if ($auto && $currentStep->isAuto() && $currentStep->getNext()->count() === 1) {
            $this->reachNext($object, $currentStep->getNext()->first()->getName());
        }
    }

    /**
     * @param WorkflowedObjectInterface $object
     * @param WorkflowStep $step
     * @throws \Exception
     */
    protected function changeCurrentStep(WorkflowedObjectInterface $object, WorkflowStep $step)
    {
        if (!$this->nextStepReachable($object)) {
            throw new \Exception('You have no permissions to reach the next step');
        }

        $object->setWorkflowStep($step->getName());

        $eventName = sprintf('%s.%s.reached', $this->workflow->getName(), $step->getName());
        $this->dispatcher->dispatch($eventName, new StepReachedEvent($step, $object));
    }

    /**
     * @param \Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface $object
     * @return bool
     */
    public function nextStepReachable(WorkflowedObjectInterface $object)
    {
        $violations = array();

        if ($object->getWorkflowStep() != '' && $this->container->isScopeActive('request')) {
            $step = $this->workflow->getStep($object->getWorkflowStep());

            if (count($step->getRoles()) > 0 && !$this->securityContext->isGranted($step->getRoles())) {
                $violations[] = 'Current user doesn\'t have permissions to reach next step';
            } else {
                $event = new StepValidationEvent($step, $object);

                $eventName = sprintf('%s.%s.validation', $this->workflow->getName(), $step->getName());
                $this->dispatcher->dispatch($eventName, $event);
                $violations = $event->getViolations();
            }
        }

        return count($violations) > 0 ? false : true;
    }

    /**
     * @param WorkflowedObjectInterface $object
     * @return \Lazyants\WorkflowBundle\Model\WorkflowStepCollection
     */
    public function nextSteps(WorkflowedObjectInterface $object)
    {
        $nextSteps = $this->workflow->getStep($object->getWorkflowStep())->getNext();

        return $nextSteps;
    }

    /**
     * @return WorkflowStep[]
     */
    public function stepsForCurrentRoles()
    {
        $result = array();

        foreach ($this->workflow->getSteps() as $step) {
            if ($this->securityContext->getToken() === null) {
                $result[] = $step;
            } else {
                foreach ($this->securityContext->getToken()->getRoles() as $role) {
                    if (in_array($role->getRole(), $step->getRoles())) {
                        $result[] = $step;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return WorkflowStep[]
     */
    public function stepNamesForCurrentRoles()
    {
        $result = array();

        foreach ($this->stepsForCurrentRoles() as $step) {
            $result[] = $step->getName();
        }

        return $result;
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