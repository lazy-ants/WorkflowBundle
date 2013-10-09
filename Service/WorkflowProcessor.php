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
     * @var bool
     */
    protected $flush = true;

    /**
     * @param string $workflowName
     * @param ContainerInterface $container
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct($workflowName, ContainerInterface $container, EventDispatcherInterface $dispatcher)
    {
        $this->container = $container;
        $this->workflow = $this->getWorkflow($workflowName);
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
            $nextStep = $currentStep->getNext()->get($nextStepName);

            $violations = $this->changeCurrentStep($object, $nextStep);
            if ($violations !== null) {
                return $violations;
            }

            $currentStep = $nextStep;
        }

        if ($auto && $currentStep->isAuto() && $currentStep->getNext()->count() === 1) {
            $this->reachNext($object, $currentStep->getNext()->first()->getName());
        }
    }

    /**
     * @param WorkflowedObjectInterface $object
     * @param WorkflowStep $step
     * @return array|null
     */
    protected function changeCurrentStep(WorkflowedObjectInterface $object, WorkflowStep $step)
    {
        $violations = $this->nextStepReachable($object);

        if ($violations !== null) {
            return $violations;
        }

        $previousStep = null;
        if ($object->getWorkflowStep() != '') {
            $previousStep = $this->workflow->getStep($object->getWorkflowStep());
        }

        $object->setWorkflowStep($step->getName());

        $eventName = sprintf('%s.%s.reached', $this->workflow->getName(), $step->getName());
        $this->dispatcher->dispatch(
            $eventName,
            new StepReachedEvent($step, $previousStep, $object, $this->isFlushEnabled())
        );

        $eventName = sprintf('%s.step.reached', $this->workflow->getName(), $step->getName());
        $this->dispatcher->dispatch(
            $eventName,
            new StepReachedEvent($step, $previousStep, $object, $this->isFlushEnabled())
        );

        return null;
    }

    /**
     * @param WorkflowedObjectInterface $object
     * @return array|null
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

        return count($violations) > 0 ? $violations : null;
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
            if ($this->container->isScopeActive('request')) {
                foreach ($this->securityContext->getToken()->getRoles() as $role) {
                    if (in_array($role->getRole(), $step->getRoles())) {
                        $result[] = $step;
                    }
                }
            } else {
                $result[] = $step;
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

    /**
     * @return $this
     */
    public function enableFlush()
    {
        $this->setFlush(true);

        return $this;
    }

    /**
     * @return $this
     */
    public function disableFlush()
    {
        $this->setFlush(false);

        return $this;
    }

    /**
     * @param bool $flush
     * @return $this
     */
    protected function setFlush($flush)
    {
        $this->flush = $flush;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFlushEnabled()
    {
        return $this->flush;
    }
}