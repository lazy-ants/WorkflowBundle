<?php

namespace Lazyants\WorkflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkflowStep
 *
 * @ORM\Table(name="lants_workflow_step",
 *  uniqueConstraints={
 * @ORM\UniqueConstraint(columns={"workflow_id","task_id","prev_step_id"})
 * })
 * @ORM\Entity
 */
class WorkflowStep
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255, nullable=true)
     */
    private $role;

    /**
     * @var boolean
     *
     * @ORM\Column(name="assigne", type="boolean", nullable=false, options={"default" = false})
     */
    private $assigne;

    /**
     * @var boolean
     *
     * @ORM\Column(name="author", type="boolean", nullable=false, options={"default" = false})
     */
    private $author;

    /**
     * @var \Workflow
     *
     * @ORM\ManyToOne(targetEntity="Workflow")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="workflow_id", referencedColumnName="id", onDelete="cascade", nullable=false)
     * })
     */
    private $workflow;

    /**
     * @var \WorkflowTask
     *
     * @ORM\ManyToOne(targetEntity="WorkflowTask")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", onDelete="cascade", nullable=false)
     * })
     */
    private $task;

    /**
     * @var \WorkflowStep
     *
     * @ORM\ManyToOne(targetEntity="WorkflowStep")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="prev_step_id", referencedColumnName="id", onDelete="cascade")
     * })
     */
    private $prevStep;

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
     * Set role
     *
     * @param string $role
     * @return WorkflowStep
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set assigne
     *
     * @param boolean $assigne
     * @return WorkflowStep
     */
    public function setAssigne($assigne)
    {
        $this->assigne = $assigne;

        return $this;
    }

    /**
     * Get assigne
     *
     * @return boolean
     */
    public function getAssigne()
    {
        return $this->assigne;
    }

    /**
     * Set author
     *
     * @param boolean $author
     * @return WorkflowStep
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return boolean
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set workflow
     *
     * @param \Lazyants\WorkflowBundle\Entity\Workflow $workflow
     * @return WorkflowStep
     */
    public function setWorkflow(\Lazyants\WorkflowBundle\Entity\Workflow $workflow = null)
    {
        $this->workflow = $workflow;

        return $this;
    }

    /**
     * Get workflow
     *
     * @return \Lazyants\WorkflowBundle\Entity\Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * Set task
     *
     * @param \Lazyants\WorkflowBundle\Entity\WorkflowTask $task
     * @return WorkflowStep
     */
    public function setTask(\Lazyants\WorkflowBundle\Entity\WorkflowTask $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \Lazyants\WorkflowBundle\Entity\WorkflowTask
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set prevStep
     *
     * @param \Lazyants\WorkflowBundle\Entity\WorkflowStep $prevStep
     * @return WorkflowStep
     */
    public function setPrevStep(\Lazyants\WorkflowBundle\Entity\WorkflowStep $prevStep = null)
    {
        $this->prevStep = $prevStep;

        return $this;
    }

    /**
     * Get prevStep
     *
     * @return \Lazyants\WorkflowBundle\Entity\WorkflowStep
     */
    public function getPrevStep()
    {
        return $this->prevStep;
    }
}