<?php

namespace Lazyants\WorkflowBundle\Service;

use Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface;
use Lazyants\WorkflowBundle\Model\WorkflowStep;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Lazyants\WorkflowBundle\Event\WorkflowStepEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContext;

class WorkflowProcessor implements ContainerAwareInterface
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
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param SecurityContext $securityContext
     */
    public function setSecurityContext(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param string $workflowName
     * @param WorkflowedObjectInterface $object
     * @param bool $auto If steps with auto=true should be processed
     */
    public function start($workflowName, WorkflowedObjectInterface $object, $auto = true)
    {
        $step = $this->getWorkflow($workflowName)->getFirstStep();

        $this->changeCurrentStep($workflowName, $object, $step);

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
            $currentStep = $currentStep->getNext()->get($nextStepName);

            $this->changeCurrentStep($workflowName, $object, $currentStep);
        }

        if ($auto && $currentStep->isAuto() && $currentStep->getNext()->count() === 1) {
            $this->reachNext($workflowName, $object, $currentStep->getNext()->first()->getName());
        }
    }

    /**
     * @param string $workflowName
     * @param WorkflowedObjectInterface $object
     * @param WorkflowStep $step
     * @throws \Exception
     */
    protected function changeCurrentStep($workflowName, WorkflowedObjectInterface $object, WorkflowStep $step)
    {
        if (!$this->stepReachable($step)) {
            throw new \Exception('You have no permissions to reach the next step');
        }

        $object->setWorkflowStep($step->getName());

        $this->stepReachedEvent($workflowName, $object, $step);
    }

    /**
     * @param WorkflowStep $step
     * @return bool
     */
    protected function stepReachable(WorkflowStep $step)
    {
        if (count($step->getRoles()) > 0 &&
            $this->securityContext->getToken() !== null &&
            !$this->securityContext->isGranted($step->getRoles())
        ) {
            return false;
        } else {
            return true;
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

        $nextSteps = $workflow->getStep($object->getWorkflowStep())->getNext();

        return $nextSteps;
    }

    /**
     * @param string $workflowName
     * @return WorkflowStep[]
     */
    public function stepsForCurrentRoles($workflowName)
    {
        $result = array();

        foreach ($this->getWorkflow($workflowName)->getSteps() as $step) {
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
     * @param string $workflowName
     * @return WorkflowStep[]
     */
    public function stepNamesForCurrentRoles($workflowName)
    {
        $result = array();

        foreach ($this->stepsForCurrentRoles($workflowName) as $step) {
            $result[] = $step->getName();
        }

        return $result;
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