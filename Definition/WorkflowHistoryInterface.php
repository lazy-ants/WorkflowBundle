<?php

namespace Lazyants\WorkflowBundle\Definition;

interface WorkflowHistoryInterface
{
    public function __construct($workflowStep, WorkflowedObjectInterface $object);

    public function getId();

    public function setWorkflowStep($workflowStep);

    public function getWorkflowStep();

    public function getCreatedAt();

    public function setObject(WorkflowedObjectInterface $object);

    public function getObject();

    public function setManagedBy($managedBy);

    public function getManagedBy();
}