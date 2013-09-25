<?php

namespace Lazyants\WorkflowBundle\Model;

/**
 * Class Task
 * @package LazyantsWorkflowBundle
 */
class Task extends AbstractModel
{
    public function __construct($name, $description = '')
    {
        $this
            ->setName($name)
            ->setDescription($description);
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }
}