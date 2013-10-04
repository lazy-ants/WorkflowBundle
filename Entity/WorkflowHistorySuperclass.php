<?php

namespace Lazyants\WorkflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface;

/**
 * @ORM\MappedSuperclass
 */
abstract class WorkflowHistorySuperclass
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_step", type="string", length=100, nullable=false)
     */
    private $workflowStep;

    /**
     * @var WorkflowedObjectInterface
     */
    protected $object;

    /**
     * @var mixed
     */
    protected $managedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @param string $workflowStep
     * @param WorkflowedObjectInterface $object
     */
    public function __construct($workflowStep, WorkflowedObjectInterface $object)
    {
        $this->setWorkflowStep($workflowStep);
        $this->setObject($object);

        $this->createdAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $workflowStep
     * @return $this
     */
    public function setWorkflowStep($workflowStep)
    {
        $this->workflowStep = $workflowStep;

        return $this;
    }

    /**
     * @return string
     */
    public function getWorkflowStep()
    {
        return $this->workflowStep;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param WorkflowedObjectInterface $object
     * @return $this
     */
    public function setObject(WorkflowedObjectInterface $object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return WorkflowedObjectInterface
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $managedBy
     * @return $this
     */
    public function setManagedBy($managedBy)
    {
        $this->managedBy = $managedBy;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getManagedBy()
    {
        return $this->managedBy;
    }
}
