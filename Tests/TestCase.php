<?php

namespace Lazyants\WorkflowBundle\Tests;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Security\Core\SecurityContext;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    protected function getConfig()
    {
        $yaml = <<<EOF
tasks:
    wft_article_write: 'New article should be written'
    wft_article_approve: 'Article should be approved'
    wft_article_removed: 'Article will be deleted'
    wft_finish: 'Finish workflow'

workflows:
    article_workflow:
        first_step: wfs_article_write
        last_step: wfs_article_removed
        steps:
            wfs_article_write:
                task: wft_article_write
                next: wfs_article_approve
                roles: [ ROLE_ADMIN, ROLE_USER ]

            wfs_article_approve:
                task: wft_article_approve
                next: wfs_article_removed
                roles: ROLE_ADMIN

            wfs_article_removed:
                task: wft_article_removed
                next: wfs_finish
                roles: [ ROLE_ADMIN, ROLE_USER ]

            wfs_finish:
                task: wft_finish
                next: ~
EOF;
        $parser = new Parser();

        return  $parser->parse($yaml);
    }

    protected function getMockSecurityContext()
    {
        $authManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $decisionManager = $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');

        $context = new SecurityContext($authManager, $decisionManager);
        $context->setToken($token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));

        return $context;
    }
}
