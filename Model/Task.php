<?php

namespace Lazyants\WorkflowBundle\Model;

/**
 * Class Task
 * @package LazyantsWorkflowBundle
 */
class Task
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    public function __construct($name, $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @param string $name
     * @return Task
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $description
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}