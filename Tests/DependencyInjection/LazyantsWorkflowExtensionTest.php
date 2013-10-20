<?php

namespace Lazyants\WorkflowBundle\Tests\DependencyInjection;

use Lazyants\WorkflowBundle\DependencyInjection\LazyantsWorkflowExtension;
use Lazyants\WorkflowBundle\Model\Workflow;
use Lazyants\WorkflowBundle\Service\WorkflowManager;
use Lazyants\WorkflowBundle\Tests\TestCase;
use Lazyants\WorkflowBundle\DependencyInjection\LexikWorkflowExtension;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LazyantsWorkflowExtensionTest extends TestCase
{
    public function testLoad()
    {
        $container = new ContainerBuilder();

        // fake entity manager and security context services
        $container->set('security.context', $this->getMockSecurityContext());
        $container->set('event_dispatcher', new EventDispatcher());

        $extension = new LazyantsWorkflowExtension();
        $extension->load(array($this->getConfig()), $container);

        $this->assertTrue(is_array($container->getParameter('lazyants.tasks')));
        $this->assertTrue(is_array($container->getParameter('lazyants.workflows')));

        /** @var $workflowManager \Lazyants\WorkflowBundle\Service\WorkflowManager */
        $workflowManager = $container->get('lazyants.workflow_manager');
        $this->assertTrue($workflowManager instanceof WorkflowManager);
        $this->assertTrue($workflowManager->getWorkflow('article_workflow') instanceof Workflow);
    }
}
