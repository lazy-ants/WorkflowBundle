<?php

namespace Lazyants\WorkflowBundle\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Lazyants\WorkflowBundle\Definition\WorkflowedObjectInterface;

class BaseWorkflowHistoryRepository extends EntityRepository
{
    /**
     * @param WorkflowedObjectInterface $object
     * @param int $hydrationMode
     * @return mixed
     */
    public function getLastLeavedStep(WorkflowedObjectInterface $object, $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder('wh');

        return $qb
            ->where($qb->expr()->eq('wh.object', ':object'))
            ->set('object', $object)
            ->orderBy('wh.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult($hydrationMode);
    }

    /**
     * @param WorkflowedObjectInterface $object
     * @param int $hydrationMode
     * @return array
     */
    public function getAllSteps(WorkflowedObjectInterface $object, $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder('wh');

        return $qb
            ->where($qb->expr()->eq('wh.object', ':object'))
            ->set('object', $object)
            ->orderBy('wh.id', 'ASC')
            ->getQuery()
            ->getResult($hydrationMode);
    }

    /**
     * @param WorkflowedObjectInterface $object
     * @param string $step
     * @return int
     */
    public function countStepReached(WorkflowedObjectInterface $object, $step)
    {
        $qb = $this->createQueryBuilder('wh');

        return (int)$qb
            ->select($qb->expr()->count('wh.id'))
            ->where($qb->expr()->eq('wh.object', ':object'))
            ->andWhere($qb->expr()->eq('wh.workflowStep', ':workflowStep'))
            ->set('object', $object)
            ->set('workflowStep', $step)
            ->getQuery()
            ->getSingleScalarResult();
    }
}