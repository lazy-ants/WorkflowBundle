<?php

namespace Lazyants\WorkflowBundle\Definition;

interface WorkflowedObjectInterface
{
    public function setWorkflowStep($workflowStep);

    public function getWorkflowStep();
}