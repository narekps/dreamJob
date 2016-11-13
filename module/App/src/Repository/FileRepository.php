<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityManager;
use App\Entity\User as UserEntity;
use App\Entity\File as FileEntity;

class FileRepository extends BaseEntityRepository
{

    protected $alias = 'file';

    /**
     * @param FileEntity $entity
     *
     * @return array
     */
    public function getArrayCopy($entity)
    {
        $arr         = [];
        $arr['id']   = (int)$entity->getId();
        $arr['name'] = $entity->getName();

        return $arr;
    }
}
